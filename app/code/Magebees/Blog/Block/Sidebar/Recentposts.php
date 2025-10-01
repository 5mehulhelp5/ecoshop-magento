<?php
namespace Magebees\Blog\Block\Sidebar;
class Recentposts extends \Magebees\Blog\Block\AbstractPostList {
    public function getRecentPosts() {
		
        $collection = parent::getPostCollection();
        $collection->addFieldToFilter('is_recent_posts_skip', array('eq' => 1));
		$collection->setPageSize(5);
		
        return $collection;
    }
	
}

