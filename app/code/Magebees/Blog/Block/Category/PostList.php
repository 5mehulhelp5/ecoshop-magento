<?php
namespace Magebees\Blog\Block\Category;
class PostList extends \Magebees\Blog\Block\AbstractPostList {
    protected function _prepareLayout() {
		
       // parent::_prepareLayout();
        if ($this->_registry->registry('current_blog_category')):
            $this->_currentCategory = $this->_registry->registry('current_blog_category');
            $this->category_id = $this->_currentCategory->getCategoryId();
            $title = $this->_currentCategory->getMetaTitle();
            if (!$title) {
                $title = $this->_currentCategory->getTitle();
            }
            $meta_keywords = $this->_currentCategory->getMetaKeywords();
            $meta_description = $this->_currentCategory->getMetaDescription();
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
        if ($this->category_id) {
			
			  $collection->addFieldToFilter('category_ids', array('finset' => $this->category_id));
           
			        
        }
		$sort_order = $this->getPostSortOrder();
		if ($sort_order == 1) {
            $collection->setOrder('position', 'asc');
		} elseif ($sort_order == 2) {
            $collection->setOrder('title', 'asc');
        } elseif ($sort_order == 3) {
			  $collection->getSelect()->order('rand()');
		}else if(!$sort_order){
        
			$collection->setOrder('creation_time', 'asc');
			
		}
		$collection->setPageSize($pageSize);
        $collection->setCurPage($page);
		return $collection;
    }
    public function getDescription() {
        if ($this->_currentCategory):
            if ($this->_currentCategory->getCategoryId()):
                $category_desc = $this->_currentCategory->getContent();
				if($category_desc):
                return $this->_filterProvider->getBlockFilter()->setStoreId($this->_storemanager->getStore()->getId())->filter($category_desc);
				else:
				return null;
				endif;
			endif;
        endif;
        return null;
    }
    public function getDisplayMode() {
        if ($this->_currentCategory):
            if ($this->_currentCategory->getCategoryId()):
                return $this->_currentCategory->getDisplayMode();
            endif;
        endif;
        return null;
    }
}

