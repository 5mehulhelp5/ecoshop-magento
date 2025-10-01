<?php
namespace Magebees\Onepagecheckout\Controller\Account;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\AbstractAccount;

class Login extends AbstractAccount
{
    protected $session;
    protected $resultPageFactory;
	
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
		$sesstion = $this->_objectManager->get('Magento\Framework\Session\SessionManagerInterface');		
			if ($this->session->isLoggedIn()) {
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('*/*/');
				return $resultRedirect;
			}
		if($sesstion->getOpcflag()){
			$sesstion->setOpcflag("");
			$config= $this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory');
			$msg = __('Incorrect CAPTCHA.');
			$response = ['error' => true, 'type'=> 'captcha' ,'message' => $msg];
			return $config->create()->setData($response);	
		}else{
			/** @var \Magento\Framework\View\Result\Page $resultPage */
			$resultPage = $this->resultPageFactory->create();
			$resultPage->setHeader('Login-Required', 'true');
			return $resultPage;
		}		
    }
}
