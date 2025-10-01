<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\System\Config;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
class Mockup extends \Magento\Config\Block\System\Config\Form\Field
{		
	protected $_template = 'Magebees_Onepagecheckout::system/config/opcsuccess.phtml';

	public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
       
		return $this->toHtml();
    }
}