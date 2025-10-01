<?php

declare(strict_types=1);

namespace Magebees\CacheWarmer\Cron;

use Magebees\CacheWarmer\Exception\LockException;
use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Queue;
use Psr\Log\LoggerInterface;

class ProcessPageQueue
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Queue $queue,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->queue = $queue;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute()
    {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        try {
            $this->queue->process();
        } catch (LockException $e) {
            $this->logger->info(__('Can\'t get a file lock for queue processing process: %1', $e->getMessage()));
        }
    }
}
