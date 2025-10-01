<?php
namespace Magebees\Blog\Plugin;
class Topmenu
{
    /**
    * @param Context                                   $context
    * @param array                                     $data
    */
	protected $_blogcategorycollection;
	protected $_storeManager;
	protected $_parentcategoryurl = array();
	protected $_customerSession;
	protected $configuration;
	protected $_urlInterface;
	protected $_category;
	protected $_blogcategory;
	protected $_blogurl;
	protected $category_level;
	
    public function __construct(
        \Magento\Catalog\Model\Category $category,
		\Magebees\Blog\Model\Category $blogcategory,		
		\Magebees\Blog\Model\Url $blogurl,	
		\Magebees\Blog\Model\ResourceModel\Category\Collection $blogcategorycollection,
		\Magento\Store\Model\StoreManagerInterface $storeManager,     
		\Magento\Customer\Model\Session $customerSession,
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_category = $category;
		$this->_blogcategory = $blogcategory;
		$this->_blogurl = $blogurl;
		$this->_blogcategorycollection = $blogcategorycollection;
		$this->_storeManager = $storeManager;      
		$this->_customerSession = $customerSession;
		$this->configuration = $Configuration;
		$this->_urlInterface = $urlInterface;
    }


    public function afterGetHtml(\Magento\Theme\Block\Html\Topmenu $topmenu, $html)
    {
		
		$enable_blog_top_navigation = $this->configuration->getEnableMenuLink();
		if($enable_blog_top_navigation):
		$blog_route_key = $this->configuration->getBlogRoute();
		$title = $this->configuration->getConfig('blog/top_navigation/link_text');
		$include_categories = $this->configuration->getConfig('blog/top_navigation/include_categories');
		if(!$title)
		{
			$title = $this->configuration->getBlogTitle();
		}
		$categories = $this->_category->getCollection()->addAttributeToSelect('*')
							->addAttributeToFilter('level', 2)
							->addAttributeToFilter('include_in_menu', 1)
							->addAttributeToFilter('is_active', 1);
		
		
		$nav = count($categories->getData()) + 1;
		
        $blogUrl = $topmenu->getUrl($blog_route_key);//here you can set link
		$blogUrl = rtrim($blogUrl, '/');
		$storeId = $this->_storeManager->getStore()->getId();
		if($this->_customerSession->isLoggedIn()):
        	$customerGroupId=$this->_customerSession->getCustomer()->getGroupId();
    		else:
			$customerGroupId=0;
		endif;
		$blogcategories = $this->_blogcategory->getCollection();
		$blogcategories->addFieldToFilter('include_in_menu', 1);
		$blogcategories->addFieldToFilter('is_active', 1);
			
		$blogcategories->addFieldToFilter(['store_id','store_id'],[['eq' => 0],['eq'=>$storeId]]);
		$blogcategories->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
		$blogcategories->addFieldToFilter('parent_category_id', array('eq' => 0));
		$blogcategories->setOrder('position', 'asc');
		$has_child_class = null;
		if(($blogcategories->getSize()>0) && ($include_categories)):
		$has_child_class = 'parent';
		endif;
		
        $magentoCurrentUrl = $topmenu->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        if (strpos($magentoCurrentUrl,$blog_route_key) !== false) {
            $html .= "<li class=\"level0 nav-".$nav." active level-top ".$has_child_class." ui-menu-item\" role=\"presentation\">";
        } else {
            $html .= "<li class=\"level0 nav-".$nav." level-top ".$has_child_class." ui-menu-item\"  role=\"presentation\">";
        }
		
		
			
        $html .= "<a href=\"" . $blogUrl . "\" class=\"level-top ui-corner-all\"><span class=\"ui-menu-icon ui-icon ui-icon-carat-1-e\"  role=\"menuitem\"></span><span>" . __($title) . "</span></a>";
		if($include_categories):
		$blog_categories = $this->getBlogCategories($blog_route_key);
		$html .=$blog_categories;
		endif;
		$html .= "</li>";
		endif;
		
		return $html;
		
    }
	public function getBlogCategories($blog_route_key){
		
		$storeId = $this->_storeManager->getStore()->getId();
		$parentCategoryurl = $this->configuration->getConfig('blog/permalink/category_use_categories');
		$category_route = $this->configuration->getBlogCategoryRoute();
		$category_sufix = $this->configuration->getConfig('blog/permalink/category_sufix');
		if($this->_customerSession->isLoggedIn()):
        	$customerGroupId=$this->_customerSession->getCustomer()->getGroupId();
    		else:
			$customerGroupId=0;
		endif;
		
				$blogcategories = $this->_blogcategory->getCollection();
				$blogcategories->addFieldToFilter('include_in_menu', array('eq' => 1));
				$blogcategories->addFieldToFilter('is_active', array('eq' => 1));
				
				$blogcategories->addFieldToFilter(['store_id','store_id'],[['eq' => 0],['eq'=>$storeId]]);
				$blogcategories->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
				$blogcategories->addFieldToFilter('parent_category_id', array('eq' => 0));
				$blogcategories->setOrder('position', 'asc');
		$rootLevelBlogCategory = null;
		$count=0;
		if($blogcategories->getSize()>0):
		
		$rootLevelBlogCategory .= '<ul class="level0 submenu">';
		foreach($blogcategories as $parentcategory):
		
		$categoryTitle = $parentcategory->getTitle();
		$this->category_level = null;
		$level = $this->getlevel($parentcategory);
		$category_url = $this->_blogurl->getBlogCategoryurl($parentcategory);
		$categoryId = $parentcategory->getCategoryId();
		$has_child = $this->hasChildCategories($categoryId,$storeId,$customerGroupId);
		$maximun_depth = $this->configuration->getConfig('blog/top_navigation/maximum_depth');
		
		$has_child_class = null;
		if(($has_child) && ($level < $maximun_depth)):
		
		
		if($blogcategories->getSize()>0):
		$has_child_class = 'parent';
		endif;
		endif;
		
		
		$category_url = rtrim($category_url, '/');
		$menu_class = 'level'.$level." nav-".$level."-".++$count." ".$has_child_class." ui-menu-item";
		$rootLevelBlogCategory .= '<li class="'.$menu_class.'" role="presentation">';
		$rootLevelBlogCategory .= "<a href=\"" . $category_url . "\" class=\"level-top ui-corner-all\" role=\"menuitem\"><span class=\"ui-menu-icon ui-icon ui-icon-carat-1-e\"></span><span>" . __($categoryTitle) . "</span></a>";
		
		$categoryTitle;
		
		if(($has_child) && ($level < $maximun_depth)):
		
		$childLevelblogcategories = $this->getChildCategories($parentcategory,$level,$storeId,$customerGroupId,$blog_route_key,$parentCategoryurl,$category_route,$category_sufix);
		$rootLevelBlogCategory .= $childLevelblogcategories; 
		endif;
		$rootLevelBlogCategory .= '</li>';
		endforeach;
		$rootLevelBlogCategory .= '</ul>';
		endif;
		return $rootLevelBlogCategory;
	}
	
	public function getlevel($category, $level=0)
    {
       $p_id=$category->getParentCategoryId();
        $parent_category = $this->_blogcategory->load($p_id);
        if ($p_id!=0) {
            $this->category_level = $level+1;
            $this->getlevel($parent_category, $this->category_level);
        } else {
            $level = '1';
            return $level;
        }
        
        return $this->category_level;
    }
   
	public function hasChildCategories($categoryId,$storeId,$customerGroupId){
		$blogChildcategories = $this->_blogcategory->getCollection()->setOrder("position", "asc");
		$blogChildcategories->addFieldToFilter('include_in_menu', '1');
		$blogChildcategories->addFieldToFilter('is_active', '1');
		$blogChildcategories->addFieldToFilter(['store_id','store_id'],[['finset' => 0],['finset'=>$storeId]]);
		$blogChildcategories->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
		$blogChildcategories->addFieldToFilter('parent_category_id', $categoryId);
		return count($blogChildcategories->getData());
	}
	public function getChildCategories($category,$level,$storeId,$customerGroupId,$blog_route_key,$parentCategoryurl,$category_route,$category_sufix){
		$categoryId = $category->getCategoryId();
		$blogChildcategories = $this->_blogcategory->getCollection()->setOrder("position", "asc");
		$blogChildcategories->addFieldToFilter('include_in_menu', '1');
		$blogChildcategories->addFieldToFilter('is_active', '1');
		$blogChildcategories->addFieldToFilter(['store_id','store_id'],[['finset' => 0],['finset'=>$storeId]]);
		$blogChildcategories->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
		$blogChildcategories->addFieldToFilter('parent_category_id', $categoryId);
			$count=0;
		 $ChildLevelBlogCategory = isset($ChildLevelBlogCategory) ? $ChildLevelBlogCategory : '';
		
		
		$ChildLevelBlogCategory .= '<ul class="level'.$level.' submenu">';	
		foreach($blogChildcategories as $Category):
			
			$categoryTitle = $Category->getTitle();
			$level = $this->getlevel($Category,$level);
			
	
		$category_url = $this->_blogurl->getBlogCategoryurl($Category);
		$categoryId = $Category->getCategoryId();
		$has_child = $this->hasChildCategories($categoryId,$storeId,$customerGroupId);
		$maximun_depth = $this->configuration->getConfig('blog/top_navigation/maximum_depth');
		
		$has_child_class = null;
		if(($has_child) && ($level < $maximun_depth)):
		
		
		if($blogcategories->getSize()>0):
		$has_child_class = 'parent';
		endif;
		endif;
		
		
		$menu_class = 'level'.$level." nav-".$level."-".++$count." ".$has_child_class." ui-menu-item";
		$ChildLevelBlogCategory .= '<li class="'.$menu_class.'" role="presentation">';
		$ChildLevelBlogCategory .= "<a href=\"" . $category_url . "\" class=\"level-top ui-corner-all\" role=\"menuitem\"><span class=\"ui-menu-icon ui-icon ui-icon-carat-1-e\"></span><span>" . __($categoryTitle) . "</span></a>";
		
		
		$has_child = $this->hasChildCategories($categoryId,$storeId,$customerGroupId);
		
		$maximun_depth = $this->configuration->getConfig('blog/top_navigation/maximum_depth');
		
		if(($has_child) && ($level < $maximun_depth)):
		
		$childLevelblogcategories = $this->getChildCategories($Category,$level,$storeId,$customerGroupId,$blog_route_key,$parentCategoryurl,$category_route,$category_sufix);
		$ChildLevelBlogCategory .= $childLevelblogcategories; 
		endif;
		$ChildLevelBlogCategory .= '</li>';
			endforeach;
		$ChildLevelBlogCategory .= '</ul>';
		return $ChildLevelBlogCategory;
			
	}
	
	
	
}
