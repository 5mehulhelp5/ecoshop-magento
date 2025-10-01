<?php
namespace Magebees\Blog\Model\ResourceModel;

class LikeDislike extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magebees_blog_post_like_dislike', 'like_dislike_id');
    }
}
