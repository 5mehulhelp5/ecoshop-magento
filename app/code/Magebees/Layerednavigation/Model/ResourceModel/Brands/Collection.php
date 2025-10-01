<?php
namespace Magebees\Layerednavigation\Model\ResourceModel\Brands;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Layerednavigation\Model\Brands', 'Magebees\Layerednavigation\Model\ResourceModel\Brands');
    }
}
