<?php

namespace Magebees\Advertisementblock\Model\ResourceModel\Advertisementimages;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Advertisementblock\Model\Advertisementimages', 'Magebees\Advertisementblock\Model\ResourceModel\Advertisementimages');
    }
}
