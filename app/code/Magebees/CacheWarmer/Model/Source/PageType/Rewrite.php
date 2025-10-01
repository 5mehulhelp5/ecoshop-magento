<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\PageType;

use Magebees\CacheWarmer\Model\Queue\Combination\Provider;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\DB\Select;
use Magento\Framework\Url as FrontendUrlBuilder;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

abstract class Rewrite extends Emulated
{
    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $rewriteCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var string
     */
    protected $rewriteType;

    public function __construct(
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
        FrontendUrlBuilder $urlBuilder,
        Emulation $appEmulation,
        State $appState,
        StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollectionFactory,
        Provider $combinationProvider,
        $isMultiStoreMode = false,
        array $stores = [],
        ?\Closure $filterCollection = null
    ) {
        parent::__construct(
            $urlBuilder,
            $appEmulation,
            $appState,
            $storeManager,
            $combinationProvider,
            $isMultiStoreMode,
            $stores,
            $filterCollection
        );
        $this->rewriteCollectionFactory = $rewriteCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @param int $storeId
     *
     * @return UrlRewriteCollection
     */
    protected function getEntityCollection(int $storeId): UrlRewriteCollection
    {
        /** @var UrlRewriteCollection $rewriteCollection */
        $rewriteCollection = $this->rewriteCollectionFactory->create()
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('entity_type', $this->rewriteType)
            ->addFilterToMap('store_id', 'main_table.store_id');
        $rewriteCollection->getSelect()->group('url_rewrite_id');

        if ($storeId) {
            $rewriteCollection->addStoreFilter($storeId);
        }

        return $rewriteCollection;
    }

    protected function getEnabledCategoriesSelect(int $storeId): Select
    {
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->setStore($storeId);
        $categoryCollection->addAttributeToFilter(CategoryInterface::KEY_IS_ACTIVE, ['eq' => 1]);

        return $categoryCollection->getAllIdsSql();
    }

    /**
     * @param $entity
     * @param $storeId
     *
     * @return mixed
     */
    protected function getUrl($entity, $storeId)
    {
        return $entity->getData('request_path');
    }
}
