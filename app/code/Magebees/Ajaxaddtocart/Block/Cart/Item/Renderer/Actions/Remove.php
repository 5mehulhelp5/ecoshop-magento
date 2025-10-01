<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxaddtocart\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Helper\Cart;
use Magento\Framework\View\Element\Template;

class Remove extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Remove
{

    protected $cartHelper;
        protected $ajaxPopup;
    public function __construct(
        Template\Context $context,
        Cart $cartHelper,
        \Magebees\Ajaxaddtocart\Block\Popup $ajaxPopup
    ) {
        $this->cartHelper = $cartHelper;
        $this->ajaxPopup = $ajaxPopup;
        parent::__construct($context, $cartHelper);
    }
    
    public function _beforeToHtml()
    {
        
        $config=$this->ajaxPopup->getConfig();
        if ($config['enable']==1) {
            $this->setTemplate('Magebees_Ajaxaddtocart::cart/item/renderer/actions/remove.phtml');
        }
    }
    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Checkout\Block\Cart\Item\Renderer\Actions\Remove'));
        return parent::_toHtml();
    }
}
