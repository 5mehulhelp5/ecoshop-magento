<?php
namespace Magebees\Blog\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\Post', 'Magebees\Blog\Model\ResourceModel\Post');
    }
}
