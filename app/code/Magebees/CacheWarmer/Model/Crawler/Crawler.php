<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Crawler;

use Magebees\CacheWarmer\Model\Log;
use Magebees\CacheWarmer\Model\Queue\Combination;
use Magebees\CacheWarmer\Model\Queue\Page;
use Magebees\CacheWarmer\Model\ResourceModel\Queue\Page\Collection as PageCollection;
use GuzzleHttp\RequestOptions;
use Magento\Framework\App\Http\ContextFactory;
use Magento\Framework\App\Response\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Session\SessionManagerInterface;

class Crawler
{
    /**
     * @var Log
     */
    private $crawlerLog;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var ContextFactory
     */
    private $contextFactory;

    /**
     * @var Combination\Provider
     */
    private $combinationProvider;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var Request\DefaultParamProvider
     */
    private $defaultParamProvider;

    public function __construct(
        Log $crawlerLog,
        ClientFactory $clientFactory,
        ContextFactory $contextFactory,
        Combination\Provider $combinationProvider,
        SessionManagerInterface $sessionManager,
        Request\DefaultParamProvider $defaultParamProvider
    ) {
        $this->crawlerLog = $crawlerLog;
        $this->clientFactory = $clientFactory;
        $this->contextFactory = $contextFactory;
        $this->combinationProvider = $combinationProvider;
        $this->sessionManager = $sessionManager;
        $this->defaultParamProvider = $defaultParamProvider;
    }

    public function processPages(PageCollection $pageCollection): int
    {
        $pagesProcessed = 0;
        $this->crawlerLog->trim();
        $client = $this->clientFactory->create();
        $requestCombinations = $this->combinationProvider->getCombinations();
        $combinationSources = $this->combinationProvider->getCombinationSources();
        $requestParamsClosure = \Closure::fromCallable([$this, 'buildRequestParams']);

        /** @var DataObject $responseData */
        foreach ($client->execute($pageCollection, $requestCombinations, $requestParamsClosure) as $responseData) {
            /** @var Page $page */
            if ($page = $responseData->getPage()) {
                /*$combination = $responseData->getCombination();
                $crawlerLogData = [
                    'url' => $page->getUrl(),
                    'rate' => $page->getRate(),
                    'status' => $responseData->getStatus(),
                    'load_time' => round($responseData->getLoadTime() ?? 0, 3)
                ];*/

                /** @var Combination\Context\CombinationSourceInterface $source */
                /*foreach ($combinationSources as $source) {
                    $crawlerLogData = $source->prepareLog($crawlerLogData, $combination);
                }

                $this->crawlerLog->add($crawlerLogData); */
                $pagesProcessed++;
            }
        }

        return $pagesProcessed;
    }

    public function processUsingVisitParams(PageCollection $pageCollection): int
    {
        $pagesProcessed = 0;
        $this->crawlerLog->trim();
        $client = $this->clientFactory->create();
        $requestParamsClosure = \Closure::fromCallable([$this, 'buildRequestParams']);
        $combinationSources = $this->combinationProvider->getCombinationSources();

        $batchSize = $pageCollection->getPageSize();
        $pageCollection->setPageSize(1);
        $batchSize = $batchSize <= $pageCollection->getLastPageNumber()
            ? $batchSize
            : $pageCollection->getLastPageNumber();
        $currentPage = 1;

        while ($currentPage <= $batchSize) {
            $pageCollection->clear();
            $pageCollection->setCurPage($currentPage);
            $requestCombinations = [];

            foreach ($pageCollection->getItems() as $page) {
                $requestCombinations[] = [
                    'crawler_store' => $page->getStore(),
                    'crawler_currency' => $page->getData('currency'),
                    'crawler_mobile' => $page->getData('mobile'),
                    'crawler_customer_group' => $page->getData('customer_group'),
                ];
            }

            /** @var DataObject $responseData */
            foreach ($client->execute($pageCollection, $requestCombinations, $requestParamsClosure) as $responseData) {
                /** @var Page $page */
                if ($page = $responseData->getPage()) {
                    /*$combination = $responseData->getCombination();
                    $crawlerLogData = [
                        'url' => $page->getUrl(),
                        'rate' => $page->getRate(),
                        'status' => $responseData->getStatus(),
                        'load_time' => round($responseData->getLoadTime() ?? 0)
                    ]; */

                    /** @var Combination\Context\CombinationSourceInterface $source */
                    /*foreach ($combinationSources as $source) {
                        $crawlerLogData = $source->prepareLog($crawlerLogData, $combination);
                    }

                    $this->crawlerLog->add($crawlerLogData);*/
                    $pagesProcessed++;
                }
            }

            $currentPage++;
        }

        return $pagesProcessed;
    }

    public function buildRequestParams(Page $page, array $combination, int $crawledPageIndex = 0)
    {
        $httpContext = $this->contextFactory->create();
        $combinationSources = $this->combinationProvider->getCombinationSources();
        $requestParams = $this->defaultParamProvider->getDefaultParams();
        $requestParams[RequestOptions::HEADERS][RegistryConstants::CRAWLER_URL_HEADER] = rtrim($page->getUrl(), '/');

        /** @var Combination\Context\CombinationSourceInterface $source */
        foreach ($combinationSources as $source) {
            $source->modifyRequest($combination, $requestParams, $httpContext);
        }

        if ($varyString = $httpContext->getVaryString()) {
            $requestParams[RequestOptions::COOKIES][Http::COOKIE_VARY_STRING] = $varyString;
        }

        if ($crawledPageIndex) {
            $requestParams[RequestOptions::COOKIES][RegistryConstants::CRAWLER_SESSION_COOKIE_NAME] .=
                '-' . $crawledPageIndex;
        }

        /**
         * Combine all cookie data into single CookieJar object
         */
        $requestParams[RequestOptions::COOKIES] = \GuzzleHttp\Cookie\CookieJar::fromArray(
            $requestParams[RequestOptions::COOKIES],
            $this->resolveCookieDomain((string)$page->getUrl())
        );
        $requestParams[RequestOptions::HEADERS]['User-Agent'] .= ' ' . RegistryConstants::CRAWLER_AGENT_EXTENSION;

        return $requestParams;
    }

    private function resolveCookieDomain(string $pageUrl): string
    {
        if (!$this->sessionManager->getCookieDomain()) {
            preg_match('/^https?\:\/\/(?<domain>[^\/?#]+)(?:[\/?#]|$)/', $pageUrl, $matches);

            return $matches['domain'] ?? '';
        }

        return $this->sessionManager->getCookieDomain();
    }
}
