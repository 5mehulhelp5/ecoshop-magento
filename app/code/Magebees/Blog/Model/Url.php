<?php
namespace Magebees\Blog\Model;
class Url extends \Magento\Framework\Model\AbstractModel
{
	public $_parentcategoryurl = array();
	protected $_urlInterface;
	public $blog_route_key;
	public $category_route;
	public $category_sufix;
	public $parentCategoryurl;
	protected $_registry;
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magebees\Blog\Model\PostFactory $postFactory,
		\Magebees\Blog\Model\CategoryFactory $categoryFactory,
		\Magebees\Blog\Model\TagFactory $tagFactory,
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\Framework\UrlInterface $urlInterface,
array $data = []
    ) {
		$this->request = $request;
        $this->_storeManager = $storeManager;
		$this->_registry = $registry;
		$this->_blogpostFactory = $postFactory;
		$this->_blogcategoryFactory = $categoryFactory;
		$this->_blogtagFactory = $tagFactory;
		$this->_blogConfiguration = $Configuration;
		
		$this->_urlInterface = $urlInterface;
		$this->blog_route_key = $this->_blogConfiguration->getBlogRoute();
		$this->category_route = $this->_blogConfiguration->getBlogCategoryRoute();
		$this->category_sufix = $this->_blogConfiguration->getConfig('blog/permalink/category_sufix');
		$this->post_sufix = $this->_blogConfiguration->getConfig('blog/permalink/post_sufix');
		$this->parentCategoryurl = $this->_blogConfiguration->getConfig('blog/permalink/category_use_categories');
		$this->blog_post_route = $this->_blogConfiguration->getBlogPostRoute();
		$this->blog_archive_route = $this->_blogConfiguration->getBlogArchiveRoute();
		$this->blog_search_route = $this->_blogConfiguration->getBlogSearchRoute();
		$this->blog_tag_route = $this->_blogConfiguration->getBlogTagRoute();
		$this->blog_author_route = $this->_blogConfiguration->getBlogAuthorRoute();
		$this->tag_suffix = $this->_blogConfiguration->getBlogTagSuffix();
		$this->author_suffix = $this->_blogConfiguration->getBlogAuthorSuffix();
		$this->config_category_link_type = $this->_blogConfiguration->getConfig('blog/permalink/cateogry_permalink_type');
		$this->config_post_link_type = $this->_blogConfiguration->getConfig('blog/permalink/type');
		$this->config_archive_link_type = $this->_blogConfiguration->getConfig('blog/permalink/archive_url_type');
		$this->config_tag_link_type = $this->_blogConfiguration->getConfig('blog/permalink/tag_url_type');
		$this->config_author_link_type = $this->_blogConfiguration->getConfig('blog/permalink/author_url_type');
		$this->post_use_categories	 = $this->_blogConfiguration->getConfig('blog/permalink/post_use_categories');
		
		parent::_construct();
    }
	
	public function getViewAllUrl($path)
	{
		return $_url = $this->_urlInterface->getUrl().$path;
	}
	public function getBlogCategoryurl($category)
    {
		
		$p_id=$category->getParentCategoryId();
		$identifier = $category->getIdentifier();
		if($this->parentCategoryurl)
		{
		
			if ($p_id!=0) {
			$parent_category = $this->_blogcategoryFactory->Create()->load($p_id);
			$this->_parentcategoryurl[] = $identifier;
            return $this->getBlogCategoryurl($parent_category);
        	} 
			else 
			{
			$this->_parentcategoryurl[] = $identifier;
			if($this->config_category_link_type=='default')
			{
				$this->_parentcategoryurl[] = $this->category_route;
				$this->_parentcategoryurl[] = $this->blog_route_key;
				
			
			}else if($this->config_category_link_type=='short')
			{
				$this->_parentcategoryurl[] = $this->blog_route_key;
			}
			$parent_url = array_reverse($this->_parentcategoryurl);	
			$category_path = implode('/', (array)$parent_url);
				
			if($this->category_sufix){
			$category_path .= $this->category_sufix;
			}
				$category_url = $this->_urlInterface->getUrl().$category_path;
				$category_url = rtrim($category_url, '/');
				$this->_parentcategoryurl = array();
        		return $category_url;
			}
		}else{
			
			$category_url = array();
			if($this->config_category_link_type=='default')
			{
				
				$category_url[] = $this->blog_route_key;
				$category_url[] = $this->category_route;
			
			}else if($this->config_category_link_type=='short')
			{
				$category_url[] = $this->blog_route_key;
			}
			if($this->category_sufix){
			$category_identifier = $identifier.$this->category_sufix;
			}
			$category_url[] = $category_identifier;
			$category_path = implode('/', (array)$category_url);
			$category_url = $this->_urlInterface->getUrl().$category_path;
			$category_url = rtrim($category_url, '/');
			$this->_parentcategoryurl = array();
        	return $category_url;
		}
        
		
       }
		public function getBlogPosturl($post,$section)
		{
			
			$identifier = trim($this->request->getOriginalPathInfo(), '/');
			$pathInfo = explode('/', (string)$identifier);
			
			if (($key = array_search($this->blog_route_key, $pathInfo)) !== FALSE) {
			  	/* Remove Blog route */
				unset($pathInfo[$key]);
			}
			if (($key = array_search('blog', $pathInfo)) !== FALSE) {
			  	/* Remove Blog route */
				unset($pathInfo[$key]);
			}
			if (($key = array_search($this->category_route, $pathInfo)) !== FALSE) {
			  	/* Remove Blog Category route */
				unset($pathInfo[$key]);
			}
			if (($key = array_search($this->blog_search_route, $pathInfo)) !== FALSE) {
			  	
				/* Remove Blog Search route */
				
				$next_key = $key + 1;
				if(isset($pathInfo[$key]))
				{
				unset($pathInfo[$next_key]);
				}
				unset($pathInfo[$key]);
			}
			if (($key = array_search($this->blog_post_route, $pathInfo)) !== FALSE) {
			  	/* Remove Blog Post route */
				unset($pathInfo[$key]);
			}
			if (($key = array_search($this->blog_archive_route, $pathInfo)) !== FALSE) {
			  	/* Remove Blog Archive route */
				unset($pathInfo[$key]);
			}
			if (($key = array_search($this->blog_tag_route, $pathInfo)) !== FALSE) {
			  	/* Remove Blog Archive route */
				unset($pathInfo[$key]);
			}
			if (($key = array_search($this->blog_author_route, $pathInfo)) !== FALSE) {
			  	/* Remove Blog Archive route */
				unset($pathInfo[$key]);
			}
			
			if($this->_registry->registry('current_blog_author')):
					$last_path = end($pathInfo);
					if (($key = array_search($last_path, $pathInfo)) !== FALSE) {
					$last_path_arr = explode(".",(string)$last_path);
						if(isset($last_path_arr[0]))
						{
						$pathInfo[$key] = $last_path_arr[0];}
								
					
					}
					
			endif;
		
			
			if($this->_registry->registry('current_blog_category')):
			$last_path = end($pathInfo);
			if (($key = array_search($last_path, $pathInfo)) !== FALSE) {
			  	/* Remove Category Suffix From the Category URL */
					$pathInfo[$key] = str_replace((string)$this->category_sufix,"",$last_path);
				}
			endif;
			if($this->_registry->registry('current_blog_post')):
				$last_path = end($pathInfo);
				if (($key = array_search($last_path, $pathInfo)) !== FALSE) {
			  		unset($pathInfo[$key]);
				
			}
			endif;
			
			if($this->_registry->registry('current_blog_tag')):
			$last_path = end($pathInfo);
			if (($key = array_search($last_path, $pathInfo)) !== FALSE) {
			  	/* Remove Tag From the URL */
					//unset($pathInfo[$key]);
					//$pathInfo[$key] = str_replace($this->tag_suffix,"",$last_path);
					unset($pathInfo[$key]);
				}
			endif;
			
			if(($this->request->getControllerName()=='archive') && ($this->request->getParams('archive_id')))
			{
				$last_path = end($pathInfo);
				if (($key = array_search($last_path, $pathInfo)) !== FALSE) {
			  	/* Remove Archive Id From the Path Info */
					unset($pathInfo[$key]);
				
				}

			}
			
			
			$post_identifier = $post->getIdentifier();
			$cat_path = null;
			if(count($pathInfo)>0)
			{
			$cat_path = implode("/",(array)$pathInfo);
			}
			if(($section=='recent_post') || ($section=='related_post') || ($section=='next_prev'))
			{
			
			$cat_path = null;
			}
			
			if(!$cat_path)
			{
			$category_ids = explode(",",(string)$post->getCategoryIds());
			if(isset($category_ids[0]))
			{
				$post_cat_id = $category_ids[0];
				$postCategory = $this->_blogcategoryFactory->Create()->load($post_cat_id);
				$cat_path = $postCategory->getIdentifier();
			}
				
			}
			
			
			
			$post_path = array();
			if($this->config_post_link_type=='default')
			{
			$post_path[] = $this->blog_route_key;
			
				if(($this->post_use_categories)):
					$post_path[] = $cat_path;
				endif;
			$post_path[] = $this->blog_post_route;
			
			}else if($this->config_post_link_type=='short')
			{ 
				$post_path[] = $this->blog_route_key;
				if(($this->post_use_categories)):
					$post_path[] = $cat_path;
				endif;
			}
			
			if($this->post_sufix){
			$post_identifier = $post_identifier.$this->post_sufix;
			}	
			$post_path[] = $post_identifier;
			if (($key = array_search('index/ajaxload', $post_path)) !== false) {
			
			unset($post_path[$key]);
			}
			
			$post_path = array_filter($post_path);
			$post_path = implode("/",(array)$post_path);
			$post_url = $this->_urlInterface->getUrl().$post_path;
			$post_url = rtrim($post_url, '/');
			
        	return $post_url;
		}
		public function getBlogArchiveurl($identifier)
		{
			$archive_url = array();
			if($this->config_archive_link_type=='default'){
				$archive_url[] = $this->blog_route_key;
				$archive_url[] = $this->blog_archive_route;
				
			}elseif($this->config_archive_link_type=='short'){
				$archive_url[] = $this->blog_archive_route;
			}	
			$archive_url[] = $identifier;
			$archive_path =  implode("/",(array)$archive_url);
			$archive_url = $this->_urlInterface->getUrl().$archive_path;
			return $archive_url = rtrim($archive_url, '/');
		}
		public function getBlogTagurl($tag)
		{
		
			$tag_url = array();
			if($this->config_tag_link_type=='default'){
				$tag_url[] = $this->blog_route_key;
				$tag_url[] = $this->blog_tag_route;
				
			}elseif($this->config_tag_link_type=='short'){
				$tag_url[] = $this->blog_tag_route;
			}	
			if($this->tag_suffix)
			{
				$tag_url[] = $tag->getIdentifier().$this->tag_suffix;
			}else{
				$tag_url[] = $tag->getIdentifier();
			} 
			$tag_path =  implode("/",(array)$tag_url);
			$tag_url = $this->_urlInterface->getUrl().$tag_path;
			return $tag_url = rtrim($tag_url, '/');
		}
		public function getBlogAuthorurl($fname,$lname)
			{
				$author_url = array();
				if($this->config_author_link_type=='default'){
					$author_url[] = $this->blog_route_key;
					$author_url[] = $this->blog_author_route;

				}elseif($this->config_author_link_type=='short'){
					$author_url[] = $this->blog_author_route;
				}
				$author_name = $fname."-".$lname;
				if($this->author_suffix)
				{
					$author_url[] = $author_name.$this->author_suffix;
				}else{
					$author_url[] = $author_name;
				} 
			
				$author_path =  implode("/",(array)$author_url);
				$author_url = $this->_urlInterface->getUrl().$author_path;
				return $author_url = rtrim($author_url, '/');
			}

}

