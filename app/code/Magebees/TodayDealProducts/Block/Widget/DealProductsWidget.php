<?php
namespace Magebees\TodayDealProducts\Block\Widget;

class DealProductsWidget extends \Magebees\TodayDealProducts\Block\DealProducts implements \Magento\Widget\Block\BlockInterface
{
    public function addData(array $arr)
    {
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        $this->_data[$key] = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    /*protected function _beforeToHtml() {
        return parent::_beforeToHtml();
    }*/
    
    public function _toHtml(){		
		$load_ajax = $this->getData('wd_load_ajax');
		if($load_ajax){
			
			$this->setTemplate('Magebees_PocoBase::todayDealProducts.phtml');
			
	}	
	if(($this->getTemplate())&&(!$load_ajax)){
		$this->setTemplate($this->getTemplate());
	}
	    return parent::_toHtml();    
    }
}
