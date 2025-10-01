<?php
namespace Magebees\Layerednavigation\Observer;
use Magento\Framework\Event\ObserverInterface;

class LayoutGenerate implements ObserverInterface
{
	protected $pageConfig;
	protected $_request;
	protected $_attr_helper;
	protected $helper_url;
	protected $helper;
	protected $_requesthttp;
	protected $productAttributeCollectionFactory;

   public function __construct(   
    \Magento\Framework\View\Page\Config $pageConfig,
	\Magento\Framework\App\RequestInterface $request,
	\Magebees\Layerednavigation\Helper\Attributes $attr_helper,
	\Magebees\Layerednavigation\Helper\Url $helper_url,
	\Magebees\Layerednavigation\Helper\Data $helper,
	\Magento\Framework\App\Request\Http $requesthttp,
	\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory$productAttributeCollectionFactory
) {    
    $this->pageConfig = $pageConfig; 
	$this->_request = $request;       	
	$this->_attr_helper = $attr_helper;	
	$this->helper_url = $helper_url;
	$this->helper = $helper;
	$this->_requesthttp = $requesthttp;
	$this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
}
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$moduleName = $this->_requesthttp->getModuleName();
		if ($this->_requesthttp->getFullActionName() == 'catalog_category_view' || $this->_requesthttp->getFullActionName() == "catalogsearch_result_index" || $moduleName == "layerednavigation") {

			$applied_params=$this->_request->getParams();
			$seoConfigData=$this->helper->getSeoConfigData();
			$optionarr=$this->_attr_helper->getAllFilterableOptionsAsHash();
			foreach($optionarr as $key=>$value)	{
				$key_arr[]=$key;
			}
			foreach($applied_params as $key=>$value){			
				if (in_array($key,$key_arr)){
					$applied_key_arr[]=$key;
					$option_id=$this->_request->getParam($key);
					if(is_array($option_id)){
						$meta_title_arr=array();
						$meta_desc_arr=array();
						$meta_keyword_arr=array();
						foreach($option_id as $option){
							$meta_title_arr[]=trim($this->helper_url->getMetaTitle($option));
							$meta_desc_arr[]=trim($this->helper_url->getMetaDescription($option));
							$meta_keyword_arr[]=trim($this->helper_url->getMetaKeyword($option));
						}
					}else{
						$meta_title_arr[]=trim($this->helper_url->getMetaTitle($option_id));
						$meta_desc_arr[]=trim($this->helper_url->getMetaDescription($option_id));
						$meta_keyword_arr[]=trim($this->helper_url->getMetaKeyword($option_id));
					}				
				}
			} 
			/*set no index,no follow tag*/
			if(isset($applied_key_arr))	{
				end($applied_key_arr);			
				
				if($applied_key_arr[0]=='price'){
					$nofollowtag=$seoConfigData['nofollw_price'];
					$noindextag=$seoConfigData['noindex_price'];
				}else{
					$productAttributes = $this->productAttributeCollectionFactory->create(); 
					$collection=$productAttributes->addFieldToFilter('attribute_code',$applied_key_arr);			
					$attr_data=$collection->getData();
					$attribute_id=$attr_data[0]['attribute_id'];
					$noindextag=$this->helper->getNoIndexTag($attribute_id);
					$nofollowtag=$this->helper->getNoFollowTag($attribute_id);
				}	

				$cat_id=$this->_request->getParam('id');
				if(isset($seoConfigData['nofollw_cat'])){
					$nofollw_cat=$seoConfigData['nofollw_cat'];
					$nofollw_cat_arr=explode(',', (string)$nofollw_cat);
					if (in_array($cat_id,$nofollw_cat_arr)){
						$nofollowtag='nofollow';
					}else{
						$nofollowtag=$nofollowtag> 0 ? 'nofollow' : 'follow';
					}
				}else{
					$nofollowtag='follow';
				}
				
				if(isset($seoConfigData['noindex_cat'])){
					$noindex_cat=$seoConfigData['noindex_cat'];
					$noindex_cat_arr=explode(',', (string)$noindex_cat);
					
					if (in_array($cat_id,$noindex_cat_arr))	{
						$noindextag='noindex';
					}else{
						$noindextag=$noindextag> 0 ? 'noindex' : 'index';		
					}
				}else{
					$noindextag='index';
				}
				if ($this->_requesthttp->getFullActionName() == 'catalog_category_view') {
					$this->pageConfig->setRobots($noindextag.','.$nofollowtag);	
				}
			}else{
				
				$cat_id=$this->_request->getParam('id');
				if(isset($seoConfigData['nofollw_cat'])){
					$nofollw_cat=$seoConfigData['nofollw_cat'];
				}else{
					$nofollw_cat='';
				}			
				$nofollw_cat_arr=explode(',', (string)$nofollw_cat);
				if(isset($seoConfigData['noindex_cat'])){
					$noindex_cat=$seoConfigData['noindex_cat'];
				}else{
					$noindex_cat='';
				}
				
			
				$noindex_cat_arr=explode(',', (string)$noindex_cat);
			
				if (in_array($cat_id,$nofollw_cat_arr)){
					$nofollowtag='nofollow';
				}else{
					$nofollowtag='FOLLOW';
				}
			
				if (in_array($cat_id,$noindex_cat_arr))	{
					$noindextag='noindex';
				}else{
					$noindextag='INDEX';
				}
				if ($this->_requesthttp->getFullActionName() == 'catalog_category_view') {
					$this->pageConfig->setRobots($noindextag.','.$nofollowtag);	
				}	
			}
			/*set meta title for attibute filter*/
			if(isset($meta_title_arr))	{
				$title_separator=$this->helper->getTitleSeperator();
				if(!$title_separator){
					$title_separator='-';
				}
				$meta_title=implode($title_separator,array_filter($meta_title_arr));
				$oldtitle=$this->pageConfig->getTitle()->get();		
				if(trim($meta_title)){
					$this->pageConfig->getTitle()->set($oldtitle.$title_separator.$meta_title);
				}
			}
			/*set meta description for attibute filter*/
			if(isset($meta_desc_arr)){			  
				$meta_desc=implode(',',array_filter($meta_desc_arr));
				$olddesc=$this->pageConfig->getDescription();
				if(trim($meta_desc)){
					$this->pageConfig->setDescription($olddesc.','.$meta_desc);
				}
				
			}
			/*set meta keywords for attibute filter*/
			if(isset($meta_keyword_arr)){	
				$meta_keyword=implode(',',array_filter($meta_keyword_arr));		
				$oldkey=$this->pageConfig->getKeywords();
				if(trim($meta_keyword))	{
					$this->pageConfig->setKeywords($oldkey.','.$meta_keyword);
				}
			}
			
	    }
	}    

    
}
