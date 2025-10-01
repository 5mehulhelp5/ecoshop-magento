<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\BackgroundJob\ResourceModel\Job;

use Magebees\CacheWarmer\Model\BackgroundJob\Job;
use Magebees\CacheWarmer\Model\BackgroundJob\ResourceModel\Job as JobResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Job::class, JobResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
