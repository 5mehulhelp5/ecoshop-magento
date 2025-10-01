<?php
namespace Magebees\Blog\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
class RecentPosts extends \Magebees\Blog\Block\AbstractPost implements BlockInterface {
    protected $_template = "widget/recentposts.phtml";
    public function getRecentPosts($recent_count) {
        $collection = parent::getPostCollection();
        $collection->addFieldToFilter('is_recent_posts_skip', array('eq' => 1));
        $collection->setPageSize($recent_count);
        return $collection;
    }
}

