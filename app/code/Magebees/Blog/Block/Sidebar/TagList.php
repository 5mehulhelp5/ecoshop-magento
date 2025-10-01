<?php
namespace Magebees\Blog\Block\Sidebar;
class TagList extends \Magebees\Blog\Block\AbstractPostList {
    public function getTagbyId($tagId) {
        return $this->_blogtag->load($tagId);
    }
    public function getTagurl($_tag) {
        return $this->_blogurl->getBlogTagurl($_tag);
    }
    public function getRecentTagsIds() {
        $tagCollection = parent::getTagCollection();
        $tag_arr = $tagCollection->getData();
        $tag_ids = array_column($tag_arr, 'tag_id');
        $tag_post_count = array();
        foreach ($tag_ids as $id):
            $collection = parent::getPostCollection();
            $collection->addFieldToFilter('tag_ids', array('finset' => $id));
            if ($collection->getSize() > 0) {
                $tag_post_count[$id] = $collection->getSize();
            }
        endforeach;
        return $tag_post_count;
    }
}

