<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\DataProvider\Category as CategoryDataProvider;

/**
 * Layer category filter
 */
class Category extends \Magento\CatalogSearch\Model\Layer\Filter\Category
{
    private $escaper;
    private $dataProvider;
    protected $_coreResource;
    protected $_requestVar;
    protected $level;
    protected $resourceConnection;
    protected $_scopeConfig;
    protected $_categoryFactory;
    protected $category;
    protected $categoryRepository;
    protected $itemCollectionProvider;
    protected $layerHelper;
    protected $request;
    protected $_collection;
    protected $_objectManager;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $itemCollectionProvider,
        \Magebees\Layerednavigation\Helper\Data $layerHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Helper\Category $category,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $escaper,
            $categoryDataProviderFactory,
            $data
        );
        $this->_coreResource = $resource;
        $this->escaper = $escaper;
        $this->_requestVar = "cat";
        $this->dataProvider = $categoryDataProviderFactory->create([
            "layer" => $this->getLayer(),
        ]);
        $this->level = 0;
        $this->resourceConnection = $resourceConnection;
        $this->_scopeConfig = $scopeConfig;
        $this->_categoryFactory = $categoryFactory;
        $this->category = $category;
        $this->categoryRepository = $categoryRepository;
        $this->itemCollectionProvider = $itemCollectionProvider;
        $this->layerHelper = $layerHelper;
        $this->request = $request;
        $this->_collection = $collection;
        $this->_objectManager = $objectManager;
    }

    /**
     * Apply category filter to product collection
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $currentEngine = $this->layerHelper->getCurrentSearchEngine();
        $IsElasticSearchEnabled = $this->layerHelper->IsElasticSearch();
        $categoryId = $request->getParam($this->_requestVar);
        if (empty($categoryId)) {
            return $this;
        }

        $is_enabled = $this->_scopeConfig->getValue(
            "layerednavigation/setting/enable",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $display_mode = $this->_scopeConfig->getValue(
            "layerednavigation/category_filter/display_mode",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $enable_multiselect = $this->_scopeConfig->getValue(
            "layerednavigation/category_filter/enable_multiselect",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $is_default_enabled = $this->_scopeConfig->getValue(
            "advanced/modules_disable_output/Magebees_Layerednavigation",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($is_default_enabled == 0) {
            if ($is_enabled) {
                if ($enable_multiselect && $display_mode == 0) {
                    if (preg_match("/,/", $categoryId)) {
                        if ($IsElasticSearchEnabled) {
                            $categoryIdArr = explode(",", (string) $categoryId);
                            // FIX: using addCategoriesFilter instead of category_ids
                            $this->getLayer()
                                ->getProductCollection()
                                ->addCategoriesFilter(["in" => $categoryIdArr]);

                            foreach ($categoryIdArr as $category) {
                                $applied_params = $request->getParams();
                                if (isset($applied_params["cat"])) {
                                    $cat_id = $category;
                                    $category_data = $this->_categoryFactory
                                        ->create()
                                        ->load($category);
                                    $this->dataProvider->setCategoryId($cat_id);

                                    if (
                                        $category_data->getId() &&
                                        $this->dataProvider->isValid()
                                    ) {
                                        $this->getLayer()
                                            ->getState()
                                            ->addFilter(
                                                $this->_createItem(
                                                    $category_data->getName(),
                                                    $category
                                                )
                                            );
                                    }
                                }
                            }
                        } else {
                            // FIX: non-elastic: use addCategoriesFilter to avoid breaking getSize()/pagination
                            $categoryIdArr = explode(",", (string) $categoryId);
                            $this->getLayer()
                                ->getProductCollection()
                                ->addCategoriesFilter(["in" => $categoryIdArr]);

                            foreach ($categoryIdArr as $category) {
                                $applied_params = $request->getParams();
                                if (isset($applied_params["id"])) {
                                    $cat_id = $applied_params["id"];
                                    $category_data = $this->_categoryFactory
                                        ->create()
                                        ->load($category);
                                    $this->dataProvider->setCategoryId($cat_id);
                                    if (
                                        $request->getParam("id") !=
                                            $category_data->getId() &&
                                        $this->dataProvider->isValid()
                                    ) {
                                        $this->getLayer()
                                            ->getState()
                                            ->addFilter(
                                                $this->_createItem(
                                                    $category_data->getName(),
                                                    $category
                                                )
                                            );
                                    }
                                }
                            }
                        }
                        return $this;
                    }
                }
            } else {
                return parent::apply($request);
            }
        }
        return parent::apply($request);
    }

    /**
     * Get data array for building category filter items
     */
    protected function _getItemsData()
    {
        $is_enabled = $this->_scopeConfig->getValue(
            "layerednavigation/setting/enable",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $is_default_enabled = $this->_scopeConfig->getValue(
            "advanced/modules_disable_output/Magebees_Layerednavigation",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($is_default_enabled == 0) {
            if ($is_enabled) {
                $sku_arr = [];
                $display_mode = $this->_scopeConfig->getValue(
                    "layerednavigation/category_filter/display_mode",
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $enable_multiselect = $this->_scopeConfig->getValue(
                    "layerednavigation/category_filter/enable_multiselect",
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );

                $activeFilters = $this->_objectManager
                    ->get("\Magento\LayeredNavigation\Block\Navigation\State")
                    ->getActiveFilters();
                $applied_filter_count = count($activeFilters);

                // usual faceted data
                $productCollection = $this->getLayer()->getProductCollection();
                $optionsFacetedData = $productCollection->getFacetedData("category");

                if ($enable_multiselect && $display_mode == 0) {
                    // === FIXED CODE START ===
                    // 1) Build base collection that includes all active filters EXCEPT category
                    $baseCollection = $this->_objectManager->create(
                        \Magento\Catalog\Model\ResourceModel\Product\Collection::class
                    );
                    $baseCollection->addAttributeToSelect('sku');
                    $baseCollection->setStore($this->_storeManager->getStore()->getId());

                    try {
                        $visibility = $this->_objectManager->create(
                            \Magento\Catalog\Model\Product\Visibility::class
                        );
                        $baseCollection->setVisibility($visibility->getVisibleInCatalogIds());
                    } catch (\Exception $e) {
                        // ignore
                    }

                    $baseCollection->addAttributeToFilter(
                        'status',
                        \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
                    );

                    $stateFilters = $this->getLayer()->getState()->getFilters();
                    foreach ($stateFilters as $stateFilter) {
                        $filterModel = $stateFilter->getFilter();
                        if (! $filterModel) {
                            continue;
                        }
                        if (method_exists($filterModel, 'getRequestVar') &&
                            $filterModel->getRequestVar() == $this->_requestVar) {
                            // skip category filter
                            continue;
                        }
                        if (method_exists($filterModel, 'applyFilterToCollection')) {
                            try {
                                $filterModel->applyFilterToCollection($baseCollection, $stateFilter->getValue());
                            } catch (\Exception $e) {
                                // ignore and continue
                            }
                        } else {
                            $rv = method_exists($filterModel, 'getRequestVar') ? $filterModel->getRequestVar() : null;
                            if ($rv && $this->request->getParam($rv)) {
                                $val = $this->request->getParam($rv);
                                if (is_array($val)) {
                                    $baseCollection->addFieldToFilter($rv, ['in' => $val]);
                                } else {
                                    $baseCollection->addFieldToFilter($rv, $val);
                                }
                            }
                        }
                    }

                    // 2) Get SKUs for products matching all non-category filters
                    $connection = $this->resourceConnection->getConnection();
                    $select = $baseCollection->getSelect();
                    $rows = $connection->fetchAll($select);
                    $sku_arr = [];
                    foreach ($rows as $r) {
                        if (isset($r['sku'])) {
                            $sku_arr[] = $r['sku'];
                        } elseif (isset($r['cpe.sku'])) {
                            $sku_arr[] = $r['cpe.sku'];
                        }
                    }

                    // 3) Determine the PAGE category (prefer 'id' request param). This is the key change:
                    $appliedParams = $this->request->getParams();
                    if (!empty($appliedParams['id'])) {
                        $pageCategoryId = (int)$appliedParams['id'];
                    } else {
                        $pageCategoryId = (int)$this->dataProvider->getCategory()->getId();
                    }

                    // load the page category and get its children (so we always show the page's child categories)
                    $pageCategory = $this->_categoryFactory->create()->load($pageCategoryId);
                    $categories = $pageCategory->getChildrenCategories();

                    // 4) Compute counts per category via single DB query if possible
                    $countsByCategory = [];
                    if (!empty($sku_arr)) {
                        $ccpTable = $this->_coreResource->getTableName('catalog_category_product');
                        $cpeTable = $this->_coreResource->getTableName('catalog_product_entity');

                        $selectCounts = $connection->select()
                            ->from(['ccp' => $ccpTable], ['category_id', 'cnt' => new \Zend_Db_Expr('COUNT(DISTINCT ccp.product_id)')])
                            ->join(['cpe' => $cpeTable], 'ccp.product_id = cpe.entity_id', [])
                            ->where('cpe.sku IN (?)', $sku_arr)
                            ->group('ccp.category_id');

                        $rows = $connection->fetchAll($selectCounts);
                        foreach ($rows as $r) {
                            $countsByCategory[(int)$r['category_id']] = (int)$r['cnt'];
                        }
                    }

                    // selected categories — keep them visible even if count = 0
                    $selectedCategories = explode(',', (string)$this->request->getParam($this->_requestVar));

                    // 5) Build items from categories (children of page category)
                    foreach ($categories as $category) {
                        if (! $category->getIsActive()) {
                            continue;
                        }

                        $catId = (int)$category->getId();

                        if (isset($countsByCategory[$catId])) {
                            $count = (int)$countsByCategory[$catId];
                        } elseif (isset($optionsFacetedData[$catId]['count'])) {
                            $count = (int)$optionsFacetedData[$catId]['count'];
                        } else {
                            $count = 0;
                        }

                        if ($count > 0 || in_array($catId, $selectedCategories)) {
                            $this->itemDataBuilder->addItemData(
                                $this->escaper->escapeHtml($category->getName()),
                                $catId,
                                $count
                            );
                        }
                    }

                    return $this->itemDataBuilder->build();
                    // === FIXED CODE END ===
                }

                // (other display modes unchanged)
                elseif ($display_mode == 0 || $display_mode == 1 || $display_mode == 2) {
                    $category = $this->dataProvider->getCategory();
                    $collectionSize = $productCollection->getSize();
                    $categories = $category->getChildrenCategories();

                    if ($category->getIsActive()) {
                        foreach ($categories as $category) {
                            if (
                                $category->getIsActive() &&
                                isset($optionsFacetedData[$category->getId()]) &&
                                $this->isOptionReducesResults(
                                    $optionsFacetedData[$category->getId()]["count"],
                                    $collectionSize
                                )
                            ) {
                                $this->itemDataBuilder->addItemData(
                                    $this->escaper->escapeHtml($category->getName()),
                                    $category->getId(),
                                    $optionsFacetedData[$category->getId()]["count"]
                                );
                                if ($display_mode == 2) {
                                    $this->getChildCategoryData($category);
                                }
                            }
                        }
                    }
                }
                // (tree mode unchanged)
                elseif ($display_mode == 4 || $display_mode == 3) {
                    // Use unfiltered product collection (page + non-category filters applied) to get SKUs
                    $connection = $this->resourceConnection->getConnection();
                    $unfilteredCollection = $this->getUnfilteredProductCollection();
                    $select = $unfilteredCollection->getSelect();
                    $result = $connection->query($select)->fetchAll();
                    foreach ($result as $res) {
                        if (isset($res["sku"])) {
                            $sku_arr[] = $res["sku"];
                        } elseif (isset($res["cpe.sku"])) {
                            $sku_arr[] = $res["cpe.sku"];
                        }
                    }
                    $sku_str = implode(",", $sku_arr);

                    $categories = $this->category->getStoreCategories();
                    foreach ($categories as $category) {
                        if ($category->getIsActive()) {
                            $category = $category->getId();
                            $category = $this->_categoryFactory->create()->load($category);
                            $collection = $this->_objectManager->create(
                                "\Magento\Catalog\Model\ResourceModel\Product\Collection"
                            );
                            if ($applied_filter_count > 0) {
                                $prodCollection = $collection
                                    ->addCategoryFilter($category)
                                    ->addFieldToFilter("sku", ["in" => $sku_arr]);
                            } else {
                                $prodCollection = $collection->addCategoryFilter($category);
                            }
                            $count = $prodCollection->count();

                            if ($count) {
                                $this->itemDataBuilder->addItemData(
                                    $this->escaper->escapeHtml($category->getName()),
                                    $category->getId(),
                                    $count
                                );
                            }
                            $this->getChildCatForTree($category);
                        }
                    }
                }
                return $this->itemDataBuilder->build();
            } else {
                return parent::_getItemsData();
            }
        }
        return parent::_getItemsData();
    }

    // (you can keep them as-is since they are not directly affected by the bug)
    public function getChildCatForTree($category, $level = 0)
    {
        $sku_arr = [];
        $activeFilters = $this->_objectManager
            ->get("\Magento\LayeredNavigation\Block\Navigation\State")
            ->getActiveFilters();
        $applied_filter_count = count($activeFilters);
        $connection = $this->resourceConnection->getConnection();

        // Use unfiltered product collection (page + non-category filters) to get SKUs for accurate counting
        $unfilteredCollection = $this->getUnfilteredProductCollection();
        $select = $unfilteredCollection->getSelect();
        $result = $connection->query($select)->fetchAll();
        foreach ($result as $res) {
            if (isset($res["sku"])) {
                $sku_arr[] = $res["sku"];
            } elseif (isset($res["cpe.sku"])) {
                $sku_arr[] = $res["cpe.sku"];
            }
        }
        $sku_str = implode(",", $sku_arr);
        $level++;
        $child = 0;
        $display_mode = $this->_scopeConfig->getValue(
            "layerednavigation/category_filter/display_mode",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $nonEscapableNbspChar = html_entity_decode(
            "&#160;",
            ENT_NOQUOTES,
            "UTF-8"
        );
        $category = $category->getId();
        $category = $this->_categoryFactory->create()->load($category);
        $subcategories = $category->getChildrenCategories();
        if ($subcategories) {
            foreach ($subcategories as $subcategory) {
                $category = $subcategory->getId();
                $category = $this->_categoryFactory->create()->load($category);
                if ($subcategory->getIsActive()) {
                    $collection = $this->_objectManager->create(
                        "\Magento\Catalog\Model\ResourceModel\Product\Collection"
                    );
                    if ($applied_filter_count > 0) {
                        $prodCollection = $collection
                            ->addCategoryFilter($category)
                            ->addFieldToFilter("sku", ["in" => $sku_arr]);
                    } else {
                        $prodCollection = $collection->addCategoryFilter(
                            $category
                        );
                    }
                    $count = $prodCollection->count();
                    if ($count) {
                        $child++;
                        if ($display_mode == 3) {
                            $this->itemDataBuilder->addItemData(
                                $subcategory->getName(),
                                $subcategory->getId(),
                                $count
                            );
                        } elseif ($display_mode == 4) {
                            $this->itemDataBuilder->addItemData(
                                $subcategory->getName(),
                                $subcategory->getId(),
                                $count
                            );
                        }
                    }
                    if ($display_mode == 4) {
                        $this->getChildCatForTree($subcategory, $level);
                    }
                }
            }
            return $this;
        }
    }

    public function getChildCategoryData($category)
    {
        $this->level++;
        $nonEscapableNbspChar = html_entity_decode(
            "&#160;",
            ENT_NOQUOTES,
            "UTF-8"
        );
        $display_mode = $this->_scopeConfig->getValue(
            "layerednavigation/category_filter/display_mode",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // === FIXED CODE START ===
        // Use filtered collection only if other filters applied, else use full collection
        $activeFilters = $this->_objectManager
            ->get("\Magento\LayeredNavigation\Block\Navigation\State")
            ->getActiveFilters();

        $otherFiltersApplied = false;
        foreach ($activeFilters as $filter) {
            if ($filter->getFilter()->getRequestVar() !== 'cat') {
                $otherFiltersApplied = true;
                break;
            }
        }

        // If other filters are applied, use the layer's product collection (which includes other filters)
        // Otherwise use the unfiltered product collection (page + default filters)
        if ($otherFiltersApplied) {
            $productCollection = $this->getLayer()->getProductCollection();
        } else {
            $productCollection = $this->getUnfilteredProductCollection();
        }
        $optionsFacetedData = $productCollection->getFacetedData("category");
        // === FIXED CODE END ===

        $categories = $category->getChildrenCategories();
        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                if (
                    $category->getIsActive() &&
                    isset($optionsFacetedData[$category->getId()]) &&
                    isset($optionsFacetedData[$category->getId()]["count"])
                ) {
                    // === FIXED CODE: hide categories with zero products if other filters applied
                    if ($optionsFacetedData[$category->getId()]["count"] > 0) {
                        $this->itemDataBuilder->addItemData(
                            $category->getName(),
                            $category->getId(),
                            $optionsFacetedData[$category->getId()]["count"]
                        );
                        $this->getChildCategoryData($category);
                    }
                }
            }
        }
        return $this;
    }


    private function getUnfilteredProductCollection()
    {
        $layer = $this->getLayer();
        $productCollection = $this->itemCollectionProvider->getCollection(
            $layer->getCurrentCategory()
        );
        $layer->prepareProductCollection($productCollection);
        return $productCollection;
    }
}
