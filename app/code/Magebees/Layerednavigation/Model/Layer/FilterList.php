<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magebees\Layerednavigation\Model\Layer;

class FilterList
{
   
    const STOCK_FILTER_CLASS        = 'Magebees\Layerednavigation\Model\Layer\Filter\Stock';
    const RATING_FILTER_CLASS        = 'Magebees\Layerednavigation\Model\Layer\Filter\Rating';
    const CONFIG_SEARCH_ENGINE_PATH = 'catalog/search/engine';
    
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_layer;
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\CatalogInventory\Model\Resource\Stock\Status
     */
    protected $_stockResource;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    protected $_url;
    protected $_request;
    protected $httpRequest;
    protected $resourceConnection;
    
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\CatalogInventory\Model\Resource\Stock\Status $stockResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
		 \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $url
    ) {
         $this->_url = $url;
		 $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_stockResource = $stockResource;
        $this->_scopeConfig = $scopeConfig;
         $this->httpRequest = $httpRequest;
        $this->resourceConnection = $resourceConnection;
    }

   
    /**
     * @param \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList
     * @param \Magento\Catalog\Model\Layer $layer
     * @return array
     */
    public function beforeGetFilters(
        \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $this->_layer = $layer;
        $collection = $layer->getProductCollection();
        $websiteId = $this->_storeManager->getStore($collection->getStoreId())->getWebsiteId();
        $currentEngine = $this->_scopeConfig->getValue(self::CONFIG_SEARCH_ENGINE_PATH,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($currentEngine=='mysql')
        {
          //  $this->_addStockStatusToSelect($collection->getSelect(), $websiteId);
        }
       
        return [$layer];
    }

    /**
     * @param \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList
     * @param array $filters
     * @return array
     */
    public function afterGetFilters(
        \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList,
        array $filters
    ) {
		$route=$this->_request->getRouteName();
		$controller=$this->_request->getControllerName();
                $cat_id=$this->httpRequest->getParam('id');
                $config=$this->_scopeConfig->getValue('layerednavigation/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $rating_filter_config=$this->_scopeConfig->getValue('layerednavigation/rating_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $stock_filter_config=$this->_scopeConfig->getValue('layerednavigation/stock_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $price_filter_config=$this->_scopeConfig->getValue('layerednavigation/price_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $cat_filter_config=$this->_scopeConfig->getValue('layerednavigation/category_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $currenturl=$this->_url->getCurrentUrl();
                $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        foreach ($filters as $filter) {
            if ($filter instanceof \Magento\CatalogSearch\Model\Layer\Filter\Price) {
                if (!$price_filter_config['enable']) {
                   /*disable price filter*/
                    $price_key = array_search($filter, $filters);
                    unset($filters[$price_key]);
                } else {
                    /*exclude price filter from category id as per configuration*/
                    if (isset($price_filter_config['exclude_cat'])) {
                        $exclude_cat_price=$price_filter_config['exclude_cat'];
                        $excludecat_price_arr=explode(',',  (string)$exclude_cat_price);
                        if (in_array($cat_id, $excludecat_price_arr)) {
                            $price_key = array_search($filter, $filters);
                            unset($filters[$price_key]);
                        }
                    }
                }
            } elseif ($filter instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Category) {
               // if ((!$cat_filter_config['enable'])||(preg_match('/layerednavigation/',$route))||($controller=='brand')){
                if ((!$cat_filter_config['enable'])){
                    /*disable category filter*/
                    $cat_key = array_search($filter, $filters);
                    unset($filters[$cat_key]);
                }
            }
        }
                
        if ($is_default_enabled==0) {
            if ($config['enable']) {
                if ($rating_filter_config['enabled']) {
                    if (preg_match('/catalogsearch/', $currenturl)) {
                        //if(strpos($currenturl,'catalogsearch')  > -1)
                        if ($rating_filter_config['enabled_catalogsearch']) {
                            $filters[] = $this->getRatingFilter();
                        }
                    } else {
                        if (isset($rating_filter_config['exclude_cat'])) {
                            $exclude_cat_rating=$rating_filter_config['exclude_cat'];
                            $excludecat_rat_arr=explode(',',  (string)$exclude_cat_rating);
                            if (!in_array($cat_id, $excludecat_rat_arr)) {
                                $filters[] = $this->getRatingFilter();
                            }
                        } else {
                            $filters[] = $this->getRatingFilter();
                        }
                    }
                }
                if ($stock_filter_config['enabled']) {
                    if (preg_match('/catalogsearch/', $currenturl)) {
                    //if(strpos($currenturl,'catalogsearch')  > -1)
                        if ($stock_filter_config['enabled_catalogsearch']) {
                            $filters[] = $this->getStockFilter();
                        }
                    } else {
                        if (isset($stock_filter_config['exclude_cat'])) {
                            $exclude_cat_stock=$stock_filter_config['exclude_cat'];
                            $excludecat_stock_arr=explode(',',  (string)$exclude_cat_stock);
                            if (!in_array($cat_id, $excludecat_stock_arr)) {
                                $filters[] = $this->getStockFilter();
                            }
                        } else {
                            $filters[] = $this->getStockFilter();
                        }
                    }
                }
            }
        }
                // for set the position of attribute as per configuration
                usort($filters, [$this, "_sortByPosition"]);
                return $filters;
    }
    public function _sortByPosition($a, $b)
    {
                $rating_filter_config=$this->_scopeConfig->getValue('layerednavigation/rating_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $stock_filter_config=$this->_scopeConfig->getValue('layerednavigation/stock_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $cat_filter_config=$this->_scopeConfig->getValue('layerednavigation/category_filter', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $a_position = 0;        
        if ($a->hasAttributeModel()) {
            $a_position=$a->getAttributeModel()->getData('position');
        } elseif ($a instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Category) {
            $a_position=$cat_filter_config['sort_order'];
        } elseif ($a instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Stock) {
            $a_position=$stock_filter_config['sort_order'];
        } elseif ($a instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Rating) {
            $a_position=$rating_filter_config['sort_order'];
        }
        if ($b->hasAttributeModel()) {
            $b_position=$b->getAttributeModel()->getData('position');
        } elseif ($b instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Category) {
            $b_position=$cat_filter_config['sort_order'];
        } elseif ($b instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Stock) {
            $b_position=$stock_filter_config['sort_order'];
        } elseif ($b instanceof \Magebees\Layerednavigation\Model\Layer\Filter\Rating) {
            $b_position=$rating_filter_config['sort_order'];
        }
        
        if ($a_position==$b_position) {
            return 0;
        }
        
        return ($a_position > $b_position ? 1 : -1);
    }
    public function getStockFilter()
    {
        $filter = $this->_objectManager->create(
            $this->getStockFilterClass(),
            ['layer' => $this->_layer]
        );
        return $filter;
    }
    public function getRatingFilter()
    {
        $filter = $this->_objectManager->create(
            $this->getRatingFilterClass(),
            ['layer' => $this->_layer]
        );
        return $filter;
    }
    public function getStockFilterClass()
    {
        return self::STOCK_FILTER_CLASS;
    }
    
    public function getRatingFilterClass()
    {
        return self::RATING_FILTER_CLASS;
    }
    protected function _addStockStatusToSelect(\Magento\Framework\DB\Select $select, $websiteId)
    {
        $from = $select->getPart(\Magento\Framework\DB\Select::FROM);
        if (!isset($from['stock_status_index'])) {
            $joinCondition = $this->_stockResource->getConnection()->quoteInto(
                'e.entity_id = stock_status_index.product_id' . ' AND stock_status_index.website_id = ?',
                $websiteId
            );

            $joinCondition .= $this->_stockResource->getConnection()->quoteInto(
                ' AND stock_status_index.stock_id = ?',
                \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID
            );

            $select->join(
                [
                    'stock_status_index' => $this->_stockResource->getMainTable()
                ],
                $joinCondition,
                []
            );
        }
        return $this;
    }
}
