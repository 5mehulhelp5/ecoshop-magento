<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;
class Category extends \Magento\Backend\App\Action
{
    
	protected $_scopeConfig;
	protected $category;
	protected $connectionFactory = null;
	protected $customergroup;
    protected $session;
	protected $_logger;
	protected $_timezoneInterface;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
		\Magebees\Blog\Model\CategoryFactory $category,
		\Magento\Customer\Model\Group $customergroup,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magebees\Blog\Block\Logger $logger,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    ) {
    
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->category = $category;
		$this->connectionFactory = $connectionFactory;
		$this->customergroup = $customergroup;
		$this->session = $session;
		$this->_logger = $logger;
		$this->_timezoneInterface = $timezoneInterface;
		
    }
    public function execute()
    {
		
		       
		$currentDate = $this->_timezoneInterface->date()->format('Y-m-d H:i:s'); 
		$params = $this->getRequest()->getParams();
		$response = array();
		try {
			
			$this->session->start();
        	$all_categories = $this->session->getWPBlogCategoryInfo();
			$all_tags = $this->session->getWPBlogTagInfo();
			
			
			$current_count = $params['current_count'];
			$total_category = count($all_categories);
			if(isset($all_categories[$current_count]))
			{
				$currentCategory = $all_categories[$current_count];
				$term_id = $currentCategory['term_id'];
				$store_ids =  implode(",",(array)$params['selectedStores']);
				$current_cat_key = array_search($term_id, array_column($all_categories, 'term_id'));
				
				$category_name = $currentCategory['categoryname'];
				
				$categories = $this->category->Create()->getCollection();
        		$categories->addFieldToFilter('title', array('eq' => $category_name));
        		
				if ($categories->getFirstItem()->getCategoryId()):
					$this->_logger->info('Category :: '.$category_name .' Already Exists, So We Skipped it');
					$current_category_id = $categories->getFirstItem()->getCategoryId();
					$all_categories[$current_count]['skipped'] = true;
				else:
					$all_categories[$current_count]['skipped'] = false;
					
				$data = array();
				$customer_group_ids = array();
				 $customergroupcollection = $this->customergroup->getCollection();
        		foreach ($customergroupcollection as $value) {
            			$customer_group_ids[] = $value->getCustomerGroupId();
        		}
				
				
				
				
				
					if(!$currentCategory['categoryurl'])
					{

						$output = preg_replace('!\s+!', ' ', $currentCategory['categoryname']); // Replace Multiple Space
					}else{
						$output = preg_replace('!\s+!', ' ',  $currentCategory['categoryurl']); // Replace Multiple Space
					}
					$identifier = str_replace(' ', '-', strtolower($output)); // Replaces all spaces with hyphens.
					$identifier = preg_replace('/[^A-Za-z0-9\-]/', '', $identifier); // Removes special chars.
					$data['identifier'] = $identifier;
					$data['current_identifier'] = $identifier;
					
					$data['is_active'] = true;
					$data['title'] = $currentCategory['categoryname'];
					$data['parent_category_id'] = $currentCategory['parentcategory'];
					$data['position'] = 0;
					$data['content'] = $currentCategory['description'];
				
					$data['display_mode'] = false;
					$data['posts_sort_by'] = false;
					$data['customer_group'] = implode(",",(array)$customer_group_ids);
					$data['category_website'] = $store_ids;
					$data['include_in_menu'] = true;
					$data['store_id'] = $store_ids;
					$data['created_time'] = $currentDate;
					$data['update_time'] = $currentDate;
					$data['is_imported'] = true;
					$data['term_id'] = $term_id;
					$category = $this->category->Create();
				
					$category->setData($data);
					$category->save();
					$current_category_id = $category->getCategoryId();
				
					$data['category_id'] = $current_category_id;
					$this->_eventManager->dispatch('magebees_blog_category_url', ['data' => $data]);
					$this->_logger->info($category_name .' Created From Wordpress');
				
				endif;
				
				if(($current_category_id) && isset($all_categories[$current_count]))
					{
						$all_categories[$current_count]['magento_category_id'] = $current_category_id;
					}
					$this->session->setWPBlogCategoryInfo($all_categories);
					$response['success'] = true;	
			}
			}
			catch (\Magento\Framework\Model\Exception $e) 
			{
				$this->messageManager->addError(__($e->getMessage()));
				$response['message'] = $e->getMessage();
				$response['success'] = false;
				$response['error'] = true;
			}
			$response['wordpress_blog_category_count'] = $total_category;
			
		
			if($total_category==($current_count))
			{
			$response['next'] = false;
			}else{
			$response['next'] = true;
			$response['current_count'] = $current_count;
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
