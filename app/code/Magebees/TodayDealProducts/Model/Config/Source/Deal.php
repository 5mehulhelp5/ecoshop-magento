<?php
namespace Magebees\TodayDealProducts\Model\Config\Source;class Deal implements \Magento\Framework\Option\ArrayInterface{	protected $_deal;
    public function __construct(\Magebees\TodayDealProducts\Model\Deal $deal) {
        $this->_deal = $deal;
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_deal->getCollection()->toOptionArray();
    }
}
