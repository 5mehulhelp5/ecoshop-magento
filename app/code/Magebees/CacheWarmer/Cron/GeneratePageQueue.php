<?php

declare(strict_types=1);

namespace Magebees\CacheWarmer\Cron;

use Magebees\CacheWarmer\Exception\LockException;
use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Queue;
use Psr\Log\LoggerInterface;

class GeneratePageQueue
{
    /**
     * @var Queue\RegenerateHandler
     */
    private $regenerateHandler;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Queue\RegenerateHandler $regenerateHandler,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->regenerateHandler = $regenerateHandler;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute()
    {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        try {
            $this->regenerateHandler->execute(true);
        } catch (LockException $e) {
            $this->logger->info(__('Can\'t get a file lock for queue generation process: %1', $e->getMessage()));
        }
    }
}
