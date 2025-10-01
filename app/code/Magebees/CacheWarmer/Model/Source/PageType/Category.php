<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\PageType;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite as UrlRewrite;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

class Category extends Rewrite
{
    /**
     * @var string
     */
    protected $rewriteType = UrlRewrite::ENTITY_TYPE_CATEGORY;

    /**
     * @param int $storeId
     * @return UrlRewriteCollection
     */
    protected function getEntityCollection(int $storeId): UrlRewriteCollection
    {
        $collection = parent::getEntityCollection($storeId);
        $collection->addFieldToFilter('entity_id', ['in' => $this->getEnabledCategoriesSelect($storeId)]);

        return $collection;
    }
}
