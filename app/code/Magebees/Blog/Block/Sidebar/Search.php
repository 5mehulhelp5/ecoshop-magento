<?php
namespace Magebees\Blog\Block\Sidebar;
class Search extends \Magebees\Blog\Block\AbstractPostList {
    public function getFormUrl() {
        $permalinkOptions = $this->configuration->getPermalinkSettings();
        $blog_route_key = $this->configuration->getBlogRoute()."/";
        $blog_search_route = $permalinkOptions['search_route'];
        if (!$blog_search_route):
            $blog_search_route = 'search';
        endif;
		
        return $this->_urlInterface->getUrl($blog_route_key) . $blog_search_route;
    }
    public function getSearchQuery() {
        return $search_query = ($this->getRequest()->getParam('search_query')) ? $this->getRequest()->getParam('search_query') : '';
    }
}

