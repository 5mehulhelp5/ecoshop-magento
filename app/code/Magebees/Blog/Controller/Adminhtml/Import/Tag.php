<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;
class Tag extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $tag;
	protected $connectionFactory = null;
	protected $session;
    protected $_logger;
	protected $_timezoneInterface;
	
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
		\Magebees\Blog\Model\TagFactory $tag,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magebees\Blog\Block\Logger $logger,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    ) {
    
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->tag = $tag;
		$this->connectionFactory = $connectionFactory;
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
        	$all_tags = $this->session->getWPBlogTagInfo();
			$current_count = $params['current_count'];
			$total_tag = count($all_tags);
			if(isset($all_tags[$current_count]))
			{
				$currentTag = $all_tags[$current_count];
				$term_id = $currentTag['term_id'];
				
				$current_tag_key = array_search($term_id, array_column($all_tags, 'term_id'));
				
				$tag_name = $currentTag['tagname'];
				
				$tags = $this->tag->Create()->getCollection();
        		$tags->addFieldToFilter('title', array('eq' => $tag_name));
        		
				if ($tags->getFirstItem()->getTagId()):
					$this->_logger->info('Tag :: '. $tag_name .' Already Exists, So We Skipped it');
					$current_tag_id = $tags->getFirstItem()->getTagId();
					$all_tags[$current_count]['skipped'] = true;
				else:
					$all_tags[$current_count]['skipped'] = false;
					
				$data = array();
				
					if(!$currentTag['tagurl'])
					{

						$output = preg_replace('!\s+!', ' ', $currentTag['tagname']); // Replace Multiple Space
					}else{
						$output = preg_replace('!\s+!', ' ',  $currentTag['tagurl']); // Replace Multiple Space
					}
					$identifier = str_replace(' ', '-', strtolower($output)); // Replaces all spaces with hyphens.
					$identifier = preg_replace('/[^A-Za-z0-9\-]/', '', $identifier); // Removes special chars.
					$data['identifier'] = $identifier;
					$data['current_identifier'] = $identifier;
					
					$data['is_active'] = true;
					$data['title'] = $currentTag['tagname'];
					$data['content'] = $currentTag['description'];
					$data['created_time'] = $currentDate;
					$data['update_time'] = $currentDate;
					$data['is_imported'] = true;
					$data['term_id'] = $currentTag['term_id'];
					
					$tag = $this->tag->Create();
					$tag->setData($data);
					$tag->save();
					$current_tag_id = $tag->getTagId();
					$data['tag_id'] = $current_tag_id;
					$this->_eventManager->dispatch('magebees_blog_tag_url', ['data' => $data]);
				
				
				endif;
				
				if(($current_tag_id) && isset($all_tags[$current_count]))
					{
						$all_tags[$current_count]['magento_tag_id'] = $current_tag_id;
					}
					$this->session->setWPBlogTagInfo($all_tags);
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
			$response['wordpress_blog_tag_count'] = $total_tag;
			
		
			if($total_tag==($current_count))
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
