<?php
namespace  Magebees\Blog\Helper;
class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{	
	protected $_mediaDirectory;
	protected $_urlInterface;
	protected $_category;
	protected $_post;
	protected $_blogcategory;
	protected $_blogcategorycollection;
	protected $userCollectionFactory;
	protected $_storeManager;
	const BLOG_ENABLE = 'blog/general/module_enable_disable';
	const BLOG_PERMALINK_ROUTE = 'blog/permalink/route';
	const BLOG_TITLE = 'blog/blogpage/title';
	const BLOG_CATEGORY_ROUTE = 'blog/permalink/category_route';
	const BLOG_CATEGORY_LINK_TYPE = 'blog/permalink/cateogry_permalink_type';
	const BLOG_POST_ROUTE = 'blog/permalink/post_route';
	const BLOG_POST_LINK_TYPE = 'blog/permalink/type';
	const BLOG_PERMALINK_OPTIONS = 'blog/permalink';
	const BLOG_SEARCH_ROUTE = 'blog/permalink/search_route';
	const BLOG_ARCHIVE_ROUTE = 'blog/permalink/archive_route';
	const BLOG_TAG_ROUTE = 'blog/permalink/tag_route';
	const BLOG_AUTHOR_ROUTE = 'blog/permalink/author_route';
	const BLOG_TAG_SUFFIX = 'blog/permalink/tag_sufix';
	const BLOG_AUTHOR_SUFFIX = 'blog/permalink/author_sufix';
	const BLOG_ENABLE_TOP_MENU_LINK = 'blog/top_navigation/enable_link';
	const BLOG_AUTO_REDIRECT_NO_SLASH_URL = 'blog/permalink/redirect_to_no_slash';
	const BLOG_FEATURE_IMAGE_HEIGHT = 'blog/post_list/feature_image_height';
	const BLOG_FEATURE_IMAGE_WIDTH = 'blog/post_list/feature_image_width';
	const BLOG_FEATURE_IMAGE_RESIZETYPE = 'blog/post_list/resize_type';
	
	
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magebees\Blog\Model\Category $category,
		\Magebees\Blog\Model\Post $post,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\UrlInterface $urlInterface,
		\Magebees\Blog\Model\Category $blogcategory,		
		\Magebees\Blog\Model\ResourceModel\Category\Collection $blogcategorycollection,
		\Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
	) {
		$this->_category = $category;
		$this->_post = $post;
		$this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		$this->_storeManager = $storeManager;
		$this->_urlInterface = $urlInterface;
		$this->_blogcategory = $blogcategory;
		$this->_blogcategorycollection = $blogcategorycollection;
		$this->userCollectionFactory = $userCollectionFactory;
		
		parent::__construct($context);
    }
	public function isEnableBlogModule()
	{
		
		$blog_enable = $this->scopeConfig->getValue(
            self::BLOG_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $blog_enable;
		
	$enable_blog_top_navigation = $this->configuration->getConfig('blog/top_navigation/enable_link');
	}
	public function isAutoRedirectToNoSlash()
	{
		
		$auto_redirect_to_no_slash = $this->scopeConfig->getValue(
            self::BLOG_AUTO_REDIRECT_NO_SLASH_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $auto_redirect_to_no_slash;
		
	$enable_blog_top_navigation = $this->configuration->getConfig('blog/top_navigation/enable_link');
	}
	public function getEnableMenuLink()
	{
		$blog_enable_top_menu_link = $this->scopeConfig->getValue(
            self::BLOG_ENABLE_TOP_MENU_LINK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		$blog_enable = $this->scopeConfig->getValue(
            self::BLOG_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		return $blog_enable * $blog_enable_top_menu_link;
		
	$enable_blog_top_navigation = $this->configuration->getConfig('blog/top_navigation/enable_link');
	}
	public function getBlogTagSuffix()
	{
	return $blog_tag_suffix = $this->scopeConfig->getValue(
            self::BLOG_TAG_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getBlogAuthorSuffix()
	{
	return $blog_author_suffix = $this->scopeConfig->getValue(
            self::BLOG_AUTHOR_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
    public function getBlogRoute()
    {
        $config_permalink_route = $this->scopeConfig->getValue(
            self::BLOG_PERMALINK_ROUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(!$config_permalink_route)
		{
			$config_permalink_route = 'blog';
		}
		return $config_permalink_route;
    }
	 public function getBlogSearchRoute()
    {
        $config_search_route = $this->scopeConfig->getValue(
            self::BLOG_SEARCH_ROUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(!$config_search_route)
		{
			$config_search_route = 'search';
		}
		return $config_search_route;
    }
	public function getBlogArchiveRoute()
    {
        $config_archive_route = $this->scopeConfig->getValue(
            self::BLOG_ARCHIVE_ROUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(!$config_archive_route)
		{
			$config_archive_route = 'archive';
		}
		return $config_archive_route;
    }
	public function getBlogTagRoute()
    {
        $config_tag_route = $this->scopeConfig->getValue(
            self::BLOG_TAG_ROUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(!$config_tag_route)
		{
			$config_tag_route = 'tag';
		}
		return $config_tag_route;
    }
	public function getBlogAuthorRoute()
    {
        $config_author_route = $this->scopeConfig->getValue(
            self::BLOG_AUTHOR_ROUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(!$config_author_route)
		{
			$config_author_route = 'author';
		}
		return $config_author_route;
    }
	public function getBlogTitle()
    {
        $blog_title = $this->scopeConfig->getValue(
            self::BLOG_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(!$blog_title)
		{
			$blog_title = 'Blog';
		}
		return $blog_title;
    }
	public function getPostLinkType()
	{
	$config_post_link_type = $this->scopeConfig->getValue(
            self::BLOG_POST_LINK_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getBlogPostRoute()
    {
       $config_post_route = $this->scopeConfig->getValue(
            self::BLOG_POST_ROUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(!$config_post_route){
			$config_post_route = 'post';
			}
		return $config_post_route;
		/* $config_post_link_type = $this->scopeConfig->getValue(
            self::BLOG_POST_LINK_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		if(($config_post_link_type=='default'))
		{
			if(!$config_post_route){
			$config_post_route = 'post';
			}
		}else if($config_post_link_type=='short')
		{
		$config_post_route = null;
		}
		return $config_post_route;
		*/
    }
	public function getBlogCategoryRoute()
    {
       return  $config_category_route = $this->scopeConfig->getValue(
            self::BLOG_CATEGORY_ROUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		
    }
	public function getBlogCategoryLinkType()
	{
		return $config_category_link_type = $this->scopeConfig->getValue(
            self::BLOG_CATEGORY_LINK_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		
	}
	public function getPermalinkSettings()
	{
	 return $config_permalink_options = $this->scopeConfig->getValue(
            self::BLOG_PERMALINK_OPTIONS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
	}
	public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	public function getFeatureImageSize()
	{
		$size = array();
		$height = $this->scopeConfig->getValue(
             self::BLOG_FEATURE_IMAGE_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		$width = $this->scopeConfig->getValue(
            self::BLOG_FEATURE_IMAGE_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		$resize_type = $this->scopeConfig->getValue(
            self::BLOG_FEATURE_IMAGE_RESIZETYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		
		$size['resize_type'] = $resize_type;
		$size['height'] = $height;
		$size['width'] = $width;
		return $size;
	}
	
}
