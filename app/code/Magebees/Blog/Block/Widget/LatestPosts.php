<?php
namespace Magebees\Blog\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
class LatestPosts extends \Magebees\Blog\Block\AbstractPostList implements BlockInterface {
    //protected $_template = "widget/latestposts.phtml";
		public $template;
		public $wd_spacing;
		public $wd_bottom_spacing;
		public $wd_show_heading;
		public $wd_heading;
		public $wd_show_description;
		public $wd_description;
		public $wd_post_show_feature_image;
		public $feature_image_height;
		public $feature_image_width;
		public $resize_type;
		public $wd_post_type;
		public $wd_post_limit;
		public $wd_slider;
		public $wd_items_per_slide;
		public $wd_autoscroll;
		public $wd_auto_play_delaytime;
		public $wd_autoplayoff;
		public $wd_slide_auto_height;
		public $wd_navarrow;
		public $wd_pagination;
		public $wd_infinite_loop;
		public $wd_scrollbar;
		public $wd_grap_cursor;
		public $wd_sort_by;
		public $wd_category;
		public $wd_comment_count;
		public $wd_tags;
		public $wd_author;
		public $wd_add_this;
		public $wd_post_readmore;
		public $wd_post_show_view_all;
		public $store_id;
		public $wd_bgimage;
		public $wd_bgcolor;
		public $wd_items_per_row ;
		public $wd_post_content;
		public $wd_post_view_all_text;
		public $wd_post_show_view_all_url;
		public $wd_post_ids;
		
	protected function getCacheLifetime()
	{
		return 84600;
	}
	public function getCacheKeyInfo()
	{
		$name =   'magebees_blog_latest_post';
		$keyInfo     =  parent::getCacheKeyInfo();
		$keyInfo[]   =  $name; // adding the product id to the cache key so that each cache built remains unique as per the product page.
		
		return $keyInfo;
	}
	public function getPostCollection() {
        
		$storeId = $this->_storemanager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        $collection = $this->postCollection->getCollection();
		
		
        $collection->addFieldToFilter('is_active', array('eq' => 1));
		//$collection->addFieldToFilter('include_in_recent', array('eq' => 1));
		
		$collection->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
		$collection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $collection->addFieldToFilter('publish_time', ['lteq' => $now]);
		$collection->addFieldToFilter('save_as_draft', ['eq' => 0]);
		//$collection->setOrder('creation_time', 'ASC');
	   return $collection;
	   
		
    }
	public function getViewAllUrl($path){
		return	$this->_blogurl->getViewAllUrl($path);
	}
	protected function _beforeToHtml()
    {	
		$this->load_ajax = $this->getData('wd_load_ajax');
		if($this->load_ajax){
		$this->setTemplate('Magebees_PocoBase::load_blog_list.phtml');
		return parent::_beforeToHtml();
		}
	}
	public function _toHtml()
    {
		$this->load_ajax = $this->getData('wd_load_ajax');
		if (($this->getData('template'))&&(!$this->load_ajax)) {
            $this->setTemplate($this->getData('template'));
		}
		return parent::_toHtml();
    }
}

