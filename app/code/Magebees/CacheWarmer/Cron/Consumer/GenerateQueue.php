<?php

declare(strict_types=1);

namespace Magebees\CacheWarmer\Cron\Consumer;

use Magebees\CacheWarmer\Cron\GeneratePageQueue;
use Magebees\CacheWarmer\Model\BackgroundJob;
use Magebees\CacheWarmer\Model\Config;

class GenerateQueue implements JobConsumerInterface
{
    public const JOB_CODE = 'generate_queue';

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var GeneratePageQueue
     */
    private $generatePageQueueCommand;

    /**
     * @var BackgroundJob\Repository
     */
    private $jobRepository;

    public function __construct(
        Config $configProvider,
        GeneratePageQueue $generatePageQueueCommand,
        BackgroundJob\Repository $jobRepository
    ) {
        $this->configProvider = $configProvider;
        $this->generatePageQueueCommand = $generatePageQueueCommand;
        $this->jobRepository = $jobRepository;
    }

    public function consume()
    {
        if ($this->isNeedToRegenerate()) {
            $this->generatePageQueueCommand->execute();
        }
    }

    private function isNeedToRegenerate(): bool
    {
        if (!$this->configProvider->getQueueAfterGenerate()) {
            return false;
        }

        $jobCollection = $this->jobRepository->getListAndLock(self::JOB_CODE);
        $regenerateAllowed = (bool)$jobCollection->count();

        foreach ($jobCollection as $job) {
            $this->jobRepository->delete($job);
        }

        return $regenerateAllowed;
    }
}
