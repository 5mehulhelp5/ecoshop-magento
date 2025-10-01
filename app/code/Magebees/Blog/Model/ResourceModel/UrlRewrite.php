<?php
namespace Magebees\Blog\Model\ResourceModel;

class UrlRewrite extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magebees_blog_url_rewrite', 'url_id');
    }
}
