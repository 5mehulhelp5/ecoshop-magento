<?php
namespace Magebees\Ajaxaddtocart\Block\Success;

class Checkout extends \Magento\Framework\View\Element\Template
{
    
    protected $urlProvider;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url $urlProvider
    ) {
        parent::__construct($context);
        $this->urlProvider = $urlProvider;
    }
    
    /* For add template for 'Go to checkout' button and set path for it */
    protected function _prepareLayout()
    {
        $this->setTemplate('Success/checkout.phtml');
        return parent::_toHtml();
    }
    public function getCheckoutUrl()
    {
        return $this->url = $this->urlProvider->getUrl('checkout/index/index');
    }
}
