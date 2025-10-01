<?php
namespace Magebees\Blog\Block\Post;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Blog\Model\Post;
use Magebees\Blog\Model\Tag;
use Magebees\Blog\Model\Comment;
use Magebees\Blog\Model\Category;
use Magento\Framework\App\Filesystem\DirectoryList;
class View extends Template {
    protected $postCollection;
    protected $post_id;
    protected $_parentcategoryurl = array();
    protected $_registry;
    protected $_currentPost;
	protected $_blogpost;
	protected $_blogtag;
	protected $_blogcomment;
    protected $_blogcategory;
	protected $_urlInterface;
	protected $_storemanager;
	protected $_bloghelper;
	protected $_timezoneInterface;
	protected $_customerSession;
	protected $_filterProvider;
	protected $configuration;
	protected $_userFactory;
	protected $_page;
	protected $_catalogProductVisibility;
	protected $_productCollectionFactory;
	protected $imageFactory;
	protected $_filesystem;
	protected $_directory;
	protected $_blogurl;
	
	 
	
    public function __construct(
		Context $context,
		Post $postCollection,
		Tag $tagCollection, 
		Comment $commentCollection, 
		Category $blogcategory, 
		\Magebees\Blog\Helper\Data $bloghelper, 
		\Magento\Framework\UrlInterface $urlInterface, 
		\Magento\Store\Model\StoreManagerInterface $storemanager, 
		\Magento\Customer\Model\Session $customerSession, 
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider, 
		\Magebees\Blog\Helper\Configuration $Configuration, 
		\Magento\User\Model\UserFactory $userFactory, 
		\Magento\Cms\Model\Page $page, 
		\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, 
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
		\Magento\Framework\Filesystem $filesystem, 
		\Magento\Framework\Image\AdapterFactory $imageFactory, 
		\Magento\Framework\Registry $registry, 
		\Magebees\Blog\Model\Url $blogurl) 
	{
        $this->_registry = $registry;
        $this->_blogpost = $postCollection;
        $this->_blogtag = $tagCollection;
        $this->_blogcomment = $commentCollection;
        $this->_blogcategory = $blogcategory;
        $this->_urlInterface = $urlInterface;
        $this->_storemanager = $storemanager;
        $this->_bloghelper = $bloghelper;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_customerSession = $customerSession;
        $this->_filterProvider = $filterProvider;
        $this->_currentPost = null;
        $this->configuration = $Configuration;
        $this->_userFactory = $userFactory;
        $this->_page = $page;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->imageFactory = $imageFactory;
        $this->_filesystem = $filesystem;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_blogurl = $blogurl;
        parent::__construct($context);
    }
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if ($this->_registry->registry('current_blog_post')):
            $this->_currentPost = $this->_registry->registry('current_blog_post');
            $this->post_id = $this->_currentPost->getPostId();
            $title = $this->_currentPost->getMetaTitle();
            $meta_keywords = $this->_currentPost->getMetaKeywords();
            $meta_description = $this->_currentPost->getMetaDescription();
            if (!$title) {
                $title = $this->_currentPost->getTitle();
            }
            if (!$title) {
                $title = $this->_currentPost->getTitle();
            }
            if (!$meta_keywords) {
                $meta_keywords = $this->configuration->getConfig('blog/blogpage/meta_keywords');
            }
            if (!$meta_description) {
                $meta_description = $this->configuration->getConfig('blog/blogpage/meta_description');
            }
	   $meta_robots = $this->configuration->getConfig('blog/blogpage/robots');
            $this->pageConfig->getTitle()->set(__($title)); // meta title
            $this->pageConfig->setKeywords(__($meta_keywords)); // meta keywords
            $this->pageConfig->setDescription(__($meta_description)); // meta description
            $this->pageConfig->setRobots($meta_robots);// meta robots
        endif;
        return $this;
    }
    public function getPost() {
        if ($this->_registry->registry('current_blog_post')):
            return $this->_registry->registry('current_blog_post');
        endif;
    }
    public function getPosturl() {
        if (($this->post_id) && ($this->_currentPost)):
            $section = 'post';
            return $this->_blogurl->getBlogPosturl($this->_currentPost, $section);
        endif;
        return null;
    }
    public function getDescription() {
        if ($this->_currentPost):
            if ($this->_currentPost->getPostId()):
                $post_desc = $this->_currentPost->getContent();
                return $this->_filterProvider->getBlockFilter()->setStoreId($this->_storemanager->getStore()->getId())->filter($post_desc);
            endif;
        endif;
        return null;
    }
	public function getPostTitle()
	{
		if(($this->post_id)&& ($this->_currentPost)):
		return $this->_currentPost->getTitle();	
		endif;
		return null;
	} 
    public function IsPostAddThisEnable() {
        return $add_this_enable = $this->configuration->getConfig('blog/add_this/enabled');
    }
	public function enablePostLikeDislikes() {
		return $this->configuration->getConfig('blog/post_view/like_dislike_post/enable');
	    }
	public function IsPostCommentEnable() {
		return $this->configuration->getConfig('blog/post_view/comment/enable');
	    }
}

