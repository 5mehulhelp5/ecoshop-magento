<?php
namespace Magebees\Pagebanner\Helper;
use Magento\Framework\App\ObjectManager;
class Data extends \Magento\Framework\Url\Helper\Data
{
	const PAGEBANNER_ENABLE = 'pagebanner/general/enabled';
	protected $_pagebannerexist = null;
	protected $_request;
	protected $httpContext;
	protected $httpActionContext;
	protected $_storeManager;
	protected $PagebannerFactory;
	protected $_cmsPage;
	public $_registry;
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\Http\Context $http_context,
		\Magento\Framework\App\Action\Context $http_action_context,
		\Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magebees\Pagebanner\Model\PagebannerFactory $PagebannerFactory,
		\Magento\Cms\Model\Page $cmsPage,
		\Magento\Framework\Registry $registry
    ) {
		$this->_request = $request;
		$this->httpContext = $http_context;
		$this->httpActionContext = $http_action_context;
    	$this->_storeManager = $storeManager; 
		$this->PagebannerFactory = $PagebannerFactory;
		$this->_cmsPage = $cmsPage;	
		$this->_registry = $registry;		
	    parent::__construct($context);
    }
	public function isEnablePageBanner()
	{
		
		$pagebanner_enable = $this->scopeConfig->getValue(
            self::PAGEBANNER_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $pagebanner_enable;
	}
	public function getStoreId()
	{
		return $this->_storeManager->getStore()->getId();
	}
	public function getActionName(){
		return $this->httpActionContext->getRequest()->getFullActionName();
	}
	public function isHomePage(){
		$currentFullAction = $this->httpActionContext->getRequest()->getFullActionName();
		$homepage = array('cms_index_index');
		
		if(in_array($currentFullAction, $homepage)){
			return true;
		}
		return false;
	}
	public function isProductPage(){
		$currentFullAction = $this->httpActionContext->getRequest()->getFullActionName();
		$productpages = array('catalog_product_view','checkout_cart_configure');
		
		if(in_array($currentFullAction, $productpages)){
			return true;
		}
		return false;
	}
	public function getPageBanner(){
		$storeId = $this->getStoreId();
		$action_name = $this->getActionName();
		$pageinfo = $this->getPageInformation($action_name);
		if((isset($pageinfo['page_type'])&&!empty($pageinfo['page_type'])) && 
			(isset($pageinfo['identifier'])&&!empty($pageinfo['identifier'])))
		{
		$pageBannerCollection = $this->PagebannerFactory->create()->getCollection();	
		$pageBannerCollection->addFieldToFilter('stores', array('eq' => $storeId));
		$pageBannerCollection->addFieldToFilter('status', array('eq' => 1));
		$page_type = $pageinfo['page_type'];
		
		if($page_type=='cmspage'){
			$cms_page = $pageinfo['identifier'];
			$pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
			$pageBannerCollection->addFieldToFilter('cms_page', array('eq' => $cms_page));
			
		}else if($page_type=='catalogcategory'){
			$catalog_category = $pageinfo['identifier'];
			$pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
			$pageBannerCollection->addFieldToFilter('catalog_category', array('eq' => $catalog_category));
			
		}else if($page_type=='blogcategory'){
			$blog_category = $pageinfo['identifier'];
			$pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
			$pageBannerCollection->addFieldToFilter('blog_category', array('eq' => $blog_category));
		
		}else if($page_type=='specifiedpage'){
			$layout_handle = $pageinfo['identifier'];
			$pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
			$pageBannerCollection->addFieldToFilter('layout_handle', array('eq' => $layout_handle));
		}
		$currentPageBanner = $pageBannerCollection->getFirstItem();
		
		if($currentPageBanner->getBannerId()>0){
			$this->_pagebannerexist = true;
			return $currentPageBanner;
		}else{
			$currentPageSpecifiedBanner = $this->getSpecifiedPageBanner($action_name);
		}
		if($currentPageSpecifiedBanner->getBannerId()>0){
			$this->_pagebannerexist = true;
			return $currentPageSpecifiedBanner;
			
		}else{
			$page_type = 'none';
			$pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
			$this->_pagebannerexist = false;
			return $pageBannerCollection->getFirstItem();
		}
		}else{
			$page_type = 'none';
			$pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
			$this->_pagebannerexist = false;
			return $pageBannerCollection->getFirstItem();
		}
		return $pageBannerCollection->getFirstItem();
	}
	public function isPageBannerExist(){
		return $this->_pagebannerexist;
	}
	public function getBannerImageAbsolutePath($imageName){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
		return $imagePath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath().'pagebanner'.$imageName;
   }
   
	public function getBannerImagePath($imageName){
		$storeId = $this->_storeManager->getStore()->getId();
		$currentStore = $this->_storeManager->getStore($storeId);
		return $image_url= $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'pagebanner'.$imageName;
	}
	public function getSpecifiedPageBanner($currentFullAction){
		$storeId = $this->_storeManager->getStore()->getId();
		$pageBannerCollection = $this->PagebannerFactory->create()->getCollection();	
		$pageBannerCollection->addFieldToFilter('stores', array('eq' => $storeId));
		$pageBannerCollection->addFieldToFilter('status', array('eq' => 1));
		$page_type = 'specifiedpage';
		$pageBannerCollection->addFieldToFilter('page_type_options', array('eq' => $page_type));
		$pageBannerCollection->addFieldToFilter('layout_handle', array('eq' => $currentFullAction));
		return $pageBannerCollection->getFirstItem();
	
	}
	public function getPageInformation($currentFullAction){
		
		$cmspages = array('cms_index_index','cms_page_view');
		$blogcategorypages = array('blog_category_index','blog_category_view');
		$blogtagpages = array('blog_tag_view');
		
		$pageinfo = array();
		if(in_array($currentFullAction, $cmspages)){
			$pageinfo['page_type'] = 'cmspage';
			$pageinfo['identifier'] = $this->_cmsPage->getIdentifier();
			//return $pageinfo;
		}else if(in_array($currentFullAction, $blogcategorypages)){
			$blogcategory = $this->_registry->registry('current_blog_category');//get current category
			$pageinfo['page_type'] = 'blogcategory';
			$pageinfo['identifier'] = $blogcategory->getCategoryId();
			//return $pageinfo;
		}else if (($this->_request->getFullActionName() == 'catalog_category_view')&&
				($currentFullAction == 'catalog_category_view'))		{
			$category = $this->_registry->registry('current_category');//get current category
			$pageinfo['page_type'] = 'catalogcategory';
			$pageinfo['identifier'] = $category->getId();
			//return $pageinfo;
		}
		if(empty($pageinfo)){
			if($this->_request->getFullActionName() == $currentFullAction)
			{
				$pageinfo['page_type'] = 'specifiedpage';
				$pageinfo['identifier'] = $currentFullAction;
				//return $pageinfo;
			}	
		}
		
		return $pageinfo;
	}
	
}
