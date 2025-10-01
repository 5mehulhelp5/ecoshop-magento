<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\BackgroundJob;

use Magebees\CacheWarmer\Api\Data\BackgroundJobInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository
{
    /**
     * @var ResourceModel\Job
     */
    private $jobResource;

    /**
     * @var ResourceModel\Job\CollectionFactory
     */
    private $jobCollectionFactory;

    /**
     * @var JobFactory
     */
    private $jobFactory;

    public function __construct(
        ResourceModel\Job $jobResource,
        ResourceModel\Job\CollectionFactory $jobCollectionFactory,
        JobFactory $jobFactory
    ) {
        $this->jobResource = $jobResource;
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->jobFactory = $jobFactory;
    }

    public function save(BackgroundJobInterface $job)
    {
        try {
            $this->jobResource->save($job);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save job. Error: %1', $e->getMessage()));
        }

        return $job;
    }

    public function getById(int $jobId)
    {
        /** @var BackgroundJobInterface $job */
        $job = $this->jobFactory->create();
        $this->jobResource->load($job, $jobId);

        if (!$job->getJobId()) {
            throw new NoSuchEntityException(__('Job with specified ID "%1" not found.', $jobId));
        }

        return $job;
    }

    public function getListAndLock(string $jobCode): ResourceModel\Job\Collection
    {
        $jobCollection = $this->jobCollectionFactory->create();
        $jobCollection->addFieldToFilter(BackgroundJobInterface::JOB_CODE, $jobCode);
        $jobCollection->getSelect()->forUpdate(1);

        return $jobCollection;
    }

    public function delete(BackgroundJobInterface $job): bool
    {
        try {
            $this->jobResource->delete($job);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete job. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function getEmptyJob(): BackgroundJobInterface
    {
        return $this->jobFactory->create();
    }
}
