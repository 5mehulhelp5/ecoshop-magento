<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Crawler\HttpClient;

use Magebees\CacheWarmer\Model\Queue\Page;
use Magebees\CacheWarmer\Model\QueuePageRepository;
use Magebees\CacheWarmer\Model\ResourceModel\Queue\Page\Collection as PageCollection;
use GuzzleHttp\Exception\ClientException;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;

class Client implements CrawlerClientInterface
{
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
     * @var string|null
     */
    private $method = null;

    public function __construct(
        LoggerInterface $logger,
        \GuzzleHttp\Client $client,
        QueuePageRepository $queuePageRepository
    ) {
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
        /** @var Page $page */
        foreach ($pageCollection as $page) {
            foreach ($requestCombinations as $combination) {
                $response = null;
                $requestStartTime = microtime(true);

                if (!isset($combination['crawler_store'])
                    || (isset($combination['crawler_store'])
                        && (int)$page->getStore() === (int)$combination['crawler_store'])
                ) {
                    try {
                        $response = $this->client->request(
                            $this->getMethod(),
                            $page->getUrl(),
                            $getRequestParams($page, $combination)
                        );
                    } catch (ClientException $e) {
                        $response = $e->getResponse();
                    } catch (\Exception $e) {
                        $this->logger->critical($e->getMessage());
                    } finally {
                        $requestTime = microtime(true) - $requestStartTime;
                    }
                }

                if (!$response) {
                    continue;
                }

                yield new DataObject([
                    'page' => $page,
                    'status' => $response->getStatusCode(),
                    'load_time' => $requestTime,
                    'combination' => $combination,
                ]);
            }

            $this->queuePageRepository->delete($page);
        }
    }

    private function getMethod(): string
    {
        return $this->method ?? 'GET';
    }
}
