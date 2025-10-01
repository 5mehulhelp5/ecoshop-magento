<?php
namespace Magebees\Finder\Model\ResourceModel\UniversalProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Finder\Model\UniversalProduct', 'Magebees\Finder\Model\ResourceModel\UniversalProduct');
    }
}
