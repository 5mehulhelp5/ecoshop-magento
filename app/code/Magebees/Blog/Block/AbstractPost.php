<?php
namespace Magebees\Blog\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Blog\Model\Post;
use Magebees\Blog\Model\Category;
use Magebees\Blog\Model\Tag;
use Magento\Framework\App\Filesystem\DirectoryList;
class AbstractPost extends Template {
    
    public $_registry;
	protected $_mediaDirectory;
	protected $_parentcategoryurl = array();
	protected $post;
    protected $postfactory;
    protected $_blogcategory;
    protected $_urlInterface;
    protected $_storemanager;
    protected $_bloghelper; 
    protected $_timezoneInterface;
    protected $_customerSession;
    protected $_filterProvider;
    protected $_currentCategory;
    protected $configuration;
    protected $_userFactory; 
	protected $_robots;
    protected $_blogurl;
    protected $_bloglikedislike;
    protected $imageFactory;
    protected $imageAdapterFactory; 
	protected $_filesystem;
    protected $_directory; 
	
    public function __construct(
		Context $context,
		\Magebees\Blog\Model\Post $post,
		\Magebees\Blog\Model\PostFactory $postfactory,
		\Magebees\Blog\Model\Category $blogcategory,
		\Magebees\Blog\Model\LikeDislike $bloglikedislike,
		\Magebees\Blog\Helper\Data $bloghelper,
		\Magento\Framework\UrlInterface $urlInterface,
		\Magento\Store\Model\StoreManagerInterface $storemanager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\User\Model\UserFactory $userFactory,
		\Magento\Config\Model\Config\Source\Design\Robots $robots,
		\Magento\Framework\Registry $registry,
		\Magebees\Blog\Model\Url $blogurl,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\Factory $imageFactory,
		\Magento\Framework\Image\AdapterFactory $imageAdapterFactory) 
	{
	
        $this->post = $post;
        $this->postfactory = $postfactory;
        $this->_blogcategory = $blogcategory;
        $this->_urlInterface = $urlInterface;
        $this->_storemanager = $storemanager;
        $this->_bloghelper = $bloghelper;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_customerSession = $customerSession;
        $this->_filterProvider = $filterProvider;
        $this->_currentCategory = null;
        $this->configuration = $Configuration;
        $this->_userFactory = $userFactory;
        $this->_robots = $robots;
        $this->_registry = $registry;
        $this->_blogurl = $blogurl;
        $this->_bloglikedislike = $bloglikedislike;
		$this->imageFactory = $imageFactory;
		$this->imageAdapterFactory = $imageAdapterFactory;
        $this->_filesystem = $filesystem;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
		
        parent::__construct($context);
    }
    public function getPost() {
        if ($this->_registry->registry('current_post')):
            return $this->_registry->registry('current_post');
        else:
            if ($this->getRequest()->getParam('post_id')) {
                $post_id = $this->getRequest()->getParam('post_id');
                return $this->_currentPost = $this->post->load($post_id);
            }
        endif;
        return null;
    }
    public function getPostCollection() {
        $storeId = $this->_storemanager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        $collection = $this->post->getCollection();
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        $collection->addFieldToFilter('save_as_draft', array('neq' => 1));
        $collection->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
        $collection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $collection->addFieldToFilter('publish_time', ['lteq' => $now]);
        return $collection;
    }
    public function getCategoryCollection() {
        $storeId = $this->_storemanager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $blogcategories = $this->_blogcategory->getCollection();
        $blogcategories->addFieldToFilter('is_active', array('eq' => 1));
        $blogcategories->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $blogcategories->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
        $blogcategories->setOrder('position', 'asc');
        return $blogcategories;
    }
    public function getPostLikesDislikesCollection() {
        $storeId = $this->_storemanager->getStore()->getId();
        $vote_collection = $this->_bloglikedislike->getCollection();
        $vote_collection->addFieldToFilter('store_id', array('eq' => $storeId));
        return $vote_collection;
    }
    public function getPosturl($post) {
        $section = 'recent_post';
        return $this->_blogurl->getBlogPosturl($post, $section);
    }
	 public function getFeaturedImage($post,$width=null,$height=null,$resize_type=null) {
        $featured_image = $post->getFeaturedImg();
        if ($post->getFeaturedImg()):
            $image_size = $this->configuration->getFeatureImageSize();
			if((!$width) && (!$height))
			{
			$width = $image_size['width'];
			$height = $image_size['height'];	
			}
		 	if(!$resize_type)
			{
				$resize_type = $image_size['resize_type'];
			}
		 	
            return $this->getFeaturedImageResize($featured_image, $width, $height,$resize_type);
        else:
            return null;
        endif;
    }
	public function getFeaturedImageResize($imageName, $width = 258, $height = 200,$type='resizeonly') {
     
		$realPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog' . $imageName);
        if (!$this->_directory->isFile($realPath) || !$this->_directory->isExist($realPath)) {
            return false;
        }
      
        $targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog/resized/' . $width . 'x' . $height);
        $pathTargetDir = $this->_directory->getRelativePath($targetDir);
      
        if (!$this->_directory->isExist($pathTargetDir)) {
            $this->_directory->create($pathTargetDir);
        }
        if (!$this->_directory->isExist($pathTargetDir)) {
            return false;
        }
		
		if($type=='cropandresize')
		{
			$image = $this->imageAdapterFactory->create();
		$image->open($realPath);
		$currentRatio = $image->getOriginalWidth() / $image->getOriginalHeight();
        $targetRatio = $width / $height;
		//$image->keepAspectRatio(true);
		if ($targetRatio > $currentRatio) {
            //$image->resize($width, $height);
			$image->resize($width, null);
        } else {
            //$image->resize($width, $height);
			$image->resize(null, $height);
        }
		
		$diffWidth  = $image->getOriginalWidth() - $width;
        $diffHeight = $image->getOriginalHeight() - $height;

		
		 $image->crop(
            floor($diffHeight * 0.5),
            floor($diffWidth / 2),
            ceil($diffWidth / 2),
            ceil($diffHeight * 0.5)
        );
		
        $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
        $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);
			if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $resize_image_name;
        }
		}else {
		$image = $this->imageAdapterFactory->create();
        $image->open($realPath);
        $image->keepAspectRatio(true);
        $image->resize($width, $height);
        $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
        $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);
			if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $resize_image_name;
        }
		}
		
        return false;
		
		
        
    }
	public function getFeaturedImageResizeCount($count,$imageName, $width = 258, $height = 200,$type='resizeonly') {
		
		$realPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog' . $imageName);
		$realPathNew = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog' . '/p/o/post'.$count.'.jpg');
		$new_imageName = '/p/o/post'.$count.'.jpg';
		$new_realPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog' . $new_imageName);
		
        if (!$this->_directory->isFile($realPath) || !$this->_directory->isExist($realPath)) {
            return false;
        }
      
        $targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog/resized/' . $width . 'x' . $height);
	
        $pathTargetDir = $this->_directory->getRelativePath($targetDir);
    
	 
        if (!$this->_directory->isExist($pathTargetDir)) {
            $this->_directory->create($pathTargetDir);
        }
        if (!$this->_directory->isExist($pathTargetDir)) {
            return false;
        }
		
		if($type=='cropandresize')
		{
			$image = $this->imageAdapterFactory->create();
		$image->open($realPath);
		$currentRatio = $image->getOriginalWidth() / $image->getOriginalHeight();
        $targetRatio = $width / $height;
		//$image->keepAspectRatio(true);
		if ($targetRatio > $currentRatio) {
            //$image->resize($width, $height);
			$image->resize($width, null);
        } else {
            //$image->resize($width, $height);
			$image->resize(null, $height);
        }
		
		$diffWidth  = $image->getOriginalWidth() - $width;
        $diffHeight = $image->getOriginalHeight() - $height;

		
		 $image->crop(
            floor($diffHeight * 0.5),
            floor($diffWidth / 2),
            ceil($diffWidth / 2),
            ceil($diffHeight * 0.5)
        );
		
        $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
        $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);
			if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $resize_image_name;
        }
		}else {
		$image = $this->imageAdapterFactory->create();
        $image->open($realPath);
		$image->keepAspectRatio(true);
        $image->resize($width, $height);
		 //echo 'resize_image_name::'.$resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
		 $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
		 $resize_image_name = 'post'.$count.'.jpg';
		 
        $dest = $targetDir . '/' . pathinfo($realPathNew, PATHINFO_BASENAME);
       // $resize_image_name = pathinfo($realPath, PATHINFO_BASENAME);
       // $dest = $targetDir . '/' . pathinfo($realPath, PATHINFO_BASENAME);
        $image->save($dest);
			if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $resize_image_name;
        }
		}
		
        return false;
		
    }
	
 	
}
