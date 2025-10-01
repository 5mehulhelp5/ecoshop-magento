<?php
namespace Magebees\Ajaxaddtocart\Block\Success;

class Shopcontinue extends \Magento\Framework\View\Element\Template
{
    /* For add template for 'Continue Shopping' button and set path for it */
    protected function _prepareLayout()
    {
        $this->setTemplate('Success/shopcontinue.phtml');
        return parent::_toHtml();
    }
    public function getContinueUrl()
    {
        return $this->url = $this->_storeManager->getStore()->getBaseUrl();
    }
}
