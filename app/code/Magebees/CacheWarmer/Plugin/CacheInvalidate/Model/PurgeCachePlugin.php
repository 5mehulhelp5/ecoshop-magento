<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Plugin\CacheInvalidate\Model;

use Magebees\CacheWarmer\Model\FlushesLog\FlushesLogProvider;
use Magebees\CacheWarmer\Model\Queue\RegenerateHandler;
use Magebees\CacheWarmer\Model\Repository\FlushesLogRepository;
use Magento\CacheInvalidate\Model\PurgeCache;
use Psr\Log\LoggerInterface;

class PurgeCachePlugin
{
    /**
     * @var FlushesLogProvider
     */
    private $flushesLogProvider;

    /**
     * @var FlushesLogRepository
     */
    private $flushesLogRepository;

    /**
     * @var RegenerateHandler
     */
    private $regenerateHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FlushesLogProvider $flushesLogProvider,
        FlushesLogRepository $flushesLogRepository,
        RegenerateHandler $regenerateHandler,
        LoggerInterface $logger
    ) {
        $this->flushesLogProvider = $flushesLogProvider;
        $this->flushesLogRepository = $flushesLogRepository;
        $this->regenerateHandler = $regenerateHandler;
        $this->logger = $logger;
    }

    /**
     * @see \Magento\CacheInvalidate\Observer\InvalidateVarnishObserver::execute
     *
     * @param PurgeCache $subject
     * @param \Closure $proceed
     * @param $tagsPattern
     * @return mixed
     */
    public function aroundSendPurgeRequest(PurgeCache $subject, \Closure $proceed, $tagsPattern)
    {
        /**
         * We must log Varnish cache flushes only after succeed flush
         */
        if ($result = $proceed($tagsPattern)) {
            try {
                $tagsPattern = !is_array($tagsPattern)
                    ? [$tagsPattern]
                    : $tagsPattern;
                $tags = [];
                foreach ($tagsPattern as $pattern) {
                    // phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                    $tags = array_merge($tags, $this->unpackTags($pattern));
                }
                $mode = \Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG . ' Varnish';
                $flushLogModel = $this->flushesLogProvider->getFlushesLogModel($mode, $tags);
                $this->flushesLogRepository->save($flushLogModel);
                $this->regenerateHandler->execute();
            } catch (\Throwable $e) {
                $this->logger->critical($e);
            }
        }

        return $result;
    }

    /**
     * @param string $tagsPattern
     * @return array
     */
    private function unpackTags(string $tagsPattern): array
    {
        $tags = str_replace(['((^|,)', '(,|$))'], '', $tagsPattern);

        return explode('|', $tags);
    }
}
