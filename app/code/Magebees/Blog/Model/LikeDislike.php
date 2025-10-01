<?php
namespace Magebees\Blog\Model;

class LikeDislike extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\ResourceModel\LikeDislike');
    }
}
