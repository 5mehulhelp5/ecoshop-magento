<?php
namespace Magebees\Blog\Block\Post;
class ArchivePostList extends \Magebees\Blog\Block\AbstractPostList {
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if ($this->getRequest()->getParam('archive_id')) {
            $archive_id = $this->getRequest()->getParam('archive_id');
            $archive_info = explode("~", (string)$archive_id);
            $year = $archive_info[0];
            $month = $archive_info[1];
            $archive_query_date = $year . "-" . $month . "-" . "01";
            $date = date_create($archive_query_date);
            $archive_title = date_format($date, "F") . " " . date_format($date, "Y");
            $title = 'Monthly Archives: "' . $archive_title . '"';
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
        if ($this->getRequest()->getParam('archive_id')) {
            $archive_id = $this->getRequest()->getParam('archive_id');
            $archive_info = explode("~",(string)$archive_id);
            $year = $archive_info[0];
            $month = $archive_info[1];
            $first_day_this_month = date($year . '-' . $month . '-01');
            $firstDayThisMonth = new \DateTime($first_day_this_month);
            $lastDayThisMonth = new \DateTime($firstDayThisMonth->format('Y-m-t'));
            $lastDayThisMonth->setTime(23, 59, 59);
            $start_date = $firstDayThisMonth->format("Y-m-d H:i:s");
            $end_date = $lastDayThisMonth->format("Y-m-d H:i:s");
            $start_date = $archive_info[0] . '-' . $archive_info[1] . '-01 00:00:00';
            $end_date = $archive_info[0] . '-' . $archive_info[1] . '-30 00:00:00';
            $collection->addFieldToFilter('creation_time', ['gteq' => $start_date]);
            $collection->addFieldToFilter('creation_time', ['lteq' => $end_date]);
        }
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }
}

