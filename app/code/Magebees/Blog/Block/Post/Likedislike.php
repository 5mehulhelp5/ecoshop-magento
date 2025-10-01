<?php
namespace Magebees\Blog\Block\Post;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
class Likedislike extends \Magebees\Blog\Block\AbstractPost {
   
    public function getLikeUrl() {
        return $this->getUrl("blog/post/vote");
    }
    public function getLikesCount($post_id) {
        $vote_collection = parent::getPostLikesDislikesCollection();
        $vote_collection->addFieldToFilter('post_id', array('eq' => $post_id));
        $vote_collection->addFieldToFilter('postlike', array('eq' => 1));
        $vote_collection->addFieldToFilter('postdislike', array('eq' => 0));
        return $vote_collection->getSize();
    }
    public function getDisLikesCount($post_id) {
        $vote_collection = parent::getPostLikesDislikesCollection();
        $vote_collection->addFieldToFilter('post_id', array('eq' => $post_id));
        $vote_collection->addFieldToFilter('postdislike', array('eq' => 1));
        $vote_collection->addFieldToFilter('postlike', array('eq' => 0));
        return $vote_collection->getSize();
    }
}

