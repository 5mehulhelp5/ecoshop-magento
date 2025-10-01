<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;
class Connection extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $connectionFactory = null;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
		\Magebees\Blog\Model\Import $import
    ) {
    
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->import = $import;
		$this->connectionFactory = $connectionFactory;
		
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
				
			$connection = $this->import->getConnection($host,$db_username,$db_password,$db_name);
				
			//$connection = $this->_connect = mysqli_connect($host,$db_username,$db_password,$db_name);
			mysqli_set_charset($connection, "utf8");
    	   	$_pref = mysqli_real_escape_string($connection, $db_prefix);
			
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
