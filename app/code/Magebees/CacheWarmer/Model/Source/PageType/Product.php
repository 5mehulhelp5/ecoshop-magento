<?php
/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model\Source\PageType;

use Magebees\CacheWarmer\Model\Queue\Combination\Provider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\CatalogUrlRewrite\Model\ResourceModel\Category\Product as CatalogProductUrlRewrite;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Url as FrontendUrlBuilder;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite as UrlRewrite;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

class Product extends Rewrite
{
    /**
     * @var string
     */
    protected $rewriteType = UrlRewrite::ENTITY_TYPE_PRODUCT;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AttributeResource
     */
    protected $attributeResource;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ProductStatus
     */
    protected $productStatus;

    /**
     * @var ProductVisibility
     */
    protected $productVisibility;

    public function __construct(
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
        FrontendUrlBuilder $urlBuilder,
        Emulation $appEmulation,
        State $appState,
        StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollectionFactory,
        Provider $combinationProvider,
        ScopeConfigInterface $scopeConfig,
        AttributeResource $attributeResource,
        MetadataPool $metadataPool,
        ProductStatus $productStatus,
        ProductVisibility $productVisibility,
        $isMultiStoreMode = false,
        array $stores = [],
        ?\Closure $filterCollection = null
    ) {
        parent::__construct(
            $rewriteCollectionFactory,
            $urlBuilder,
            $appEmulation,
            $appState,
            $storeManager,
            $categoryCollectionFactory,
            $combinationProvider,
            $isMultiStoreMode,
            $stores,
            $filterCollection
        );
        $this->scopeConfig = $scopeConfig;
        $this->attributeResource = $attributeResource;
        $this->metadataPool = $metadataPool;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }

    /**
     * @param int $storeId
     * @return UrlRewriteCollection
     */
    protected function getEntityCollection(int $storeId): UrlRewriteCollection
    {
        $collection = parent::getEntityCollection($storeId);

        $collection->getSelect()->joinLeft(
            ['relation' => $collection->getTable(CatalogProductUrlRewrite::TABLE_NAME)],
            'main_table.url_rewrite_id = relation.url_rewrite_id',
            ['relation.category_id', 'relation.product_id']
        );

        if ($this->isUseCategoriesPathForProductUrls($storeId)) {
            $collection->getSelect()->where(
                'relation.category_id IN (?)',
                $this->getEnabledCategoriesSelect($storeId)
            );
        } else {
            $collection->getSelect()->where('relation.category_id IS NULL');
        }

        $this->joinAttributes($collection);

        return $collection;
    }

    private function joinAttributes(UrlRewriteCollection $collection)
    {
        /** @var \Magento\Framework\EntityManager\EntityMetadataInterface $metadata */
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $linkField = $metadata->getLinkField();
        $collection->getSelect()->joinInner(
            ['product_entity' => $collection->getTable('catalog_product_entity')],
            'main_table.entity_id = product_entity.entity_id',
            []
        );

        $codes = [
            'status' => [
                'table' => 'catalog_product_entity_int',
                'values' => $this->productStatus->getVisibleStatusIds()
            ],
            'visibility' => [
                'table' => 'catalog_product_entity_int',
                'values' => $this->productVisibility->getVisibleInSiteIds()
            ]
        ];
        foreach ($codes as $code => $config) {
            $attributeTable = $collection->getTable($config['table']);
            $attributeId = $this->attributeResource->getIdByCode(ModelProduct::ENTITY, $code);
            $alias = 'product_' . $code;
            $defaultAlias = $alias . '_default';
            $collection->getSelect()->joinInner(
                [$defaultAlias => $attributeTable],
                $defaultAlias . '.' . $linkField . ' = product_entity.' . $linkField
                . ' AND ' . $defaultAlias . '.attribute_id = ' . $attributeId
                . ' AND ' . $defaultAlias . '.store_id = 0',
                []
            )->joinLeft(
                [$alias => $attributeTable],
                $alias . '.' . $linkField . ' = product_entity.' . $linkField
                . ' AND ' . $alias . '.attribute_id = ' . $attributeId
                . ' AND ' . $alias . '.store_id = main_table.store_id',
                []
            )->where(
                $collection->getConnection()->quoteInto(
                    '(' . $alias . '.value_id > 0 AND ' . $alias . '.value IN (?))'
                    . ' OR (' . $alias . '.value_id IS NULL AND ' . $defaultAlias . '.value IN (?))',
                    $config['values']
                )
            );
        }
    }

    /**
     * @param $storeId
     * @return bool
     */
    private function isUseCategoriesPathForProductUrls($storeId)
    {
        if ((int)$storeId) {
            return $this->scopeConfig->isSetFlag(
                \Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $this->scopeConfig->isSetFlag(\Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY);
    }
}
