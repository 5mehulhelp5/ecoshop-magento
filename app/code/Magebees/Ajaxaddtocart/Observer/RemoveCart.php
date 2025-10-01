<?php
namespace Magebees\Ajaxaddtocart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class RemoveCart implements ObserverInterface
{

  
     protected $ajaxPopup;
     protected $productContent;
     protected $catalogModel;
     protected $httpRequest;
     protected $catalogHelper;
     protected $checkoutHelper;
     protected $cartModel;
     protected $pageFactory;
     protected $configProvider;
        protected $_coreRegistry;


    public function __construct(
        \Magebees\Ajaxaddtocart\Block\Popup $ajaxPopup,
        \Magebees\Ajaxaddtocart\Model\Productcontent $productContent,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Catalog\Model\Product $catalogModel,
        \Magento\Catalog\Helper\Product $catalogHelper,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Checkout\Model\DefaultConfigProvider $configProvider,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\Model\Cart $cartModel,
        Registry $coreRegistry
    ) {
    
        $this->_coreRegistry = $coreRegistry;
        $this->ajaxPopup = $ajaxPopup;
        $this->productContent = $productContent;
        $this->httpRequest = $httpRequest;
        $this->catalogModel = $catalogModel;
        $this->catalogHelper = $catalogHelper;
        $this->pageFactory = $pageFactory;
        $this->configProvider = $configProvider;
        $this->checkoutHelper = $checkoutHelper;
        $this->cartModel = $cartModel;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $config=$this->ajaxPopup->getConfig();
        if ($config['enable']==1) {
            if ($this->httpRequest->isAjax()) {
                $productId=$observer->getProduct();
                $product=$this->catalogModel->load($productId);
                $this->_coreRegistry->register('product', $product);
                $url=$this->catalogHelper->getImageUrl($product);
                $content=$this->ajaxPopup->getRemoveConfirmContent();
                $dialogConfig=$this->ajaxPopup->getDialogConfig();
                $totalQuantity =$this->cartModel->getQuote()->getItemsQty();
                $subTotal = $this->cartModel->getQuote()->getSubtotal();
                $formated_total=$this->checkoutHelper->formatPrice($subTotal);
                 $resultPage= $this->pageFactory->create();
                //$productblk = $resultPage->addHandle('product_section')->getLayout();
                   $layoutblk = $resultPage->addHandle('ajaxcheckout_cart_index')->getLayout();
                 
                $added = __('removed from your cart');
                $html="<h3 class='pname removebox'>".$product->getName()." ".$added."</h3>";
                if ($dialogConfig['productimage']==1) {
                    $html.="<img src='".$url."' class='pImage' alt='' />";
                }
                
                $there = __('There are');
                $your = __('item in your cart');
                $cartsubtotal = __('Cart Subtotal');
                $shoppingmsg = __('You have no items in your shopping cart');
                if ($totalQuantity=='0') {
                    $html.="<div class='totalRow'>".$shoppingmsg.".</div>".$content;
                } else {
                    $html.="<div class='totalRow'>".$there." <b> ".$totalQuantity."</b> ".$your.".<br />".$cartsubtotal.": <b>".$formated_total."</b></div>".$content;
                }
                if ($dialogConfig['related']==1) {
                    $related_content= $layoutblk->getBlock('catalog.product.related')->toHtml();
                    $html.=$related_content;
                }
                if ($dialogConfig['upsell']==1) {
                    $upsell_content= $layoutblk->getBlock('product.info.upsell')->toHtml();
                     $html.=$upsell_content;
                }
                if ($dialogConfig['crosssell']==1) {
                    $crosssell_content= $layoutblk->getBlock('addtocart_crosssell')->toHtml();
                     $html.=$crosssell_content;
                }
            
                    //$html.=$content;
                    $resultPage= $this->pageFactory->create();
                    //$layoutblk = $resultPage->addHandle('checkout_cart_index')->getLayout();
                    $cart_content= $layoutblk->getBlock('checkout.cart')->toHtml();
                    $_response=$this->productContent;
                    $_response->setCartContent($cart_content);
                    $_response->setPopupContent($html);
                if (strpos($cart_content, 'no items') !== false) {
                    $_response->setIsEmpty('true');
                }
                   $_response->send();
            }
        }
    }
}
