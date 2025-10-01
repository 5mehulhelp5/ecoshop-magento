<?php
namespace Magebees\Blog\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\Category', 'Magebees\Blog\Model\ResourceModel\Category');
    }
}
