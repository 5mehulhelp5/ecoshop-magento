<?php
namespace Magebees\Ajaxaddtocart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class WishlistConfigure implements ObserverInterface
{
    protected $ajaxPopup;
    protected $productContent;
    protected $_catalogModel;
    protected $httpRequest;
    protected $_catalogHelper;
    protected $checkoutHelper;
    protected $cartModel;
    protected $_pageFactory;
    protected $productMetadata;
    protected $_coreRegistry;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magebees\Ajaxaddtocart\Block\Popup $ajaxPopup,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Catalog\Model\Product $catalogModel,
        \Magento\Catalog\Helper\Product $catalogHelper,
        \Magebees\Ajaxaddtocart\Model\Productcontent $productContent,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->httpRequest = $httpRequest;
        $this->ajaxPopup = $ajaxPopup;
        $this->_pageFactory = $pageFactory;
        $this->_catalogModel = $catalogModel;
        $this->_catalogHelper = $catalogHelper;
        $this->productContent = $productContent;
        $this->productMetadata = $productMetadata;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $config=$this->ajaxPopup->getConfig();
        if ($config['enable']==1) {
            if ($this->httpRequest->isAjax()) {
                $res = '';
                $product =$this->_coreRegistry->registry('current_product');
                $productId=$product->getId();
                $products=$this->_catalogModel->load($productId);
                $url=$this->_catalogHelper->getImageUrl($products);
                $resultPage= $this->_pageFactory->create();
                $loader_image=$this->ajaxPopup->getViewFileUrl('Magebees_Ajaxaddtocart::images/ajax_loader.gif');
                $res="<div class='pImageBox'><div id='swatchImgLoader'><img src='".$loader_image."'/></div><img id=product-image-photo_".$product->getId()." src='".$url."' class='pImage' alt='' /></div>";
                $res.="<h3 class='pName'>".$product->getName()."</h3>";
                if ((version_compare($this->productMetadata->getVersion(), '2.3.2', '>=')) && ($product->getTypeId()=='bundle')) {
                    $layoutblk = $resultPage->addHandle('update_ajaxaddtocart_wishlist_configure_type_'.$product->getTypeId())->getLayout();
                } else {
                    $layoutblk = $resultPage->addHandle('ajaxaddtocart_wishlist_configure_type_'.$product->getTypeId())->getLayout();
                }
               
                $res.= $layoutblk->getBlock('product.info')->toHtml();
    
                if (!empty($res)) {
                    $_response=$this->productContent->setHtmlPopup($res);
                    $_response->addBlockContent($_response);
                    $_response->send();
                }
            }
        }
    }
}
