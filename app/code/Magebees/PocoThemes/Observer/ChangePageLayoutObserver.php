<?php
namespace Magebees\PocoThemes\Observer;
use Magento\Framework\Event\ObserverInterface;

class ChangePageLayoutObserver implements ObserverInterface
{	
		protected $pageConfig;
		protected $helper;
		protected $Ajaxsearchhelper;
		
    public function __construct(
    \Magento\Framework\View\Page\Config $pageConfig,
	\Magebees\Ajaxsearch\Helper\Data $Ajaxsearchhelper,
	\Magebees\PocoThemes\Helper\Data $helper
    ) {    
        $this->pageConfig = $pageConfig; 
        $this->helper = $helper;
		$this->Ajaxsearchhelper = $Ajaxsearchhelper;
		$store_code = $this->Ajaxsearchhelper->getStoreCode();
		$dynamic_css = 'Magebees_Ajaxsearch::css/dynamic_search'.$store_code.'.css';
		if(!$this->Ajaxsearchhelper->checkCssExist())
		{
		$dynamic_css = 'Magebees_Ajaxsearch::css/dynamic_search.css';
		}
		$quickViewGalleryCss = 'mage/gallery/gallery.css';
		
		$this->pageConfig->addPageAsset($quickViewGalleryCss);
		$storeId = $this->helper->getStoreId();
		
		$animationEnabled = $this->helper->getConfigValue('theme_layout','animation','pocothemes',$storeId);
		if($animationEnabled):
		$this->pageConfig->addPageAsset('Magebees_PocoThemes::css/animate.css');
		endif;
    }
	
	public function execute(\Magento\Framework\Event\Observer $observer){
		$event = $observer->getEvent();
		$action = $event->getFullActionName();
		$storeId = $this->helper->getStoreId();
		$enable_breadcrumbs = $this->helper->getThemeLayout('breadcrumbs',$storeId);
		$change_layout = $this->helper->getProductListing('page_layout',$storeId);
		if(!$enable_breadcrumbs):

			$layout = $event->getData('layout');

            $layoutUpdate = $layout->getUpdate();

			$layoutUpdate->addHandle('show_hide_breadcrums');

		endif;
		if ($action == 'cms_page_view'){
			$layout = $event->getData('layout');
            $layoutUpdate = $layout->getUpdate();
			
			$layoutUpdate->addHandle('remove_cms_script');
		}
		if ($action == 'catalog_category_view' || 
		$action == 'catalogsearch_result_index' || 
		$action == 'layerednavigation_brand_index'){
			$move_desc_atbottom = $this->helper->getProductListing('move_desc_atbottom',$storeId);
			$move_image_atbottom = $this->helper->getProductListing('move_image_atbottom',$storeId);
			if($move_desc_atbottom):
			$layout = $event->getData('layout');
            $layoutUpdate = $layout->getUpdate();
			
			$layoutUpdate->addHandle('move_category_description_bottom');
			else:
			$layout = $event->getData('layout');
            $layoutUpdate = $layout->getUpdate();
			//$layoutUpdate->addHandle('move_category_description_top');
				if($change_layout=="1column"):
					//$layoutUpdate->addHandle('move_category_image_top_1column');
				else:
					$layoutUpdate->addHandle('move_category_description_top');
				endif;
			endif;
			
			if($move_image_atbottom):
			$layout = $event->getData('layout');
            $layoutUpdate = $layout->getUpdate();
			$layoutUpdate->addHandle('move_category_image_bottom');
			else:
				if($change_layout=="1column"):
					$layout = $event->getData('layout');
					$layoutUpdate = $layout->getUpdate();
					//$layoutUpdate->addHandle('move_category_image_top_1column');
				else:
					$layout = $event->getData('layout');
					$layoutUpdate = $layout->getUpdate();
					$layoutUpdate->addHandle('move_category_image_top');
				endif;
			
			endif;
			
			
			
			
			if($change_layout=="1column"):
				$layout = $observer->getLayout();
				$layout->getUpdate()->addHandle('move_catalogsearch_leftnav');
				//$layout->getUpdate()->addHandle('remove_catalog_leftnav');
				
				$this->pageConfig->setPageLayout($change_layout);
				else: 
				$layout = $observer->getLayout();
				$layout->getUpdate()->addHandle('move_catalogsearch_leftnav_sidebar');
				$this->pageConfig->setPageLayout($change_layout);
			endif;
            
			$this->pageConfig->setPageLayout($change_layout);
			//$this->pageConfig->setPageLayout('3columns');
		}
		
		if ($action == 'catalog_product_view' || $action == 'checkout_cart_configure') {
			$change_layout = $this->helper->getProductDetail('page_layout',$storeId);
			
			
			if(($change_layout=='1column')||($change_layout=='2columns-left')||($change_layout=='2columns-right'))
			{
				$layout = $event->getData('layout');
				$layoutUpdate = $layout->getUpdate();
				$layoutUpdate->addHandle('move_products_bottom');
			}else if($change_layout=='3columns')
			{
				$layout = $event->getData('layout');
				$layoutUpdate = $layout->getUpdate();
				$layoutUpdate->addHandle('3_columns_move_products_bottom');
			}
			$this->pageConfig->setPageLayout($change_layout);
			
		}
	}
}