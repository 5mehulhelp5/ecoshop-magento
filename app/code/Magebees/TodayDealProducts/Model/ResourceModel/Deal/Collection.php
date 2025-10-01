<?php
namespace Magebees\TodayDealProducts\Model\ResourceModel\Deal;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\TodayDealProducts\Model\Deal', 'Magebees\TodayDealProducts\Model\ResourceModel\Deal');
    }
    
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        $options = [['value' => '', 'label' => 'Please Select']];
        $result = array_merge($options, parent::_toOptionArray('today_deal_id', 'title'));
        return $result;
    }
}
