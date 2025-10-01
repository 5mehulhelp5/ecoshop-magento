<?php
namespace Magebees\Blog\Model;

class Category extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\ResourceModel\Category');
    }
}
