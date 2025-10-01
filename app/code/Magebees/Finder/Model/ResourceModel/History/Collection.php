<?php
namespace Magebees\Finder\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magebees\Finder\Model\History', 'Magebees\Finder\Model\ResourceModel\History');
    }
}
