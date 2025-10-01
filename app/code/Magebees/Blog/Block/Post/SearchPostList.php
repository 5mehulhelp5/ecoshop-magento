<?php
namespace Magebees\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Blog\Model\Post;
use Magebees\Blog\Model\Category;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
class SearchPostList extends \Magebees\Blog\Block\AbstractPostList {
    protected $postCollection;
    protected $_urlInterface;
    protected $_storemanager;
    protected $category_id;
    protected $currentCategory;
    protected $_parentcategoryurl = array();
    protected $_userFactory;
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if ($this->getRequest()->getParam('search_query')) {
            $search_query_string = $this->getRequest()->getParam('search_query');
            $title = 'Search "' . $search_query_string . '"';
        } else {
            $title = $this->configuration->getConfig('blog/blogpage/title');
        }
        $meta_keywords = $this->configuration->getConfig('blog/blogpage/meta_keywords');
        $meta_description = $this->configuration->getConfig('blog/blogpage/meta_description');
        $configPagination = $this->configuration->getConfig('blog/post_list/pagination');
        $configPagination = explode(",", (string)$configPagination);
        $configPagination = array_combine($configPagination, $configPagination);
        if (isset($configPagination[0])) {
            $per_page_post = $configPagination[0];
        }
        $this->pageConfig->getTitle()->set(__($title)); // meta title
        $this->pageConfig->setKeywords(__($meta_keywords)); // meta keywords
        $this->pageConfig->setDescription(__($meta_description)); // meta description
        $collection = $this->getPostCollection();
        if ($collection) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'blog.post.pager')->setAvailableLimit($configPagination)->setShowPerPage(true)->setCollection($collection);
            $this->setChild('pager', $pager);
            $collection->load();
        }
        return $this;
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
        if ($this->getRequest()->getParam('search_query')) {
            $display_mode = $this->configuration->getConfig('blog/blogpage/display_mode');
            $search_term = trim($this->getRequest()->getParam('search_query'));
            $collection->addFieldToFilter(['title', 'content_heading', 'content'], [['like' => '%' . $search_term . '%'], ['like' => '%' . $search_term . '%'], ['like' => '%' . $search_term . '%']]);
        }
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }
}
