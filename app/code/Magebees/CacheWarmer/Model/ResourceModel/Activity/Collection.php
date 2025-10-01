<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel\Activity;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Select;
use Magebees\CacheWarmer\Model\Activity;
use Magebees\CacheWarmer\Model\ResourceModel\Activity as ActivityResource;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Activity::class, ActivityResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Get data from activity table
     *
     * @param $queueLimit
     *
     * @return array
     */
    public function getPagesData($queueLimit)
    {
        $this->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(['activity_id' => 'id', 'url', 'rate', 'store'])
            ->where('status NOT IN (?)', [404])
            ->limit($queueLimit);

        return $this->getData();
    }
}
