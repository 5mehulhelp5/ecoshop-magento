<?php

namespace Magebees\Layerednavigation\Block;

class TopNavigation extends \Magento\Framework\View\Element\Template
{
    
    protected $layerattributeFactory;
    protected $productAttributeCollectionFactory;
    protected $helper;
    protected $_registry;
    protected $_scopeConfig;
    protected $_request;
   
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magebees\Layerednavigation\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->layerattributeFactory = $layerattributeFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->helper = $helper;
        $this->_registry = $registry;
        $this->_scopeConfig=$context->getScopeConfig();
        $this->_request = $context->getRequest();
    }
    

    public function getCurrentCategory()
    {
        if($this->_registry->registry('current_category')){
             return $this->_registry->registry('current_category')->getDisplayMode();
        }else{
            return false;
        }
       
    }
    
    public function getTopFilters($filters)
    {
        $new_filters=[];
        $catConfig=$this->helper->getCatConfigData();
        $layer_model=$this->layerattributeFactory->create();
        $collection = $layer_model->getCollection()
                        ->addFieldToFilter('show_in_block', ['in' =>[1,2]]);
        $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $ratingBlockPos=$this->helper->getRatingBlockPosition();
        $stockBlockPos=$this->helper->getStockBlockPosition();
        $priceBlockPos=$this->helper->getPriceBlockPosition();
        $catBlockPos=$catConfig['nav_block_pos'];
        if (!empty($collection->getData())) {
            $custom_layer_data=$collection->getData();
            foreach ($custom_layer_data as $data) {
                $attribute_id=$data['attribute_id'];
                $attr_model=$this->productAttributeCollectionFactory->create();
                $attr_collection = $attr_model->addFieldToFilter('main_table.attribute_id', $attribute_id);
                $att_layer_data=$attr_collection->getData();
                $attr_code[]=$att_layer_data[0]['attribute_code'];
            };
            foreach ($filters as $filter) {
                $requestvar=$filter->getRequestVar();
                if ($requestvar==$rat_param) {
                    if (($ratingBlockPos=='1') || ($ratingBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar==$stock_param) {
                    if (($stockBlockPos=='1') || ($stockBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar=='price') {
                    if (($priceBlockPos=='1') || ($priceBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar=='cat') {
                    if (($catBlockPos=='1') || ($catBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if (in_array($requestvar, $attr_code)) {
                    $new_filters[]=$filter;
                }
            }
            if (empty($new_filters)) {
                $new_filters=[];
            }
        } else {
            foreach ($filters as $filter) {
                $requestvar=$filter->getRequestVar();
                
                if ($requestvar==$rat_param) {
                    if (($ratingBlockPos=='1') || ($ratingBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar==$stock_param) {
                    if (($stockBlockPos=='1') || ($stockBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar=='price') {
                    if (($priceBlockPos=='1') || ($priceBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar=='cat') {
                    if (($catBlockPos=='1') || ($catBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
            }
            if (empty($new_filters)) {
                $new_filters=[];
            }
        }
        return $new_filters;
    }
    public function getSidebarFilters($filters)
    {
        
        $new_filters=[];
            
        $catConfig=$this->helper->getCatConfigData();
        $layer_model=$this->layerattributeFactory->create();
        $collection = $layer_model->getCollection()
                        ->addFieldToFilter('show_in_block', ['in' =>[0,2]]);
        
        $ratingBlockPos=$this->helper->getRatingBlockPosition();
        $stockBlockPos=$this->helper->getStockBlockPosition();
        $priceBlockPos=$this->helper->getPriceBlockPosition();
        $catBlockPos=$catConfig['nav_block_pos'];
        $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($collection->getData())) {
            $custom_layer_data=$collection->getData();
            foreach ($custom_layer_data as $data) {
                $attribute_id=$data['attribute_id'];
                $attr_model=$this->productAttributeCollectionFactory->create();
                $attr_collection = $attr_model->addFieldToFilter('main_table.attribute_id', $attribute_id);
                $att_layer_data=$attr_collection->getData();
                if (array_key_exists(0,$att_layer_data)) {
                    $attr_code[]=$att_layer_data[0]['attribute_code'];
                }
            };
        
            foreach ($filters as $filter) {
                $requestvar=$filter->getRequestVar();
                if ($requestvar=='cat') {
                    if (($catBlockPos=='0') || ($catBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                } elseif ($requestvar==$rat_param) {
                    if (($ratingBlockPos=='0') || ($ratingBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                } elseif ($requestvar==$stock_param) {
                    if (($stockBlockPos=='0') || ($stockBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                } elseif ($requestvar=='price') {
                    if (($priceBlockPos=='0') || ($priceBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                } elseif (in_array($requestvar, $attr_code)) {
                    $new_filters[]=$filter;
                }
            }
        } else {
            foreach ($filters as $filter) {
                $requestvar=$filter->getRequestVar();
                
                if ($requestvar==$rat_param) {
                    if (($ratingBlockPos=='1') || ($ratingBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar==$stock_param) {
                    if (($stockBlockPos=='1') || ($stockBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar=='price') {
                    if (($priceBlockPos=='1') || ($priceBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
                if ($requestvar=='cat') {
                    if (($catBlockPos=='1') || ($catBlockPos=='2')) {
                        $new_filters[]=$filter;
                    }
                }
            }
            if (empty($new_filters)) {
                $new_filters=[];
            }
        }
        return $new_filters;
    }
    public function checkAppliedFilter($request_var)
    {
        $applied_params=$this->_request->getParams();
        
        if (array_key_exists($request_var, $applied_params)) {
            return true;
        }
    }
}
