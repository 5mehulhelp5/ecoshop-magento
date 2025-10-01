<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magebees\PocoThemes\Block\Html;

/**
 * Html page header block
 */
class Header extends \Magento\Theme\Block\Html\Header
{	protected $_scopeConfig;
   	public function _toHtml()	{	
		$header_style = $this->_scopeConfig->getValue('pocothemes/header/header_style',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$this->setTemplate('Magento_Theme::html/header/'.$header_style.'.phtml');
		return parent::_toHtml();    
    }
}
