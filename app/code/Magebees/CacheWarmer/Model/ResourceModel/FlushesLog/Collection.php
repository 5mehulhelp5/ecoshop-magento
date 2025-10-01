<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel\FlushesLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magebees\CacheWarmer\Model\FlushesLog;
use Magebees\CacheWarmer\Model\ResourceModel\FlushesLog as FlushesLogResource;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(FlushesLog::class, FlushesLogResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
