<?php
namespace Magebees\Blog\Block\Sidebar;
class Recentcomments extends \Magebees\Blog\Block\AbstractPostList {
    public function getRecentComments() {
		$collection = parent::getCommentCollection();
		$collection->setPageSize(3);
        return $collection;
    }
	public function getShortComment($content){
		$content_length = 20;
		if (str_word_count((string)$content, 0) > $content_length) {
            $words = str_word_count((string)$content, 2);
            $pos = array_keys($words);
            $content = substr($content, 0, $pos[$content_length]).'...';
        }
        return $content;
	}
	
}

