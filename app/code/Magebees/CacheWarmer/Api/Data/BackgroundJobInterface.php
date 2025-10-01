<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Api\Data;

interface BackgroundJobInterface
{
    public const JOB_ID = 'job_id';
    public const JOB_CODE = 'job_code';

    public function getJobId(): int;

    public function setJobId(int $jobId): BackgroundJobInterface;

    public function getJobCode(): string;

    public function setJobCode(string $jobCode): BackgroundJobInterface;
}
