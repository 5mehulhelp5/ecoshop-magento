<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Crawler\HttpClient;

use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Queue\Page;
use Magebees\CacheWarmer\Model\QueuePageRepository;
use Magebees\CacheWarmer\Model\ResourceModel\Queue\Page\Collection as PageCollection;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;

class AsyncClient implements CrawlerClientInterface
{
    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var QueuePageRepository
     */
    private $queuePageRepository;

    /**
     * @var Page[]
     */
    private $crawledPagesData = [];

    /**
     * @var int
     */
    private $crawledPageIndex = 0;

    /**
     * @var string|null
     */
    private $method = null;

    public function __construct(
        Config $configProvider,
        LoggerInterface $logger,
        \GuzzleHttp\Client $client,
        QueuePageRepository $queuePageRepository
    ) {
        $this->configProvider = $configProvider;
        $this->logger = $logger;
        $this->client = $client;
        $this->queuePageRepository = $queuePageRepository;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function execute(
        PageCollection $pageCollection,
        array $requestCombinations,
        \Closure $getRequestParams
    ): \Generator {
        $concurrency = $this->configProvider->getProcessesNumber();
        $batchRequests = function ($pageCollection, $requestCombinations) use ($getRequestParams) {
            /** @var Page $page */
            foreach ($pageCollection as $page) {
                foreach ($requestCombinations as $combination) {
                    if (!isset($combination['crawler_store'])
                        || (isset($combination['crawler_store'])
                            && (int)$page->getStore() === (int)$combination['crawler_store'])
                    ) {
                        yield function () use ($page, $combination, $getRequestParams) {
                            $this->crawledPagesData[$this->crawledPageIndex] = [
                                'page' => $page,
                                'combination' => $combination
                            ];

                            return $this->client->requestAsync(
                                $this->getMethod(),
                                $page->getUrl(),
                                $getRequestParams($page, $combination, ++$this->crawledPageIndex)
                            );
                        };
                    }
                }
            }
        };

        $pool = new Pool(
            $this->client,
            $batchRequests($pageCollection, $requestCombinations),
            [
                'concurrency' => $concurrency,
                'fulfilled' => function (Response $response, $index) {
                    if (isset($this->crawledPagesData[$index]['page'])) {
                        $this->crawledPagesData[$index]['response'] = $response;
                        $this->queuePageRepository->delete($this->crawledPagesData[$index]['page']);
                    }
                },
                'rejected' => function (RequestException $reason, $index) {
                    if (isset($this->crawledPagesData[$index]['page'])) {
                        $this->crawledPagesData[$index]['response'] = $reason->getResponse();
                        $this->logger->critical($reason->getMessage());
                    }
                },
            ]
        );
        $pool->promise()->wait();

        foreach ($this->crawledPagesData as $responseIndex => $response) {
            if (isset($this->crawledPagesData[$responseIndex]['page'])) {
                yield new DataObject([
                    'page' => $this->crawledPagesData[$responseIndex]['page'],
                    'status' => $this->crawledPagesData[$responseIndex]['response']
                        ? $this->crawledPagesData[$responseIndex]['response']->getStatusCode()
                        : 0,
                    'load_time' => 0, // It's unable to track async requests load time.
                    'combination' => $this->crawledPagesData[$responseIndex]['combination'],
                ]);
            }
        }

        $this->crawledPagesData = [];
        $this->crawledPageIndex = 0;
    }

    private function getMethod(): string
    {
        return $this->method ?? 'GET';
    }
}
