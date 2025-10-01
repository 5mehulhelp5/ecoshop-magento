<?php
namespace Magebees\Blog\Block\Sidebar;
class Category extends \Magebees\Blog\Block\AbstractPost {
    public function getTitle()
	{
		return 'Categories';
	}
	public function getBlogCategories() {
        $blogcategories = parent::getCategoryCollection();
        $blogcategories->addFieldToFilter('include_in_menu', array('eq' => 1));
        return $blogcategories->getSize();
    }
    public function getBlogCategoriesTree() {
        $blog_route_key = $this->configuration->getBlogRoute();
        $blog_route_title = $this->configuration->getBlogTitle();
        $permalinkOptions = $this->configuration->getPermalinkSettings();
        $blog_search_route = $this->configuration->getBlogSearchRoute();
        $category_sufix = $permalinkOptions['category_sufix'];
        $parentCategoryurl = $permalinkOptions['category_use_categories'];;
        $category_route = $this->configuration->getBlogCategoryRoute();
        $blogcategories = parent::getCategoryCollection();
        $blogcategories->addFieldToFilter('include_in_menu', array('eq' => 1));
        $blogcategories->addFieldToFilter('parent_category_id', array('eq' => 0));
        $blogcategories->setOrder('position', 'asc');
        $blog_cat_html = '<ul class="sidebar-blog-category-tree">';
        foreach ($blogcategories as $category):
            $categoryId = $category->getCategoryId();
            $postCount = $this->getCategoryPostCount($categoryId);
            $category_url = $this->_blogurl->getBlogCategoryurl($category);
          
            $blog_cat_html.= '<li class="blog-category-item"> <a href="' . $category_url . '">' . $category->getTitle();
            $blog_cat_html.= '<span> ( ' . $postCount . ' ) </span></a>';
            if ($this->hasChildCategories($categoryId)) {
                $blog_child_cat_html = $this->getChildCategory($categoryId, $blog_route_key, $category_sufix, $parentCategoryurl, $category_route);
                $blog_cat_html.= $blog_child_cat_html;
            }
            $blog_cat_html.= '</li>';
        endforeach;
        $blog_cat_html.= '</ul>';
        return $blog_cat_html;
    }
    public function getChildCategory($categoryId, $blog_route_key, $category_sufix, $parentCategoryurl, $category_route) {
        $blogcategories = parent::getCategoryCollection();
        $blogcategories->addFieldToFilter('include_in_menu', array('eq' => 1));
        $blogcategories->addFieldToFilter('parent_category_id', array('eq' => $categoryId));
        $blog_child_cat_html = isset($blog_child_cat_html) ? $blog_child_cat_html : '';
        if (count($blogcategories->getData()) > 0) {
            $blog_child_cat_html.= '<ul class="blog-category has-child">';
        } else {
            $blog_child_cat_html.= '<ul class="blog-category">';
        }
        foreach ($blogcategories as $category):
            $categoryId = $category->getCategoryId();
            $postCount = $this->getCategoryPostCount($categoryId);
          
            $category_url = $this->_blogurl->getBlogCategoryurl($category);
            $blog_child_cat_html.= '<li class="blog-category-item"> <a href="' . $category_url . '">' . $category->getTitle();
            $blog_child_cat_html.= '<span> ( ' . $postCount . ' ) </span></a>';
            if ($this->hasChildCategories($categoryId)) {
                $blog_cat_html = $this->getChildCategory($categoryId, $blog_route_key, $category_sufix, $parentCategoryurl, $category_route);
                $blog_child_cat_html.= $blog_cat_html;
            }
            $blog_child_cat_html.= '</li>';
        endforeach;
        $blog_child_cat_html.= '</ul>';
        return $blog_child_cat_html;
    }
    public function getCategoryPostCount($categoryId) {
        $collection = parent::getPostCollection();
        $collection->addFieldToFilter('category_ids', array('finset' => $categoryId));
        return count($collection->getData());
    }
    public function hasChildCategories($categoryId) {
        $blogChildcategories = parent::getCategoryCollection();
        $blogChildcategories->addFieldToFilter('include_in_menu', '1');
        $blogChildcategories->addFieldToFilter('parent_category_id', $categoryId);
        return count($blogChildcategories->getData());
    }
}

