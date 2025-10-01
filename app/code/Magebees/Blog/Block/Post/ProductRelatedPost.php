<?php
namespace Magebees\Blog\Block\Post;
class ProductRelatedPost extends \Magebees\Blog\Block\Post\Postlist {
    public function getProductRelatedPosts($product_id) {
        $size = $this->relatedPostSize();
        $storeId = $this->_storemanager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        $collection = $this->postCollection->getCollection();
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        $collection->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
        $collection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $collection->addFieldToFilter('publish_time', ['lteq' => $now]);
        $collection->addFieldToFilter('save_as_draft', ['eq' => 0]);
        $collection->addFieldToFilter('products_id', array('finset' => $product_id));
        if($size):
        $collection->setPageSize($size);
        endif;
        return $collection;
    }
    public function getRelatedPosturl($post) {
        $section = 'related_post';
        return $this->_blogurl->getBlogPosturl($post, $section);
    }
        
    public function relatedPostSize() {
        return $this->configuration->getConfig('blog/product/related_post/count');
    } 
    public function enableRelatedPost() {
		$related_post = $this->configuration->getConfig('blog/product/related_post/enable');
		$module_enable = $this->configuration->getConfig('blog/general/module_enable_disable');
        return $related_post * $module_enable;
	}
    public function imageHeight() {
        return $this->configuration->getConfig('blog/product/related_post/feature_image_height');
    }
    public function imageWidth() {
        return $this->configuration->getConfig('blog/product/related_post/feature_image_width');
    }
	public function imageResizeType() {
        return $this->configuration->getConfig('blog/product/related_post/resize_type');
    }
	
}

