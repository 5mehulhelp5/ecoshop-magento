<?php
namespace Magebees\Blog\Model\ResourceModel\Tag;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\Tag', 'Magebees\Blog\Model\ResourceModel\Tag');
    }
}
