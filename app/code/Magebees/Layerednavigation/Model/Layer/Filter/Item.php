<?php
namespace Magebees\Layerednavigation\Model\Layer\Filter;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{
    protected $_url;
    protected $_htmlPagerBlock;
    protected $_storeManager;
    protected $categoryRepository;
    protected $_attr_helper;
    protected $_request;
    protected $_helper;
    protected $helper_url;
    protected $_categoryFactory;
    protected $brands;
    protected $_objectManager;   
    protected $_scopeConfig;
    protected $productAttributeCollectionFactory;
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Magento\Framework\App\RequestInterface $request,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magebees\Layerednavigation\Helper\Url $helper_url,
        \Magebees\Layerednavigation\Model\Brands $brands, \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        array $data = []
    ) {
        $this->_url = $url;
		 $this->_attr_helper = $attr_helper;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->_request = $request;
        $this->_helper = $helper;
        $this->helper_url = $helper_url;
        $this->_categoryFactory = $categoryFactory;
        $this->brands = $brands;
        $this->_objectManager=$objectManager;
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->_scopeConfig=$scopeConfig;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }

    /**
     * Get filter instance
     *
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFilter()
    {
        $filter = $this->getData('filter');
        if (!is_object($filter)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The filter must be an object. Please set the correct filter.')
            );
        }
        return $filter;
    }

    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {
		$defaultBaseUrl=$this->_url->getBaseUrl();
		 $optionarr=$this->_attr_helper->getAllFilterableOptionsAsHash();
		$seperateChar=$this->_helper->getSeperateAttrChar();
		$replaceChar=$this->_helper->getReplaceSpecialChar();
		$isAttributeInclude=$this->_helper->IsEnableAttributeInclude();
        $path = $this->_request->getRequestString();
        $path = trim($path, '/');
        $finderparams = explode('/',  (string)$path);
         $item_value=$this->getValue();
        /*Get Filter url from here */       
        $applied_filter=[];
        $brandConfig=$this->_helper->getBrandConfigData();
        if (isset($brandConfig['brand_url_key'])) {
            $brand_url_key=$brandConfig['brand_url_key'];
        } else {
            $brand_url_key=null;
        }
    
		$is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $url_suffix=$this->_helper->getUrlSuffix();
        $applied_params=$this->_request->getParams();
        $activeFilters=$this->_objectManager->get('\Magento\LayeredNavigation\Block\Navigation\State')->getActiveFilters();
        $check_arr=[$rat_param,'price',$stock_param,'p','cat'];
        if ($activeFilters) {
            foreach ($activeFilters as $filter) {
               
                $applied_filter[]=$filter->getFilter()->getRequestVar();
            }
        } else {
            $applied_filter=[];
        }
        
        $diff_arr=array_diff($applied_filter, $check_arr);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                $filterCount=count($activeFilters);
                $urltype=$this->_helper->getUrlType();
                $currenturl=$this->_url->getCurrentUrl();
        
                $url_key=$this->_helper->getUrlKey();
        
        
                if (preg_match('/catalogsearch/', $currenturl)) {
                    return $this->getConditionalUrl();
                } else {
                    if ($urltype==0) {
                        return $this->getConditionalUrl();
                    } elseif ($urltype==1) {
                        // long with url key mode
			if (($this->getFilter()->getRequestVar()==$rat_param) ||($this->getFilter()->getRequestVar()=='price') ||($this->getFilter()->getRequestVar()==$stock_param)) 
			{
            	$check_custom_arr=[$rat_param,$stock_param,'price'];
				foreach ($check_custom_arr as $custom_arr) {
					if ($this->getFilter()->getRequestVar()==$custom_arr) {
					
						if (in_array($custom_arr, $applied_filter)) {
							$new_filter=array_flip($applied_filter);$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
					//if ($new_filter[$custom_arr]==$this->getFilter()->getValue())
					if ($applied_param_val==$this->getFilter()->getValue())
					{
								return $this->getRemoveUrl();
					} else {
						return $this->getDefaultUrl();
					}
						}
					}
				}
				$url=$this->getDefaultUrl();
				return $url;
			}
			elseif ($this->getFilter()->getRequestVar()=='cat') {
				$default_url=$this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
				$categoryId=$this->getValue();
				$category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
			if (!empty($applied_filter)) {
				$caturl=$category->getUrl();

				if ($url_suffix) {
					$cat_part = explode($url_suffix,  (string)$caturl);
					if (preg_match('/'.$url_key.'/', $default_url)) 
					{
					$query_part= explode('/'.$url_key.'/',  (string)$default_url);
					$url=$cat_part[0].'/'.$url_key.'/'.$query_part[1];
					} 
					else 
					{
						$query_part= explode('?', (string)$default_url);
						$url=$cat_part[0].'?'.$query_part[1];
					}
				} 
				else {
					$cat_part =$caturl;
					if (preg_match('/'.$url_key.'/', $default_url)) {
						$query_part= explode('/'.$url_key.'/',  (string)$default_url);
						$url=$cat_part.'/'.$url_key.'/'.$query_part[1];
					} 
					else {
						$query_part= explode('?',  (string)$default_url);
						$url=$cat_part.'?'.$query_part[1];
					}
				}
				return $url;
			} 
			else {
			return $category->getUrl();
			}
		}
		else{
			$attributeModel = $this->getFilter()->getAttributeModel();	
			$attr_id = $attributeModel->getId();
			$option_alias=$this->helper_url->getUrlAlias($this->getValue(), $attr_id);
			if (strpos($currenturl, '/'.$url_key.'/') === false) {
			if ($url_suffix) {
				if (preg_match('/'.$url_suffix.'/', $currenturl)) {
						$suburl = explode($url_suffix,  (string)$currenturl);
				} else {
					$suburl = explode('?',  (string)$currenturl);
				}
				$url=$suburl['0'];
				$baseurl = substr($currenturl, 0, strpos($currenturl, $url_suffix));
				$attr = substr(strrchr($baseurl, "/"), 1);
			} else {
				$url_arr = explode("?",  (string)$currenturl);
				$url=$url_arr[0];
				$url = preg_replace('{/$}', '', $url);
				$attr = substr(strrchr($url, "/"), 1);
				if (!$attr) {
					$s = explode("/",  (string)$url);
					$e=array_slice($s, -2);
					$attr=$e[0];
				}
			}
		}
		else
		{
	   	$suburl = explode('/'.$url_key.'/',  (string)$currenturl);
			$attr_arr=explode("?",  (string)$suburl[1]);
			$attr=$attr_arr[0];
			if($url_suffix)
			{			
			$attr=str_replace($url_suffix, "",$attr);
				
			}
			else
			{
			$attr=$suburl[1];
			}
			$attr=preg_replace('{/$}','',$attr);		
			$url=$suburl[0].'/'.$url_key.'/'.$attr;
			$url = preg_replace('{/$}', '', $url);
		}
			
		if (strpos($currenturl, '/'.$url_key.'/') === false) {

			$url.='/'.$url_key.'/';
		} else {
			if (!$this->_helper->isApplyMultiSelect($attributeModel)) {
				$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
				if (!$applied_param_val) {				   
					$url.=$seperateChar;
				}
			}
		}
 		if ((isset($applied_params[$rat_param])) || (isset($applied_params['price'])) || (isset($applied_params[$stock_param])) || (isset($applied_params['p'])) || (isset($applied_params['cat']))) 
 		{
		$queryUrl = explode('?',  (string)$currenturl);
	 	$attributeModel = $this->getFilter()->getAttributeModel();	
		$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);
	if (!$this->_helper->isApplyMultiSelect($attributeModel))
	{
		$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
		if ($applied_param_val) {
			$old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);
			$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
			/** update for fix issue remove attribute***/	
			$applied_params=$this->_request->getParams();
			$attributeModel = $this->getFilter()->getAttributeModel();
			$request_var=$this->getFilter()->getRequestVar();
			$attr_str=$this->_helper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$item_value,$isAttributeInclude,$request_var);	
			$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
			//$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
			if(!$attr_str)
			{						$url=str_replace('/'.$url_key.'/'.$attr,'',$url);
			}
			else
			{
			$url=str_replace('/'.$url_key.'/'.$attr,'/'.$url_key.'/'.$attr_str,$url);
			}		                

			$url.=$url_suffix.'?'.$queryUrl[1];
        } 
        else {
                if($this->_helper->IsEnableAttributeInclude())
				{	$url.=$attributeCode.$seperateChar.$option_alias.$url_suffix.'?'.$queryUrl[1];
				}
				else
				{				
				$url.=$option_alias.$url_suffix.'?'.$queryUrl[1];
				}
        }
	}
	return $url;
	} 
	else 
	{
		if (!$this->_helper->isApplyMultiSelect($attributeModel)) {
			$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
			if ($applied_param_val) {
				$old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);
				$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
		/** update for fix issue remove attribute***/	
	$applied_params=$this->_request->getParams();
  $attributeModel = $this->getFilter()->getAttributeModel();
	$request_var=$this->getFilter()->getRequestVar();
	$attr_str=$this->_helper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$item_value,$isAttributeInclude,$request_var);	
				
				$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
				//$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
				
				if(!$attr_str)
				{						$url=str_replace('/'.$url_key.'/'.$attr,'',$url);
				}
				else
				{
					$url=str_replace('/'.$url_key.'/'.$attr,'/'.$url_key.'/'.$attr_str,$url);
				}			

		
		$url.=$url_suffix;
		} 
		else {
			if($this->_helper->IsEnableAttributeInclude())
			{								
				$attributeModel = $this->getFilter()->getAttributeModel();
				$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);	
				$url.=$attributeCode.$seperateChar.$option_alias.$url_suffix;
			}
			else
			{
				$url.=$option_alias.$url_suffix;
			}						
		}
		}       
        return $url;
		}
		}
		}
		else {
                    // short without url key mode
		if (($this->getFilter()->getRequestVar()==$rat_param) ||
		($this->getFilter()->getRequestVar()=='price') ||
		($this->getFilter()->getRequestVar()==$stock_param)) {
			$check_custom_arr=[$rat_param,$stock_param,'price'];
			foreach ($check_custom_arr as $custom_arr) {
			if ($this->getFilter()->getRequestVar()==$custom_arr) {
			
				if (in_array($custom_arr, $applied_filter)) {
					$new_filter=array_flip($applied_filter);
					$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
					//if ($new_filter[$custom_arr]==$this->getFilter()->getValue())
					if ($applied_param_val==$this->getFilter()->getValue())
					{
						return $this->getRemoveUrl();
					} else {
						return $this->getDefaultUrl();
					}
				}
			}
		}
		$url=$this->getDefaultUrl();
		return $url;
		} 
		elseif ($this->getFilter()->getRequestVar()=='cat')
		{
		$default_url=$this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
		$categoryId=$this->getValue();
		$category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
		if (!empty($applied_filter)) {
			if (!empty($diff_arr)) {
				$caturl=$category->getUrl();
				if ($url_suffix) {
					$cat_part = explode($url_suffix,  (string)$caturl);
					$baseurl = substr($currenturl, 0, strpos($currenturl, $url_suffix));
					$attr = substr(strrchr($baseurl, "/"), 1);		
					$query_part= explode($url_suffix,  (string)$default_url);
				
					$url=$cat_part[0].'/'.$attr.$url_suffix.$query_part[1];
				} else {
					$cat_part =$caturl;
					$attr_str = substr(strrchr($currenturl, "/"), 1);
					$attr_arr=explode('?',  (string)$attr_str);
					$attr=$attr_arr[0];
					$query_part= explode($attr,  (string)$default_url);
					$url=$cat_part.'/'.$attr.$query_part[1];
				}

					return $url;
			} 
			else 
			{
			$caturl=$category->getUrl();
			if ($url_suffix)
			{
				$cat_part = explode($url_suffix,  (string)$caturl);
				$query_part= explode('?',  (string)$default_url);
				$url=$cat_part[0].$url_suffix.'?'.$query_part[1];
			} 
			else 
			{
				$cat_part = $caturl;
				$query_part= explode('?',  (string)$default_url);
				$url=$cat_part.'?'.$query_part[1];
			}
			return $url;
			}
			} else {
			return $category->getUrl();
			}
		} 
		else 
		{
							
		$attributeModel = $this->getFilter()->getAttributeModel();
		$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);	
		$attr_id = $attributeModel->getId();
		$option_alias=$this->helper_url->getUrlAlias($this->getValue(), $attr_id);
		$route=$this->_request->getRouteName();
		$controller=$this->_request->getControllerName();
		
			$path_arr=explode('?', (string)$path);
			if(isset($path_arr[0]))
			{
				if (($url_suffix)&&(preg_match('/'.$url_suffix.'/', $currenturl))) {
					$suburl_arr = explode($url_suffix,  (string)$path_arr[0]);
					$base_path=$suburl_arr[0];
				}
				else
				{
					$base_path=$path_arr[0];
				}
			if((preg_match('/layerednavigation/',$route))&&($controller=='brand'))
			{		
				
				$bsuburl = explode($brand_url_key.'/',  (string)$base_path);
				if($brand_url_key)
				{
				$bsuburl_arr=explode('/',  (string)$bsuburl[1]);
				$brand_url_arr= array_slice($bsuburl_arr,1);
				$brand_url_str=implode('/',$brand_url_arr);
				$attr=$brand_url_str;
				}
				else
				{
				
					if(isset($bsuburl[1]))
					{
						$brand_url_arr= array_slice($bsuburl,1);
				$brand_url_str=implode('/',$brand_url_arr);
				$attr=$brand_url_str;
					}
					else
					{
						$attr='';
					}
				}
			}
			else
			{
			
				 $position = strrpos($base_path, '/');
				  $category = substr($base_path, 0, $position);

				
				if(!$category){
                    $category = $base_path;
                }  

				$category=$this->_helper->checkCatExist($category,$base_path);
				if($category)
				{	$category_arr=explode($category.'/', (string)$base_path);
				//$param_str=$category_arr[1];
				if (array_key_exists('1', $category_arr)) {
                            $param_str=$category_arr[1];
                        }else{
						  $param_str=$category_arr[0];
						}
				 $param_arr=explode($url_suffix,  (string)$param_str);
				 $param=$param_arr[0];
				 $attr=$param;
				}
				
			}
		
				
			}
			$url=$defaultBaseUrl.$base_path;			
			if (($brand_url_key=='')||($brand_url_key!='')) {
				
			$category = $this->_categoryFactory->create()
			->addAttributeToFilter('url_key', $attr)
			->addAttributeToSelect('*')
			->getFirstItem();
			$cat_id = $category->getId();

			if ($cat_id) {

				if($this->_helper->IsEnableAttributeInclude())
				{
					 $url.='/'.$attributeCode.$seperateChar;
				}
				else
				{
					 $url.='/';
				}

			}
			else 
			{
				
				if (!$attr) {
					
					if($this->_helper->IsEnableAttributeInclude())
					{
						 
						 $url.='/'.$attributeCode.$seperateChar;
					}
					else
					{
							$url.='/';
					}
				} 
				elseif (!$this->_helper->isApplyMultiSelect($attributeModel)) {

			$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
			if (!$applied_param_val) {
				if($this->_helper->IsEnableAttributeInclude())
				{
					$url.=$seperateChar.$attributeCode.$seperateChar;
				}
				else
				{
					  $url.=$seperateChar;
				}
			}
		}
		}
			} 
			else 
			{

			$brand = $this->brands->getCollection()
			->addFieldToFilter('seo_url', ['eq'=>$attr.$url_suffix]);
			$brandData=$brand->getData();

			if ($brandData) {

				if($this->_helper->IsEnableAttributeInclude())
				{
					 $url.='/'.$attributeCode.$seperateChar;		
				}
				else
				{							
				$url.='/';
				}
			} else {
				if (!is_array($attr)) {
					$category = $this->_categoryFactory->create()
					->addAttributeToFilter('url_key', $attr)
					->addAttributeToSelect('*')
					->getFirstItem();
					$cat_id = $category->getId();

					if ($cat_id) {
						if($this->_helper->IsEnableAttributeInclude())
					{
						 $url.='/'.$attributeCode.$seperateChar;
					}
					else
					{
					$url.='/';
					}
					} else {
						
					if (!$attr) {
							if($this->_helper->IsEnableAttributeInclude())
					{
						 $url.='/'.$attributeCode.$seperateChar;
					}
					else
					{
					$url.='/';
					}
					} elseif (!$this->_helper->isApplyMultiSelect($attributeModel)) {
					$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
					if (!$applied_param_val) {
						$url.=$seperateChar;				
					}
					}
					}
				} else {
					$url.=$seperateChar;
				 
				}
			}
		}		
	if ((isset($applied_params[$rat_param])) || (isset($applied_params['price'])) || (isset($applied_params[$stock_param])) || (isset($applied_params['p'])) || (isset($applied_params['cat']))) {
		$queryUrl = explode('?',  (string)$currenturl);
	if (!$this->_helper->isApplyMultiSelect($attributeModel)) 
	{
	$attributeModel = $this->getFilter()->getAttributeModel();
	$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);	
	
	$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
	if ($applied_param_val) {
		$old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);
	//$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
		/** update for fix issue remove attribute***/	
	$applied_params=$this->_request->getParams();
  $attributeModel = $this->getFilter()->getAttributeModel();
	$request_var=$this->getFilter()->getRequestVar();
	$attr_str=$this->_helper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$item_value,$isAttributeInclude,$request_var);	
	
			$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
				$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
				if(!$attr_str)
				{	$url=str_replace('/'.$attr,'',$url);
				}
				else
				{					$url=str_replace('/'.$attr,'/'.$attr_str,$url);
				}	
		
		
	
	$url.=$url_suffix.'?'.$queryUrl[1];
	} else {
		$url.=$option_alias.$url_suffix.'?'.$queryUrl[1];
	}
	}
	return $url;
	} 
	else 
	{
	if (!$this->_helper->isApplyMultiSelect($attributeModel)) {
		$applied_param_val=$this->_request->getParam($this->getFilter()->getRequestVar());
		if ($applied_param_val) {
		$old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);	
		/** update for fix issue remove attribute***/	
			$applied_params=$this->_request->getParams();
			$attributeModel = $this->getFilter()->getAttributeModel();
			$request_var=$this->getFilter()->getRequestVar();
			$attr_str=$this->_helper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$item_value,$isAttributeInclude,$request_var);	

			$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
			$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
			if(!$attr_str)
			{	$url=str_replace('/'.$attr,'',$url);
			}
			else
			{					$url=str_replace('/'.$attr,'/'.$attr_str,$url);
			}
			
			$url.=$url_suffix;
			} else {
				$url.=$option_alias.$url_suffix;
			}
			}
		 	return $url;
			}
		}
	}
}
            } else {
                $url=$this->getDefaultUrl();
                return $url;
            }
        } else {
            $url=$this->getDefaultUrl();
            return $url;
        }
    }
    public function getConditionalUrl()
    {
		 $optionarr=$this->_attr_helper->getAllFilterableOptionsAsHash();
        $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        $currenturl=$this->_url->getCurrentUrl();
        $activeFilters=$this->_objectManager->get('\Magento\LayeredNavigation\Block\Navigation\State')->getActiveFilters();
        $applied_filter_count=count($activeFilters);
        if ($this->getFilter()->getRequestVar()=='cat') {
                $categoryId=$this->getValue();
                     $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
            if ($applied_filter_count>=1) {
                     //if(strpos($currenturl,'?')  > -1)
                     $caturl=$category->getUrl();
                $currentquery = explode('?',  (string)$currenturl);

               
                if (array_key_exists("1",$currentquery)) {
                	$url=$caturl.'?'.$currentquery[1];
	            }else{
	            	$url=$caturl.'?'.$currentquery[0];
	            }
                return $url; 
            } else {
                return $category->getUrl();
            }
        } elseif ($this->getFilter()->hasAttributeModel()) {
                $attributeModel = $this->getFilter()->getAttributeModel();
            if (!$this->_helper->isApplyMultiSelect($attributeModel)) {
                        $requestParameters=$this->_request->getParam($this->getFilter()->getRequestVar());
                if ($requestParameters) {
                    $requestParameters = [$requestParameters];
                    foreach ($requestParameters as $key => $value) {
                        if ($value == $this->getValue()) {
                            unset($requestParameters[$key]);
                            $query = [$this->getFilter()->getRequestVar() => $requestParameters];
                            return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
                        }
                    }
                }
            }
                    $url=$this->getDefaultUrl();
                    return $url;
        } else {
            $requestParameters=$this->_request->getParam($this->getFilter()->getRequestVar());
            if ($requestParameters!='') {
                $requestParameters = [$requestParameters];
                foreach ($requestParameters as $key => $value) {
                    if ($value == $this->getValue()) {
                        unset($requestParameters[$key]);
                        $query = [$this->getFilter()->getRequestVar() => $requestParameters];
                        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
                    }
                }
            }
            $url=$this->getDefaultUrl();
            return $url;
        }
    }
    public function getDefaultUrl()
    {
            return parent::getUrl();
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        return parent::getRemoveUrl();
    }

    /**
     * Get url for "clear" link
     *
     * @return false|string
     */
    public function getClearLinkUrl()
    {
        return parent::getClearLinkUrl();
    }

    /**
     * Get item filter name
     *
     * @return string
     */
    public function getName()
    {
        return parent::getName();
    }

    /**
     * Get item value as string
     *
     * @return string
     */
    public function getValueString()
    {
        return parent::getValueString();
    }
	 public function _sortByPosition($a, $b)
    {		$key1=key($a);
    		$key2=key($b);
		  return strlen($a[$key1]) - strlen($b[$key2]);
	}
    
}
