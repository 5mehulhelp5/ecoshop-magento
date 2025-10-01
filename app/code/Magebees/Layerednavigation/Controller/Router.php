<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Layerednavigation\Controller;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Controller\ResultFactory;

class Router implements \Magento\Framework\App\RouterInterface
{  
   protected $_helper;
    protected $_attr_helper;
    protected $_objectManager;
    protected $brands;
    protected $_registry;
    protected $messageManager;
    protected $resultFactory;
    protected $_redirect;
    protected $productAttributeCollectionFactory;
    protected $_scopeConfig;
    protected $actionFactory;
    protected $_categoryFactory;
    protected $_response;
    protected $_param;
    protected $_remainparam;
    protected $_mainparam;
    protected $layerattributeFactory;
    protected $_resetflag=0;
    protected $_i;	
    protected $_queryarr=[];
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magebees\Layerednavigation\Helper\Data $helper,
        \Magebees\Layerednavigation\Helper\Attributes $attr_helper,
        \Magebees\Layerednavigation\Model\Brands $brands,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
       \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
         ResultFactory $resultFactory,        \Magebees\Layerednavigation\Model\LayerattributeFactory $layerattributeFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_helper = $helper;
        $this->_attr_helper = $attr_helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_categoryFactory = $categoryFactory;
        $this->_objectManager=$objectManager;
        $this->brands = $brands;
         $this->_registry = $registry;
        $this->messageManager = $messageManager;	
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
         $this->_redirect = $redirect;
          $this->resultFactory = $resultFactory;

        $this->layerattributeFactory = $layerattributeFactory;
    }

    /**
     * Match corresponding URL Rewrite and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
		$seperateChar=$this->_helper->getSeperateAttrChar();
		$replaceChar=$this->_helper->getReplaceSpecialChar();
        $brandConfig=$this->_helper->getBrandConfigData();
        $brand_url_key=$brandConfig['brand_url_key'];
        $seo_url_key=$this->_helper->getUrlKey();
        $pageId = trim($request->getPathInfo(), '/');
        $is_enable = $this->_scopeConfig->getValue('layerednavigation/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $identifier = trim($request->getPathInfo(), '/');
        $url_suffix=$this->_helper->getUrlSuffix();
        $urltype=$this->_helper->getUrlType();
		$optionarr=$this->_attr_helper->getAllFilterableOptionsAsHash();
		
        if (strpos($pageId, '/'.$seo_url_key.'/') === false) {     
			// without url key mode

			$page =  str_replace($url_suffix, '', $identifier);		
			$position = strrpos($page, '/');              
			if ((!preg_match('/finder/', $pageId)) ) {

				if (strpos($page, 'catalogsearch/result') !== false)
				{
					$category = '';
				} else {
					//brand page

					$burl_arr=array();
					$burl=explode('/', (string)$identifier);
					$burl_coll = $this->brands->getCollection();
					foreach($burl_coll as $burls)
					{		
						$new_seo_alias=str_replace($url_suffix, '',$burls->getSeoUrl());

						if (strpos($identifier,$new_seo_alias) !== false) {
							$burl_arr[]=explode($new_seo_alias,(string)$identifier);
						}
					}
					if(isset($burl_arr[0]))
					{
						if(($burl_arr[0][0]=='')&&(strpos($burl_arr[0][1],'/') === false)){

							$burl=ltrim($request->getPathInfo(),'/');
							$url = $this->brands->getCollection()
								->addFieldToFilter('seo_url', ['eq'=>$burl]);
							$brand=$url->getData();
							if (isset($brand['0'])) {
							$request->setModuleName('layerednavigation');
							$request->setControllerName('brand');
							$request->setActionName('index');
						//	$request->setParam('brand_url', $burl[0]);
							$request->setParam('brand_url', $burl);
							$request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
							return $this->actionFactory->create(
								'Magento\Framework\App\Action\Forward',
								['request' => $request]
							);
							} else {
									return $this->errorRedirect($request);
							}
						}                	

						if (isset($burl_arr[0][1])) {
							$burl_str=ltrim($request->getPathInfo(),'/');
							$burl_str_arr=explode('/',(string)$burl_str);
							if($brand_url_key){
								$brand_url_str=$burl_str_arr[1];
							}else{
								$brand_url_str=$burl_str_arr[0];
							}
							if ((strpos($brand_url_str, $url_suffix) === false)&&($url_suffix)){
								$brand_url_str.=$url_suffix;
							}
							$brand_filter=$burl_arr[0][1];
							$brand_filter=ltrim($brand_filter,'/');
						    $param=  str_replace($url_suffix, '', $brand_filter);
						    $query=[];
							if($this->_helper->IsEnableAttributeInclude()){
								$param=$this->createAttributeParam($param);
							}
							if (strpos($param,$seperateChar) !== false) {         
								$query=$this->queryForMultipleOptions($param);
							} else {
								$query=$this->queryForSingleOption($param);
							}
						}
						if (isset($query)) {
							$request->setParams($query);
							$request->setModuleName('layerednavigation');
							$request->setControllerName('brand');
							$request->setActionName('index');
							//$request->setParam('brand_url', $burl[0]);
							$request->setParam('brand_url',$brand_url_str);
							$request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
							return $this->actionFactory->create(
								'Magento\Framework\App\Action\Forward',
								['request' => $request]
							);
						}	

					}else{	// category page param

						/*if($this->_registry->registry('current_category')){
							$category = substr($page, 0, $position);
							if($category == ''){
								return;	
							}
							$category=$this->_helper->checkCatExist($category,$page);
							if($category){																			$category_arr=explode($category.'/',(string)$page);
								$param=$category_arr[1];
							}
						}else{
							return; 
						}*/
						$category = substr($page, 0, $position);
						if($category == ''){
							return;	
						}
						$category=$this->_helper->checkCatExist($category,$page);
						if($category){																			$category_arr=explode($category.'/',$page);
							$param=$category_arr[1];
						}
						if($param == "") {
							return; 
						}


						
					}
				}
			} else {
				$category = '';
				$param = $page;
			}                
			$param = trim($param, '/');

				
				$category = trim($category, '/');
				$category  = substr($category, strrpos($category, '/'));
				$category = trim($category, '/');

				$category_coll = $this->_categoryFactory->create()
					->addAttributeToFilter('url_key', $category)
					->addAttributeToSelect('*')
					->getFirstItem();
					$cat_id = $category_coll->getId();

				if($this->_helper->IsEnableAttributeInclude()){
					$param=$this->createAttributeParam($param);
				}
				$query=[];                     				
				if (strpos($param,$seperateChar) !== false) {									 		$query=$this->queryForMultipleOptions($param);
				} else {
					$query=$this->queryForSingleOption($param);
				}

				$request->setParams($query);
				if ((!empty($query))&&($cat_id)) {
					return $this->forawardToCategory($request, $category, $pageId);
				} else {
					return $this->errorRedirect($request);
				}
		

		    if($this->_helper->IsEnableAttributeInclude()){
				$param=$this->createAttributeParam($param);
			}
			$query=[];
			if (strpos($param,$seperateChar) !== false) {
				$query=$this->queryForMultipleOptions($param);
			} else {
				$query=$this->queryForSingleOption($param);
			}
			if (!empty($query)) {
				if ($category) {
					$category = trim($category, '/');
					$category  = substr($category, strrpos($category, '/'));
					$category = trim($category, '/');
					$request->setParams($query);
					return $this->forawardToCategory($request, $category, $pageId);
				}
			} else {
				return $this->errorRedirect($request);
			}
			
        }else {
            $p = strpos($pageId, $seo_url_key);
            $cat = substr($pageId, 0, $p);
            $cat = trim($cat, '/');
            if (strpos($pageId, '/') !== false) {
                $cat_array = explode('/', (string)$cat);
                $url_key = end($cat_array);
            } else {
                $url_key = $cat;
            }
            $suburl = explode('/'.$seo_url_key.'/', (string)$pageId);
            if (!isset($suburl[1])) {
               //return;
				if ($brand_url_key) {
					$brand_param_arr=explode($brand_url_key, (string)$suburl[0]);
					$brand_param=$brand_param_arr[1];
				} else {
					$brand_param=$suburl[0];
				}
				$url = $this->brands->getCollection()
				->addFieldToFilter('seo_url', ['eq'=>$brand_param]);
				$brand=$url->getData();
				if (isset($brand['0'])) {
					$request->setModuleName('layerednavigation');
					$request->setControllerName('brand');
					$request->setActionName('index');
					$request->setParam('brand_url', $brand_param);
					$request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
					return $this->actionFactory->create(
						'Magento\Framework\App\Action\Forward',
						['request' => $request]
					);
				} else {
						return $this->errorRedirect($request);
				}
            } else {
				if ($url_suffix) {
					$subparam=explode($url_suffix, (string)$suburl[1]);

					//$attr_option=str_replace("/", "", $subparam[0]);
					$attr_option=$subparam[0];
				} else {
					$attr_option=$suburl[1];
				}
            }         
			if($this->_helper->IsEnableAttributeInclude()){							
				$attr_option=$this->createAttributeParam($attr_option);
			}                     
		  	if (strpos($attr_option,$seperateChar) !== false){              
               $query=$this->queryForMultipleOptions($attr_option);
            } else{
                $query=$this->queryForSingleOption($attr_option);
            }			
            $request->setParams($query);            
            if (!empty($query)) {
            
            } else {
				return $this->errorRedirect($request);
	        }
           
            
            $burl=explode('/', (string)$identifier);
            if ($brand_url_key) {
                if ($burl[0]==$brand_url_key) {
                    return $this->forawardToBrandPage($request, $pageId);
                } else {
                    return $this->forawardToCategory($request, $url_key, $pageId);
                }
            } else {
                $category = $this->_categoryFactory->create()
                    ->addAttributeToFilter('url_key', $burl[0])
                    ->addAttributeToSelect('*')
                    ->getFirstItem();
                    $cat_id = $category->getId();
                if ($cat_id) {
                    if (!empty($query)) {
                		return $this->forawardToCategory($request, $url_key, $pageId);
                    } else {
                       	return $this->errorRedirect($request);
                    }
                } else {
                    $url = $this->brands->getCollection()
                                    ->addFieldToFilter('seo_url', ['eq'=>$burl[0].$url_suffix]);
                    $brand=$url->getData();
                    if (isset($brand['0'])) {
                        $burl=explode('/', (string)$identifier);
                        $request->setModuleName('layerednavigation');
                        $request->setControllerName('brand');
                        $request->setActionName('index');
                        $request->setParam('brand_url', $burl[0].$url_suffix);
                        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
                        return $this->actionFactory->create(
                            'Magento\Framework\App\Action\Forward',
                            ['request' => $request]
                        );
                    } else {
                        	return $this->errorRedirect($request);
                    }
                }
            }
        }
    }
	 public function _sortByPosition($a, $b)
    {		$key1=key($a);
    		$key2=key($b);
		  return strlen($a[$key1]) - strlen($b[$key2]);
	}
    public function isMultiSelectAttribute($attr_code)
    {
        $attributes = $this->productAttributeCollectionFactory->create();
        $attributes->addFieldToFilter('attribute_code', $attr_code);
        $attribute_data=$attributes->getData();
        $attribute_id=$attribute_data['0']['attribute_id'];
        $layer_model=$this->layerattributeFactory->create();
                $collection = $layer_model->getCollection()
            ->addFieldToFilter('attribute_id', $attribute_id);
        $custom_layer_data=$collection->getData();
        $allow_multiselect=$custom_layer_data[0]['display_mode'];
            
        if (($allow_multiselect==4) || ($allow_multiselect==5)) {
            return true;
        }
    }
    public function queryForSingleOption($attr_option)
    {
       
        $query=[];            
        $optionarr=$this->_attr_helper->getAllFilterableOptionsAsHashEncodeChar();    
		
        $decode_attr_option=urldecode($attr_option);
		foreach ($optionarr as $key => $value) {
           
			if (isset($value[$decode_attr_option])) {
               
                $attr_code=$key;
                $opt_val=$value[$decode_attr_option];
                $query[$attr_code]=$opt_val;
            }
        }
		
        return $query;
    }
    
    public function queryForMultipleOptions($param)
    {
		
		 $param=urldecode($param);
		$param=html_entity_decode($param);			
		$seperateChar=$this->_helper->getSeperateAttrChar();
		$replaceChar=$this->_helper->getReplaceSpecialChar();		
		$optionarr1=$this->_attr_helper->getAllFilterableOptionsAsHashChar();
		$str_len=strlen($param);
		$opt_word_len_arr=array_keys($optionarr1);	
		usort($opt_word_len_arr, [$this, "_sortByLength"]);	 		
		$this->_mainparam=$param;	
		$this->_remainparam=$param;	
		$this->_i=0;

	$this->checkStrLenExist($str_len,$optionarr1,$opt_word_len_arr,$param,$param);	
		
		return $this->_queryarr;
		
    }
    public function forawardToCategory($request, $category, $pageId)
    {
        $category = $this->_categoryFactory->create()
        ->addAttributeToFilter('url_key', $category)
        ->addAttributeToSelect('*')
        ->getFirstItem();
        $cat_id = $category->getId();
    
        $request->setModuleName('catalog')->setControllerName('category')->setActionName('view')->setParam('id', $cat_id);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('catalog')->setControllerName('category')->setActionName('view')->setParam('id', $cat_id));
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    
    public function forawardToBrandPage($request, $pageId)
    {
        
        $brandConfig=$this->_helper->getBrandConfigData();
        $brand_url_key=$brandConfig['brand_url_key'];
        $identifier = trim($request->getPathInfo(), '/');
        $url_suffix=$this->_helper->getUrlSuffix();
        $seo_url_key=$this->_helper->getUrlKey();
        $burl=explode('/', (string)$identifier);
                    
        if ($burl[0]==$brand_url_key && isset($burl[1])) {
            $request->setModuleName('layerednavigation');
            $request->setControllerName('brand');
            
            if ($burl[1]=='search') {
                $request->setActionName('search');
            } else {
                $request->setActionName('index');
            }      
        
            if (strpos($pageId, $seo_url_key) !== false) {
                $request->setParam('brand_url', $burl[1].$url_suffix);
            } elseif (isset($burl[2])) {
                if ($burl[2]!=$seo_url_key) {
                    $request->setParam('brand_url', $burl[1].$url_suffix);
                }
            } else {
                $request->setParam('brand_url', $burl[1]);
            }
            $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        } elseif (!$brand_url_key) {
            $request->setModuleName('layerednavigation');
             $request->setControllerName('brand');
             $request->setActionName('index');
             $request->setParam('brand_url', $burl[0].$url_suffix);
             $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        }
        $brand_param_url=$request->getParam('brand_url');
        $url = $this->brands->getCollection()
                                    ->addFieldToFilter('seo_url', ['eq'=>$brand_param_url]);
                            $brand=$url->getData();
        if (isset($brand['0'])) {
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        } else {
            return;
        }
    }
	public function checkMultikey($key,$arr,$str_len,$arr_key)
	{
        	
		$seperateChar=$this->_helper->getSeperateAttrChar();
			
		 if (isset($arr[$key])) {			
			 $value=$arr[$key];			
			 if(isset($this->_queryarr[$arr_key])) {
				 $old=$this->_queryarr[$arr_key];				
				 unset($this->_queryarr[$arr_key]);
				 $this->_queryarr[$arr_key]=$value.','.$old;
			 }
			 else
			 {
			  $this->_queryarr[$arr_key]=$value;
			 }
			
			 $this->_remainparam =substr($this->_remainparam,strlen($key));
			 $this->_remainparam=ltrim($this->_remainparam,$seperateChar);
			 $this->_remainparam=rtrim($this->_remainparam,$seperateChar);
			 $this->_param =$this->_remainparam;
			 $this->_mainparam =$this->_remainparam;
			 $this->_resetflag=1;			
			 return true;
      
		 	}

		// check arrays contained in this array
		foreach ($arr as $arr_key => $element) {
			if (is_array($element)) {			
				if ($this->checkMultikey($key,$element,$str_len,$arr_key)) {
					return true;
				}
			}

		}

    	return false;
	}
	public function checkStrLenExist($str_len,$optionarr1,$opt_word_len_arr,$param,$remain)
	{

		$seperateChar=$this->_helper->getSeperateAttrChar();
		$this->_i++;		
		if(isset($optionarr1[$str_len]))
		{	
			
			$key1 = array_search($str_len, $opt_word_len_arr);		
            /**add for fix issue on 28 march and comment below line*/
          array_shift($opt_word_len_arr);
           $slice_arr=$opt_word_len_arr;
            /*add for fix issue on 28 march*/           
           // $slice_arr=array_slice($opt_word_len_arr,$key1);
           
			$split_pos=current($slice_arr);			
		}
		else
		{
			
				$slice_arr = array_filter(
				$opt_word_len_arr,
				function ($opt_word_len_arr) use($str_len) {
					return ($opt_word_len_arr <= $str_len);
				}
			);		
			
			reset($slice_arr);
			$str_len=current($slice_arr);			
			$split_pos=$str_len;			
		}


			
		/*if(($str_len==0)&&(!isset($optionarr1[$str_len])))
		{
			return;
		}*/
			$this->checkMultikey($param,$optionarr1[$str_len],$str_len,$arr_key=null);	
			if($this->_remainparam)
			{
						
			if(empty($this->_queryarr)||(!$this->_resetflag))
			{	
				if(is_int($split_pos))
				{

					
				$this->checkStrLenExist($str_len,$optionarr1,$opt_word_len_arr,$this->_remainparam,$this->_remainparam);	

				$this->_param= substr($param,0,$split_pos);	      
				$this->checkStrLenExist($split_pos,$optionarr1,$slice_arr,$this->_param,$this->_remainparam);
				}
					
			}
			else
			{

				$this->_resetflag=0;			
				$this->_remainparam=ltrim($this->_remainparam,$seperateChar);				   
				$opt_word_len_arr=array_keys($optionarr1);
				usort($opt_word_len_arr, [$this, "_sortByLength"]); 
				$str_len = strlen($this->_remainparam);			
				$this->checkStrLenExist($str_len,$optionarr1,$opt_word_len_arr,$this->_remainparam,$this->_remainparam);			
			}
			}
		
	}
	 public function _sortByLength($a, $b)
    {		
		return (int) (($a) < ($b));
		  
	}
	public function createAttributeParam($param)
	{
		$query=[];		
		$remove_attr_arr=[];	
		
		if($this->_helper->IsAttributeIncludeData()==0)
		{
			
			$optionarr=$this->_attr_helper->getAllFilterableOptionsAsHash();	
		}
		else
		{
			
			$optionarr=$this->_attr_helper->getAllFilterableOptionsAsHashLabel();
		}
		
		$seperateChar=$this->_helper->getSeperateAttrChar();
		$replaceChar=$this->_helper->getReplaceSpecialChar();
		$attribute_arr=array_keys($optionarr);
		foreach($attribute_arr as $key=>$value)
		{
			$new_value=$this->_helper->urlAliasAfterReplaceChar($value);			
		if(strpos($param,$new_value)!== false)
		{	
			$remove_attr_arr[$value]=$new_value;	
		}
		}
		
		foreach($remove_attr_arr as $k=>$v)
		{
			$param=str_replace($v, '', $param);
		}
		$param = rtrim($param,$seperateChar);
		$param = ltrim($param,$seperateChar);				
		$param = preg_replace('/'.$seperateChar.'+/',$seperateChar, $param);
		
		return $param;
	}
	public function errorRedirect($request)
	{
		$refererUrl = $this->_redirect->getRefererUrl();
				 $request->setModuleName('layerednavigation');
                        $request->setControllerName('error');
                        $request->setActionName('index');
           $request->setParam('redirect_url',$refererUrl);
                      
                        return $this->actionFactory->create(
                            'Magento\Framework\App\Action\Forward',
                            ['request' => $request]
                        );
	}
	public function checkCatExist($category,$page)
	{
		$category_coll = $this->_categoryFactory->create()
					->addAttributeToFilter('url_path', $category)
					->addAttributeToSelect('*')
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
}
