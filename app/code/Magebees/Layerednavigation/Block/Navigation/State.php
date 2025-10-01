<?php

namespace Magebees\Layerednavigation\Block\Navigation;

use Magento\Framework\View\Element\Template;

class State extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magebees_Layerednavigation::layer/state.phtml';

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;
    protected $_helper; 
    protected $_attr_helper; 
    protected $helper_url; 
    protected $_request; 
    protected $_scopeConfig; 

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magebees\Layerednavigation\Helper\Data $helper, 		\Magebees\Layerednavigation\Helper\Url $helper_url,
        \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_helper = $helper;
        $this->_attr_helper = $attr_helper;
		$this->helper_url = $helper_url;
        $this->_request =$context->getRequest();
        $this->_scopeConfig=$context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }
        return $filters;
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl() {
		$brandConfig=$this->_helper->getBrandConfigData();
        if (isset($brandConfig['brand_url_key'])) {
            $brand_url_key=$brandConfig['brand_url_key'];
        } else {
            $brand_url_key=null;
        }
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $check_arr=[$rat_param,'price',$stock_param,'p','cat'];
        $active_filter=$this->getActiveFilters();
        if ($active_filter) {
            foreach ($active_filter as $filter) {
                $applied_filter[]=$filter->getFilter()->getRequestVar();
            }
        } else {
            $applied_filter=[];
        }
        $diff_arr=array_diff($applied_filter, $check_arr);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                $urltype=$this->_helper->getUrlType();
                $currenturl=$this->_urlBuilder->getCurrentUrl();
                $seo_url_key=$this->_helper->getUrlKey();
                $url_suffix=$this->_helper->getUrlSuffix();
                if (preg_match('/catalogsearch/', $currenturl)) {              
                    $result=$this->getClearAllUrl();
                    return $result;
                } else {
                    if ($urltype==0) {
                        $result=$this->getClearAllUrl();
                        return $result;
                    } elseif ($urltype==1) {
                        if (!empty($diff_arr)) {                 
							 $suburl = explode('/'.$seo_url_key.'/', (string)$currenturl);
                        if ($url_suffix) {
                            $subparam=explode($url_suffix, (string)$suburl[1]);
							$attr_option=$subparam[0];
                        
                        } else {
                            $subparam=explode('?', (string)$suburl[1]);
                            $attr_option=str_replace("/", "", $subparam[0]);
                        }
							  $clearurl=str_replace('/'.$seo_url_key.'/'.$attr_option,'',$currenturl);
							$clearurl_arr=explode('?', (string)$clearurl);
							
                                return $clearurl_arr[0];
                        } else {
                            $result=$this->getClearAllUrl();
                            return $result;
                        }
                    } else {
						$path = $this->_request->getRequestString();
          				$path = trim($path, '/');
						$route=$this->_request->getRouteName();
						$controller=$this->_request->getControllerName();
						$defaultBaseUrl=$this->_urlBuilder->getBaseUrl();
                        if (!empty($diff_arr)) {
							$path_arr=explode('?',(string)$path);
							if(isset($path_arr[0]))
							{
								if (($url_suffix)&&(preg_match('/'.$url_suffix.'/', $currenturl))) {
									$suburl_arr = explode($url_suffix, (string)$path_arr[0]);
									$base_path=$suburl_arr[0];
							


				}else{
									$base_path=$path_arr[0];
								}
								if((preg_match('/layerednavigation/',$route))&&($controller=='brand'))	{




									$bsuburl = explode($brand_url_key.'/', (string)$base_path);
									if($brand_url_key)	{
										$bsuburl_arr=explode('/', (string)$bsuburl[1]);			
										$attr=$bsuburl_arr[0];
										$new_url=$defaultBaseUrl;				
										$new_url.=$brand_url_key.'/';				
										$new_url.=$attr;
									}else{
										$attr=$bsuburl[0];
										$new_url=$defaultBaseUrl;	
										$new_url.=$attr;
									}

								}else{		
								
									$position = strrpos($base_path, '/');
									$category = substr($base_path, 0, $position);
	
	
									$category=$this->_helper->checkCatExist($category,$base_path);


									if($category){																	
										$category_arr=explode($category.'/', (string)$base_path);
										$param_str=$category_arr[1];
										$param_arr=explode($url_suffix, (string)$param_str);
										$param=$param_arr[0];
										$attr=$param;
									}

									//$new_url=$defaultBaseUrl.$base_path;	

								//	$new_url=rtrim(str_replace($attr,'',$new_url),'/');

								// start by amarish

									$base_path = explode("/", $base_path);
									$base_path =  array_slice($base_path, 0, -1);
									$base_path =  implode("/",$base_path);

									$new_url=$defaultBaseUrl.$base_path;	

								// end by amarish	
									

								}
								if ($url_suffix) {
									$clearurl=$new_url.$url_suffix;
								} else {
									 $clearurl=$new_url;
								}
							}
							return $clearurl;
                          
                        } else {
                            $result=$this->getClearAllUrl();
                            return $result;
                        }
                    }
                }
            } else {
                $result=$this->getClearAllUrl();
                return $result;
            }
        } else {
            $result=$this->getClearAllUrl();
            return $result;
        }
    }
    public function getClearAllUrl()
    {
        $filterState = [];
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }
    
    /*This function work for set remove filter url in our module's state.phtml */
    public function getRemoveUrl($item)
    {
		$defaultBaseUrl=$this->_urlBuilder->getBaseUrl();
		$route=$this->_request->getRouteName();
		$controller=$this->_request->getControllerName();
		$path = $this->_request->getRequestString();
        $path = trim($path, '/');
		$brandConfig=$this->_helper->getBrandConfigData();
        if (isset($brandConfig['brand_url_key'])) {
            $brand_url_key=$brandConfig['brand_url_key'];
        } else {
            $brand_url_key=null;
        }
		$seperateChar=$this->_helper->getSeperateAttrChar();
		$replaceChar=$this->_helper->getReplaceSpecialChar();
		$isAttributeInclude=$this->_helper->IsEnableAttributeInclude();
        $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($is_default_enabled==0) {
            if ($is_enabled) {
                $urltype=$this->_helper->getUrlType();
                $currenturl=$this->_urlBuilder->getCurrentUrl();
                $seo_url_key=$this->_helper->getUrlKey();
                $url_suffix=$this->_helper->getUrlSuffix();
                $optionarr=$this->_attr_helper->getAllFilterableOptionsAsHash();
                $item_value=$item->getValue();
                $applied_params=$this->_request->getParams();
        
                if (preg_match('/catalogsearch/', $currenturl)) {              
                    $result=$this->getFilterRemoveUrl($item);
                    return $result;
                } else {
                    if ($urltype==0) {
                        $result=$this->getFilterRemoveUrl($item);
                        return $result;
                    } elseif ($urltype==1) {
                        if (($item->getFilter()->getRequestVar()==$rat_param) ||
                        ($item->getFilter()->getRequestVar()=='price') ||
                        ($item->getFilter()->getRequestVar()=='cat') ||
                        ($item->getFilter()->getRequestVar()==$stock_param)) {
                            $result=$this->getFilterRemoveUrl($item);
                            return $result;
                        }
                        $suburl = explode('/'.$seo_url_key.'/', (string)$currenturl);
                        if ($url_suffix) {
                            $subparam=explode($url_suffix, (string)$suburl[1]);
							$attr_option=$subparam[0];
                          
                        } else {
                            $subparam=explode('?', (string)$suburl[1]);
                            $attr_option=str_replace("/", "", $subparam[0]);
                        }
            
				if (strpos($attr_option,$seperateChar) !== false) {		/** update for fix issue remove attribute***/	
						$applied_params=$this->_request->getParams();
					    $attributeModel = $item->getFilter()->getAttributeModel();
						$attr_id = $attributeModel->getId();
						$new_attr_str_arr=[];
						foreach ($applied_params as $a_key => $a_value) {		
							if(array_key_exists($a_key,$optionarr)) {
								if (strpos($a_value,',') !== 	false) {
									$applied_param_arr=explode(',',(string)$a_value);
									foreach($applied_param_arr as $m_key=>$m_val){
										if($m_val!=$item_value)	{
											if(isset($new_attr_str_arr[$a_key])){
												$old_label=$new_attr_str_arr[$a_key];				
												$v_label=$this->helper_url->getUrlAlias($m_val, $attr_id);		$new_attr_str_arr[$a_key]=$old_label.$seperateChar.$v_label;
											}else{
												$v_label=$this->helper_url->getUrlAlias($m_val, $attr_id);
												$new_attr_str_arr[$a_key]=$v_label;
											}
										}
									}
								 }else{
									if($a_value!=$item_value){
										$a_value_label=$this->helper_url->getUrlAlias($a_value, $attr_id);
										$new_attr_str_arr[$a_key]=$a_value_label;
									}
								 }
							 }
						 }
						 $new_attr_str='';
						 foreach ($new_attr_str_arr as $new_key => $new_val) {				  
							if($isAttributeInclude)	{
								$attributeModel=$this->_helper->loadAttributeModelByCode($new_key);
								$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);	
								$new_attr_str.=$attributeCode.$seperateChar.$new_val.$seperateChar;
							}else {
								  $new_attr_str.=$new_val.$seperateChar;
							}
						 }
						/** update for fix issue remove attribute***/		

							$new_attr_str = rtrim($new_attr_str,$seperateChar);
							$new_attr_str = ltrim($new_attr_str,$seperateChar);				
							$new_attr_str = preg_replace('/'.$seperateChar.'+/',$seperateChar, $new_attr_str);
							if(!$new_attr_str){
								$clearurl=$suburl[0].$url_suffix;
							}else{			
								$clearurl=$suburl[0].'/'.$seo_url_key.'/'.$new_attr_str.$url_suffix;			
							}



				} else {
                    $new_url = preg_replace('{/$}', '', $suburl['0']);
					
					$clearurl=$new_url.$url_suffix;
                          
               }
                
                
					   if (isset($applied_params[$rat_param]) || isset($applied_params['price']) || isset($applied_params[$stock_param]) || isset($applied_params['p']) || isset($applied_params['cat'])) {
						   $queryUrl = explode('?', (string)$currenturl);
						   $url=$clearurl.'?'.$queryUrl[1];
						   return $url;
					   } else {
							 return $clearurl;
					   }
            	} else {
                        if (($item->getFilter()->getRequestVar()==$rat_param) ||
                        ($item->getFilter()->getRequestVar()=='price') ||
                        ($item->getFilter()->getRequestVar()=='cat') ||
                        ($item->getFilter()->getRequestVar()==$stock_param)) {
                            $result=$this->getFilterRemoveUrl($item);
                            return $result;
                        }                   
						
					$path_arr=explode('?',(string)$path);
					if(isset($path_arr[0]))	{
						if (($url_suffix)&&(preg_match('/'.$url_suffix.'/', $currenturl))) {
							$suburl_arr = explode($url_suffix, (string)$path_arr[0]);
							$base_path=$suburl_arr[0];
						}else{
							$base_path=$path_arr[0];
						}
						if((preg_match('/layerednavigation/',$route))&&($controller=='brand'))	{		
							$bsuburl = explode($brand_url_key.'/',(string)$base_path);
							if($brand_url_key)	{
								$bsuburl_arr=explode('/',(string)$bsuburl[1]);
								$brand_url_arr= array_slice($bsuburl_arr,1);
								$brand_url_str=implode('/',$brand_url_arr);
								$attr_option=$brand_url_str;
							}else{
								if(isset($bsuburl[1])){
									$brand_url_arr= array_slice($bsuburl,1);
									$brand_url_str=implode('/',$brand_url_arr);
									$attr_option=$brand_url_str;
								}else{
									$attr_option='';
								}
							}
						}else{
							  $position = strrpos($base_path, '/');
							  $category = substr($base_path, 0, $position);
							  $category=$this->_helper->checkCatExist($category,$base_path);
							  if($category)	{																																	 $category_arr=explode($category.'/',(string)$base_path);
								 $param_str=$category_arr[1];
								 $param_arr=explode($url_suffix,(string)$param_str);
								 $param=$param_arr[0];
								 $attr_option=$param;
							  }
						}				
			}
					$url=$defaultBaseUrl.$base_path;
    if (strpos($attr_option,$seperateChar) !== false) {
		
	/** update for fix issue remove attribute***/	
			$applied_params=$this->_request->getParams();
			$attributeModel = $item->getFilter()->getAttributeModel();
			$attr_id = $attributeModel->getId();
			$new_attr_str_arr=[];
			foreach ($applied_params as $a_key => $a_value) {		
				if(array_key_exists($a_key,$optionarr)) {
					if (strpos($a_value,',') !== 	false) {
						$applied_param_arr=explode(',',(string)$a_value);
						foreach($applied_param_arr as $m_key=>$m_val){
							if($m_val!=$item_value)	{
								if(isset($new_attr_str_arr[$a_key])){
									$old_label=$new_attr_str_arr[$a_key];				
									$v_label=$this->helper_url->getUrlAlias($m_val, $attr_id);		$new_attr_str_arr[$a_key]=$old_label.$seperateChar.$v_label;
								}else{
									$v_label=$this->helper_url->getUrlAlias($m_val, $attr_id);
									$new_attr_str_arr[$a_key]=$v_label;
								}
							}
						}
					 }else {
						if($a_value!=$item_value){
							$a_value_label=$this->helper_url->getUrlAlias($a_value, $attr_id);
							$new_attr_str_arr[$a_key]=$a_value_label;
						}
					 }
				 }
			 }
			$new_attr_str='';
			foreach ($new_attr_str_arr as $new_key => $new_val) {				  
				if($isAttributeInclude)	{
					$attributeModel=$this->_helper->loadAttributeModelByCode($new_key);
					$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);	
					$new_attr_str.=$attributeCode.$seperateChar.$new_val.$seperateChar;
				}else {
				  $new_attr_str.=$new_val.$seperateChar;
			    }
			}
			/** update for fix issue remove attribute***/		
		
			$new_attr_str = rtrim($new_attr_str,$seperateChar);
			$new_attr_str = ltrim($new_attr_str,$seperateChar);				
			$new_attr_str = preg_replace('/'.$seperateChar.'+/',$seperateChar, $new_attr_str);						  
			$attr_option=htmlentities(urldecode($attr_option), ENT_QUOTES, "UTF-8");	
	
		
		
			if ($url_suffix) {     
                $filter = $item->getFilter();
				$attributeModel = $filter->getAttributeModel();
				$currenturl=htmlentities(urldecode($currenturl), ENT_QUOTES, "UTF-8");		
				$attrUrl=str_replace($attr_option,$new_attr_str,$currenturl);
				$new=explode($url_suffix, (string)$attrUrl);
				$new_url = preg_replace('{/$}', '', $new[0]);    		     
				$clearurl=$new_url.$url_suffix;
			} else {
				$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
				$attrUrl=str_replace($attr_option, $new_attr_str, $url);
				$clearurl = $attrUrl;
			}                      
       } else {
			if ($url_suffix) {
				$currenturl=htmlentities(urldecode($currenturl), ENT_QUOTES, "UTF-8");
				$attrUrl=str_replace('/'.$attr_option, '', $currenturl);
				$new=explode($url_suffix, (string)$attrUrl);
				$new_url = preg_replace('{/$}', '', $new[0]);
			} else {
				$attrUrl=str_replace($attr_option, '', $url);
				$new_url = preg_replace('{/$}', '', $attrUrl);
			}
			if ($url_suffix) {
					$clearurl=$new_url.$url_suffix;
				} else {
					$clearurl=$new_url;
				}
      }
                
				  if (isset($applied_params[$rat_param]) || isset($applied_params['price']) || isset($applied_params[$stock_param]) || isset($applied_params['p']) || isset($applied_params['cat'])) {
					  $queryUrl = explode('?', (string)$currenturl);
					  $url=$clearurl.'?'.$queryUrl[1];
					  return $url;
				   } else {
					  return $clearurl;
				   }
      		 }
     	 }
  		} else {
          $result=$this->getFilterRemoveUrl($item);
          return $result;
    	}
 	} else {
            $result=$this->getFilterRemoveUrl($item);
            return $result;
        }
    }
    public function getFilterRemoveUrl($item)
    {
        
        $filter = $item->getFilter();
        $rat_label=$this->_scopeConfig->getValue('layerednavigation/rating_filter/label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $requestParameters = $this->_request->getParam($filter->getRequestVar());
        if ($requestParameters) {
            if (strpos($requestParameters, ',')===false) {
                $requestParameters = [$requestParameters];
            } else {
                $requestParameters = explode(',', (string)$requestParameters);
            }
        } else {
             $requestParameters =[];
        }
        
        foreach ($requestParameters as $key => $value) {
            if (($item->getName()=='Price')) {
                $item_value=$item->getValue();
                $val_str=implode("-", $item_value);
                if ($val_str==$value) {
                    unset($requestParameters[$key]);
                }
            }
            
            if ($value == $item->getValue()) {
                unset($requestParameters[$key]);
            }
        }
        if (!empty($requestParameters)) {
            $requestParameters=implode(",", $requestParameters);
        }
    
        
        $query = [
            $filter->getRequestVar() => $requestParameters
           
        ];
        
        
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * Retrieve Layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer($this->_catalogLayer);
        }
        return $this->_getData('layer');
    }
	 public function _sortByPosition($a, $b)
    {		$key1=key($a);
    		$key2=key($b);
		  return strlen($a[$key1]) - strlen($b[$key2]);
	}
	
}
