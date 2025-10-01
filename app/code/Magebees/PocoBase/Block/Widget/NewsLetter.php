<?php
namespace Magebees\PocoBase\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class NewsLetter extends Template  implements BlockInterface
{	
	public $wd_enable=null;
	public $load_ajax=null;
	public $wd_spacing=null;
	public $wd_bottom_spacing=null;
	public $wd_bgimage=null;
	public $wd_bgcolor=null;
	public $wd_show_heading=null;
	public $wd_newsletter_title=null;
	public $wd_heading_logo=null;
	public $wd_newsletter_text_placeholder=null;
	public $wd_newsletter_text=null;
	public $wd_newsletter_button_text=null;
	public $store_id=null;
	public $wd_slider=null;
	public $template =null;
	 
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
	protected function getCacheLifetime()
	{
		return 84600;
	}
	public function getCacheKeyInfo()
	{
		$name =   'magebees_newsletter_widget';
		$keyInfo     =  parent::getCacheKeyInfo();
		$keyInfo[]   =  $name; // adding the product id to the cache key so that each cache built remains unique as per the product page.
		
		return $keyInfo;
	}
    protected function _beforeToHtml()
    {	
		$this->load_ajax = $this->getData('wd_load_ajax');
		if($this->load_ajax){
		$this->setTemplate('Magebees_PocoBase::load_newsletter.phtml');
		return parent::_beforeToHtml();
		}
		
	}

}