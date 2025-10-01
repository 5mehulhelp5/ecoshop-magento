<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue;

use Magento\Framework\FlagManager;

class ProcessMetaInfo
{
    public const PROCESSING_FLAG = 'magebees_cachewarmer_warmer_processing';
    public const QUEUE_TOTAL_FLAG = 'magebees_cachewarmer_queue_total';
    public const QUEUE_CRAWLED_FLAG = 'magebees_cachewarmer_queue_crawled';

    /**
     * @var FlagManager
     */
    private $flagManager;

    public function __construct(
        FlagManager $flagManager
    ) {
        $this->flagManager = $flagManager;
    }

    public function addToTotalPagesCrawled(int $incrementValue): void
    {
        $totalPagesCrawled = $this->getTotalPagesCrawled() + $incrementValue;
        $this->saveFlag(self::QUEUE_CRAWLED_FLAG, (string)$totalPagesCrawled);
    }

    public function addToTotalPagesQueued(int $incrementValue): void
    {
        $totalPagesQueued = $this->getTotalPagesQueued() + $incrementValue;
        $this->saveFlag(self::QUEUE_TOTAL_FLAG, (string)$totalPagesQueued);
    }

    public function isQueueLocked(): bool
    {
        return (bool)$this->getFlag(self::PROCESSING_FLAG);
    }

    public function getTotalPagesCrawled(): int
    {
        return (int)$this->getFlag(self::QUEUE_CRAWLED_FLAG);
    }

    public function getTotalPagesQueued(): int
    {
        return (int)$this->getFlag(self::QUEUE_TOTAL_FLAG);
    }

    public function setIsQueueLocked(bool $value): void
    {
        $this->saveFlag(self::PROCESSING_FLAG, (string)$value);
    }

    public function setTotalPagesQueued(int $value): void
    {
        $this->saveFlag(self::QUEUE_TOTAL_FLAG, (string)$value);
    }

    public function resetTotalPagesCrawled(): void
    {
        $this->saveFlag(self::QUEUE_CRAWLED_FLAG, 0);
    }

    protected function getFlag(string $code): string
    {
        return (string)$this->flagManager->getFlagData($code);
    }

    protected function saveFlag(string $code, $value): void
    {
        $this->flagManager->saveFlag($code, (string)$value);
    }
}
