<?php
namespace Magebees\Blog\Block\Post;
class Relatedpost extends \Magebees\Blog\Block\AbstractPost {
    public function getRelatedPosts($post_ids) {
        $size = $this->relatedPostSize();
        $collection = parent::getPostCollection();
        $collection->addFieldToFilter('post_id', array('in' => $post_ids));
        if ($size):
            $collection->setPageSize($size);
        endif;
        return $collection;
    }
    public function getRelatedPosturl($post) {
        $section = 'related_post';
        return $this->_blogurl->getBlogPosturl($post, $section);
    }
    public function enableRelatedPost() {
		$related_post = $this->configuration->getConfig('blog/post_view/related_post/enable');
		$module_enable = $this->configuration->getConfig('blog/general/module_enable_disable');
        return $related_post * $module_enable;
		
    }
    public function relatedPostSize() {
        return $this->configuration->getConfig('blog/post_view/related_post/count');
    }
}

