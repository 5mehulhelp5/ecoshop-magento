<?php
namespace Magebees\PocoBase\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Content extends Template  implements BlockInterface
{
    protected function _beforeToHtml()
    {	
		$load_ajax = $this->getData('wd_load_ajax');
		if($load_ajax){
		$this->setTemplate('Magebees_PocoBase::load_content.phtml');
		return parent::_beforeToHtml();
		}
		
	}
    /*public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        //$this->setTemplate('widget/content.phtml');
    }*/
    

}