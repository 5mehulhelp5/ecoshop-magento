<?php
namespace Magebees\Layerednavigation\Block\Navigation;
class MultiSelect extends AbstractRenderLayered
{    
    protected $_template = 'Magebees_Layerednavigation::layer/multiselect.phtml';
    protected $_request; 
    protected $_attr_helper; 
    protected $_helper; 
    protected $_url; 
    protected $brands; 
    protected $_categoryFactory; 
    protected $helper_url; 
    protected $_scopeConfig; 
    protected $layerattributeFactory; 

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
        \Magebees\Layerednavigation\Helper\Url $helper_url,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magebees\Layerednavigation\Model\Brands $brands,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        array $data = []
    ) {
        $this->_request = $context->getRequest();
        $this->_attr_helper = $attr_helper;
        $this->_helper = $helper;
        $this->_url = $context->getUrlBuilder();
        $this->brands = $brands;
        $this->_categoryFactory = $categoryFactory;
        $this->helper_url = $helper_url;
        $this->_scopeConfig=$context->getScopeConfig();
        $this->layerattributeFactory = $layerattributeFactory;
         parent::__construct($context, $data);
    }

    private $htmlPagerBlock;

    public function setHtmlPagerBlock(\Magento\Theme\Block\Html\Pager $htmlPagerBlock)
    {
        $this->htmlPagerBlock = $htmlPagerBlock;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilterItems()
    {
       /*fro here set sort order for options in layer navigation*/
        $items = $this->filter->getItems();
        if ($this->filter->hasAttributeModel()) {
            $attributeModel = $this->filter->getAttributeModel();
            $sort_order ='';
            $attr_id = $attributeModel->getId();
            $layer_model=$this->layerattributeFactory->create();
            $attributeCollection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attr_id);
        
            foreach ($attributeCollection as $attr) {
                $sort_order =  $attr->getData('sort_option');
            }
        } else {
            $sort_order='';
        }
        if ($sort_order) {
            if ($sort_order == 1) {
                usort($items, [$this, "_sortByName"]);
            } elseif ($sort_order == 2) {
                usort($items, [$this, "_sortByCounts"]);
            }
        } else {
            return $items ;
        }
        return $items;
    }
    public function _sortByName($a, $b)
    {
        $x = trim($a->getLabel());
        $y = trim($b->getLabel());

        if ($x == '') {
            return 1;
        }
        if ($y == '') {
            return -1;
        }

        if (is_numeric($x) && is_numeric($y)) {
            if ($x == $y) {
                return 0;
            }
            return ($x < $y ? 1 : -1);
        } else {
            return strcasecmp($x, $y);
        }
    }
    public function _sortByCounts($a, $b)
    {
        
        if ($a->getCount() == $b->getCount()) {
            return 0;
        }
        
        return ($a->getCount() < $b->getCount() ? 1 : -1);
    }

    public function getFilterItemUrl(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {

		$seperateChar=$this->_helper->getSeperateAttrChar();
		$isAttributeInclude=$this->_helper->IsEnableAttributeInclude();
		$request_var=$item->getFilter()->getRequestVar();
		$is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if ($is_default_enabled==0) {
            if ($is_enabled) {
				$path = $this->_request->getRequestString();
				$path = trim($path, '/');
				$finderparams = explode('/', (string)$path);
				$brandConfig=$this->_helper->getBrandConfigData();
				if (isset($brandConfig['brand_url_key'])) {
					$brand_url_key=$brandConfig['brand_url_key'];
				} else {
					$brand_url_key=null;
				}
				$urltype=$this->_helper->getUrlType();
				$currenturl=$this->_url->getCurrentUrl();
				$rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
				$stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
				$applied_params=$this->_request->getParams();
        
				if (preg_match('/catalogsearch/', $currenturl)) {
					return $this->getDefaultFilterItemUrl($item);
				}else{
					if($urltype==0) {
						return $this->getDefaultFilterItemUrl($item);
					}elseif ($urltype==1) {
						$applied_param_val=$this->_request->getParam($item->getFilter()->getRequestVar());
						$item_value=$item->getValue();
						if (($item->getFilter()->getRequestVar()==$rat_param) ||
						($item->getFilter()->getRequestVar()=='price') ||
						($item->getFilter()->getRequestVar()=='cat') ||
						($item->getFilter()->getRequestVar()==$stock_param)) {
							return $this->getDefaultFilterItemUrl($item);
						}
						$attributeModel = $item->getFilter()->getAttributeModel();
						$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);	
						$attr_id = $attributeModel->getId();
						$option_alias=$this->helper_url->getUrlAlias($item->getValue(), $attr_id);
						$url_suffix=$this->_helper->getUrlSuffix();
						$url_key=$this->_helper->getUrlKey();

						if (strpos($currenturl, '/'.$url_key.'/') === false) {
							if ($url_suffix) {
								if (preg_match('/'.$url_suffix.'/', $currenturl)) {
										$suburl = explode($url_suffix, (string)$currenturl);
								} else {
									$suburl = explode('?', (string)$currenturl);
								}
								$url=$suburl['0'];
								$baseurl = substr($currenturl, 0, strpos($currenturl, $url_suffix));
								$attr = substr(strrchr($baseurl, "/"), 1);
							} else {
								$url_arr = explode("?", (string)$currenturl);
								$url=$url_arr[0];
								$url = preg_replace('{/$}', '', $url);
								$attr = substr(strrchr($url, "/"), 1);
								if (!$attr) {
									$s = explode("/", (string)$url);
									$e=array_slice($s, -2);
									$attr=$e[0];
								}
							}
						}else{
							$suburl = explode('/'.$url_key.'/', (string)$currenturl);
							$attr_arr=explode("?", (string)$suburl[1]);
							$attr=$attr_arr[0];
							if($url_suffix){			
								$attr=str_replace($url_suffix, "",$attr);
							}else{
								$attr=$suburl[1];
							}
							$attr=preg_replace('{/$}','',$attr);		
							$url=$suburl[0].'/'.$url_key.'/'.$attr;
							$url = preg_replace('{/$}', '', $url);
						}

						if (strpos($currenturl, '/'.$url_key.'/') === false) {
							$url.='/'.$url_key.'/';

						}else{
							if(!$applied_param_val)	{
								$url.=$seperateChar;
							}
						}

						if($applied_param_val){	

							$attr_str=$this->_helper->resetMultipleFilter($applied_param_val,$applied_params,$attributeModel,$item_value,$attributeCode,$isAttributeInclude,$request_var);

							$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
							$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
											$url=str_replace('/'.$url_key.'/'.$attr,'/'.$url_key.'/'.$attr_str,$url);
							$option_alias='';

						}else{
							if($this->_helper->IsEnableAttributeInclude()){	
								$url.=$attributeCode.$seperateChar;
							}				
						}

						if ((isset($applied_params[$rat_param])) || (isset($applied_params['price'])) || (isset($applied_params[$stock_param])) || (isset($applied_params['p'])) || (isset($applied_params['cat']))) {
							$queryUrl = explode('?', (string)$currenturl);
							if (strpos($queryUrl[1], '?') !== false) {
								$url.=$option_alias.$url_suffix.$queryUrl[1];
							} else {
								$url.=$option_alias.$url_suffix.'?'.$queryUrl[1];
							}

						 return $url;
						} else {
							$url.=$option_alias.$url_suffix;
							return $url;
						}
				} else {
					$item_value=$item->getValue();
					if (($item->getFilter()->getRequestVar()==$rat_param) ||
					($item->getFilter()->getRequestVar()=='price') ||
					($item->getFilter()->getRequestVar()=='cat') ||
					($item->getFilter()->getRequestVar()==$stock_param)) {
						return $this->getDefaultFilterItemUrl($item);
					} else {					
						$attributeModel = $item->getFilter()->getAttributeModel();
						$attributeCode=$this->_helper->getAttributeIncludeData($attributeModel);	
						$attr_id = $attributeModel->getId();
						$option_alias=$this->helper_url->getUrlAlias($item->getValue(), $attr_id);
						$url_suffix=$this->_helper->getUrlSuffix();	
						$route=$this->_request->getRouteName();
						$controller=$this->_request->getControllerName();			
						$path_arr=explode('?',(string)$path);
						if(isset($path_arr[0]))	{
							if (($url_suffix)&&(preg_match('/'.$url_suffix.'/', $currenturl))) {
								$suburl_arr = explode($url_suffix, (string)$path_arr[0]);
								$base_path=$suburl_arr[0];
							}else{
								$base_path=$path_arr[0];
							}
						if((preg_match('/layerednavigation/',$route))&&($controller=='brand')){		

							$bsuburl = explode($brand_url_key.'/', (string)$base_path);
							if($brand_url_key){
								$bsuburl_arr=explode('/', (string)$bsuburl[1]);
								$brand_url_arr= array_slice($bsuburl_arr,1);
								$brand_url_str=implode('/',$brand_url_arr);
								$attr=$brand_url_str;
							}else{
								if(isset($bsuburl[1])){
									$brand_url_arr= array_slice($bsuburl,1);
									$brand_url_str=implode('/',$brand_url_arr);
									$attr=$brand_url_str;
								}else{
									$attr='';
								}
							}
						}else{			
							 $position = strrpos($base_path, '/');
							 $category = substr($base_path, 0, $position);
							 $category=$this->_helper->checkCatExist($category,$base_path);
							 if($category){																												                     	$category_arr=explode($category.'/', (string)$base_path);
								 $param_str=$category_arr[1];
								 $param_arr=explode($url_suffix, (string)$param_str);
								 $param=$param_arr[0];
								 $attr=$param;
							}				
						}				
					}

					$defaultBaseUrl=$this->_url->getBaseUrl();
					$url=$defaultBaseUrl.$base_path;

					if (($brand_url_key=='')||($brand_url_key!='')){

						$category = $this->_categoryFactory->create()
						->addAttributeToFilter('url_key', $attr)
						->addAttributeToSelect('*')
						->getFirstItem();
						$cat_id = $category->getId();

						if ($cat_id) {					
							if($this->_helper->IsEnableAttributeInclude()){				
								 $url.='/'.$attributeCode.$seperateChar;
							}else{
								$url.='/';
							}
						}else{
							if (!$attr) {
								if($this->_helper->IsEnableAttributeInclude()){				
									$url.='/'.$attributeCode.$seperateChar;
								}else{
									$url.='/';
								}
							}else {				
								if($this->_helper->IsEnableAttributeInclude()){
									$applied_param_val=$this->_request->getParam($item->getFilter()->getRequestVar());
									if (!$applied_param_val) {
										$url.=$seperateChar.$attributeCode.$seperateChar;
									}else{						
										$attr_str=$this->_helper->resetMultipleFilter($applied_param_val,$applied_params,$attributeModel,$item_value,$attributeCode,$isAttributeInclude,$request_var);
										$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
										$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
										$url=str_replace('/'.$attr,'/'.$attr_str,$url);
										$option_alias='';
									}
								}else{
									$url.=$seperateChar;
								}
							}
						}
					}else{					
						$brand = $this->brands->getCollection()
						->addFieldToFilter('seo_url', ['eq'=>$attr]);
						$brandData=$brand->getData();
						if ($brandData) {					
							$url.='/';
						} else {
							if (!is_array($attr)) {
								$category = $this->_categoryFactory->create()
								->addAttributeToFilter('url_key', $attr)
								->addAttributeToSelect('*')
								->getFirstItem();
								$cat_id = $category->getId();

								if ($cat_id) {
									$url.='/';
								} elseif (!$attr) {
									$url.='/';
								} else {						   
									$url.=$seperateChar;
								}
							} else {					  
								$url.=$seperateChar;
							}
						}
					}		
					if ((isset($applied_params[$rat_param])) || (isset($applied_params['price'])) || (isset($applied_params[$stock_param])) || (isset($applied_params['p'])) || (isset($applied_params['cat']))) {
						if ($url_suffix) {
							$queryUrl = explode('?', (string)$currenturl);
							$url.=$option_alias.$url_suffix.'?'.$queryUrl[1];
						} else {
							$queryUrl = explode('?', (string)$currenturl);
							$url.=$option_alias.'?'.$queryUrl[1];
						}
						return $url;
					} else {
						if ($url_suffix) {
							$url.=$option_alias.$url_suffix;
						} else {
							$url.=$option_alias;
						}
						return $url;
					}
					}
				}
			}
		}else{
			return $this->getDefaultFilterItemUrl($item);
		}
	}
		return $this->getDefaultFilterItemUrl($item);
    }

    public function getDefaultFilterItemUrl($item)
    {
         
        $filter = $item->getFilter();
        $requestParameters = $this->_request->getParam($filter->getRequestVar());
        
        if ($requestParameters) {
            $requestParameters =$requestParameters .','.$item->getValue();
        } else {
            $requestParameters =$item->getValue();
        }
      
        $query = [
          $filter->getRequestVar() => $requestParameters,
         // $this->htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
     
    public function getFilterItemRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
		$seperateChar=$this->_helper->getSeperateAttrChar();
		$brandConfig=$this->_helper->getBrandConfigData();
        if (isset($brandConfig['brand_url_key'])) {
            $brand_url_key=$brandConfig['brand_url_key'];
        } else {
            $brand_url_key=null;
        }

		$isAttributeInclude=$this->_helper->IsEnableAttributeInclude();
		 $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		 $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		  if ($is_default_enabled==0) {
            if ($is_enabled) {
        $urltype=$this->_helper->getUrlType();
	 	$path = $this->_request->getRequestString();
        $path = trim($path, '/');
        $currenturl=$this->_urlBuilder->getCurrentUrl();
        $seo_url_key=$this->_helper->getUrlKey();
        $url_suffix=$this->_helper->getUrlSuffix();
        $optionarr=$this->_attr_helper->getAllFilterableOptionsAsHash();
        $filter = $item->getFilter();
        $requestvar=$filter->getRequestVar();
        $item_value=$item->getValue();
        $applied_params=$this->_request->getParams();
        $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
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
                    $attr_option=str_replace("/", "", $subparam[0]);
                } else {
                    $subparam=explode('?', (string)$suburl[1]);
                    $attr_option=str_replace("/", "", $subparam[0]);
                }
                            
                
				if (strpos($attr_option,$seperateChar) !== false) {
						/** update for fix issue remove attribute***/	
					$applied_params=$this->_request->getParams();
					$attributeModel = $item->getFilter()->getAttributeModel();
					$attr_id = $attributeModel->getId();
					$new_attr_str_arr=[];
					foreach ($applied_params as $a_key => $a_value) {		
						if(array_key_exists($a_key,$optionarr))	 {
							if (strpos($a_value,',') !== 	false) {
								$applied_param_arr=explode(',', (string)$a_value);
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
							 } else {
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
						} else {
							$new_attr_str.=$new_val.$seperateChar;
						 }
					}							/** update for fix issue remove attribute***/		

					$new_attr_str = rtrim($new_attr_str,$seperateChar);
					$new_attr_str = ltrim($new_attr_str,$seperateChar);				
					$new_attr_str = preg_replace('/'.$seperateChar.'+/',$seperateChar, $new_attr_str);	

					if(!$new_attr_str)	{
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
               
				$controller=$this->_request->getControllerName();
				$path_arr=explode('?', (string)$path);
				if(isset($path_arr[0]))	{
					if (($url_suffix)&&(preg_match('/'.$url_suffix.'/', $currenturl))) {
						$suburl_arr = explode($url_suffix, (string)$path_arr[0]);
						$base_path=$suburl_arr[0];
					}else{
						$base_path=$path_arr[0];
					}
					$route=$this->_request->getRouteName();
					if((preg_match('/layerednavigation/',$route))&&($controller=='brand'))	{		
						$bsuburl = explode($brand_url_key.'/', (string)$base_path);
						if($brand_url_key)
						{
							$bsuburl_arr=explode('/', (string)$bsuburl[1]);
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
						if($category){	
							$category_arr=explode($category.'/', (string)$base_path);
							$param_str=$category_arr[1];
							$param_arr=explode($url_suffix, (string)$param_str);
							$param=$param_arr[0];
							$attr_option=$param;
						}				
					}				
				}
			
				$defaultBaseUrl=$this->_url->getBaseUrl();
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
								$applied_param_arr=explode(',', (string)$a_value);
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
					 		} else {
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
						} else {
							  $new_attr_str.=$new_val.$seperateChar;
						}
					}
					/** update for fix issue remove attribute***/		

					$new_attr_str = rtrim($new_attr_str,$seperateChar);
					$new_attr_str = ltrim($new_attr_str,$seperateChar);				
					$new_attr_str = preg_replace('/'.$seperateChar.'+/',$seperateChar, $new_attr_str);						  
					$attr_option=htmlentities(urldecode($attr_option), ENT_QUOTES, "UTF-8");	
					$currenturl=htmlentities(urldecode($currenturl), ENT_QUOTES, "UTF-8");

					if ($url_suffix) {
						$attrUrl=str_replace($attr_option, $new_attr_str, $currenturl);
						$new=explode($url_suffix, (string)$attrUrl);
						$new_url = preg_replace('{/$}', '', $new[0]);
						$clearurl=$new_url.$url_suffix;
					} else {
						$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
						$attrUrl=str_replace($attr_option, $new_attr_str, $url);
						$new_url = preg_replace('{/$}', '', $attrUrl);
						$clearurl=$new_url;
					}

                    
                } else {
                    if ($url_suffix) {
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
}
		 $result=$this->getFilterRemoveUrl($item);
        return $result;
		
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
            /*check if price attribute then set remove element*/
            if (($item->getName()=='Price') || ($item->getName()==$rat_label)) {
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
}
