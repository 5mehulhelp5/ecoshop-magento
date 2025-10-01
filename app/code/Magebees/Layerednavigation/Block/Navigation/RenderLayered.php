<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Block\Navigation;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\View\Element\Template;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;

/**
 * Class RenderLayered Render Swatches at Layered Navigation
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RenderLayered extends Template
{
    /**
     * For `Filterable (with results)` setting
     */
    const FILTERABLE_WITH_RESULTS = '1';

    /**
     * @var \Magento\Eav\Model\Attribute
     */
    protected $layereavAttribute;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected $filter;

    /**
     * @var AttributeFactory
     */

    protected $swatchHelper;

    /**
     * @var \Magento\Swatches\Helper\Media
     */
    protected $mediaHelper;

    /**
     * @param Template\Context $context
     * @param Attribute $layereavAttribute
     * @param AttributeFactory $layerAttribute
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param array $data
     */

    protected $layerHelper; 
    protected $_attr_helper; 
    protected $helper_url; 
    protected $_url; 
    protected $_categoryFactory; 
    protected $brands; 
    protected $_request; 
    protected $_objectManager; 
    protected $_scopeConfig; 
    protected $layerattributeFactory; 
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $layereavAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Magebees\Layerednavigation\Helper\Data $layerHelper,
        \Magebees\Layerednavigation\Helper\Url $helper_url,
		 \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magebees\Layerednavigation\Model\Brands $brands,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory,
        array $data = []
    ) {
        $this->layereavAttribute = $layereavAttribute;
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;
        $this->layerHelper = $layerHelper;
        $this->helper_url = $helper_url;
		$this->_attr_helper = $attr_helper;
        $this->_url = $context->getUrlBuilder();
        $this->_categoryFactory = $categoryFactory;
        $this->brands = $brands;
        $this->_request =$context->getRequest();
        $this->_objectManager=$objectManager;
        $this->_scopeConfig=$context->getScopeConfig();
        $this->layerattributeFactory = $layerattributeFactory;

        parent::__construct($context, $data);
        $default_config=$this->_scopeConfig->getValue('layerednavigation/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            
		$is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		 $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		 if ($is_default_enabled==0) {
            if ($is_enabled) {
				if ($default_config['default_swatch_config']=='0') {
						$this->setTemplate('Magebees_Layerednavigation::renderer.phtml');
				} else {
					$this->setTemplate('Magebees_Layerednavigation::swatch_renderer.phtml');
				}
			}else{
			   $this->setTemplate('Magento_Swatches::product/layered/renderer.phtml');
		  	}
		}else{
			 $this->setTemplate('Magento_Swatches::product/layered/renderer.phtml');
		}
    }


    public function isDefaultMultiselectSwatch()
    {
         return $multiselect_swatch=$this->_scopeConfig->getValue('layerednavigation/setting/multiselect_swatch', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    /**
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSwatchFilter(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        $this->filter = $filter;
        $this->layereavAttribute = $filter->getAttributeModel();

        return $this;
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

    /**
     * @return array
     */
  	  public function getSwatchData()
    {
		 $is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		 $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		 if ($is_default_enabled==0) {
            if ($is_enabled) {
				if (false === $this->layereavAttribute instanceof Attribute) {
					throw new \RuntimeException('Magento_Swatches: RenderLayered: Attribute has not been set.');
				}        
				$sort_order ='';
				$items=$this->filter->getItems();
				$attr_id = $this->layereavAttribute->getId();
				$layer_model=$this->layerattributeFactory->create();
				$attributeCollection = $layer_model->getCollection()
                                ->addFieldToFilter('attribute_id', $attr_id);
        
				foreach ($attributeCollection as $attr) {
					$sort_order =  $attr->getData('sort_option');
				}
				if ($sort_order) {
					if ($sort_order == 1) {
						usort($items, [$this, "_sortByName"]);
					} elseif ($sort_order == 2) {
						usort($items, [$this, "_sortByCounts"]);
					}
				}
        
				$attributeOptionsArr = [];
				foreach ($items as $option) {
					if ($currentFilterOption = $this->getFilterOption($items, $option)) {
						$attributeOptionsArr[$option->getValue()] = $currentFilterOption;
					} elseif ($this->isShowEmptyResults()) {
						$attributeOptionsArr[$option->getValue()] = $this->getUnusedOption($option);
					}
				}

				$layerattributeOptionIds = array_keys($attributeOptionsArr);
				$swatches = $this->swatchHelper->getSwatchesByOptionsId($layerattributeOptionIds);

				$data = [
					'attribute_id' => $this->layereavAttribute->getId(),
					'attribute_code' => $this->layereavAttribute->getAttributeCode(),
					'attribute_label' => $this->layereavAttribute->getStoreLabel(),
					'options' => $attributeOptionsArr,
					'swatches' => $swatches,
				];

				return $data;
			}else {
				return $this->defaultSwatchData();
			}
	  }
	 return $this->defaultSwatchData();
		
    }
	
	public function defaultSwatchData()
	{
		if (false === $this->layereavAttribute instanceof Attribute) {
            throw new \RuntimeException('Magento_Swatches: RenderLayered: Attribute has not been set.');
        }

        $attributeOptions = [];
        foreach ($this->layereavAttribute->getOptions() as $option) {
            if ($currentOption = $this->getFilterOption($this->filter->getItems(), $option)) {
                $attributeOptions[$option->getValue()] = $currentOption;
            } elseif ($this->isShowEmptyResults()) {
                $attributeOptions[$option->getValue()] = $this->getUnusedOption($option);
            }
        }

        $layerattributeOptionIds = array_keys($attributeOptions);
        $swatches = $this->swatchHelper->getSwatchesByOptionsId($layerattributeOptionIds);

        $data = [
            'attribute_id' => $this->layereavAttribute->getId(),
            'attribute_code' => $this->layereavAttribute->getAttributeCode(),
            'attribute_label' => $this->layereavAttribute->getStoreLabel(),
            'options' => $attributeOptions,
            'swatches' => $swatches,
        ];

        return $data;
	}
    public function isSwatchActive($optionid, $attribute_code)
    {
        return $this->layerHelper->isSwatchActive($optionid, $attribute_code);
    }
    public function getSwatchDisplayType($attribute_id)
    {
        $swatch_display_type=$this->layerHelper->getAttrDisplayMode($attribute_id);
        return $swatch_display_type;
    }

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     */
  	public function buildUrl($attributeCode, $attribute_id, $optionId)
    {
		$path = $this->_request->getRequestString();
        $path = trim($path, '/');
		$seperateChar=$this->layerHelper->getSeperateAttrChar();
		$isAttributeInclude=$this->layerHelper->IsEnableAttributeInclude();
        $default_config=$this->_scopeConfig->getValue('layerednavigation/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($default_config['default_swatch_config']=='0') {
            $apply_multiselect_swatch=$this->layerHelper->isApplyMultiSelectWithId($attribute_id);
        } else {
            $apply_multiselect_swatch=$this->isDefaultMultiselectSwatch();
        }
        $urltype=$this->layerHelper->getUrlType();
        $currenturl=$this->_url->getCurrentUrl();
        $url_suffix=$this->layerHelper->getUrlSuffix();
        $applied_params=$this->_request->getParams();
        $rat_param=$this->_scopeConfig->getValue('layerednavigation/rating_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $stock_param=$this->_scopeConfig->getValue('layerednavigation/stock_filter/url_param', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $url_key=$this->layerHelper->getUrlKey();
        $brandConfig=$this->layerHelper->getBrandConfigData();	
        $finderparams = explode('/', (string)$path);
        if (isset($brandConfig['brand_url_key'])) {
            $brand_url_key=$brandConfig['brand_url_key'];
        } else {
            $brand_url_key=null;
        }
        if (preg_match('/catalogsearch/', $currenturl)) {
            return $this->getDefaultSwatchUrl($attributeCode, $attribute_id, $optionId);
        } else {
            if ($urltype==0) {
                return $this->getDefaultSwatchUrl($attributeCode, $attribute_id, $optionId);
            } elseif ($urltype==1) {
            // long with url key mode                
                $option_alias=$this->helper_url->getUrlAlias($optionId, $attribute_id);
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
					$suburl = explode('/'.$url_key.'/',(string) $currenturl);
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
                } else {
                    if (!$apply_multiselect_swatch) {
                        $applied_param_val=$this->_request->getParam($attributeCode);
                        if (!$applied_param_val) {
                            $url.=$seperateChar;
                        }
                    }
                }
                
                if ((isset($applied_params[$rat_param])) || (isset($applied_params['price'])) || (isset($applied_params[$stock_param])) || (isset($applied_params['p'])) || (isset($applied_params['cat']))) {
					$attributeModel = $this->filter->getAttributeModel();
					$attr_id = $attributeModel->getId();
					$attributeCode=$this->layerHelper->getAttributeIncludeData($attributeModel);		
                    $queryUrl = explode('?', (string)$currenturl);
                    if (!$apply_multiselect_swatch) {
                        $applied_param_val=$this->_request->getParam($attributeCode);
                        if ($applied_param_val) {
							$old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);
							$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
								/** update for fix issue remove attribute***/	
							$applied_params=$this->_request->getParams();
						 	$attributeModel = $this->filter->getAttributeModel();
							$request_var=$this->filter->getRequestVar();
							$attr_str=$this->layerHelper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$optionId,$isAttributeInclude,$request_var);	
						
							$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
							if(!$attr_str){		
								$url=str_replace('/'.$url_key.'/'.$attr,'',$url);
							}else{
								$url=str_replace('/'.$url_key.'/'.$attr,'/'.$url_key.'/'.$attr_str,$url);
							}
				
                            $url.=$url_suffix.'?'.$queryUrl[1];    
                            
                        } else {
               
							if($this->layerHelper->IsEnableAttributeInclude()){
	
								$url.=$attributeCode.$seperateChar.$option_alias.$url_suffix.'?'.$queryUrl[1];
							}else{	
								$url.=$option_alias.$url_suffix.'?'.$queryUrl[1];
							}
                        }
                    }
					return $url;
                } else {
					$attributeModel = $this->filter->getAttributeModel();
					$attr_id = $attributeModel->getId();
                    if (!$apply_multiselect_swatch) {
                        $applied_param_val=$this->_request->getParam($attributeCode);
                        if ($applied_param_val) {
						    $old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);
							$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
							/** update for fix issue remove attribute***/	
							$applied_params=$this->_request->getParams();
					 		$attributeModel = $this->filter->getAttributeModel();
							$request_var=$this->filter->getRequestVar();
							$attr_str=$this->layerHelper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$optionId,$isAttributeInclude,$request_var);	
					
							$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
							if(!$attr_str)	{	
								$url=str_replace('/'.$url_key.'/'.$attr,'',$url);
							}else{
								$url=str_replace('/'.$url_key.'/'.$attr,'/'.$url_key.'/'.$attr_str,$url);
							}
				            $url.=$url_suffix;
							
                        } else {
							$attributeCode=$this->layerHelper->getAttributeIncludeData($attributeModel);
							if($this->layerHelper->IsEnableAttributeInclude()){    
								$url.=$attributeCode.$seperateChar.$option_alias.$url_suffix;
							}else{
								$url.=$option_alias.$url_suffix;
							}
                        }
                    }
                                
                     return $url;
                }
            } else {
                // short without url key mode
				$attributeModel = $this->filter->getAttributeModel();
				$attr_id = $attributeModel->getId();
           		 $attributeCode=$this->layerHelper->getAttributeIncludeData($attributeModel);
                $option_alias=$this->helper_url->getUrlAlias($optionId, $attribute_id);
				
				$path_arr=explode('?',(string)$path);
				if(isset($path_arr[0]))
				{
					if (($url_suffix)&&(preg_match('/'.$url_suffix.'/', $currenturl))) {
						$suburl_arr = explode($url_suffix, (string)$path_arr[0]);
						$base_path=$suburl_arr[0];
					}
					else
					{
						$base_path=$path_arr[0];
					}
					$controller=$this->_request->getControllerName();
					$route=$this->_request->getRouteName();
					if((preg_match('/layerednavigation/',$route))&&($controller=='brand'))	{		

						$bsuburl = explode($brand_url_key.'/',(string)$base_path);
						if($brand_url_key){
							$bsuburl_arr=explode('/',(string)$bsuburl[1]);
							$brand_url_arr= array_slice($bsuburl_arr,1);
							$brand_url_str=implode('/',$brand_url_arr);
							$attr=$brand_url_str;
						}else{
							if(isset($bsuburl[1]))	{
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
                    if(!$category){
                         $category = $base_path;
                    }
					$category=$this->layerHelper->checkCatExist($category,$base_path);
					if($category){	
						$category_arr=explode($category.'/',(string)$base_path);
                        if (array_key_exists('1', $category_arr)) {
                            $param_str=$category_arr[1];
                        }else{
						  $param_str=$category_arr[0];
						}
                        $param_arr=explode($url_suffix,(string)$param_str);
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
					if($this->layerHelper->IsEnableAttributeInclude()){		
						 $url.='/'.$attributeCode.$seperateChar;
					}else{
						$url.='/';
					}
				} else {
				if (!$attr) {
				if($this->layerHelper->IsEnableAttributeInclude())
				{					
				$url.='/'.$attributeCode.$seperateChar;
				}
				else
				{						
				$url.='/';
				}
				} elseif (!$apply_multiselect_swatch) {
				$applied_param_val=$this->_request->getParam($attributeCode);
				if (!$applied_param_val) {
				if($this->layerHelper->IsEnableAttributeInclude())
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
				} else {
				$brand = $this->brands->getCollection()
				->addFieldToFilter('seo_url', ['eq'=>$attr.$url_suffix]);
				$brandData=$brand->getData();

				if ($brandData) {
				if($this->layerHelper->IsEnableAttributeInclude())
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
						if($this->layerHelper->IsEnableAttributeInclude())
				{
				$url.='/'.$attributeCode.$seperateChar;
				}
				else
				{
				$url.='/';
				}
				} else {
					if (!$attr) {
							if($this->layerHelper->IsEnableAttributeInclude())
				{
				$url.='/'.$attributeCode.$seperateChar;
				}
						else
						{
						$url.='/';
						}
					} elseif (!$apply_multiselect_swatch) {
		$applied_param_val=$this->_request->getParam($attributeCode);
		if (!$applied_param_val) {
				if($this->layerHelper->IsEnableAttributeInclude())
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
                            } else {
                                $url.=$seperateChar;
                            }
                        }
                    }
             
                if ((isset($applied_params[$rat_param])) || (isset($applied_params['price'])) || (isset($applied_params[$stock_param])) || (isset($applied_params['p'])) || (isset($applied_params['cat']))) {
					$attributeModel = $this->filter->getAttributeModel();
					$attr_id = $attributeModel->getId();			
					$attributeCode=$this->layerHelper->getAttributeIncludeData($attributeModel);	
                    $queryUrl = explode('?', (string)$currenturl);
                    if (!$apply_multiselect_swatch) {
                        $applied_param_val=$this->_request->getParam($attributeCode);
                        if ($applied_param_val) {
                            $old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);
	$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
		/** update for fix issue remove attribute***/	
	$applied_params=$this->_request->getParams();
  $attributeModel = $this->filter->getAttributeModel();
	$request_var=$this->filter->getRequestVar();
	$attr_str=$this->layerHelper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$optionId,$isAttributeInclude,$request_var);	
	
		
	$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
				//$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
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
                } else {
                    if (!$apply_multiselect_swatch) {
                        $applied_param_val=$this->_request->getParam($attributeCode);
                        if ($applied_param_val) {
                           $old_option_alias=$this->helper_url->getUrlAlias($applied_param_val, $attr_id);
							
		$attr=htmlentities(urldecode($attr), ENT_QUOTES, "UTF-8");
		/** update for fix issue remove attribute***/	
	$applied_params=$this->_request->getParams();
  $attributeModel = $this->filter->getAttributeModel();
	$request_var=$this->filter->getRequestVar();
	$attr_str=$this->layerHelper->itemResetAppliedFilterUrl($applied_params,$attributeModel,$optionId,$isAttributeInclude,$request_var);	

				$url=htmlentities(urldecode($url), ENT_QUOTES, "UTF-8");
					
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


    public function getDefaultSwatchUrl($attributeCode, $attribute_id, $optionId)
    {
        $requestParameters = $this->_request->getParam($attributeCode);
         $default_config=$this->_scopeConfig->getValue('layerednavigation/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            
        if ($default_config['default_swatch_config']=='0') {
            $apply_multiselect_swatch=$this->layerHelper->isApplyMultiSelectWithId($attribute_id);
        } else {
            $apply_multiselect_swatch=$this->isDefaultMultiselectSwatch();
        }
                
        if ($apply_multiselect_swatch) {
            if ($requestParameters) {
                $requestParameters = explode(',', (string)$requestParameters);

                if (!in_array($optionId, $requestParameters)) {
                    $requestParameters = implode(',', $requestParameters);
                    $requestParameters =$requestParameters .','.$optionId;
                } else {
                    foreach ($requestParameters as $key => $value) {
                        if ($value ==$optionId) {
                            unset($requestParameters[$key]);
                        }
                    }
                    if (!empty($requestParameters)) {
                        $requestParameters=implode(",", $requestParameters);
                    }
                }
            } else {
                $requestParameters =$optionId ;
            }
      
            $query = [
            $attributeCode=> $requestParameters
            ];
            return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        } else {
            if ($requestParameters) {
                $requestParameters = [$requestParameters];
                foreach ($requestParameters as $key => $value) {
                    if ($value ==$optionId) {
                        unset($requestParameters[$key]);
                    } else {
                        $requestParameters =$optionId ;
                    }
                }
            } else {
                $requestParameters =$optionId ;
            }
            $query = [
            $attributeCode=> $requestParameters
            ];
            return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
        }
    }
    /**
     * @param Option $swatchOption
     * @return array
     */
    protected function getUnusedOption(Option $swatchOption)
    {
        return [
            'label' => $swatchOption->getLabel(),
            'link' => 'javascript:void();',
            'custom_style' => 'disabled'
        ];
    }

    /**
     * @param FilterItem[] $filterItems
     * @param Option $swatchOption
     * @return array
     */
    protected function getFilterOption(array $filterItems, $swatchOption)
    {
        $resultOption = false;
        $filterItem = $this->getFilterItemById($filterItems, $swatchOption->getValue());
        if ($filterItem && $this->isOptionVisible($filterItem)) {
            $resultOption = $this->getOptionViewData($filterItem, $swatchOption);
        }

        return $resultOption;
    }

    /**
     * @param FilterItem $filterItem
     * @param Option $swatchOption
     * @return array
     */
    protected function getOptionViewData(FilterItem $filterItem, $swatchOption)
    {
		$is_default_enabled=$this->_scopeConfig->getValue('advanced/modules_disable_output/Magebees_Layerednavigation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		 $is_enabled=$this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		  if ($is_default_enabled==0) {
            if ($is_enabled) {
        $swatch_customStyle = '';
        $linkToOption = $this->buildUrl($this->layereavAttribute->getAttributeCode(), $this->layereavAttribute->getAttributeId(), $filterItem->getValue());
        if ($this->isOptionDisabled($filterItem)) {
            $swatch_customStyle = 'disabled';
            $linkToOption = 'javascript:void();';
        }

        return [
            'label' => $swatchOption->getLabel(),
            'count' => $filterItem->getCount(),
            'link' => $linkToOption,
            'filterItem' => $filterItem,
            'option_id'=>$filterItem->getValue(),
            'custom_style' => $swatch_customStyle
        ];
			}
			  else
			  {
				  return $this->getDefaultOptionViewData($filterItem, $swatchOption);
			  }
		  }
		 return $this->getDefaultOptionViewData($filterItem, $swatchOption);
		
    }
	public function getDefaultOptionViewData(FilterItem $filterItem, $swatchOption)
	{
		 $customStyle = '';
        $linkToOption = $this->defaultBuildUrl($this->layereavAttribute->getAttributeCode(), $filterItem->getValue());
        if ($this->isOptionDisabled($filterItem)) {
            $customStyle = 'disabled';
            $linkToOption = 'javascript:void();';
        }

        return [
            'label' => $swatchOption->getLabel(),
            'link' => $linkToOption,
			 'filterItem' => $filterItem,
            'custom_style' => $customStyle
        ];
	}

	 public function defaultBuildUrl($attributeCode, $optionId)
    {
        $query = [$attributeCode => $optionId];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
    /**
     * @param FilterItem $filterItem
     * @return bool
     */
    protected function isOptionVisible(FilterItem $filterItems)
    {
        return $this->isOptionDisabled($filterItems) && $this->isShowEmptyResults() ? false : true;
    }

    /**
     * @return bool
     */
    protected function isShowEmptyResults()
    {
        return $this->layereavAttribute->getIsFilterable() != self::FILTERABLE_WITH_RESULTS;
    }

    /**
     * @param FilterItem $filterItem
     * @return bool
     */
    protected function isOptionDisabled(FilterItem $filterItems)
    {
        return !$filterItems->getCount();
    }

    /**
     * @param FilterItem[] $filterItems
     * @param integer $id
     * @return bool|FilterItem
     */
    protected function getFilterItemById(array $filterItems, $id)
    {
        foreach ($filterItems as $item) {
            if ($item->getValue() == $id) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param string $type
     * @param string $filename
     * @return string
     */
    public function getSwatchPath($type, $filename)
    {
        $imagePath = $this->mediaHelper->getSwatchAttributeImage($type, $filename);
        return $imagePath;
    }
}
