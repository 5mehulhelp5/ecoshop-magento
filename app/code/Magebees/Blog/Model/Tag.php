<?php
namespace Magebees\Blog\Model;

class Tag extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\ResourceModel\Tag');
    }
}
