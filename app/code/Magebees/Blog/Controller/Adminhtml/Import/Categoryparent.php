<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;
class Categoryparent extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $connectionFactory = null;
	protected $session;
	protected $category;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
		\Magebees\Blog\Model\Category $category,
		\Magento\Framework\Session\SessionManagerInterface $session
		
    ) {
    
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->category = $category;
		$this->connectionFactory = $connectionFactory;
		$this->session = $session;
    }
    public function execute()
    {
		$params = $this->getRequest()->getParams();
		$response = array();
		try {
			$this->session->start();
        	$all_categories = $this->session->getWPBlogCategoryInfo();
			$current_count = $params['current_count'];
			$total_category = count($all_categories);
			
			if(isset($all_categories[$current_count]) && isset($all_categories[$current_count]['magento_category_id']) 
			 )
			{
				$currentCategory = $all_categories[$current_count];
				
				$data = array();
				
				$term_id = $currentCategory['term_id'];
				$wordpress_parent_id = $currentCategory['parentcategory'];
				
				if(($wordpress_parent_id) && (!$all_categories[$current_count]['skipped']))
				{
				$magento_category_id = $all_categories[$current_count]['magento_category_id'];
				
				$wp_parent_category_key = array_search($wordpress_parent_id, array_column($all_categories, 'term_id'));
				$magento_parent_cat_id = $all_categories[$wp_parent_category_key]['magento_category_id'];
				$all_categories[$current_count]['magento_parent_category_id']=$magento_parent_cat_id;
					$currentCategory = $this->category->load($magento_category_id);
					$currentCategory->setParentCategoryId($magento_parent_cat_id);
					$currentCategory->save();
				}
					
			}
			$this->session->setWPBlogCategoryInfo($all_categories);
			$response['success'] = true;	
			}
			catch (\Magento\Framework\Model\Exception $e) 
			{
				$this->messageManager->addError(__($e->getMessage()));
				$response['message'] = $e->getMessage();
				$response['success'] = false;
				$response['error'] = true;
			}
			$response['wordpress_blog_category_count'] = $total_category;
			$response['current_count'] = $current_count;	
			if($total_category==($current_count))
			{
			$response['next'] = false;
			}else{
			$response['next'] = true;
			
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
