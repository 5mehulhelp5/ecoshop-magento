<?php
namespace Magebees\Blog\Block\Post;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
class NextPrevLink extends \Magebees\Blog\Block\AbstractPost {
    public function enablePostLinks() {
        return $this->configuration->getConfig('blog/post_view/next_previous_link/enable');
    }
    public function getNextpost($postId, $postCollection) {
        $post_key = array_search($postId, array_column($postCollection, 'post_id'));
        $nextpost_key = $post_key + 1;
        if (isset($postCollection[$nextpost_key])) {
            $nextPostId = $postCollection[$nextpost_key]['post_id'];
            $nextPost = $this->postfactory->create()->load($nextPostId);
            return $nextPost;
        }
        return null;
    }
    public function getPostCollection() {
        $collection = parent::getPostCollection();
        $postCollection = $collection->getData();
        return $postCollection;
    }
    public function getPrevpost($postId, $postCollection) {
        $post_key = array_search($postId, array_column($postCollection, 'post_id'));
        $prevpost_key = $post_key - 1;
        if (isset($postCollection[$prevpost_key])) {
            $prevPostId = $postCollection[$prevpost_key]['post_id'];
            $prevPost = $this->postfactory->create()->load($prevPostId);
            return $prevPost;
        }
        return null;
    }
    public function getPostLink($post) {
        $section = 'next_prev';
        return $this->_blogurl->getBlogPosturl($post, $section);
    }
}

