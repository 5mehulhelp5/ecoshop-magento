<?php

namespace Magebees\Layerednavigation\Model\Layer\Filter;

class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{
   
    protected $layerHelper;

    protected $tagFilter;
     
    protected $itemCollectionProvider;
    protected $_stockResource;

    protected $_resource;
    protected $_scopeConfig;
    protected $request;
    protected $resourceConnection;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Filter\StripTags $tagFilter,
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $itemCollectionProvider,
        \Magebees\Layerednavigation\Helper\Data $layerHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory $filterAttributeFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $tagFilter,
            $data
        );
        $this->_resource = $filterAttributeFactory->create();
        $this->tagFilter = $tagFilter;
        $this->itemCollectionProvider = $itemCollectionProvider;
        $this->layerHelper = $layerHelper;
        $this->_scopeConfig=$scopeConfig;
        $this->request=$request;
        $this->resourceConnection = $resourceConnection;
        $this->_stockResource = $stockResource;
    }

    protected function _getResource()
    {
        return $this->_resource;
    }
    /**
     * Apply attribute option filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $currentEngine=$this->layerHelper->getCurrentSearchEngine();
        $IsElasticSearchEnabled=$this->layerHelper->IsElasticSearch();
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                if($IsElasticSearchEnabled)
            {
                    $attributeValue = $request->getParam($this->_requestVar);
        if (empty($attributeValue) && !is_numeric($attributeValue)) {
            return $this;
        }
        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $attributeValuesArr=explode(',', (string)$attributeValue);
        $productCollection = $this->getLayer()
            ->getProductCollection();
        $productCollection->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);
         foreach ($attributeValuesArr as $i => $attrValue) {
                    $label = $this->getOptionText($attrValue);
                    $this->getLayer()->getState()->addFilter($this->_createItem($label, $attrValue));
                }

       /* $label = $this->getOptionText($attributeValue);
        $this->getLayer()
            ->getState()
            ->addFilter($this->_createItem($label, $attributeValue));*/

       // $this->setItems([]); // set items to disable show filtering
        return $this;
                }
                else
                {
                     $attribute = $this->getAttributeModel();
                $attributeValues = $request->getParam($this->_requestVar);

                if (empty($attributeValues)) {
                    return $this;
                }
                if ($attributeValues) {
                    $attributeValuesArr=explode(',', (string)$attributeValues);
                } else {
                    return parent::apply($request);
                }
                    $collection = $this->getLayer()->getProductCollection();
                    $alias      = $this->getAttributeModel()->getAttributeCode() . '_idx';
                    $connection = $this->_getResource()->getConnection();
                if ($this->layerHelper->hasVisualSwatch($attribute)) {
                    $and_logic=$this->layerHelper->isApplyAndLogicSwatch($attribute);
                } else {
                    $and_logic=$this->layerHelper->isApplyAndLogic($attribute);
                }
                    $apply_multiselect=$this->layerHelper->isApplyMultiSelect($attribute);
                if ($and_logic) {
                    foreach ($attributeValuesArr as $i => $attrValue) {
                        $alias = $alias . $i;
                        $conditions = [
                        "{$alias}.entity_id = e.entity_id",
                        $connection->quoteInto("{$alias}.attribute_id = ?", $attribute->getAttributeId()),
                        $connection->quoteInto("{$alias}.store_id = ?", $collection->getStoreId()),
                        $connection->quoteInto("{$alias}.value = ?", $attrValue)
                        ];
                        $collection->getSelect()->join(
                            [$alias => $this->_getResource()->getMainTable()],
                            join(' AND ', $conditions),
                            []
                        );
                    }
                } else {
                    $conditions = array(
                    "{$alias}.entity_id = e.entity_id",
                    $connection->quoteInto("{$alias}.attribute_id = ?", $attribute->getAttributeId()),
                    $connection->quoteInto("{$alias}.store_id = ?", $collection->getStoreId()),
                    $connection->quoteInto("{$alias}.value IN(?)", $attributeValuesArr)
                    );
                    $collection->getSelect()->join(
                    array($alias => $this->_getResource()->getMainTable()),
                    join(' AND ', $conditions),
                    array()
                    );  
                    /* Add below line for solve issue for apply swatch filter will not display product even if product exist*/
                    //$collection->getSelect()->group('e.entity_id');
                     $collection->getSelect()->distinct(true);
                   // $collection->addFieldToFilter($attribute->getAttributeCode(), ['in' => $attributeValuesArr]);
                }
                foreach ($attributeValuesArr as $i => $attrValue) {
                    $label = $this->getOptionText($attrValue);
                    $this->getLayer()->getState()->addFilter($this->_createItem($label, $attrValue));
                }
                if (count($attributeValuesArr) > 1) {
                    $collection->getSelect()->distinct(true);
                }
                    return $this;
                }
               
            } else {
                 return parent::apply($request);
            }
        } else {
             return parent::apply($request);
        }
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getItemsData()
    {
        
         $currentEngine=$this->layerHelper->getCurrentSearchEngine();
         $IsElasticSearchEnabled=$this->layerHelper->IsElasticSearch();
         $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                if($IsElasticSearchEnabled)
            {
                return parent::_getItemsData();
                    $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()
            ->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

        $isAttributeFilterable =
            $this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;

      //  if (count($optionsFacetedData) === 0 && !$isAttributeFilterable) {
        if (count($optionsFacetedData) === 0) {
            return $this->itemDataBuilder->build();
        }

        $productSize = $productCollection->getSize();

        $options = $attribute->getFrontend()
            ->getSelectOptions();
        foreach ($options as $option) {
            $this->buildOptionData($option, $isAttributeFilterable, $optionsFacetedData, $productSize);
        }

        return $this->itemDataBuilder->build();
                }
                else
                {
                     $attribute = $this->getAttributeModel();
        $attributeCode = $attribute->getAttributeCode();
        $layer = $this->getLayer();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $layer->getProductCollection();
        
        /*****COMMENT FOR SOLVE ISSUE FOR DUPLICATE PRODUCT IN INFINITE SCROLL PAGINATION**/
        if ($this->layerHelper->isFilterApplied($layer->getState(), $attributeCode)) {
            $productCollection = $this->getUnfilteredProductCollection();
        }
        $optionsFacetedData = $productCollection->getFacetedData($attributeCode);
        $productSize = $productCollection->getSize();
        $options = $attribute->getSource()->getAllOptions(false);
        foreach ($options as $option) {
            if (empty($option['value'])) {
                continue;
            }
            
            if ($this->getCount($option['value'], $attribute,$productCollection)) {
                $this->itemDataBuilder->addItemData(
                    $this->tagFilter->filter($option['label']),
                    $option['value'],
                    $this->getCount($option['value'], $attribute,$productCollection)
                );
            } elseif ($this->layerHelper->isFilterApplied($layer->getState(), $attributeCode)) {
                $params=$this->request->getParams();
                if (isset($params[$attributeCode])) {
                    $applied_opt=$params[$attributeCode];
                    $applied_opt_arr=explode(',',  (string)$applied_opt);
                    if (in_array($option['value'], $applied_opt_arr)) {
                        $this->itemDataBuilder->addItemData(
                            $this->tagFilter->filter($option['label']),
                            $option['value'],
                            0
                        );
                    }
                }
            }
        }
        return $this->itemDataBuilder->build();
                }
       
            }
            else
            {
                return parent::_getItemsData();
            }
        }
        return parent::_getItemsData();
    }

    private function getUnfilteredProductCollection()
    {
        $layer = $this->getLayer();
        //echo $layer->getCurrentCategory()->getId();die;
        $productCollection = $this->itemCollectionProvider->getCollection($layer->getCurrentCategory());
        $layer->prepareProductCollection($productCollection);
        return $productCollection;
    }
    public function getCount($opt_value, $attribute,$productCollection)
    {
        $select = $this->getBaseCollectionSql($productCollection);
       
        // reset columns, order and limitation conditions
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->reset(\Magento\Framework\DB\Select::GROUP);
        $from = $select->getPart(\Magento\Framework\DB\Select::FROM);
        if (!isset($from['stock_status_index'])) {

                $select->join(
                    [
                        'stock_status_index' => $this->_stockResource->getMainTable()
                    ],
                    'e.entity_id = stock_status_index.product_id',
                    []
                );
             }
        $connection = $this->_getResource()->getConnection();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
        $conditions = [
          "{$tableAlias}.entity_id = e.entity_id",
          $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
          $connection->quoteInto("{$tableAlias}.store_id = ?", $this->getStoreId()),
        ];

        $select
          ->join(
              [$tableAlias => $this->_getResource()->getMainTable()],
              join(' AND ', $conditions),
              ['value', 'count' => "COUNT(DISTINCT {$tableAlias}.entity_id)"]
          )
          ->group("{$tableAlias}.value");

        $optionsCount = $connection->fetchPairs($select);
        if (isset($optionsCount[$opt_value])) {
            return $optionsCount[$opt_value];
        }
    }
    
    protected function getBaseCollectionSql($productCollection)
    {
        $alias = $this->getAttributeModel()->getAttributeCode() . '_idx';
        
       $baseSelect =clone $this->getLayer()->getProductCollection()->getSelect();

       /*comment upper line and uncomment below line for fix issue of multi select attribute*/
        // $baseSelect =clone $productCollection->getSelect();
        $oldWhere = $baseSelect->getPart(\Magento\Framework\DB\Select::WHERE);
        $newWhere = [];

        foreach ($oldWhere as $cond) {
            if (strpos($cond, $alias)===false) {
                $newWhere[] = $cond;
            }
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND') {
            $newWhere[0] = substr($newWhere[0], 3);
        }
        
        $baseSelect->setPart(\Magento\Framework\DB\Select::WHERE, $newWhere);
        
        $oldFrom = $baseSelect->getPart(\Magento\Framework\DB\Select::FROM);
        $newFrom = [];
        
        foreach ($oldFrom as $name => $val) {
            if ($name != $alias) {
                $newFrom[$name] = $val;
            }
        }
        $baseSelect->setPart(\Magento\Framework\DB\Select::FROM, $newFrom);

        return $baseSelect;
    }
      private function buildOptionData($filterOption, $isAttributeFilterable, $layerOptionsFacetedData, $productSize)
    {
        $value = $this->getOptionValue($filterOption);
        if ($value === false) {
            return;
        }
        $filter_count = $this->getOptionCount($value, $layerOptionsFacetedData);
       // if ($isAttributeFilterable && (!$this->isOptionReducesResults($count, $productSize) || $count === 0)) {
        if (($filter_count === 0)) {
            return;
        }

        $this->itemDataBuilder->addItemData(
            $this->tagFilter->filter($filterOption['label']),
            $value,
            $filter_count
        );
    }
       private function getOptionValue($filterOption)
    {
        if (empty($filterOption['value']) && !is_numeric($filterOption['value'])) {
            return false;
        }
        return $filterOption['value'];
    }

    /**
     * Retrieve count of the options
     *
     * @param int|string $value
     * @param array $optionsFacetedData
     * @return int
     */
    private function getOptionCount($value, $layerOptionsFacetedData)
    {
        return isset($layerOptionsFacetedData[$value]['count'])
            ? (int)$layerOptionsFacetedData[$value]['count']
            : 0;
    }
    
    
    }
