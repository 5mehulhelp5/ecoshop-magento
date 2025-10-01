<?php
namespace Magebees\PocoThemes\Plugin\Result;

use Magento\Framework\App\ResponseInterface;

class Page
{
	private $context;
	private $registry;
	private $helper;
	private $pagebannerhelper;
	private $httpHeader;
	
	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Framework\Registry $registry,
		\Magebees\PocoThemes\Helper\Data $helper,
		\Magebees\Pagebanner\Helper\Data $pagebannerhelper,
		\Magento\Framework\HTTP\Header $httpHeader
	) {
		$this->context = $context;
		$this->registry = $registry;
		$this->helper = $helper;
		$this->pagebannerhelper = $pagebannerhelper;
		$this->httpHeader    = $httpHeader;
		
	}

	public function beforeRenderResult(
		\Magento\Framework\View\Result\Page $subject,
		ResponseInterface $response
	){
		
		
		if(($this->context->getRequest()->getFullActionName() == 'catalog_product_view')||($this->context->getRequest()->getFullActionName() =='checkout_cart_configure')){
			
			if($this->pagebannerhelper->isEnablePageBanner()){
			$pageBanner = $this->pagebannerhelper->getPageBanner();
			if($pageBanner->getBannerId()){
			$banner_image_class = '';
			
			}else{
			$banner_image_class = 'catalog-product-nobanner';	
			}
			
			}else{
				$banner_image_class = 'catalog-product-nobanner';
			}
			$subject->getConfig()->addBodyClass($banner_image_class);
		}
		$store_id = $this->helper->getStoreId();
		$home_style = $this->helper->getHome('home_style',$store_id);
		if($home_style == 'poco-themes-style-1'){
			$home_class = 'poco-home-page-style-1';
		}elseif($home_style == 'poco-themes-style-2'){
			$home_class = 'poco-home-page-style-2';
		}elseif($home_style == 'poco-themes-style-3'){
			$home_class = 'poco-home-page-style-3';
		}elseif($home_style == 'poco-themes-style-4'){
			$home_class = 'poco-home-page-style-4';
		}elseif($home_style == 'poco-themes-style-5'){
			$home_class = 'poco-home-page-style-5';
		}elseif($home_style == 'poco-themes-style-6'){
			$home_class = 'poco-home-page-style-6';
		}elseif($home_style == 'poco-themes-style-7'){
			$home_class = 'poco-home-page-style-7';
		}elseif($home_style == 'poco-themes-style-8'){
			$home_class = 'poco-home-page-style-8';
		}elseif($home_style == 'poco-themes-style-9'){
			$home_class = 'poco-home-page-style-9';
		}elseif($home_style == 'poco-themes-style-10'){
			$home_class = 'poco-home-page-style-10';
		}elseif($home_style == 'poco-themes-style-11'){
			$home_class = 'poco-home-page-style-11';
		}elseif($home_style == 'poco-themes-style-12'){
			$home_class = 'poco-home-page-style-12';
		}elseif($home_style == 'poco-themes-style-13'){
			$home_class = 'poco-home-page-style-13';
		}elseif($home_style == 'poco-themes-style-14'){
			$home_class = 'poco-home-page-style-14';
		}elseif($home_style == 'poco-themes-style-15'){
			$home_class = 'poco-home-page-style-15';
		}elseif($home_style == 'poco-themes-style-16'){
			$home_class = 'poco-home-page-style-16';
		}elseif($home_style == 'poco-themes-style-17'){
			$home_class = 'poco-home-page-style-17';
		}elseif($home_style == 'poco-themes-style-18'){
			$home_class = 'poco-home-page-style-18';
		}elseif($home_style == 'poco-themes-style-19'){
			$home_class = 'poco-home-page-style-19';
		}else{
			$home_class = 'poco-home-page-style-1';
		}
		$subject->getConfig()->addBodyClass($home_class);
 		$button_style = $this->helper->getGeneral('btn_style',$store_id);
		if($button_style){
			$button_style_class = 'btn-'.$button_style.'-style';
			
		}else{
			$button_style_class = 'btn-square-style';
		}
		
		$subject->getConfig()->addBodyClass($button_style_class);
		
		$layout_width = $this->helper->getThemeLayout('layout_width',$store_id);
		if($layout_width == "full"){
			$subject->getConfig()->addBodyClass('full-width-layout-poco');
		}else if($layout_width == "boxed"){
			$subject->getConfig()->addBodyClass('boxed-layout-poco');
		}
		
		$rtl = $this->helper->getThemeLayout('rtl',$store_id);
		if($rtl){
			$subject->getConfig()->addBodyClass('rtl-layout-poco');
		}
		$top_header = $this->helper->getHeader('top_header',$store_id);
		if($top_header){
			$subject->getConfig()->addBodyClass('topbar-visible');
		}
		
		$subject->getConfig()->addBodyClass('poco-themes');
		return [$response];
	}
	public function isNoJs()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
		return preg_match('/Chrome-Lighthouse|PingdomPageSpeed|PingdomPageSpeed|GTmetrix/i', $userAgent);     
    }
}