<?php
namespace  Magebees\Blog\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{	
	protected $_mediaDirectory;
	public $_storeManager;
	protected $_urlInterface;
	protected $parentcategoryurl = array();
	protected $aclRetriever;
	protected $authSession;
	public $_registry;
	protected $_blogurl;
	protected $_blogcategory;
	protected $_blogtag;
	protected $_blogcategorycollection;
	protected $userCollectionFactory;
	protected $configuration;
	protected $_customerSession;
	protected $_post;
	protected $request;
	protected $_timezoneInterface;
	protected $_category;
	protected $_parentcategoryurl;
	
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magebees\Blog\Model\Category $category,
		\Magebees\Blog\Model\Post $post,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\UrlInterface $urlInterface,
		\Magebees\Blog\Model\Category $blogcategory,	
		\Magebees\Blog\Model\Tag $blogtag,	
		\Magebees\Blog\Helper\Configuration $Configuration,
		\Magento\Customer\Model\Session $customerSession,
		\Magebees\Blog\Model\ResourceModel\Category\Collection $blogcategorycollection,
		\Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
    	\Magento\Backend\Model\Auth\Session $authSession,
		\Magebees\Blog\Model\Url $blogurl,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Framework\Registry $registry
	) { 
		$this->_category = $category;
		$this->request = $request;
		$this->_post = $post;
		$this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		$this->_storeManager = $storeManager;
		$this->_urlInterface = $urlInterface;
		$this->_blogcategory = $blogcategory;
		$this->_blogtag = $blogtag;
		$this->_blogcategorycollection = $blogcategorycollection;
		$this->userCollectionFactory = $userCollectionFactory;
		$this->configuration = $Configuration;
		$this->_customerSession = $customerSession;
		 $this->aclRetriever = $aclRetriever;
		$this->authSession = $authSession;
		$this->_blogurl = $blogurl;
		$this->_registry = $registry;
		$this->_timezoneInterface = $timezoneInterface;
		parent::__construct($context);
    }
	public function getLatestPostCollection() {
        
		$storeId = $this->_storemanager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
        $collection = $this->_post->getCollection();
		
		
        $collection->addFieldToFilter('is_active', array('eq' => 1));
		//$collection->addFieldToFilter('include_in_recent', array('eq' => 1));
		
		$collection->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
		$collection->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $collection->addFieldToFilter('publish_time', ['lteq' => $now]);
		$collection->addFieldToFilter('save_as_draft', ['eq' => 0]);
		//$collection->setOrder('creation_time', 'ASC');
	   return $collection;
	   
		
    }
	public function getViewAllUrl($path){
		return	$this->_blogurl->getViewAllUrl($path);
	}
    public function getyesnoOptionArray()
    {
        return [
            
            '1' => 'Yes',
            '0' => 'No',
        ];
    }
	public function getRewriteUrlType()
    {
        return [
            '' => 'Please Select',
            'post' => 'Post',
            'category' => 'Category',
			'tag' => 'Tag',
			'blog' => 'Custom',
        ];
    }
	public function getPostFeaturedImage($postId){
		$featuredImage = array();
		$postDetails = $this->_post->load($postId);
		if($postDetails->getFeaturedImg()):
		$target = $this->_mediaDirectory->getAbsolutePath('magebees_blog'); 
		$_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		
		$featuredImage['name'] = $postDetails->getFeaturedImg();
		$featuredImage['mediaurl'] = $_mediaUrl.'magebees_blog/'.$postDetails->getFeaturedImg();
		$featuredImage['targetpath'] = $target.$postDetails->getFeaturedImg();
		return $featuredImage;
		endif;
		return null;
		
	}
	public function getPostGalleryImages($postId){
		$galleryImages = array();
		$postDetails = $this->_post->load($postId);
		if($postDetails->getMediaGallery()):
		$target = $this->_mediaDirectory->getAbsolutePath('magebees_blog/gallery'); 
		$_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$current_media_gallery = $postDetails->getMediaGallery();
		$current_media_gallery_arr = explode(",",(string)$current_media_gallery);
		
		foreach($current_media_gallery_arr as $imagepath):
		if($imagepath):	
		$galleryImages[] = array('name'=>$imagepath,
									'mediaurl'=>$_mediaUrl.'magebees_blog/gallery'.$imagepath); 
		endif;
		endforeach;
		return $galleryImages;
		endif;
		
		return $galleryImages;
	}
	
	
public function getTagList(){
		
		$tags = $this->_blogtag->getCollection();
		$tag_arr = array();
		foreach($tags as $tag):
			$tag_arr[] = array('value'=>$tag->getId(),'label'=>$tag->getTitle());
		endforeach;
		return $tag_arr;
	
	}

	
public function getCategoryList(){
		
		$categories = $this->_category->getCollection();
		$cat_arr = array();
		foreach($categories as $category):
			$cat_arr[] = array('value'=>$category->getCategoryId(),'label'=>$category->getTitle());
		endforeach;
		return $cat_arr;
	
	}
	public function getPostListOptionArray(){
		
		$posts = $this->_post->getCollection();
		$post_arr = array();
		foreach($posts as $post):
			$post_arr[$post->getPostId()] = $post->getTitle();
		endforeach;
		return $post_arr;
	
	}
	public function getCategoryListOptionArray(){
		
		$categories = $this->_category->getCollection();
		$cat_arr = array();
		foreach($categories as $category):
			$cat_arr[$category->getCategoryId()] = $category->getTitle();
		endforeach;
		return $cat_arr;
	
	}
	public function getParentOptionArray($current_id){
		
		
		
		$categories = $this->_category->getCollection();
		$cat_arr = array();
		$cat_arr[0] =  __('Please Select Parent Category');
		foreach($categories as $category):
			if($category->getCategoryId() != $current_id):
			$cat_arr[$category->getCategoryId()] = $category->getTitle();
			endif;
		endforeach;
		return $cat_arr;
	
	}

public function getAuthorType()
    {
        return [
            'admin' => 'Admin',
            'guest' => 'Guest',
            'customer' => 'Customer',
        ];
    }

	public function getEnableDisableOptionArray()
    {
        return [
            
            '1' => 'Enabled',
            '2' => 'Disabled',
        ];
    }
	public function getdisplaymodeOptionArray()
    {
        return [
            '0' => 'Posts (default)',
            '1' => 'Posts Links',
	    '2' => 'Subcategories Links',
            '3' => 'Posts & Subcategories Links',
        ];
    }
	public function getpostsortbyOptionArray()
    {
         return [
            '0' => 'Publish Date (default)',
            '1' => 'Position',
	    '2' => 'Title',
	    '3' => 'Random'
        ];
    }
public function isImported()
    {
         return [
            '0' => 'Created',
            '1' => 'Imported',
        ];
    }


public function getMetaRobotsOptionArray()
    {
        return [
            '0' => 'Use config settings',
            'INDEX, FOLLOW' => 'INDEX, FOLLOW',
	    'NOINDEX, FOLLOW' => 'NOINDEX, FOLLOW',
            'INDEX, NOFOLLOW' => 'INDEX, NOFOLLOW',
            'NOINDEX, NOFOLLOW' => 'NOINDEX, NOFOLLOW',
        ];
    }
	public function getAdminUsers()
	{
		
		$current_user = $this->authSession->getUser();
    	
		$adminUsers = [];
		$adminUsers[] = [
				'value' => '',
				'label' => 'Please Select'
			];
			$adminUsers[] = [
				'value' => $current_user->getId(),
				'label' => $current_user->getName()
			];
		return $adminUsers;
	}

 	public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	public function getCategoryBreadcumInfo($pathInfo)
    {
		
		
		$breadcum = array();
		$pathInfo = explode('/', (string)$pathInfo);
		$pathInfo = array_filter($pathInfo);
		$blog_route_key = $this->configuration->getBlogRoute();
		$blog_route_title = $this->configuration->getBlogTitle();
		$permalinkOptions = $this->configuration->getPermalinkSettings();
		$category_sufix = $permalinkOptions['category_sufix'];
		
		$parentCategoryurl = $permalinkOptions['category_use_categories'];
		$category_route = $permalinkOptions['category_route'];
		$category_sufix = $this->configuration->getConfig('blog/permalink/category_sufix');
		
		
		
		$storeId = $this->_storeManager->getStore()->getId();
		if($this->_customerSession->isLoggedIn()):
        	$customerGroupId=$this->_customerSession->getCustomer()->getGroupId();
    		else:
			$customerGroupId=0;
		endif;
		
		
		/* Remove blog Route from the path info */
		unset($pathInfo[array_search( $blog_route_key, $pathInfo )]);
		
		/* Remove Category Route from the path info */
		if($category_route)
		{
		$route_key = array_search($category_route, $pathInfo);
		unset($pathInfo[array_search($category_route, $pathInfo)]);
		}
		/* Set Home & Blog Page In Breadcum */
		$breadcum['home'] = array('title'=>'Home','url'=>$router_url = $this->_urlInterface->getUrl());
		$breadcum[$blog_route_key] = array('title'=>$blog_route_title,'url'=> $this->_urlInterface->getUrl($blog_route_key));
		
		$lastCategoryUrl = end($pathInfo);
		$lastCategoryPath = trim(str_replace((string)$category_sufix,"",$lastCategoryUrl));
		foreach($pathInfo as $CategoryUrl):
			$identifier = $CategoryUrl;
			$blogcategories = $this->_blogcategory->getCollection();
			
			if($CategoryUrl==$lastCategoryUrl)
			{
			$blogcategories->addFieldToFilter('identifier', array('eq' => $lastCategoryPath));
			}else{
			$blogcategories->addFieldToFilter('identifier', array('eq' => $identifier));
			}
			$blogcategories->addFieldToFilter('is_active', array('eq' => 1));
			$blogcategories->addFieldToFilter(['store_id','store_id'],[['eq' => 0],['eq'=>$storeId]]);
			$blogcategories->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
			
			if($blogcategories->getFirstItem()->getCategoryId()):
				$currentCategory = $blogcategories->getData();
				$CategoryTitle = $blogcategories->getFirstItem()->getTitle();
				$this->_parentcategoryurl = array();
				if($CategoryUrl!=$lastCategoryUrl)
				{
				$category_url = $this->_blogurl->getBlogCategoryurl($blogcategories->getFirstItem());		
				}else{
				$category_url = null;
				}
				$breadcum[$identifier] = array('title'=>$CategoryTitle,'url'=>$category_url);
			endif;			
		endforeach;
		return $breadcum;
	}
	public function getBreadcumInfo($identifier)
    {
		
		$breadcum = array();
		$pathInfo = explode('/', (string)$identifier);
		$pathInfo = array_filter($pathInfo);
		$tmp_pathInfo = $pathInfo;
		$blog_route_key = $this->configuration->getBlogRoute();
		$blog_route_title = $this->configuration->getBlogTitle();
		$permalinkOptions = $this->configuration->getPermalinkSettings();
		$blog_search_route = $this->configuration->getBlogSearchRoute();
		$blog_archive_route = $this->configuration->getBlogArchiveRoute();
		$blog_tag_route = $this->configuration->getBlogTagRoute();
		$blog_tag_suffix = $this->configuration->getBlogTagSuffix();
		
		$breadcum['home'] = array('title'=>'Home','url'=>$router_url = $this->_urlInterface->getUrl());
		
		if(count($pathInfo) > 1){
		$breadcum[$blog_route_key] = array('title'=>$blog_route_title,'url'=> $this->_urlInterface->getUrl($blog_route_key));
		}else{
			$breadcum[$blog_route_key] = array('title'=>$blog_route_title,'url'=> null);
		}
		
		$blog_search_route_key = array_search($blog_search_route,$pathInfo);
			
		 if (in_array($blog_search_route, $pathInfo))
		 {
			$search_query_string = urldecode(end($pathInfo));	
			$search_page_title = 'Search "'.$search_query_string.'"';
			$breadcum['blog_search'] = array('title'=>$search_page_title,'url'=> null);
			return $breadcum; 
		 }
		if($this->_registry->registry('current_blog_tag')):
				$tag_page_title = $this->_registry->registry('current_blog_tag')->getTitle();
				$tag_page_title = 'Tag : "'.$tag_page_title.'"';
				$breadcum['blog_tag'] = array('title'=>$tag_page_title,'url'=> null);
				return $breadcum; 
		endif;
		if($this->_registry->registry('current_blog_author')):
				$current_author = $this->_registry->registry('current_blog_author');
				$author_title = $current_author->getFirstname(). " ".$current_author->getLastname();
				$author_title = 'Author : "'.$author_title.'"';
				$breadcum['blog_author'] = array('title'=>$author_title,'url'=> null);
				return $breadcum; 
		endif;
		
		if($this->_registry->registry('current_blog_archive')):
			$archive_query_string = urldecode(end($pathInfo));	
			$archive_query_string = explode("~",(string)$archive_query_string);
			$archive_query_date = $archive_query_string[0]."-".$archive_query_string[1]."-"."01";
			$date=date_create($archive_query_date);
			$year = date_format($date,"Y");
			$month = date_format($date,"m");
			$archive_title = date_format($date,"F")." ".date_format($date,"Y");
			$archive_page_title = 'Monthly Archives: "'.$archive_title.'"';
			$breadcum['blog_archive'] = array('title'=>$archive_page_title,'url'=> null);
			return $breadcum; 
		endif;
		/*if (in_array($blog_archive_route, $pathInfo))
		 {
			$archive_query_string = urldecode(end($pathInfo));	
			$archive_query_string = explode("-",$archive_query_string);
			$archive_query_date = $archive_query_string[0]."-".$archive_query_string[1]."-"."01";
			$date=date_create($archive_query_date);
			$year = date_format($date,"Y");
			$month = date_format($date,"m");
			$archive_title = date_format($date,"F")." ".date_format($date,"Y");
			$archive_page_title = 'Monthly Archives: "'.$archive_title.'"';
			$breadcum['blog_archive'] = array('title'=>$archive_page_title,'url'=> null);
			return $breadcum; 
		 }*/
		return $breadcum;
	}
	
	/* public function getBlogCategoryurl($category)
    {
		
        $p_id=$category->getParentCategoryId();
		$identifier=$category->getIdentifier();
        
        if ($p_id!=0) {
			$parent_category = $this->_blogcategory->load($p_id);
			$this->_parentcategoryurl[] = $identifier;
            $this->getBlogCategoryurl($parent_category);
        } else {
			$this->_parentcategoryurl[] = $identifier;
            return $this->_parentcategoryurl;
        }
        return $this->_parentcategoryurl;
    }*/
	
	
	/*
	public function getBlogCategoryurl($category,$blog_route_key,$category_sufix,$parentCategoryurl,$category_route)
    {
		
		$p_id=$category->getParentCategoryId();
		$identifier = $category->getIdentifier();
		if($parentCategoryurl)
		{
		
			if ($p_id!=0) {
			$parent_category = $this->_blogcategory->load($p_id);
			$this->_parentcategoryurl[] = $identifier;
            return $this->getBlogCategoryurl($parent_category,$blog_route_key,$category_sufix,$parentCategoryurl,$category_route);
        	} 
			else 
			{
			$this->_parentcategoryurl[] = $identifier;
			
			$parent_url = array_reverse($this->_parentcategoryurl);
			if($category_route){
			$category_path = $blog_route_key.'/'.$category_route.'/'.implode('/', $parent_url);
			}else{
			$category_path = $blog_route_key.'/'.implode('/', $parent_url);
			}
			if($category_sufix){
			$category_path .= $category_sufix;
			}
				$category_url = $this->_urlInterface->getUrl().$category_path;
				$category_url = rtrim($category_url, '/');
				$this->_parentcategoryurl = array();
        		return $category_url;
			}
		}else{
			
			if($category_route){
			$category_path = $blog_route_key.'/'.$category_route.'/'.$identifier;
			}else{
			$category_path = $blog_route_key.'/'.$identifier;
			}
			if($category_sufix){
			$category_path .= $category_sufix;
			}
			$category_url = $this->_urlInterface->getUrl().$category_path;
			$category_url = rtrim($category_url, '/');
			$this->_parentcategoryurl = array();
        	return $category_url;
		}
        
		
        }*/
	
	
	
}
