<?php
namespace Magebees\Ajaxaddtocart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class WishlistAddtocart implements ObserverInterface
{

    protected $ajaxPopup;
    protected $productContent;
    protected $catalogModel;
    protected $httpRequest;
    protected $catalogHelper;
    protected $checkoutHelper;
    protected $cartModel;
    protected $pageFactory;
    protected $_coreRegistry;
    public function __construct(
        \Magebees\Ajaxaddtocart\Block\Popup $ajaxPopup,
        \Magebees\Ajaxaddtocart\Model\Productcontent $productContent,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Catalog\Model\Product $catalogModel,
        \Magento\Catalog\Helper\Product $catalogHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\Model\Cart $cartModel,
        Registry $coreRegistry
    ) {
    
        $this->ajaxPopup = $ajaxPopup;
        $this->pageFactory = $pageFactory;
        $this->productContent = $productContent;
        $this->httpRequest = $httpRequest;
        $this->catalogModel = $catalogModel;
        $this->catalogHelper = $catalogHelper;
        $this->checkoutHelper = $checkoutHelper;
        $this->cartModel = $cartModel;
        $this->_coreRegistry = $coreRegistry;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $config=$this->ajaxPopup->getConfig();
        if ($config['enable']==1) {
            if ($this->httpRequest->isAjax()) {
                $productId=$observer->getProduct()->getId();
                $products=$this->catalogModel->load($productId);
                $this->_coreRegistry->register('product', $products);
                $url=$this->catalogHelper->getImageUrl($products);
                $content=$this->ajaxPopup->getConfirmContent();
                $dialogConfig=$this->ajaxPopup->getDialogConfig();
                $totalQuantity =$this->cartModel->getQuote()->getItemsQty();
                $subTotal = $this->cartModel->getQuote()->getSubtotal();
                $formated_total=$this->checkoutHelper->formatPrice($subTotal);
                
                $msg = __('added to your cart successfully');
                $html="<h3 class='pname'>".$products->getName()." ".$msg."</h3>";
                if ($dialogConfig['productimage']==1) {
                    $html.="<img src='".$url."'  class='pImage' />";
                }
                
                $there = __('There are');
                $your = __('item in your cart');
                $cartsubtotal = __('Cart Subtotal');
                
                $html.="<div class='totalRow'>".$there." <b>".$totalQuantity."</b> ".$your.".<br />".$cartsubtotal.": <b>".$formated_total."</b></div>".$content;
                $resultPage= $this->pageFactory->create();
                $productblk = $resultPage->addHandle('product_section')->getLayout();
                $layoutblk = $resultPage->addHandle('wishlist_index_index')->getLayout();
                $wishlist_content= $layoutblk->getBlock('customer.wishlist')->toHtml();
                $itemId=$observer->getItem();
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
                $_response=$this->productContent
                ->setPopupContent($html);
                $_response->setWishlistContent($wishlist_content);
                $_response->setItemId($itemId);
                if (strpos($wishlist_content, 'message info empty') !== false) {
                    $_response->setIsEmpty('true');
                }
                $_response->send();
            }
        }
    }
}
