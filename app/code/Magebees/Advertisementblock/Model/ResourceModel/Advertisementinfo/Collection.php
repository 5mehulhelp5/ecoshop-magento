<?php

namespace Magebees\Advertisementblock\Model\ResourceModel\Advertisementinfo;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Advertisementblock\Model\Advertisementinfo', 'Magebees\Advertisementblock\Model\ResourceModel\Advertisementinfo');
    }
}
