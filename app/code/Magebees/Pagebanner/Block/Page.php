<?php
namespace Magebees\Pagebanner\Block;

class Page extends \Magento\Cms\Block\Page
{
	
	
protected function _prepareLayout()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->create('Magebees\Pagebanner\Helper\Data');
		$page = $this->getPage();
        $this->_addBreadcrumbs($page);
        $this->pageConfig->addBodyClass('cms-' . $page->getIdentifier());
        $metaTitle = $page->getMetaTitle();
        $this->pageConfig->getTitle()->set($metaTitle ? $metaTitle : $page->getTitle());
        $this->pageConfig->setKeywords($page->getMetaKeywords());
        $this->pageConfig->setDescription($page->getMetaDescription());

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if($helper->isEnablePageBanner()){
			
			if ($pageMainTitle) {
				// Setting empty page title if content heading is absent
				//$cmsTitle = $page->getContentHeading() ?: $page->getTitle();
				 $cmsTitle = $page->getContentHeading() ?: $page->getTitle();
				$pageMainTitle->setPageTitle($this->escapeHtml($cmsTitle));
			}
			
		}else{
			
			if ($pageMainTitle) {
							
				// Setting empty page title if content heading is absent
				$cmsTitle = $page->getContentHeading() ?: ' ';
				$pageMainTitle->setPageTitle($this->escapeHtml($cmsTitle));
			}	
			return parent::_prepareLayout();
		}
		
		
    }

}