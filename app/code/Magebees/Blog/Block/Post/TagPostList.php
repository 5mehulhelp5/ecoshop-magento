<?php
namespace Magebees\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Blog\Model\Post;
use Magebees\Blog\Model\Category;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
class TagPostList extends \Magebees\Blog\Block\AbstractPostList {
    protected $postCollection;
    protected $_urlInterface;
    protected $_storemanager;
    protected $category_id;
    protected $currentCategory;
    protected $_parentcategoryurl = array();
    protected $_userFactory;
    protected function _prepareLayout() {
        //parent::_prepareLayout();
        if ($this->_registry->registry('current_blog_tag')):
            $this->currenttag = $this->_registry->registry('current_blog_tag');
            $title = $this->currenttag->getMetaTitle();
            if (!$title) {
                $title = $this->currenttag->getTitle();
            }
            $title = $this->currenttag->getTitle();
            $meta_keywords = $this->currenttag->getMetaKeywords();
            $meta_description = $this->currenttag->getMetaDescription();
        endif;
        if (!$title) {
            $title = $this->configuration->getConfig('blog/blogpage/title');
        }
        if (!$meta_keywords) {
            $meta_keywords = $this->configuration->getConfig('blog/blogpage/meta_keywords');
        }
        if (!$meta_description) {
            $meta_description = $this->configuration->getConfig('blog/blogpage/meta_description');
        }
        $configPagination = $this->configuration->getConfig('blog/post_list/pagination');
        $configPagination = explode(",", (string)$configPagination);
        $configPagination = array_combine($configPagination, $configPagination);
        if (isset($configPagination[0])) {
            $per_page_post = $configPagination[0];
        }
        $this->pageConfig->getTitle()->set(__($title)); // meta title
        $this->pageConfig->setKeywords(__($meta_keywords)); // meta keywords
        $this->pageConfig->setDescription(__($meta_description)); // meta de
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
        if ($this->getRequest()->getParam('tag_identifier')) {
            $tag_identifier = $this->getRequest()->getParam('tag_identifier');
            $tag_id = $this->currenttag->getTagId();
            $collection->addFieldToFilter('tag_ids', array('finset' => $tag_id));
        }
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }
	public function getDescription() {
		
        if ($this->currenttag):
            if ($this->currenttag->getTagId()):
                $tag_desc = $this->currenttag->getContent();
                return $this->_filterProvider->getBlockFilter()->setStoreId($this->_storemanager->getStore()->getId())->filter($tag_desc);
            endif;
        endif;
        return null;
    }
}

