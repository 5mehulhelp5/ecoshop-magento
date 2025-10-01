<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\BackgroundJob\ResourceModel;

use Magebees\CacheWarmer\Api\Data\BackgroundJobInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Job extends AbstractDb
{
    public const TABLE_NAME = 'magebees_cachewarmer_job_queue';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, BackgroundJobInterface::JOB_ID);
    }
}
