<?php
namespace Magebees\Blog\Model\ResourceModel\Comment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\Comment', 'Magebees\Blog\Model\ResourceModel\Comment');
    }
}
