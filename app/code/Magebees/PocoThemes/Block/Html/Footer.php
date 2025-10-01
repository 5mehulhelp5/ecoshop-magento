<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magebees\PocoThemes\Block\Html;

/**
 * Html page footer block
 */
class Footer extends \Magento\Theme\Block\Html\Footer
{	protected $_scopeConfig;
    public function _toHtml()	{	
	
		$footer_style = $this->_scopeConfig->getValue('pocothemes/footer/footer_style',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$template = 'Magento_Theme::html/footer/'.$footer_style.'.phtml';
		$this->setData('template',$template);
		$this->setTemplate('Magento_Theme::html/footer/'.$footer_style.'.phtml');
		//$this->setTemplate('Magento_Theme::html/footer/footer.phtml');
		return parent::_toHtml();    
    }
}
