<?php

namespace Magebees\Layerednavigation\Model\ResourceModel\Attributeoption;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Layerednavigation\Model\Attributeoption', 'Magebees\Layerednavigation\Model\ResourceModel\Attributeoption');
    }
}
