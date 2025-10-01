<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Queue;

use Magebees\CacheWarmer\Cron\Consumer\GenerateQueue;
use Magebees\CacheWarmer\Model\BackgroundJob\Repository;
use Magebees\CacheWarmer\Model\Config;
use Magebees\CacheWarmer\Model\Debug\ContextDebugService;
use Magebees\CacheWarmer\Model\Queue;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class RegenerateHandler
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var Repository
     */
    private $jobRepository;

    /**
     * @var ContextDebugService
     */
    private $contextDebugService;

    /**
     * @var bool
     */
    private $isQueueGenerated = false;

    public function __construct(
        Config $config,
        Queue $queue,
        State $appState,
        Repository $jobRepository,
        ?ContextDebugService $contextDebugService = null
    ) {
        $this->config = $config;
        $this->queue = $queue;
        $this->appState = $appState;
        $this->jobRepository = $jobRepository;
        $this->contextDebugService = $contextDebugService ?:
            ObjectManager::getInstance()->get(ContextDebugService::class);
    }

    /**
     * Regenerate pages queue if it possible or create background job to regenerate queue via cron
     *
     * @param bool|null $immediate
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(?bool $immediate = null): array
    {
        $result = true;
        $processedItems = 0;
        $immediate = $immediate ?? !$this->config->isRegenerateQueueInBackground()
            && $this->config->getQueueAfterGenerate();

        if (!$this->isAllowedToRegenerate()) {
            return [$result, $processedItems];
        }

        if ($immediate) {
            try {
                $this->appState->setAreaCode(Area::AREA_GLOBAL);
            } catch (\Exception $e) {
                null;
                //launched from admin
                //(emulateArea not working due the area emulation in \Magebees\CacheWarmer\Model\Source\PageType\Emulated)
            }

            $this->queue->forceUnlock();

            if (!$this->isQueueGenerated) {
                $this->contextDebugService->flush();
                list($result, $processedItems) = $this->queue->generate();
                $this->isQueueGenerated = $result;
            }
        } elseif ($this->config->isRegenerateQueueInBackground()) {
            $regenerateJob = $this->jobRepository->getEmptyJob();
            $regenerateJob->setJobCode(GenerateQueue::JOB_CODE);
            $this->jobRepository->save($regenerateJob);
        }

        return [$result, $processedItems];
    }

    /**
     * Queue regeneration is not allowed in frontend area to prevent performance issues on frontend
     */
    private function isAllowedToRegenerate(): bool
    {
        return $this->config->isModuleEnabled()
            && !$this->isFrontendArea();
    }

    private function isFrontendArea(): bool
    {
        $isFrontend = false;

        try {
            $isFrontend = $this->appState->getAreaCode() == Area::AREA_FRONTEND;
        } catch (LocalizedException $e) {
            null;
        }

        return $isFrontend;
    }
}
