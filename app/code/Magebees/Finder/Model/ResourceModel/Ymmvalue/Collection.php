<?php
namespace Magebees\Finder\Model\ResourceModel\Ymmvalue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Finder\Model\Ymmvalue', 'Magebees\Finder\Model\ResourceModel\Ymmvalue');
    }
}
