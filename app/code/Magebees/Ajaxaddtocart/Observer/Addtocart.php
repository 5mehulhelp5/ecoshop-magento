<?php
namespace Magebees\Ajaxaddtocart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class Addtocart implements ObserverInterface
{

     protected $ajaxPopup;
     protected $productContent;
     protected $catalogModel;
     protected $httpRequest;
     protected $catalogHelper;
     protected $checkoutHelper;
     protected $cartModel;
     protected $pageFactory;
     protected $redirect;
      protected $_coreRegistry;
        protected $productRepository;

    protected $coreRegistry;
    public function __construct(
        \Magebees\Ajaxaddtocart\Block\Popup $ajaxPopup,
        \Magebees\Ajaxaddtocart\Model\Productcontent $productContent,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Catalog\Model\Product $catalogModel,
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $catalogHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\Model\Cart $cartModel,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        Registry $coreRegistry
    ) {
    
        $this->ajaxPopup = $ajaxPopup;
        $this->productContent = $productContent;
          $this->productRepository = $productRepository;
        $this->httpRequest = $httpRequest;
        $this->catalogModel = $catalogModel;
        $this->catalogHelper = $catalogHelper;
        $this->checkoutHelper = $checkoutHelper;
        $this->cartModel = $cartModel;
        $this->pageFactory = $pageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->redirect = $redirect;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $config=$this->ajaxPopup->getConfig();
        if ($config['enable']==1) {
            if ($this->httpRequest->isAjax()) {
                $productId=$observer->getProduct()->getId();
                $product=$this->catalogModel->load($productId);
                $this->_coreRegistry->register('product', $product);
                  if($observer->getProduct()->getTypeId()=='configurable') {
                    $configProductImageType = $this->ajaxPopup->getConfigProductImageType();
                    if($configProductImageType == "itself") {
                        $prdimg = $this->productRepository->get($observer->getProduct()->getSku());
                        $url=$this->catalogHelper->getImageUrl($prdimg);
                    }else{
                         $url=$this->catalogHelper->getImageUrl($product);  
                    }  
                }else {

                    $url=$this->catalogHelper->getImageUrl($product);  
                }


                $content=$this->ajaxPopup->getConfirmContent();
                $dialogConfig=$this->ajaxPopup->getDialogConfig();
                $totalQuantity =$this->cartModel->getQuote()->getItemsQty();
                $subTotal = $this->cartModel->getQuote()->getSubtotal();
                $formated_total=$this->checkoutHelper->formatPrice($subTotal);
                $resultPage= $this->pageFactory->create();
                $layoutblk = $resultPage->addHandle('ajaxcheckout_cart_index')->getLayout();
                $productblk = $resultPage->addHandle('product_section')->getLayout();
                //$layoutblk = $resultPage->addHandle('checkout_cart_index')->getLayout();
                
                $added = __('added to your cart successfully');
                $html="<h3 class='pname'>".$product->getName()." ".$added."</h3>";
                if ($dialogConfig['productimage']==1) {
                    $html.="<img src='".$url."' alt='".$product->getName()."' class='pImage' />";
                }
                $there = __('There are');
                $your = __('item in your cart');
                $cartsubtotal = __('Cart Subtotal');
                 
                $html.="<div class='totalRow'>".$there."<b> ".$totalQuantity."</b> ".$your.".<br />".$cartsubtotal.": <b>".$formated_total."</b></div>".$content;
                if ($dialogConfig['related']==1) {
                    $related_content= $productblk->getBlock('catalog.product.related')->toHtml();
                    $html.=$related_content;
                }
                if ($dialogConfig['upsell']==1) {
                    $upsell_content= $productblk->getBlock('product.info.upsell')->toHtml();
                     $html.=$upsell_content;
                }
                if ($dialogConfig['crosssell']==1) {
                    $crosssell_content= $productblk->getBlock('addtocart_crosssell')->toHtml();
                     $html.=$crosssell_content;
                }
                
                $backUrl =     $this->httpRequest->getParam('return_url');
                
                if ($backUrl) {
                    $html.= $this->httpRequest->getParams();
                }
                $_response=$this->productContent;
                $_response->setPopupContent($html);
                $referUrl = $this->redirect->getRefererUrl();
                if (strpos($referUrl, 'checkout')!== false) {
                    if ($layoutblk->getBlock('checkout.cart')) {
                        $cart_content= $layoutblk->getBlock('checkout.cart')->toHtml();
                        $_response->setCartContent($cart_content);
                    }
                }
                $_response->addBlockContent($_response);
                $_response->send();
            }
        }
    }
}
