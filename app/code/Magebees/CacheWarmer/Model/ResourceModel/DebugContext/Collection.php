<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel\DebugContext;

use Magebees\CacheWarmer\Model\Debug\DebugContext;
use Magebees\CacheWarmer\Model\ResourceModel\DebugContext as DebugContextResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _construct()
    {
        $this->_init(DebugContext::class, DebugContextResource::class);
    }
}
