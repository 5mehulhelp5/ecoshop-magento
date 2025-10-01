<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\ResourceModel\FlushPages;

use Magebees\CacheWarmer\Model\FlushPages;
use Magebees\CacheWarmer\Model\ResourceModel\FlushPages as FlushPagesResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _construct()
    {
        $this->_init(FlushPages::class, FlushPagesResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
