<?php
namespace Magebees\Blog\Model\ResourceModel\LikeDislike;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\LikeDislike', 'Magebees\Blog\Model\ResourceModel\LikeDislike');
    }
}
