<?php
namespace Magebees\Layerednavigation\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;


/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_storeManager;
    protected $scopeConfig;
    protected $pageCacheConfig;
    protected $_imageFactory;
    protected $_filesystem;
    protected $_urlinterface;
    protected $_eav;
    protected $currencyFactory;
    protected $_catalogeav;
    protected $request;
    protected $productAttributeCollectionFactory;
    protected $layerattributeFactory;
    protected $attributes_helper;
    protected $brands;
    protected $productMetadata;
    protected $_convertTable;
    protected $helper_url;
    protected $_categoryFactory;
	const CONFIG_SEARCH_ENGINE_PATH = 'catalog/search/engine';
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Eav\Model\Config $eav,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $catalogeav,
        \Magebees\Layerednavigation\Model\Brands $brands,
        Filesystem $filesystem,
		\Magento\Directory\Model\CurrencyFactory $currencyFactory,  \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        \Magebees\Layerednavigation\Helper\Attributes $attributes_helper,
		 \Magebees\Layerednavigation\Helper\Url $helper_url,
		\Magento\PageCache\Model\Config $pageCacheConfig,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Filter\Translit $translit
    ) {
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_eav = $eav;
        $this->pageCacheConfig = $pageCacheConfig;
        $this->_catalogeav = $catalogeav;
        $this->brands = $brands;
        $this->currencyFactory = $currencyFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->layerattributeFactory = $layerattributeFactory;
        $this->attributes_helper = $attributes_helper;
        $this->request =$context->getRequest();
        $this->productMetadata = $productMetadata;
        $this->_convertTable = $translit;
		$this->helper_url = $helper_url;
		 $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }    

	public function isLayerFullPageCacheEnabled()
	{
		return $this->pageCacheConfig->isEnabled();
	}
  	public function isLayerVarnishEnabled()
    {
		if($this->pageCacheConfig->getType() == \Magento\PageCache\Model\Config::VARNISH)
		{
			return true;
		}
		else
		{
			return false;
		}
  	}
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }    
    public function generateRandomId($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    public function getBrandAttributeValueFromURL($attrUrl)
    {
        $getAttributeCodeValue = $this->brands->load($attrUrl, 'seo_url');
        return $getAttributeCodeValue->getData('brand_name');
    }
	public function getCurrentSearchEngine()
	{
		 $currentEngine = $this->scopeConfig->getValue(self::CONFIG_SEARCH_ENGINE_PATH,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $currentEngine;
	}
    public function IsElasticSearch()
    {
        $currentEngine=$this->getCurrentSearchEngine(); 
        if(($currentEngine=='elasticsearch8')|| ($currentEngine=='elasticsearch7')|| ($currentEngine=='elasticsearch6')||($currentEngine=='elasticsearch5')||($currentEngine=='elasticsearch')  || ($currentEngine=='opensearch') || ($currentEngine=='lmysql'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function getBrandAttributeCode($attributeCode)
    {
        
        if (isset($attributeCode) && $attributeCode!="") {
            $attrbExist = $this->_catalogeav->loadByCode('catalog_product', $attributeCode);
            if ($attrbExist->getId() == null) {
                return "attributeNotExist";
            } else {
                return $this->_eav->getAttribute("catalog_product", $attributeCode);
            }
        } else {
            return $this->_eav->getAttribute("catalog_product", "layernav_brand");
        }
    }
    public function getOptionUrl($optLabel, $optId)
    {
        //$attrCode = $this->getBrandAttributeCode()->getData('attribute_code');
       // $attrCodeReplace = str_replace("_", "-", $attrCode);
        $optLabel = $optLabel.'-';
        $optLabel .= $this->keyGenerator();
        $url = $this->createKey($optLabel);
        if ($this->getUrlSuffix()) {
            $url .= $this->getUrlSuffix();
        }
        $urlexits = $this->brands->getCollection()->addFieldToFilter('seo_url', $url);
        $urldata = $urlexits->getData();
        if ($urldata) {
            $optLabel .= $this->keyGenerator();
            $url = $this->createKey($optLabel);
            if ($this->getUrlSuffix()) {
                $url .= $this->getUrlSuffix();
            }
        }
            
        return $url;
    }
    public function keyGenerator()
    {
        $length = 1;
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    public function createKey($optionLabel)
    {
       // $key = $this->format($optionLabel);
        $key = preg_replace('/[^0-9a-z,]+/i', '-', $optionLabel);
        $key = strtolower($key);
        return $key;
    }
    public function getRatingBlockPosition()
    {
        $block_pos = $this->scopeConfig->getValue('layerednavigation/rating_filter/nav_block_pos', ScopeInterface::SCOPE_STORE);
        return $block_pos;
    }
    public function getStockBlockPosition()
    {
        $block_pos = $this->scopeConfig->getValue('layerednavigation/stock_filter/nav_block_pos', ScopeInterface::SCOPE_STORE);
        return $block_pos;
    }
    public function getGeneralConfigData()
    {
        $general_config = $this->scopeConfig->getValue('layerednavigation/setting', ScopeInterface::SCOPE_STORE);
        return $general_config;
    }
    
    public function getBrandConfigData()
    {
        $brands_config = $this->scopeConfig->getValue('layerednavigation/brands', ScopeInterface::SCOPE_STORE);
        return $brands_config;
    }
    public function getCatConfigData()
    {
        $cat_config = $this->scopeConfig->getValue('layerednavigation/category_filter', ScopeInterface::SCOPE_STORE);
        return $cat_config;
    }
    public function getSeoConfigData()
    {
        $seo_config = $this->scopeConfig->getValue('layerednavigation/seo_setting', ScopeInterface::SCOPE_STORE);
        return $seo_config;
    }
	public function IsEnableAttributeInclude()
	{
		$seoConfig=$this->getSeoConfigData();
		return $seoConfig['include_attribute'];
		
	}
	public function IsAttributeIncludeData()
	{
		$seoConfig=$this->getSeoConfigData();
		return $seoConfig['include_attribute_data'];
		
	}
	public function IsReplaceAllSpecialChar()
	{
		$seoConfig=$this->getSeoConfigData();
		return $seoConfig['replace_char_all'];
		
	}
	
	public function getReplaceSpecialChar()
	{
		$seoConfig=$this->getSeoConfigData();
		if($seoConfig['replace_char']==0)
		{
			$replaceChar='-';
		}
		else
		{
			$replaceChar='_';
		}
		return $replaceChar;
	}
	public function getSeperateAttrChar()
	{
		$seoConfig=$this->getSeoConfigData();
		if($seoConfig['seperate_char']==0)
		{
			$seperateChar='-';
		}
		else
		{
			$seperateChar='_';
		}
		return $seperateChar;
	}
	
    public function getPriceConfigData()
    {
        $price_config = $this->scopeConfig->getValue('layerednavigation/price_filter', ScopeInterface::SCOPE_STORE);
        return $price_config;
    }
    public function getRatingConfigData()
    {
        $rating_config = $this->scopeConfig->getValue('layerednavigation/rating_filter', ScopeInterface::SCOPE_STORE);
        return $rating_config;
    }
    public function getStockConfigData()
    {
        $stock_config = $this->scopeConfig->getValue('layerednavigation/stock_filter', ScopeInterface::SCOPE_STORE);
        return $stock_config;
    }
    
    
    public function getPriceBlockPosition()
    {
        $block_pos = $this->scopeConfig->getValue('layerednavigation/price_filter/nav_block_pos', ScopeInterface::SCOPE_STORE);
        return $block_pos;
    }
    public function isShowProductCountPrice()
    {
        $show_product_count = $this->scopeConfig->getValue('layerednavigation/price_filter/show_product_count', ScopeInterface::SCOPE_STORE);
        return $show_product_count;
    }
    
    public function unfoldOptionPrice()
    {
        $unfold_option = $this->scopeConfig->getValue('layerednavigation/price_filter/unfold_option', ScopeInterface::SCOPE_STORE);
        return $unfold_option;
    }
    
    
    public function getUrlSuffix()
    {
        $suffix = $this->scopeConfig->getValue('catalog/seo/category_url_suffix', ScopeInterface::SCOPE_STORE);
        if ($suffix && '/' != $suffix && '.' != $suffix[0]) {
            $suffix = '.' . $suffix;
        }
        return $suffix;
    }
    public function getTitleSeperator()
    {
         $title_separator = $this->scopeConfig->getValue('catalog/seo/title_separator', ScopeInterface::SCOPE_STORE);
         return $title_separator;
    }
    public function getUrlType()
    {
        $urltype = $this->scopeConfig->getValue('layerednavigation/seo_setting/url_type', ScopeInterface::SCOPE_STORE);
       
        return $urltype;
    }
    public function getUrlKey()
    {
        $urltype = $this->scopeConfig->getValue('layerednavigation/seo_setting/url_key', ScopeInterface::SCOPE_STORE);
       
        return $urltype;
    }
    public function getThumbnailsImage($imageName)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        return $mediaDirectory.'layernav_brand'.$imageName;
    }
    
    public function getAttrDisplayMode($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['display_mode'])) {
                            $display_mode=$custom_layer_data[0]['display_mode'];
            return $display_mode;
        }
    }
        
    
      /** Checks if the given attribute is a radio button. */
    public function isApplyRadioButton($attribute_id)
    {
    
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['display_mode'])) {
                            $display_mode=$custom_layer_data[0]['display_mode'];
                                
            if ($display_mode==6) {
                return true;
            }
        }
    }
    
     /** Checks if the given attribute have and logic or not. */
    
    public function isApplyAndLogic(Attribute $attribute)
    {
        $attribute_id=$attribute->getId();
    
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['display_mode'])) {
                            $allow_multiselect=$custom_layer_data[0]['display_mode'];
                                
            if (($allow_multiselect==4) || ($allow_multiselect==5)) {
                if ($custom_layer_data[0]['and_logic']) {
                    return true;
                }
            }
        }
    }
    
    public function isApplyAndLogicSwatch(Attribute $attribute)
    {
        $attribute_id=$attribute->getId();
        $default_config=$this->scopeConfig->getValue('layerednavigation/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if ($default_config['default_swatch_config']=='1') {
            if ($default_config['multiselect_swatch']=='1') {
                if (isset($custom_layer_data[0]['and_logic'])) {
                    if ($custom_layer_data[0]['and_logic']) {
                        return true;
                    }
                }
            }
        } else {
            $this->isApplyAndLogic($attribute);
        }
    }
    
    
    /** Checks if the given attribute is a multi select. */
    
    public function isApplyMultiSelect(Attribute $attribute)
    {
        $attribute_id=$attribute->getId();
    
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['display_mode'])) {
                            $allow_multiselect=$custom_layer_data[0]['display_mode'];
                                
            if (($allow_multiselect==4) || ($allow_multiselect==5)) {
                return true;
            }
        }
    }
    
    public function isApplyMultiSelectWithId($attribute_id)
    {
   //$attribute_id=$attribute->getId();
    
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['display_mode'])) {
                            $allow_multiselect=$custom_layer_data[0]['display_mode'];
                                
            if (($allow_multiselect==4) || ($allow_multiselect==5)) {
                return true;
            }
        }
    }
    
    /** Checks if the given attribute is a multi select with image and label. */
    
    public function isApplyMultiSelectWithImages($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
                                
                            $display_type=$custom_layer_data[0]['display_mode'];
                                
        if ($display_type==5) {
                                return true;
        }
    }
    
    /** Checks if the given attribute display type image and label. */
    
    public function isApplyLabelWithImages($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
                                
                            
        if (isset($custom_layer_data[0]['display_mode'])) {
                                            $display_type=$custom_layer_data[0]['display_mode'];
            if (($display_type==2) || ($display_type==1)) {
                return $display_type;
            }
        }
                                return 0;
    }
    
    
 /** Checks if the given attribute has dropdown property or not. */
    
    public function isApplyDropDown(Attribute $attribute)
    {
        $attribute_id=$attribute->getId();
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
                                $display_mode=$custom_layer_data[0]['display_mode'];
        if ($display_mode==3) {
                                return true;
        }
    }
    
    /**check for always expand attribute filter from manage attribute filters. */
    
    public function checkCollapseFilter($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
                                $always_expand=$custom_layer_data[0]['always_expand'];
                                return $always_expand;
    }
    
    /**check for rel nofollow attribute filter from manage attribute filters. */
    
    public function getRelNoFollow($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['rel_nofollow'])) {
                                $rel_nofollow=$custom_layer_data[0]['rel_nofollow'];
        } else {
            $rel_nofollow=0;
        }
                                
                                
                                return $rel_nofollow;
    }
    
    /**check for include attribute filter in categories from manage attribute filters. */
    
    public function getIncludeCategories($attribute_id)
    {
        if ($this->request->getParam('id')) {
            $cat=$this->request->getParam('id');
            $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
            if (isset($custom_layer_data[0]['include_cat'])) {
                                $include_cat=$custom_layer_data[0]['include_cat'];
                $include_cat_arr=explode(',', (string)$include_cat);
            } else {
                $include_cat='';
                $include_cat_arr=explode(',', (string)$include_cat);
            }
            if (in_array($cat, $include_cat_arr)) {
                                return true;
            }
        }
    }
    
    /**check for exclude attribute filter from categories from manage attribute filters. */
    
    public function getExcludeCategories($attribute_id)
    {
        
        if ($this->request->getParam('id')) {
            $cat=$this->request->getParam('id');
            $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
            if (isset($custom_layer_data[0]['exclude_cat'])) {
                                $exclude_cat=$custom_layer_data[0]['exclude_cat'];
                $exclude_cat_arr=explode(',', (string)$exclude_cat);
            } else {
                $exclude_cat='';
                $exclude_cat_arr=explode(',', (string)$exclude_cat);
            }
            if (in_array($cat, $exclude_cat_arr)) {
                                return true;
            }
        }
    }
    
    /**check for display product count on layer for attribute from manage attribute filters. */
    
    public function isDisplayProductCount($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
                                $show_product_count=$custom_layer_data[0]['show_product_count'];
                                return $show_product_count;
    }
    
    
    /** get unfolded option for attribute from manage attribute filters. */
    
    public function getUnfoldedOption($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
                                $unfold_option=$custom_layer_data[0]['unfold_option'];
                                return $unfold_option;
    }
    
    /** check for whether add searchbox for attribute from manage attribute filters or not. */
    
    public function isEnableSearchbox($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
                                $enable_searchbox=$custom_layer_data[0]['show_searchbox'];
                                return $enable_searchbox;
    }
    /*Get Tooltip for attribute from configuration*/
    public function getTooltip($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['tooltip'])) {
                                $tooltip=$custom_layer_data[0]['tooltip'];
        } else {
            $tooltip="";
        }
                            
                                return $tooltip;
    }
    
    /*Get Robots Noindex Tag for attribute from configuration*/
    public function getNoIndexTag($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['robots_noindex'])) {
                                $robots_noindex=$custom_layer_data[0]['robots_noindex'];
        } else {
            $robots_noindex=0;
        }
                            
                                return $robots_noindex;
    }
    
    /*Get Robots NoFollow Tag for attribute from configuration*/
    public function getNoFollowTag($attribute_id)
    {
        $layer_model=$this->layerattributeFactory->create();
            $collection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attribute_id);
                                $custom_layer_data=$collection->getData();
        if (isset($custom_layer_data[0]['robots_nofollow'])) {
                                $robots_nofollow=$custom_layer_data[0]['robots_nofollow'];
        } else {
            $robots_nofollow=0;
        }
                            
                                return $robots_nofollow;
    }
        
    /*Check if attribute has visual swatch or not*/
    public function hasVisualSwatch(Attribute $attribute)
    {
        $attribute_id=$attribute->getId();
        $additional_data=$attribute->getData('additional_data');
        if ($additional_data) {
            return true;
        }
    }
    
    public function SwatchAttributesArr()
    {
        $filterable_attr_arr=[];
        $filterable_attr=$this->attributes_helper->getAllFilterableOptionsAsHash();
        $attr_model=$this->productAttributeCollectionFactory->create();
        $collection =$attr_model->addFieldToFilter('attribute_code', ['in' => array_keys($filterable_attr)])->addFieldToFilter('additional_data', ['neq' => 'NULL' ]);
        $data=$collection->getData();
        $filterable_attr_arr = array_column($data, 'attribute_code');
        if (isset($filterable_attr_arr)) {
            return  $filterable_attr_arr;
        }
    }
    
    public function getAttributeCode(Attribute $attribute)
    {
        return $attribute_code=$attribute->getName();
    }
  

    public function isFilterApplied(\Magento\Catalog\Model\Layer\State $state, $attributeCode)
    {
        $appliedFilters = $state->getFilters();
        if (!empty($appliedFilters)) {
            foreach ($appliedFilters as $appliedFilter) {
                $filter=$appliedFilter->getFilter();
            
                if ($filter->hasAttributeModel()) {
                    $appliedAttributeCode = $appliedFilter->getFilter()->getAttributeModel()->getAttributeCode();
                    if ($appliedAttributeCode === $attributeCode) {
                        return true;
                    }
                } else {
                    if ($appliedFilter->getFilter()->getRequestVar()=='cat') {
                        return true;
                    }
                }
            }
        } else {
             return false;
        }
    }
    public function isFilterItemActive(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
        $filter = $item->getFilter();
        $requestParameters = $this->request->getParam($filter->getRequestVar());
        if ($requestParameters) {
            $requestParameters = explode(',', (string)$requestParameters);
        } else {
             $requestParameters =[];
        }

        return in_array($item->getValue(), $requestParameters);
    }
    
    public function isSwatchActive($optionid, $attribute_code)
    {
     
        $requestParameters = $this->request->getParam($attribute_code);
        if ($requestParameters) {
            $requestParameters = explode(',', (string)$requestParameters);
        } else {
             $requestParameters =[];
        }

        return in_array($optionid, $requestParameters);
    }
	public function urlAliasAfterReplaceChar($option_label)
	{
		$replaceChar=$this->getReplaceSpecialChar();
		$isReplaceAllChar=$this->IsReplaceAllSpecialChar();
		if($isReplaceAllChar==0)
		{        
		$label_alias = preg_replace('/[^\w\s]+/u',$replaceChar, $option_label);
		//$label_alias = preg_replace('/[^a-zA-Z0-9]/s',$replaceChar, $option_label);
		$label_alias =preg_replace('/_+/',$replaceChar, $label_alias);
		$option_label=$label_alias;
		}
		$label_alias = str_replace(' ',$replaceChar, $option_label);
		//$label_alias = str_replace('/',$replaceChar, $label_alias);
		$label_alias = preg_replace('/'.$replaceChar.'+/',$replaceChar, $label_alias);
		$label_alias=strtolower($label_alias);
		return $label_alias;
	}
	public function getAttributeIncludeData($attributeModel)
	{
		if($this->IsAttributeIncludeData()==0)
	{
	$mainAttributeCode=$attributeModel->getAttributeCode();	
	}
	else
	{
		$mainAttributeCode=$attributeModel->getStoreLabel();
	}
	/***Replace special character***/
	$attributeCode=$this->urlAliasAfterReplaceChar($mainAttributeCode);
		return $attributeCode;
	}
	public function getCurrencySymbol()
	{ 
$currencyCode =$this->_storeManager->getStore()->getCurrentCurrencyCode(); 
$currency = $this->currencyFactory->create()->load($currencyCode); 
return $currency->getCurrencySymbol(); 
	}
	public function loadAttributeModelByCode($attributeCode)
	{
		  return $this->_catalogeav->loadByCode('catalog_product', $attributeCode);
	}
	
	public function itemResetAppliedFilterUrl($applied_params,$attributeModel,$optionId,$isAttributeInclude,$request_var)
	{
		 $optionarr=$this->attributes_helper->getAllFilterableOptionsAsHash();
		$seperateChar=$this->getSeperateAttrChar();
	$attr_id = $attributeModel->getId();
	$attr_code=$attributeModel->getAttributeCode();
	$new_attr_str_arr=[];
	if (($key = array_search($optionId, $applied_params)) !== false) {
    unset($applied_params[$key]);
}
	else
	{
		$applied_params[$attr_code]=$optionId;
	}		
	foreach ($applied_params as $a_key => $a_value) {		
	if(array_key_exists($a_key,$optionarr))
	 {
		 if (strpos($a_value,',') !== false) {
			 $a_value_label='';
			$comma_value_arr=explode(',',(string)$a_value);
			 foreach($comma_value_arr as $comma_key=>$comma_val)
			 {
				
					 $a_value_label.=$this->helper_url->getUrlAlias($comma_val, $attr_id).$seperateChar;
			 }
		 }
		 else
		 {
			 
			$a_value_label=$this->helper_url->getUrlAlias($a_value, $attr_id);
		 }
			$new_attr_str_arr[$a_key]=$a_value_label;
	 }
	 }
  	$attr_str='';
	foreach ($new_attr_str_arr as $new_key => $new_val) {				  
	if($isAttributeInclude)
	{
		$attributeModel=$this->loadAttributeModelByCode($new_key);
		$attributeCode=$this->getAttributeIncludeData($attributeModel);	
		$attr_str.=$attributeCode.$seperateChar.$new_val.$seperateChar;
	}
	  else
	  {
		  $attr_str.=$new_val.$seperateChar;
	  }
	}
	/** update for fix issue remove attribute***/		
		
$attr_str = rtrim($attr_str,$seperateChar);
$attr_str = ltrim($attr_str,$seperateChar);				
$attr_str = preg_replace('/'.$seperateChar.'+/',$seperateChar, $attr_str);	
		return $attr_str;
	}
	public function resetMultipleFilter($applied_param_val,$applied_params,$attributeModel,$optionId,$attributeCode,$isAttributeInclude,$request_var)
	{
		 $optionarr=$this->attributes_helper->getAllFilterableOptionsAsHash();
		$seperateChar=$this->getSeperateAttrChar();
	$attr_id = $attributeModel->getId();
	$attr_code=$attributeModel->getAttributeCode();
	$new_attr_str_arr=[];
	$comma_value_arr=[];
	$a_value_label='';
	
		foreach ($applied_params as $a_key => $a_value) {
		if(array_key_exists($a_key,$optionarr))
	 {	
			if($request_var==$a_key)
			{
				$new_val=$a_value.','.$optionId;				
			}	
			else
			{
				$new_val=$a_value;
				
			}
			if(array_key_exists($a_key,$optionarr))
			 {
				 
			 if (strpos($new_val,',') !== false) {
			 $a_value_label='';
			$comma_value_arr=explode(',',(string)$new_val);
				$comma_value_arr=array_unique($comma_value_arr);
			 foreach($comma_value_arr as $comma_key=>$comma_val)
			 {
				
					 $a_value_label.=$this->helper_url->getUrlAlias($comma_val, $attr_id).$seperateChar;
			 }
		 }
		 else
		 {
			 
			$a_value_label=$this->helper_url->getUrlAlias($a_value, $attr_id);
		 }
			$new_attr_str_arr[$a_key]=$a_value_label;
			 }
		}
		}
	
	
  	$attr_str='';
	foreach ($new_attr_str_arr as $new_key => $new_val) {				  
	if($isAttributeInclude)
	{
		$attributeModel=$this->loadAttributeModelByCode($new_key);
		$attributeCode=$this->getAttributeIncludeData($attributeModel);	
		$attr_str.=$attributeCode.$seperateChar.$new_val.$seperateChar;
	}
	  else
	  {
		  $attr_str.=$new_val.$seperateChar;
	  }
	}
	/** update for fix issue remove attribute***/		
		
$attr_str = rtrim($attr_str,$seperateChar);
$attr_str = ltrim($attr_str,$seperateChar);				
$attr_str = preg_replace('/'.$seperateChar.'+/',$seperateChar, $attr_str);	
		return $attr_str;
	}
	public function checkCatExist($category,$page)
	{
		$category_coll = $this->_categoryFactory->create()
					->addAttributeToFilter('url_path', $category)
					->addAttributeToSelect('entity_id')
					->getFirstItem();
					$cat_id = $category_coll->getId();
		if($cat_id)
		{
			return $category;
		}
		else
		{
			if(!$cat_id)
					{
						
						$pos1 = strrpos($category, '/');
						$category = substr($page, 0, $pos1);
						 $category=$this->checkCatExist($category,$page);
						return $category;
					}
			//return false;
		}
	}
	
	public function getAjaxConfig()
    {
        return $this->scopeConfig->getValue('layerednavigation/setting/ajaxenable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
