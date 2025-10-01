<?php
namespace Magebees\Productlisting\Model\ResourceModel\SelectProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Productlisting\Model\SelectProduct', 'Magebees\Productlisting\Model\ResourceModel\SelectProduct');
    }
}
