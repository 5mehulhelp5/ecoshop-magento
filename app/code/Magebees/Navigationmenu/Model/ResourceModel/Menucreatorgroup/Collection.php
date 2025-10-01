<?php
namespace Magebees\Navigationmenu\Model\ResourceModel\Menucreatorgroup;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
    protected function _construct()
    {
        $this->_init('Magebees\Navigationmenu\Model\Menucreatorgroup', 'Magebees\Navigationmenu\Model\ResourceModel\Menucreatorgroup');    }
    public function toOptionArray()    {        $result = array();        $options = array(array('value' => '', 'label' => __('Please Select')));        foreach ($this as $item) {            $options[] = [                'value' => $item->getData('unique_code'),                'label' => $item->getData('title') . ' [ID:' . $item->getId() . ']'            ];        }        return $options;    }
}
