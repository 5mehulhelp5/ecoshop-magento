<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;
class Copytables extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $connectionFactory = null;
	protected $session;
	protected $_import;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
		\Magebees\Blog\Model\Import $import,
		\Magento\Framework\Session\SessionManagerInterface $session
    ) {
    
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->connectionFactory = $connectionFactory;
		$this->_import = $import;
		$this->session = $session;
		
    }
    public function execute()
    {
		
		$params = $this->getRequest()->getParams();
		$response = array();
		try {
			$host = $params['db_host'];
			$db_prefix = $params['db_prefix'];
			$db_name = $params['db_name'];
			$db_password = $params['db_password'];
			$db_username = $params['db_user'];
			$blog_connection = mysqli_connect($host,$db_username,$db_password,$db_name);
			
			if (mysqli_connect_errno())
			  {
			  
				$response['error'] = "Failed to connect to MySQL: " . mysqli_connect_error();;
			  }
			
			$this->session->start();
				mysqli_set_charset($blog_connection, "utf8");
    	   		$_pref = mysqli_real_escape_string($blog_connection, $db_prefix);
				$wordpress_categories = 'SELECT
                    						a.term_id as term_id,
											a.description as description,
											a.parent as parentcategory,
											a.count as postcount,
											b.name as categoryname,
											b.slug as categoryurl
											FROM '.$_pref.'term_taxonomy a
											LEFT JOIN '.$_pref.'terms b on a.term_id = b.term_id
                							WHERE a.taxonomy = "category" AND b.slug <> "uncategorized" 
											ORDER BY parentcategory	 ASC';
				$wordpress_blog_category = array();
				if ($result = mysqli_query($blog_connection, $wordpress_categories)) {
				 while ($_categoriesinfo = mysqli_fetch_assoc($result)) {
					foreach (['term_id', 'description','parentcategory','postcount','categoryname','categoryurl'] as $key) {
                		$_categoriesinfo[$key] = mb_convert_encoding($_categoriesinfo[$key], 'HTML-ENTITIES', 'UTF-8');
						
            		}
					
					$wordpress_blog_category[] = $_categoriesinfo;
					}
					
					if(count($wordpress_blog_category)>0)
					{
					$wordpress_blog_category=array_combine(range(1, count($wordpress_blog_category)), $wordpress_blog_category);
					}
					
					
        			$this->session->setWPBlogCategoryCount(count($wordpress_blog_category));
					$this->session->setWPBlogCategoryInfo($wordpress_blog_category);
					$response['wordpress_blog_category_count'] = count($wordpress_blog_category);
				
				}
			
			
			//$response['wordpress_blog_category_info'] = json_encode($wordpress_blog_category);
			
			
			
			
			$wordpress_tags = 'SELECT
                    						a.term_id as term_id,
											a.description as description,
											a.count as postcount,
											b.name as tagname,
											b.slug as tagurl
											FROM '.$_pref.'term_taxonomy a
											LEFT JOIN '.$_pref.'terms b on a.term_id = b.term_id
                							WHERE a.taxonomy = "post_tag" AND b.slug <> "uncategorized"';
				$wordpress_blog_tags = array();
				if ($result = mysqli_query($blog_connection, $wordpress_tags)) {
				 while ($_tagsinfo = mysqli_fetch_assoc($result)) {
					foreach (['term_id', 'description','postcount','tagname','tagurl'] as $key) {
                		$_tagsinfo[$key] = mb_convert_encoding($_tagsinfo[$key], 'HTML-ENTITIES', 'UTF-8');
						
            		}
					  $wordpress_blog_tags[] = $_tagsinfo;
						
					}
					
					if(count($wordpress_blog_tags)>0)
					{
					$wordpress_blog_tags=array_combine(range(1, count($wordpress_blog_tags)), $wordpress_blog_tags);
					}
					
					
					
				}
					$response['wordpress_blog_tag_count'] = count($wordpress_blog_tags);
					$this->session->setWPBlogTagCount(count($wordpress_blog_tags));
					$this->session->setWPBlogTagInfo($wordpress_blog_tags);
			
				$wordpress_posts = 'SELECT ID FROM '.$_pref.'posts WHERE post_type = "post" ORDER BY ID ASC';
				$wordpress_blog_posts_ids = array();
				$post_ids = array();
				if ($result = mysqli_query($blog_connection, $wordpress_posts)) {
				 	while ($_postinfo = mysqli_fetch_assoc($result)) {
						foreach (['ID'] as $key) {
                			$_postinfo[$key] = mb_convert_encoding($_postinfo[$key], 'HTML-ENTITIES', 'UTF-8');
							$post_ids[] = mb_convert_encoding($_postinfo[$key], 'HTML-ENTITIES', 'UTF-8');
            			}	
					  	$wordpress_blog_posts_ids[] = $_postinfo;
					}
					
					if(count($wordpress_blog_posts_ids)>0)
					{
					$wordpress_blog_posts_ids=array_combine(range(1, count($wordpress_blog_posts_ids)), $wordpress_blog_posts_ids);
					}
					$response['wordpress_blog_post_count'] = count($wordpress_blog_posts_ids);
					$this->session->setWPBlogPostCount(count($wordpress_blog_posts_ids));
					$this->session->setWPBlogPostIds($wordpress_blog_posts_ids);
			}
			$post_id_str = implode(",",(array)$post_ids);
			
			 $wordpress_comments = 'SELECT Count(*) As CommentCount FROM '.$_pref.'comments WHERE comment_post_ID IN ('.$post_id_str.')';
				$wordpress_blog_posts_commentCount = null;
				
				if ($result = mysqli_query($blog_connection, $wordpress_comments)) {
				 	while ($_commentCount = mysqli_fetch_assoc($result)) {
						foreach (['CommentCount'] as $key) {
                			$wordpress_blog_posts_commentCount = mb_convert_encoding($_commentCount[$key], 'HTML-ENTITIES', 'UTF-8');
						}	
					}
					$response['wordpress_blog_post_comment_count'] = $wordpress_blog_posts_commentCount;
					$this->session->setWPBlogCommentCount($wordpress_blog_posts_commentCount);
					
			}
			$response['success'] = true;
			}
			catch (\Magento\Framework\Model\Exception $e) 
			{
				$this->messageManager->addError(__($e->getMessage()));
				$response['message'] = $e->getMessage();
				$response['success'] = false;
				$response['error'] = true;
			}
		
		 $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
	}
	
    protected function _isAllowed()
    {
        return true;
    }
}
