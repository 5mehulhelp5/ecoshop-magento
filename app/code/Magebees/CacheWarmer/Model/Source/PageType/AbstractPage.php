<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\PageType;

abstract class AbstractPage
{
    /**
     * @var bool
     */
    protected $isMultiStoreMode;

    /**
     * @var array
     */
    protected $stores;

    public function __construct(
        $isMultiStoreMode = false,
        $stores = []
    ) {
        $this->isMultiStoreMode = $isMultiStoreMode;
        $this->stores = $stores;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    abstract public function getAllPages($limit = 0);
}
