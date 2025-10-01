<?php

namespace Magebees\Layerednavigation\Model\Layer\Filter;
use Magento\Framework\Exception\StateException;

class Stock extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    const IN_STOCK_COLLECTION_FLAG = 'stock_filter_applied';
    protected $_activeFilter = false;
    protected $_scopeConfig;
    private $attributeCode = 'stock_status';
    const FILTER_IN_STOCK_VAL = 1;
    const FILTER_OUT_OF_STOCK_VAL = 2;
    protected $_stockResource;
    protected $request;
    protected $_requestVar;
    protected $helper;
    protected $resourceConnection;
   
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
         \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->resourceConnection=$resourceConnection;
         $this->_requestVar = $this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->helper=$helper;
        $this->_stockResource = $stockResource;

        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        
        $filter = $request->getParam($this->getRequestVar(), null);
        $IsElasticSearchEnabled=$this->helper->IsElasticSearch();
       
        if (is_null($filter)) {
            return $this;
        }
        $this->_activeFilter = true;
        //$filter = (int)(bool)$filter;
         $collection = $this->getLayer()->getProductCollection();
         $collection->setFlag(self::IN_STOCK_COLLECTION_FLAG, true);
         if($IsElasticSearchEnabled)
        {
          $filter = (int)$filter;
       
        
         $this->getLayer()->getProductCollection()->addFieldToFilter($this->attributeCode, $filter);
         //if ($this->getLayer()->getProductCollection()->getData()) 
         {
            $label = $filter == self::FILTER_IN_STOCK_VAL ? __('In Stock') : __('Out of Stock');
            if ($filter) {
                $this->getLayer()->getState()->addFilter($this->_createItem($label, $filter));
            }
            return $this;
         }
        }
        else
        {
            if ($this->getLayer()->getProductCollection()->getData()) {
        		$table=$this->resourceConnection->getTableName('stock_status_index');
				
			/* Start By Amrish	*/	
				
			 $collection->getSelect()->join(
                    [
                        'stock_status_index1' => $this->_stockResource->getMainTable()
                    ],
                    'e.entity_id = stock_status_index1.product_id',
                    []
                );
				
			/* end By Amrish	*/
				
        	    $collection->getSelect()->where('stock_status_index1.stock_status = ?', $filter);

        

				$version=$this->helper->getMagentoVersion();
				if(version_compare($version, '2.3.0', '=='))
				{
					if($this->_isEnabledShowOutOfStock())
					{
						$collection->getSelect()->where('stock_status_index1.stock_status = ?', $filter);
					}
					else
					{
						$collection->getSelect()->where('stock_status_index1.is_salable = ?', $filter);
					}
				}
				else
				{
					$collection->getSelect()->where('stock_status_index1.stock_status = ?', $filter);
				}


			 }
       
			$this->getLayer()->getState()->addFilter(
				$this->_createItem($this->getLabel($filter), $filter)
			);

        }
       
        return $this;
    }
    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        $stock_filter_config=$this->_scopeConfig->getValue('layerednavigation/stock_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $label=$stock_filter_config['label'];
        return __($label);
    }
    /**
     * Get data array for building status filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
          $IsElasticSearchEnabled=$this->helper->IsElasticSearch();
          if($IsElasticSearchEnabled)
        {
        try {
            $optionsFacetedData = $this->getLayer()->getProductCollection()->getFacetedData($this->attributeCode);
        } catch (StateException $e) {
            $optionsFacetedData = [];
        }
        $inStockCount = isset($optionsFacetedData[self::FILTER_IN_STOCK_VAL])
            ? $optionsFacetedData[self::FILTER_IN_STOCK_VAL]['count'] : 0;
        $outStockCount = isset($optionsFacetedData[self::FILTER_OUT_OF_STOCK_VAL])
            ? $optionsFacetedData[self::FILTER_OUT_OF_STOCK_VAL]['count'] : 0;

        $filterData = [
            [
                'label' => __('In Stock'),
                'value' => self:: FILTER_IN_STOCK_VAL,
                'count' => $inStockCount,
            ],
            [
                'label' => __('Out of Stock'),
                'value' => self:: FILTER_OUT_OF_STOCK_VAL,
                'count' => $outStockCount,
            ]
        ];

        foreach ($filterData as $data) {
            if ($data['count'] < 1) {
                continue;
            }
            $this->itemDataBuilder->addItemData(
                $data['label'],
                $data['value'],
                $data['count']
            );
        }

        return $this->itemDataBuilder->build();
         }
         else
         {
             if ($this->getLayer()->getProductCollection()->getFlag(self::IN_STOCK_COLLECTION_FLAG)) {
           // return [];
        }
        $data = [];
        foreach ($this->getStatuses() as $status) {
            $count=$this->getProductsCount($status);
            
            if ($count>0) {
                if ($this->getLayer()->getProductCollection()->getData()) {
                    $data[] = [
                    'label' => $this->getLabel($status),
                    'value' => $status,
                    'count' => $count
                     ];
                }
            }
        }
        return $data;

         }

        
        
       
    }
    /**
     * get available statuses
     * @return array
     */
    public function getStatuses()
    {
        if($this->_isEnabledShowOutOfStock())
            {
        return [
            \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK,
            \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK
        ];
            }
        else
        {
        return [
            \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK
        ];
        }
    }
    /**
     * @return array
     */
    public function getLabels()
    {
        return [
            \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK => __('In Stock'),
            \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK => __('Out of stock'),
        ];
    }
    /**
     * @param $value
     * @return string
     */
    public function getLabel($value)
    {
        $labels = $this->getLabels();
        if (isset($labels[$value])) {
            //return $labels[$value];
            if ($this->getLayer()->getProductCollection()->getFlag(self::IN_STOCK_COLLECTION_FLAG)) {
                $data= '<div class=stock-filter style=color:#1979c3;>'. $labels[$value].'</div>';
                return $data;
            } else {
                return $labels[$value];
            }
        }
        return '';
    }

    /**
     * @param $value
     * @return string
     */
    public function getProductsCount($value)
    {
        if ($this->getLayer()->getProductCollection()->getData()) {
            $collection =$this->getLayer()->getProductCollection();
            $select=clone $collection->getSelect();
        // reset columns, order and limitation conditions
            $select->reset(\Magento\Framework\DB\Select::COLUMNS);
            $select->reset(\Magento\Framework\DB\Select::ORDER);
            $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
            $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
   
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
            $version=$this->helper->getMagentoVersion();
            if(version_compare($version, '2.3.0', '=='))
            {
            if($this->_isEnabledShowOutOfStock())
            {
            $select->where('stock_status_index.stock_status = ?', $value);
            }
            else
            {
            $select->where('stock_status_index.is_salable = ?', 1);
            }
            }
            else
            {
                if($this->_isEnabledShowOutOfStock())
            {
                 $select->where('stock_status_index.stock_status = ?', $value);
            }
                else
                {
                     $select->where('stock_status_index.stock_status = ?', 1);
                }
            }
            $select->columns(
                [
                'count' => new \Zend_Db_Expr("COUNT(DISTINCT e.entity_id)")
                ]
            );
           
            $connection=$this->resourceConnection->getConnection();
            $applied_params=$this->request->getParams();
            $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            return $collection->getConnection()->fetchOne($select);
        }
    }
     protected function _isEnabledShowOutOfStock()
    {
        return $this->_scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
