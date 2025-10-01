<?php
namespace Magebees\Ajaxaddtocart\Model;

use Magento\Framework\Json\Encoder;

class Productcontent extends \Magento\Catalog\Block\Product\AbstractProduct
{
    protected $_coreRegistry;
    protected $_pageFactory;
    protected $_catalogModel;
    protected $_catalogHelper;
    protected $_ajaxPopup;
    protected $_viewContext;
    protected $jsonHelper;
    protected $productMetadata;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Action\Context $viewcontext,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Catalog\Model\Product $catalogModel,
        \Magento\Catalog\Helper\Product $catalogHelper,
        \Magebees\Ajaxaddtocart\Block\Popup $ajaxPopup,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $coreRegistry
    ) {
            $this->_coreRegistry = $coreRegistry;
            $this->_pageFactory = $pageFactory;
            $this->_catalogModel = $catalogModel;
            $this->_catalogHelper = $catalogHelper;
            $this->_ajaxPopup = $ajaxPopup;
            $this->_viewContext = $viewcontext;
              $this->jsonHelper = $jsonHelper;
            $this->productMetadata = $productMetadata;
    }
    public function send()
    {
    
        if ($this->getError()) {
            $this->setR('error');
        } else {
            $this->setR('success');
        }
       
        
        $this->_viewContext->getResponse()->setBody($this->jsonHelper->jsonEncode($this->getData()));
    }

    public function addBlockContent(&$_response)
    {
            $result = [];
        if (!empty($result)) {
            $_response->addBlockContent($result);
        }
    }
    
    public function addProductOptionsBlock()
    {
        $res = '';
        $product =$this->_coreRegistry->registry('current_product');
        $productId=$product->getId();
        $products=$this->_catalogModel->load($productId);
        $url=$this->_catalogHelper->getImageUrl($products);
        $resultPage= $this->_pageFactory->create();
        $loader_image=$this->_ajaxPopup->getViewFileUrl('Magebees_Ajaxaddtocart::images/ajax_loader.gif');
        $res="<div class='pImageBox'><div id='swatchImgLoader'><img src='".$loader_image."'/></div><img id=product-image-photo_".$product->getId()." src='".$url."' class='pImage' alt='' /></div>";
        $res.="<h3 class='pName'>".$product->getName()."</h3>";
        if ((version_compare($this->productMetadata->getVersion(), '2.3.2', '>=')) && ($product->getTypeId()=='bundle')) {
        
             $layoutblk = $resultPage->addHandle('update_ajaxaddtocart_product_view_type_'.$product->getTypeId())->getLayout();
        } else {
              $layoutblk = $resultPage->addHandle('ajaxaddtocart_product_view_type_'.$product->getTypeId())->getLayout();
        }
     
        $res.= $layoutblk->getBlock('product.info')->toHtml();
        
        if (!empty($res)) {
            return $res;
        }
    }
}
