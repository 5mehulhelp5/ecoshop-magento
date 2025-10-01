<?php
/**
 * Copyright © 2017 Magebees. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magebees\CustomCartStyle\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $mediaUrl;
    
    protected $scopeConfig;
    
    protected $enableCustomCart;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }
    
    public function getConfig($path)
    {
		$configuration = $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $configuration;
       
    }
    
    public function enableCustomCart()
    {
        if ($this->enableCustomCart === null) {
            $this->enableCustomCart = $this->getConfig('shoppingcart/general/enable');
        }
        return $this->enableCustomCart;
    }
    
    public function getMiniCartStyle()
    {
        return $this->getConfig('shoppingcart/general/style');
    }  
	public function isAutoClosePopup()
    {
        return $this->getConfig('shoppingcart/general/auto_close_popup');
    } 
	public function getAutoClosePopupTime()
    {
        return $this->getConfig('shoppingcart/general/showing_time');
    } 
	public function getFlyCartTime()
    {
        return $this->getConfig('shoppingcart/general/flycart_time');
    } 
	public function isAutoCloseFooterCart()
    {
        return $this->getConfig('shoppingcart/general/auto_close_flycart');
    } 
	public function getFlyCartCloseTime()
    {
        return $this->getConfig('shoppingcart/general/flycart_showing_time');
    } 
    
}
