<?php
namespace Magebees\Responsivebannerslider\Block\View;
use Magento\Store\Model\ScopeInterface;
class Bottom extends \Magento\Framework\View\Element\Template
{
    protected $configValues = array();
    protected $responsivebannerslider;
    protected $slide;
    protected $coreRegistry;
	protected $_cmsPage;
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magebees\Responsivebannerslider\Model\Responsivebannerslider $responsivebannerslider,
		\Magebees\Responsivebannerslider\Model\Slide $slide,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Cms\Block\Page $cmsPage
	  
    ) {
        parent::__construct($context);
        $this->responsivebannerslider = $responsivebannerslider;
        $this->slide = $slide;
		$this->_request = $request;
		$this->_coreRegistry = $coreRegistry;
		$this->_cmsPage = $cmsPage;
	
		//Set Configuration values
		$this->setEnabled($this->_scopeConfig->getValue('responsivebannerslider/setting/enabled',ScopeInterface::SCOPE_STORE));
		$this->setCmsPage($this->_scopeConfig->getValue('responsivebannerslider/setting/cms_page',ScopeInterface::SCOPE_STORE));
		$this->setCategoryPage($this->_scopeConfig->getValue('responsivebannerslider/setting/category_page',ScopeInterface::SCOPE_STORE));
		$this->setProductPage($this->_scopeConfig->getValue('responsivebannerslider/setting/product_page',ScopeInterface::SCOPE_STORE));
		
	}
	
	public function getGroupscollection() { 
     	$groups = $this->responsivebannerslider->getCollection();
		$groups->addFieldToFilter('status',1);
		$groups->addFieldToFilter('position','content_bottom');
		$groups ->setOrder('sort_order','ASC');
		$cms_page = $this->getCmsPage();
		$category_page = $this->getCategoryPage();
		$product_page = $this->getProductPage();
			
		if ($this->_request->getFullActionName() == 'catalog_category_view') {
			if($category_page) {	
				$category_id = $this->_coreRegistry->registry('current_category')->getId();	
				$groups->categoryFilter($category_id);
			}else{
				return false;
			}	
		}elseif ($this->_request->getFullActionName() == 'catalog_product_view') {
			if($product_page) {
				$productid = $this->_coreRegistry->registry('current_product')->getId();
				$groups->productFilter($productid);
			}else{
				return false;
			}	
		}elseif ($this->_request->getFullActionName() == 'cms_page_view' || $this->_request->getFullActionName() == 'cms_index_index') {
				if($cms_page) {	
					$pageId = $this->_cmsPage->getPage()->getPageId();
					$groups->pageFilter($pageId);
				}else{
					return false;
				}
		}else{
			if($this->_request->getFullActionName() == 'cms_noroute_index') {
				$pageId = $this->_cmsPage->getPage()->getPageId();
				$groups->pageFilter($pageId);
			}	
		}
		
    	if ($this->_request->getFullActionName() == 'wishlist_index_configure') {
			return false;
		}		
		$store_id = $this->_storeManager->getStore()->getId();
		if (!$this->_storeManager->isSingleStoreMode()) {
			$groups->storeFilter($store_id);
		}
		return $groups;
	}

	public function getSlides($slidegroupId) {	
		$slide_collection = $this->slide->getCollection()
			->addFieldToFilter('group_names', array(array('finset' => $slidegroupId)))
			->addFieldToFilter('statuss', '1')
			->setOrder('sort_order','ASC');
		return $slide_collection;
	}
}