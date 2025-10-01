<?php
namespace Magebees\Pagebanner\Model\ResourceModel\Pagebanner;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Pagebanner\Model\Pagebanner', 'Magebees\Pagebanner\Model\ResourceModel\Pagebanner');
    }
}
