<?php
namespace Magebees\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Blog\Model\Post;
use Magebees\Blog\Model\Category;
class Postlist extends \Magebees\Blog\Block\AbstractPostList {
    protected $postCollection;
    protected $_urlInterface;
    protected $_storemanager;
    protected $category_id;
    protected $currentCategory;
    protected $_parentcategoryurl = array();
    protected $_userFactory;
    protected function _prepareLayout() {
        $title = $this->configuration->getConfig('blog/blogpage/title');
        $meta_keywords = $this->configuration->getConfig('blog/blogpage/meta_keywords');
        $meta_description = $this->configuration->getConfig('blog/blogpage/meta_description');
        $meta_robots = $this->configuration->getConfig('blog/blogpage/robots');
        $configPagination = $this->configuration->getConfig('blog/post_list/pagination');
        $configPagination = explode(",", (string)$configPagination);
        $configPagination = array_combine($configPagination, $configPagination);
        if (isset($configPagination[0])) {
            $per_page_post = $configPagination[0];
        }
        $this->pageConfig->getTitle()->set(__($title)); // meta title
        $this->pageConfig->setKeywords(__($meta_keywords)); // meta keywords
        $this->pageConfig->setDescription(__($meta_description)); // meta description
    
        $robots_option_arr = $this->_robots->toOptionArray();
        $collection = $this->getPostCollection();
        if ($collection) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'blog.post.pager')
				->setAvailableLimit($configPagination)
				->setShowPerPage(true)
				->setCollection($collection);
			
            $this->setChild('pager', $pager);

            $collection->load();
        }
        return $this;
        //return parent::_prepareLayout();
        
    }
    public function getPostCollection() {
        $collection = parent::getPostCollection();
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $limit = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 1;
        $configPagination = $this->configuration->getConfig('blog/post_list/pagination');
        $configPagination = explode(",", (string)$configPagination);
        if (isset($configPagination[0])) {
            $per_page_post = $configPagination[0];
        }
        if (!$per_page_post):
            $per_page_post = 5;
        endif;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : $per_page_post;
        $display_mode = $this->configuration->getConfig('blog/blogpage/display_mode');
        $featured_post_ids = array();
        if ($display_mode == 1) {
            $post_ids = $this->configuration->getConfig('blog/blogpage/post_ids');
            $collection->addFieldToFilter('post_id', array('in' => $post_ids));
        }
		
		
        $sort_order = $this->getPostSortOrder();
		
		if ($sort_order == 1) {
		    $collection->setOrder('position', 'DESC');
		} elseif ($sort_order == 2) {
            $collection->setOrder('title', 'DESC');
		} elseif ($sort_order == 3) {
			$collection->getSelect()->orderRand();
		}else if(!$sort_order){
			$collection->setOrder('creation_time', 'DESC');
			
        }
		$collection->setPageSize($pageSize);
        $collection->setCurPage($page);
		
        return $collection->load();
    }
}

