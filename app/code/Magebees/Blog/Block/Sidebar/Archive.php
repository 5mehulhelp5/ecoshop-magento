<?php
namespace Magebees\Blog\Block\Sidebar;
class Archive extends \Magebees\Blog\Block\AbstractPost {
    public function getArchivePosts() {
        $collection = parent::getPostCollection();
        $post_archive = array();
        foreach ($collection as $post):
            $date = date_create($post->getCreationTime());
            $year = date_format($date, "Y");
            $month = date_format($date, "m");
            $archive_title = date_format($date, "F") . " " . date_format($date, "Y");
            //$archive_path = $year . "-" . $month;
			$archive_path = $year . "~" . $month;
			$key = $year . $month;
            if (!isset($post_archive[$key][$archive_title])) {
                $post_archive[$key][$archive_title]['title'] = $archive_title;
                $post_archive[$key][$archive_title]['url'] = $this->_blogurl->getBlogArchiveurl($archive_path);
            }
        endforeach;
		
		
        return $post_archive;
    }
}

