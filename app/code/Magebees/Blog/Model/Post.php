<?php
namespace Magebees\Blog\Model;

class Post extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\ResourceModel\Post');
    }
}
