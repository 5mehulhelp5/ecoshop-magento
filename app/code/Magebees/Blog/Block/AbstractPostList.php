<?php
namespace Magebees\Blog\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magebees\Blog\Model\Post;
use Magebees\Blog\Model\Category;
use Magebees\Blog\Model\Tag;
use Magebees\Blog\Model\Comment;
use Magento\Framework\App\Filesystem\DirectoryList;
class AbstractPostList extends Template {
    
	protected $postCollection;
    protected $_blogcategory;
    protected $_blogtag;
    protected $_blogcomment;
    protected $_urlInterface;
    protected $_storemanager;
    protected $_bloghelper;
    protected $_timezoneInterface;
    protected $_customerSession;
	protected $_filterProvider;
    protected $_currentCategory;
    public $configuration;
    protected $_userFactory;
    protected $_robots;
	public $_registry;
    protected $_parentcategoryurl = array();
    protected $_blogurl;
    protected $imageFactory;
    protected $imageAdapterFactory;
    protected $_filesystem;
	protected $_directory;
    
    public function __construct(
		Context $context,
		Post $postCollection,
		Category $blogcategory,
		Tag $blogtag,
		Comment $blogcomment,
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
        $this->postCollection = $postCollection;
        $this->_blogcategory = $blogcategory;
        $this->_blogtag = $blogtag;
        $this->_blogcomment = $blogcomment;
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
		$this->imageFactory = $imageFactory;
		$this->imageAdapterFactory = $imageAdapterFactory;
        $this->_filesystem = $filesystem;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context);
    }
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }
    public function getPostCollection() {
		
       $storeId = $this->_storemanager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        $collection = $this->postCollection->getCollection();
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        $collection->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
		$collection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $collection->addFieldToFilter('publish_time', ['lteq' => $now]);
		$collection->addFieldToFilter('save_as_draft', ['eq' => 0]);
		$collection->setOrder('creation_time', 'ASC');
	   return $collection;
    }
    public function getTagCollection() {
        $storeId = $this->_storemanager->getStore()->getId();
        $collection = $this->_blogtag->getCollection();
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        return $collection;
    }
    public function getPostTags($post) {
        $tag_ids = $post->getTagIds();
        $collection = $this->_blogtag->getCollection();
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        $collection->addFieldToFilter('tag_id', array('in' => $tag_ids));
        return $collection;
    }
    public function getTagurl($tag) {
        return $this->_blogurl->getBlogTagurl($tag);
    }
    public function getPosturl($post) {
        $section = 'post';
        return $this->_blogurl->getBlogPosturl($post, $section);
        //return $this->_bloghelper->getBlogPosturl($post,$blog_route_key,$post_route,$post_sufix,$section);
        
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
	public function getPostIdsCollection() {
        $storeId = $this->_storemanager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        $collection = $this->postCollection->getCollection()->addFieldToSelect('post_id');
        $collection->addFieldToFilter('is_active', array('eq' => 1));
        $collection->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
        $collection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $collection->addFieldToFilter('publish_time', ['lteq' => $now]);
	$collection->addFieldToFilter('save_as_draft', ['eq' => 0]);
	$collection->setOrder('creation_time','DESC');
        $post_ids = array();
		foreach($collection as $post):
			$post_ids[] = $post->getPostId();
		endforeach;
		return $post_ids;
    }
	public function getPost($postId)
	{
	return $this->postCollection->load($postId);
	}
	 public function getCommentCollection() {
      	
		$post_ids = $this->getPostIdsCollection();
		$post_ids_str = implode(",",(array)$post_ids);
		$collection = $this->_blogcomment->getCollection();
        $collection->addFieldToFilter('status', array('eq' => 1));
        $collection->addFieldToFilter('parent_id', array('eq' => 0));
		$collection->addFieldToFilter('post_id', array('in' => $post_ids_str));
        $collection->setOrder('creation_time','DESC');
		return $collection;
    }
    public function EnablePublishDate() {
        return $post_publication_date = $this->configuration->getConfig('blog/date/post_publication_date');
    }
    public function EnableAuthorDisplay() {
        return $show_author = $this->configuration->getConfig('blog/author/enabled');
    }
    public function EnableAuthorLink() {
        return $use_author_link = $this->configuration->getConfig('blog/author/page_enabled');
    }
    public function getPostAuthorInfo($author_Id) {
        $authorInfo = array();
        $user = $this->_userFactory->create();
        $user->load($author_Id);
        if ($user->getUserId()):
            $authorInfo['name'] = $user->getFirstName() . " " . $user->getLastName();
            $author_path = $user->getUsername();
           // $authorInfo['url'] = $this->_blogurl->getBlogAuthorurl($author_path);
		$fname = $user->getFirstName();
		$lname = $user->getLastName();
		//$authorInfo['url'] = $this->_blogurl->getBlogAuthorurl($author_path);
		$authorInfo['url'] = $this->_blogurl->getBlogAuthorurl($fname,$lname);
        endif;
        return $authorInfo;
    }
    public function getCreationDate($post) {
        $post_publication_date_format = $this->configuration->getConfig('blog/date/post_publication_date_format');
        if (!$post_publication_date_format):
            $post_publication_date_format = 'F d, Y H:i:s';
        endif;
        return $this->_timezoneInterface->date($post->getCreationTime())->format($post_publication_date_format);
    }
    public function getdateago($publish_date)
   {	
		
		$current_date = $this->_timezoneInterface->formatDate();
		$current_date =  $this->_timezoneInterface->date(strtotime($current_date))->format('Y-m-d'); 
		$publish_date =  $this->_timezoneInterface->date(strtotime($publish_date))->format('Y-m-d');
		$date_diff = abs(strtotime($publish_date) - strtotime($current_date));
		
		$years = floor($date_diff / (365*60*60*24));
		$months = floor(($date_diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($date_diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		if($years > 0)
		{
			return $years." years ago";
		}
		if($months > 0)
		{
			return $months." months ago";
		}
		if($days > 0)
		{
			return $days." days ago";
		}
		return null;
   }
	
    public function getCategoriesCount($post) {
        $postCategoryIds = $post->getCategoryIds();
        $storeId = $this->_storemanager->getStore()->getId();
        $postCategoriescollection = $this->_blogcategory->getCollection();
        $postCategoriescollection->addFieldToFilter('category_id', array('in' => $postCategoryIds));
        $postCategoriescollection->addFieldToFilter('is_active', array('eq' => 1));
        $postCategoriescollection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        return $postCategoriescollection->getSize();
    }
    public function getBlockCategories($postCategoryIds) {
        $blog_route_key = $this->configuration->getBlogRoute();
        //$postCategoryIds = $post->getCategoryIds();
        $storeId = $this->_storemanager->getStore()->getId();
        $parentCategoryurl = $this->configuration->getConfig('blog/permalink/category_use_categories');
        $category_route = $this->configuration->getBlogCategoryRoute();
        $category_sufix = $this->configuration->getConfig('blog/permalink/category_sufix');
        $postCategoriescollection = $this->_blogcategory->getCollection();
        $postCategoriescollection->addFieldToFilter('category_id', array('in' => $postCategoryIds));
        $postCategoriescollection->addFieldToFilter('is_active', array('eq' => 1));
        $postCategoriescollection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $postCategoryList = array();
        foreach ($postCategoriescollection as $category):
            $category_url = $this->_blogurl->getBlogCategoryurl($category);
            $postCategoryList[] = array('title' => $category->getTitle(), 'url' => $category_url);
        endforeach;
        return $postCategoryList;
    }
    public function getParentCategoryurl($category) {
        $p_id = $category->getParentCategoryId();
        $identifier = $category->getIdentifier();
        $parent_category = $this->_blogcategory->load($p_id);
        if ($p_id != 0) {
            $this->_parentcategoryurl[] = $identifier;
            $this->getParentCategoryurl($parent_category);
        } else {
            $this->_parentcategoryurl[] = $identifier;
            return $this->_parentcategoryurl;
        }
        return $this->_parentcategoryurl;
    }
    public function getPostShortContent($_post) {
		
        $content = strip_tags((string)$_post->getContentHeading());
		if(!$content)
		{
		$content = strip_tags((string)$_post->getContent());
		}
		$post_content_length = $this->configuration->getConfig('blog/post_list/post_content_length');
        if (!$post_content_length) {
            $post_content_length = 500;
        }
        $formatted_content = $this->_filterProvider->getBlockFilter()->setStoreId($this->_storemanager->getStore()->getId())->filter($content);
        if (str_word_count((string)$formatted_content, 0) > $post_content_length) {
            $words = str_word_count((string)$formatted_content, 2);
            $pos = array_keys($words);
            $formatted_content = substr($formatted_content, 0, $pos[$post_content_length]) . '...';
        }
		 return $formatted_content;
    }
    public function getDescription() {
        if ($this->_currentCategory):
            if ($this->_currentCategory->getCategoryId()):
                $category_desc = $this->_currentCategory->getContent();
                return $this->_filterProvider->getBlockFilter()->setStoreId($this->_storemanager->getStore()->getId())->filter($category_desc);
            endif;
        endif;
        return null;
    }
    public function getSubCategoryList() {
        $blog_route_key = $this->configuration->getBlogRoute();
        $storeId = $this->_storemanager->getStore()->getId();
        $postCategoriescollection = $this->_blogcategory->getCollection();
        $postCategoriescollection->addFieldToFilter('parent_category_id', array('eq' => $this->category_id));
        $postCategoriescollection->addFieldToFilter('is_active', array('eq' => 1));
        $postCategoriescollection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $subCategoryListIds = array();
        foreach ($postCategoriescollection as $category):
            $subCategoryListIds[] = $category->getCategoryId();
        endforeach;
        return $this->getBlockCategories(implode($subCategoryListIds, ","));
    }
    public function getDisplayMode() {
        if ($this->_currentCategory):
            if ($this->_currentCategory->getCategoryId()):
                return $this->_currentCategory->getDisplayMode();
            endif;
        endif;
        return null;
    }
	public function getPostSortOrder() {
        if ($this->_currentCategory){
			if ($this->_currentCategory->getCategoryId()):
                return $sort_order = $this->_currentCategory->getPostsSortBy();
            endif;
		}else{ 
		return $sort_order = $this->configuration->getConfig('blog/blogpage/post_sort_by');
		}
        
    }
    public function IsAddthisEnable() {
        return $add_this_enable = $this->configuration->getConfig('blog/add_this/enabled');
    }
    public function getPostComment($post) {
        $post_id = $post->getPostId();
        if ($post_id):
            $collection = $this->_blogcomment->getCollection();
            $collection->addFieldToFilter('post_id', array('eq' => $post->getPostId()));
            $collection->addFieldToFilter('parent_id', array('eq' => 0));
			$collection->addFieldToFilter('status', array('eq'=> 1));
            return $collection;
        endif;
        return null;
    }
  public function checkReseizeImageAvailable($imageName, $width, $height ,$type='resizeonly'){
		
		$image_name_array = (explode("/",(string)$imageName));
		$imageName = end($image_name_array);
		if($imageName)
		{
			$targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('magebees_blog/resized/' . $width . 'x' . $height.'/'.$imageName);
			if ($this->_directory->isFile($this->_directory->getRelativePath($targetDir))) {
				return $this->_storemanager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'magebees_blog/resized/' . $width . 'x' . $height . '/' . $imageName;
			}
		}else{
			return false;
		}
		return false;
	}
	public function getFeaturedImageResize($imageName, $width = 258, $height = 200,$type='resizeonly') {
		$resizeImagePath =  $this->checkReseizeImageAvailable($imageName, $width, $height ,$type);
		if($resizeImagePath){
			return $resizeImagePath;
		}
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
		$image->keepAspectRatio(false);
		if ($targetRatio > $currentRatio) {
            $image->resize($width, null);
        } else {
            $image->resize(null, $height);
        }
		//$image->resize($width, $height);
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
    public function getFormattedContent($content)
	{
$formatted_content =  $this->_filterProvider->getBlockFilter()->setStoreId($this->_storemanager->getStore()->getId())->filter($content);;
	return $formatted_content;
	}
	public function getPageViewStyle()
	{
		$current_blog_home = $this->_registry->registry('current_blog_home');
		if($current_blog_home){
		return $view_style = $this->configuration->getConfig('blog/blogpage/view_style');
		}else{
		return $view_style = $this->configuration->getConfig('blog/post_list/view_style');
		}
		
	}
	public function EnableAddThis() {
		return $add_this_enable = $this->configuration->getConfig('blog/add_this/enabled');
	    }
	    public function getAddThisId() {
		return $add_this_id = $this->configuration->getConfig('blog/add_this/id');
	    }
	    public function getAddThisLanguage() {
		return $add_this_language = $this->configuration->getConfig('blog/add_this/language');
	    }
}
