<?php
namespace Magebees\Blog\Model\ResourceModel\UrlRewrite;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magebees\Blog\Model\UrlRewrite', 'Magebees\Blog\Model\ResourceModel\UrlRewrite');
    }
}
