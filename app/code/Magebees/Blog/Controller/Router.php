<?php
namespace Magebees\Blog\Controller;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Controller\ResultFactory;
class Router implements \Magento\Framework\App\RouterInterface {
    
	protected $resultFactory;
    protected $actionFactory;
    protected $_response;
    protected $_scopeConfig;
    protected $_objectManager;
    protected $_bloghelper;
    protected $_blogcategoryFactory;
    protected $_blogpostFactory;
	protected $_blogtagFactory;
    protected $_blogUrlRewriteFactory;
    protected $_blogcategorycollection;
    protected $configuration;
    protected $_storeManager;
    protected $_customerSession;
    public $_registry;
    protected $_user;
	protected $_timezoneInterface;
	
	 public function __construct(
		\Magento\Framework\App\ActionFactory $actionFactory,
		\Magento\Framework\Controller\ResultFactory $resultFactory,
		\Magento\Framework\App\ResponseInterface $response,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magebees\Blog\Helper\Data $bloghelper,
		\Magebees\Blog\Model\CategoryFactory $blogcategoryfactory,
		\Magebees\Blog\Model\PostFactory $blogpostfactory, 
		\Magebees\Blog\Model\TagFactory $blogtagfactory,
		\Magebees\Blog\Model\UrlRewriteFactory $blogUrlRewritefactory, 
		\Magebees\Blog\Model\ResourceModel\Category\Collection $blogcategorycollection, 
		\Magebees\Blog\Helper\Configuration $Configuration, 
		\Magento\Store\Model\StoreManagerInterface $storeManager, 
		\Magento\Customer\Model\Session $customerSession, 
		\Magento\Framework\Registry $registry, 
		\Magento\User\Model\User $user,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface) 
	{
        $this->actionFactory = $actionFactory;
        $this->resultFactory = $resultFactory;
        $this->_response = $response;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->_bloghelper = $bloghelper;
        $this->_blogcategoryFactory = $blogcategoryfactory;
        $this->_blogpostFactory = $blogpostfactory;
        $this->_blogtagFactory = $blogtagfactory;
        $this->_blogUrlRewriteFactory = $blogUrlRewritefactory;
        $this->_blogcategorycollection = $blogcategorycollection;
        $this->configuration = $Configuration;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;
        $this->_user = $user;
		$this->_timezoneInterface = $timezoneInterface;
    }
    /**
     * Match corresponding URL Rewrite and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request) {

		if ($request->getFullActionName() == 'catalog_product_view') {
		return null;
		}
		if ($request->getFullActionName() == 'catalog_category_view') {
			return null;
		}
		
		if ($this->configuration->isEnableBlogModule()):
            $blog_route_key = $this->configuration->getBlogRoute();
            $identifier = trim($request->getPathInfo(), '/');
			
            $pageId = trim($request->getPathInfo(), '/');
            $blog_search_route = $this->configuration->getBlogSearchRoute();
            $blog_tag_route = $this->configuration->getBlogTagRoute();
            $category_route = $this->configuration->getBlogCategoryRoute();
            $post_route = $this->configuration->getBlogPostRoute();
            $archive_route = $this->configuration->getBlogArchiveRoute();
            $author_route = $this->configuration->getBlogAuthorRoute();
            $identifier = trim($request->getPathInfo(), '/');
            $identifier = urldecode($identifier);
            $cateogry_permalink_type = $this->configuration->getBlogCategoryLinkType();
		
		
			
			
            if ($this->configuration->isAutoRedirectToNoSlash()):
                $current_url = $request->getUriString();
                if (substr($current_url, -1) == '/') {
                    $new_url = rtrim($current_url, '/');
                    return $this->forawardToNewStaticUrl($request, $pageId, $new_url);
                }
            endif;
            $pathInfo = explode('/', (string)$identifier);
		
			
            /* Make key with 1 insted of 0*/
            $pathInfo = array_combine(range(1, count($pathInfo)), $pathInfo);
            /* Code for Change URL value base on the URL Rewrite table Start */
            $temp_path = array();
		       foreach ($pathInfo as $path):
                if ($path != end($pathInfo)) {
                    $temp_path[] = $this->getBlogUrlUpdate($path);
                } else {
                    $last_url_arr = explode(".", (string)$path);
                    if (isset($last_url_arr['0']) && isset($last_url_arr['1'])) {
                        $last_new_url_path = $last_url_arr['0'];
                        $last_new_url_suffix = $last_url_arr['1'];
                        $last_new_url = $this->getBlogUrlUpdate($last_new_url_path);
                        $last_suffix = $this->getBlogUrlUpdate($last_new_url_suffix);
                        $temp_path[] = $last_new_url . "." . $last_suffix;
                    } else {
                        $temp_path[] = $this->getBlogUrlUpdate($path);
                    }
                }
            endforeach;
            $temp_path = array_combine(range(1, count($temp_path)), $temp_path);
            $path_difference = array_diff($temp_path, $pathInfo);
            if (count($path_difference) > 0) {
                $new_url = implode("/", (array)$temp_path);
                return $this->forawardToNewUrl($request, $pageId, $new_url);
            }
            /* Code for Change URL value base on the URL Rewrite table End */
            $permalinkOptions = $this->configuration->getPermalinkSettings();
            $storeId = $this->_storeManager->getStore()->getId();
            if ($this->_customerSession->isLoggedIn()):
                $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
            else:
                $customerGroupId = 0;
            endif;
	
            if (($pathInfo[1] != $blog_route_key) && ($pathInfo[1] != 'blog')) {
		        $identifier = end($pathInfo);
                $post_sufix = $permalinkOptions['post_sufix'];
                $post_identifier = str_replace((string)$post_sufix, '', $identifier);
                $blogposts = $this->getBlogPostCollection();
				
				
				
				
                $blogposts->addFieldToFilter('identifier', array('eq' => $post_identifier));
                if ($blogposts->getFirstItem()->getPostId()):
                    $post_id = $blogposts->getFirstItem()->getPostId();
                    
					return $this->forawardToPost($request, $post_id, $pageId);
                endif;
              
                $category_sufix = $permalinkOptions['category_sufix'];
                $category_identifier = str_replace((string)$category_sufix, '', $identifier);
                $blogcategories = $this->getBlogCategoryCollection();
                $blogcategories->addFieldToFilter('identifier', array('eq' => $category_identifier));
                if ($blogcategories->getFirstItem()->getCategoryId()):
                    $category_id = $blogcategories->getFirstItem()->getCategoryId();
                    return $this->forawardToCategory($request, $category_id, $pageId);
                endif;
              
                $tag_sufix = $permalinkOptions['tag_sufix'];
                $blog_tag_page_url = str_replace((string)$tag_sufix, '', $identifier);
                $blogtags = $this->getBlogTagCollection();
                $identifier = explode(".", (string)$identifier);
                if (isset($identifier[0])) {
                    $blog_tag_page_url = $identifier[0];
                }
                $blogtags->addFieldToFilter('identifier', array('eq' => $blog_tag_page_url));
                if ($blogtags->getFirstItem()->getTagId()):
                    $tag_id = $blogtags->getFirstItem()->getTagId();
                    $this->_registry->register('current_blog_tag', $blogtags->getFirstItem());
                    return $this->forawardToTag($request, $identifier, $pageId);
                endif;
                
                $identifier = end($pathInfo);
              //  $archive_identifier = explode("-", (string)$identifier);
			    $archive_identifier = explode("~", (string)$identifier);
                if (isset($archive_identifier[0]) && is_numeric($archive_identifier[0]) && isset($archive_identifier[1]) && is_numeric($archive_identifier[1])) {
                    $archive_id = $identifier;
                    $this->_registry->register('current_blog_archive', $archive_id);
                    return $this->forawardToArchive($request, $archive_id, $pageId);
                }
                $author_user_name = end($pathInfo);
                $author_sufix = $permalinkOptions['author_sufix'];
                if ($permalinkOptions['author_sufix']) {
                    $author_identifier = trim(str_replace($author_sufix, '', $author_user_name));
                }
				
				
				
				
				//$authorInfo = explode("-",(string)$author_identifier);
				$authorInfo = explode("~",(string)$author_identifier);
				if(isset($authorInfo[0]) && isset($authorInfo[1]))
				{
					$fname = $authorInfo[0];
					$lname = $authorInfo[1];
					$admin_user = $this->_user->getCollection();
					$admin_user->addFieldToFilter('firstname', array('eq' => $fname));
					$admin_user->addFieldToFilter('lastname', array('eq' => $lname));
					if ($admin_user->getFirstItem()->getUserId()):
                    $author_id = $admin_user->getFirstItem()->getUserId();
				    $this->_registry->register('current_blog_author', $admin_user->getFirstItem());
                    return $this->forawardToAuthor($request, $author_id, $pageId);
                	endif;
				}
			}
		
            if (($pathInfo[1] == $blog_route_key) && (count($pathInfo) == 1)) {
                /* Only route run in the url then blog main page execute */
                return $this->forawardToBlogPage($request, $pageId);
                $request->setModuleName('blog')->setControllerName('post')->setActionName('index');
                $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
            } else if ((($key = array_search($blog_route_key, $pathInfo))!== FALSE )
				&& (($key = array_search($category_route, $pathInfo))!== FALSE)
				) {
                $CategoryIdentifier = end($pathInfo);
				$category_sufix = $permalinkOptions['category_sufix'];
                if ($permalinkOptions['category_sufix']) {
                    $category_identifier = trim(str_replace($category_sufix, '', $CategoryIdentifier));
                }else{
					$category_identifier = trim($CategoryIdentifier);
				}
                $blogcategories = $this->getBlogCategoryCollection();
                $blogcategories->addFieldToFilter('identifier', array('eq' => $category_identifier));
                if ($blogcategories->getFirstItem()->getCategoryId()):
                    $category_id = $blogcategories->getFirstItem()->getCategoryId();
                    return $this->forawardToCategory($request, $category_id, $pageId);
                endif;
				
				 $identifier = end($pathInfo);
				  $post_sufix = $permalinkOptions['post_sufix'];
                if ($permalinkOptions['post_sufix']) {
                    $post_identifier = trim(str_replace((string)$post_sufix, '', $identifier));
                }else{
					$post_identifier = trim($identifier);
				}
                $blogposts = $this->getBlogPostCollection();
                $blogposts->addFieldToFilter('identifier', array('eq' => $post_identifier));
                if ($blogposts->getFirstItem()->getPostId()):
                    $post_id = $blogposts->getFirstItem()->getPostId();
					
                    return $this->forawardToPost($request, $post_id, $pageId);
                endif;
              
                
            } else if ((($key = array_search($blog_route_key, $pathInfo)) !== FALSE) && (($key = array_search($post_route, $pathInfo)) !== FALSE) ) {
				
				$Identifier = end($pathInfo);
                $post_sufix = $permalinkOptions['post_sufix'];
                if ($permalinkOptions['post_sufix']) {
                    $post_identifier = trim(str_replace((string)$post_sufix, '', $Identifier));
                }else{
					$post_identifier = trim($Identifier);
				}
                $blogposts = $this->getBlogPostCollection();
                $blogposts->addFieldToFilter('identifier', array('eq' => $post_identifier));
                if ($blogposts->getFirstItem()->getPostId()):
                    $post_id = $blogposts->getFirstItem()->getPostId();
					
                    return $this->forawardToPost($request, $post_id, $pageId);
                endif;
              
            }else if (((($key = array_search($blog_route_key, $pathInfo))!== FALSE) || 
				(($key = array_search($category_route, $pathInfo))!== FALSE) ||
				(($key = array_search($post_route, $pathInfo))!== FALSE) ||
				(($key = array_search($blog_tag_route, $pathInfo))!== FALSE) ||
				(($key = array_search($blog_search_route, $pathInfo))!== FALSE) ||
				(($key = array_search($archive_route, $pathInfo))!== FALSE) || 
				(($key = array_search($author_route, $pathInfo))!== FALSE)) && 
				(($key = array_search($blog_search_route, $pathInfo)) === FALSE)) {
				
				
                $identifier = end($pathInfo);
                $category_sufix = $permalinkOptions['category_sufix'];
                if ($permalinkOptions['category_sufix']) {
                    $category_identifier = trim(str_replace((string)$category_sufix, '', $identifier));
                }else{
					$category_identifier = trim($identifier);
				}
                $blogcategories = $this->getBlogCategoryCollection();
                $blogcategories->addFieldToFilter('identifier', array('eq' => $category_identifier));
                if ($blogcategories->getFirstItem()->getCategoryId()):
                    $category_id = $blogcategories->getFirstItem()->getCategoryId();
                    return $this->forawardToCategory($request, $category_id, $pageId);
                endif;
                
                $post_sufix = $permalinkOptions['post_sufix'];
                if ($permalinkOptions['post_sufix']) {
                    $post_identifier = trim(str_replace((string)$post_sufix, '', $identifier));
                }else{
					$post_identifier = trim($identifier);
				}
                $blogposts = $this->getBlogPostCollection();
                $blogposts->addFieldToFilter('identifier', array('eq' => $post_identifier));
                if ($blogposts->getFirstItem()->getPostId()):
                    $post_id = $blogposts->getFirstItem()->getPostId();
                    
					return $this->forawardToPost($request, $post_id, $pageId);
                endif;
               
                $tag_sufix = $permalinkOptions['tag_sufix'];
                $blog_tag_page_url = str_replace((string)$tag_sufix, '', $identifier);
                $blogtags = $this->getBlogTagCollection();
                $identifier = trim(end($pathInfo));
                $identifier = explode(".", (string)$identifier);
                if (isset($identifier[0])) {
                    $blog_tag_page_url = $identifier[0];
                }
                $blogtags->addFieldToFilter('identifier', array('eq' => $blog_tag_page_url));
                $blogtags->getFirstItem()->getTagId();
                if ($blogtags->getFirstItem()->getTagId()):
                    $tag_id = $blogtags->getFirstItem()->getTagId();
                    $this->_registry->register('current_blog_tag', $blogtags->getFirstItem());
                    return $this->forawardToTag($request, $identifier, $pageId);
                endif;
                $new_identifier = $this->getBlogNewUrl($blog_tag_page_url, 'tag');
               
                $identifier = end($pathInfo);
                //$archive_identifier = explode("-",(string) $identifier);
				$archive_identifier = explode("~",(string) $identifier);
				
                if (isset($archive_identifier[0]) && is_numeric($archive_identifier[0]) && is_numeric($archive_identifier[1]) && isset($archive_identifier[1])) {
                    $archive_id = $identifier;
                    $this->_registry->register('current_blog_archive', $archive_id);
                    return $this->forawardToArchive($request, $archive_id, $pageId);
                }
                $author_user_name = end($pathInfo);
                $author_sufix = $permalinkOptions['author_sufix'];
                if ($permalinkOptions['author_sufix']) {
                    $author_identifier = trim(str_replace($author_sufix, '', $author_user_name));
                }
				//$authorInfo = explode("-",(string)$author_identifier);
				$authorInfo = explode("~",(string)$author_identifier);
				if(isset($authorInfo[0]) && isset($authorInfo[1]))
				{
					$fname = $authorInfo[0];
					$lname = $authorInfo[1];
					$admin_user = $this->_user->getCollection();
					$admin_user->addFieldToFilter('firstname', array('eq' => $fname));
					$admin_user->addFieldToFilter('lastname', array('eq' => $lname));
					if ($admin_user->getFirstItem()->getUserId()):
                    $author_id = $admin_user->getFirstItem()->getUserId();
				    $this->_registry->register('current_blog_author', $admin_user->getFirstItem());
                    return $this->forawardToAuthor($request, $author_id, $pageId);
                	endif;
				}
				
				
				
            } else if (($key = array_search($blog_search_route, $pathInfo)) !== FALSE) {
				
                $search_query = trim(end($pathInfo));
                if (($search_query) && ($blog_search_route != $search_query)):
                    return $this->forawardToSearch($request, $search_query, $pageId);
                else:
                    return $this->forawardToBlogPage($request, $pageId);
                endif;
            } else if (($key = array_search($archive_route, $pathInfo)) !== FALSE) {
                $archive_id = end($pathInfo);
                $this->_registry->register('current_blog_archive', $archive_id);
                return $this->forawardToArchive($request, $archive_id, $pageId);
            } else if (($key = array_search($blog_tag_route, $pathInfo))!== FALSE) {
                $tag_identifier_key = end($pathInfo);
                $tag_identifier = explode(".", (string)$tag_identifier_key);
                if (isset($tag_identifier[0])) {
                    $blog_tag_page_url = $tag_identifier[0];
                }
                $blogtags = $this->getBlogTagCollection();
                $blogtags->addFieldToFilter('identifier', array('eq' => $blog_tag_page_url));
                if ($blogtags->getFirstItem()->getTagId()):
                    $this->_registry->register('current_blog_tag', $blogtags->getFirstItem());
                    return $this->forawardToTag($request, $tag_identifier_key, $pageId);
                endif;
               
            } else if (($key = array_search($author_route, $pathInfo)) !== FALSE) {
                $author_user_name = end($pathInfo);
                $author_sufix = $permalinkOptions['author_sufix'];
                if ($permalinkOptions['author_sufix']) {
                    $author_identifier = trim(str_replace($author_sufix, '', $author_user_name));
                }
				
				//$authorInfo = explode("-",(string)$author_identifier);
				$authorInfo = explode("~",(string)$author_identifier);
				if(isset($authorInfo[0]) && isset($authorInfo[1]))
				{
					$fname = $authorInfo[0];
					$lname = $authorInfo[1];
					$admin_user = $this->_user->getCollection();
					$admin_user->addFieldToFilter('firstname', array('eq' => $fname));
					$admin_user->addFieldToFilter('lastname', array('eq' => $lname));
					if ($admin_user->getFirstItem()->getUserId()):
                    $author_id = $admin_user->getFirstItem()->getUserId();
				    $this->_registry->register('current_blog_author', $admin_user->getFirstItem());
                    return $this->forawardToAuthor($request, $author_id, $pageId);
                	endif;
				}
				
            }
			return $this->forawardToNoroute($request, $pageId);
			return null;
			
            endif;
		return null;
    }
    public function forawardToNoroute($request, $pageId) {
        $request->setModuleName('cms')->setControllerName('noroute')->setActionName('index');
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToAuthor($request, $author_id, $pageId) {
        $params = array();
		
		$request->setModuleName('blog')->setControllerName('author')->setActionName('view');
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
       $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('author')->setActionName('view')->setParam('author_id', $author_id));;
		
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToTag($request, $tag_identifier, $pageId) {
        $request->setModuleName('blog')->setControllerName('tag')->setActionName('view')->setParam('tag_identifier', $tag_identifier);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('tag')->setActionName('view')->setParam('tag_identifier', $tag_identifier));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToArchive($request, $archive_id, $pageId) {
		
        $request->setModuleName('blog')->setControllerName('archive')->setActionName('view')->setParam('archive_id', $archive_id);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('archive')->setActionName('view')->setParam('archive_id', $archive_id));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToPost($request, $post_id, $pageId) {
		$post = $this->_blogpostFactory->Create()->load($post_id);
        if ($post->getPostId()):
            $this->_registry->register('current_blog_post', $post);
        endif;
        $request->setModuleName('blog')->setControllerName('post')->setActionName('view')->setParam('post_id', $post_id);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('post')->setActionName('view')->setParam('post_id', $post_id));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToNewRedirect($request, $pageId, $new_identifier) {
        $request->setModuleName('blog')->setControllerName('urlrewrite')->setActionName('index')->setParam('urlrewrite', $new_identifier);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('urlrewrite')->setActionName('index')->setParam('urlrewrite', $new_identifier));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToNewStaticUrl($request, $pageId, $url) {
        $request->setModuleName('blog')->setControllerName('urlrewrite')->setActionName('fullurl')->setParam('url', $url);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('urlrewrite')->setActionName('fullurl')->setParam('url', $url));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToNewUrl($request, $pageId, $new_url) {
        $request->setModuleName('blog')->setControllerName('urlrewrite')->setActionName('newurl')->setParam('new_url', $new_url);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('urlrewrite')->setActionName('newurl')->setParam('new_url', $new_url));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToCategory($request, $cat_id, $pageId) {
        $category = $this->_blogcategoryFactory->Create()->load($cat_id);
        if ($category->getCategoryId()):
            $this->_registry->register('current_blog_category', $category);
        endif;
        $request->setModuleName('blog')->setControllerName('category')->setActionName('view')->setParam('category_id', $cat_id);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('category')->setActionName('view')->setParam('category_id', $cat_id));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToSearch($request, $search_query, $pageId) {
        $request->setModuleName('blog')->setControllerName('search')->setActionName('index')->setParam('search_query', $search_query);
        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
        $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('search')->setActionName('index')->setParam('search_query', $search_query));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function forawardToBlogPage($request, $pageId) {
		
        $request->setModuleName('blog')->setControllerName('index')->setActionName('index')->setParam('is_home', true);
		$request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $pageId);
		 $request->setPathInfo('/' . $request->setModuleName('blog')->setControllerName('index')->setActionName('index')->setParam('is_home', true));
        $queryParameters = $request->getQueryValue();
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }
    public function getBlogPostCollection() {
        $storeId = $this->_storeManager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $now = $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
		$blogposts = $this->_blogpostFactory->Create()->getCollection();
        $blogposts->addFieldToFilter('is_active', array('eq' => 1));
        $blogposts->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $blogposts->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
		$blogposts->addFieldToFilter('publish_time', ['lteq' => $now]);
		$blogposts->addFieldToFilter('save_as_draft', ['eq' => 0]);
		
		return $blogposts;
    }
    public function getBlogCategoryCollection() {
        $storeId = $this->_storeManager->getStore()->getId();
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
        else:
            $customerGroupId = 0;
        endif;
        $blogcategories = $this->_blogcategoryFactory->Create()->getCollection();
        $blogcategories->addFieldToFilter('is_active', array('eq' => 1));
        $blogcategories->addFieldToFilter(['store_id', 'store_id'], [['finset' => 0], ['finset' => $storeId]]);
        $blogcategories->addFieldToFilter('customer_group', array('finset' => $customerGroupId));
        return $blogcategories;
    }
    public function getBlogTagCollection() {
        $blogtags = $this->_blogtagFactory->Create()->getCollection();
        $blogtags->addFieldToFilter('is_active', array('eq' => 1));
        return $blogtags;
    }
    public function getBlogNewUrl($old_url, $type) {
        $storeId = $this->_storeManager->getStore()->getId();
        $blogurls = $this->_blogUrlRewriteFactory->Create()->getCollection();
        $blogurls->addFieldToFilter('type', array('eq' => $type));
        $blogurls->addFieldToFilter('old_url', array('eq' => $old_url));
        if ($blogurls->getFirstItem()->getUrlId()):
            $new_url = $blogurls->getFirstItem()->getNewUrl();
            $newblogurls = $this->_blogUrlRewriteFactory->Create()->getCollection();
            $newblogurls->addFieldToFilter('type', array('eq' => $type));
            $newblogurls->addFieldToFilter('old_url', array('eq' => $new_url));
            if ($newblogurls->getFirstItem()->getUrlId()):
                return $this->getBlogNewUrl($new_url, $type);
            endif;
            return $blogurls->getFirstItem()->getNewUrl();
        endif;
        return null;
    }
    public function getBlogUrlUpdate($old_url) {
        $storeId = $this->_storeManager->getStore()->getId();
        $blogurls = $this->_blogUrlRewriteFactory->Create()->getCollection();
        $blogurls->addFieldToFilter('old_url', array('eq' => $old_url));
        if ($blogurls->getFirstItem()->getUrlId()):
            $new_url = $blogurls->getFirstItem()->getNewUrl();
            $newblogurls = $this->_blogUrlRewriteFactory->Create()->getCollection();
            $newblogurls->addFieldToFilter('old_url', array('eq' => $new_url));
            if ($newblogurls->getFirstItem()->getUrlId()):
                return $this->getBlogUrlUpdate($new_url);
            endif;
            return $blogurls->getFirstItem()->getNewUrl();
        endif;
        return $old_url;
    }
}
