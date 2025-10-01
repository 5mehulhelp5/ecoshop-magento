<?php
namespace Magebees\Productlisting\Model\ResourceModel\Productlisting;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Productlisting\Model\Productlisting', 'Magebees\Productlisting\Model\ResourceModel\Productlisting');
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
        $result = array_merge($options, parent::_toOptionArray('listing_id', 'title'));
        return $result;
    }
}
