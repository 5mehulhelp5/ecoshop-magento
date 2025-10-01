<?php

namespace Magebees\Layerednavigation\Model\ResourceModel\Layerattribute;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Layerednavigation\Model\Layerattribute', 'Magebees\Layerednavigation\Model\ResourceModel\Layerattribute');
    }
}
