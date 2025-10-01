<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magebees\PocoThemes\Block\Html;

/**
 * Html page header block
 */
class Notices extends \Magento\Cookie\Block\Html\Notices
{	protected $_scopeConfig;
   	public function _toHtml()	{
		$enable_cookie = $this->_scopeConfig->getValue('pocothemes/cookie_policy/enable_cookie_policy',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($enable_cookie){
			$this->setTemplate('Magento_Cookie::html/poconotices.phtml');
		}else{
			$this->setTemplate('Magento_Cookie::html/notices.phtml');
		}
		return parent::_toHtml();    
    }
	
	public function getPrivacyPolicyLink()
    {	
		$url = $this->_scopeConfig->getValue('pocothemes/cookie_policy/url',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->_urlBuilder->getUrl($url);
    }
}
