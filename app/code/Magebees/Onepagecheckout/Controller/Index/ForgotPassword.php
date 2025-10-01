<?php
namespace Magebees\Onepagecheckout\Controller\Index;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

class ForgotPassword extends \Magento\Framework\App\Action\Action
{
    protected $escaper;
    protected $session;
    protected $emailValidator;
    protected $customerAccountManagement;
	
    public function __construct(Context $context)
	{
		parent::__construct($context);
    }
	public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
		$data = $this->getRequest()->getParams();
	
		$emailValidator = $this->_objectManager->create('\Magento\Framework\Validator\EmailAddress');
		$customerAccountManagement = $this->_objectManager->create('\Magento\Customer\Api\AccountManagementInterface');
		
        if (!empty($data)) {
            $email = $data['email'];
			$responseData = array();
            $responseData['error'] = false;
            if (!$emailValidator->isValid($email)) {
                $this->session->setForgottenEmail($email);
                $responseData['message'] = __('Please correct the email address.');
            }
            try {
                $customerAccountManagement->initiatePasswordReset($email,AccountManagement::EMAIL_RESET);
            } catch (NoSuchEntityException $e) {
            } catch (\Exception $exception) {
                $responseData['message'] = __('We\'re unable to send the password reset email.');
            }
            $this->messageManager->addSuccessMessage($this->getSuccessMessage($email));
            $responseData['message'] = $this->getSuccessMessage($email);
        } else {
            $responseData['message'] = __('Please enter your email.');
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
		
		$result = "";
		$result = "<div class='message message-success success'><div data-ui-id='messages-message-success'><b style='font-size:12px'>".$responseData['message']."</b></div></div>";
		
		$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
    }
    protected function getSuccessMessage($email)
    {
		$escaper = $this->_objectManager->create('\Magento\Framework\Escaper');
        return __('If there is an account associated with %1 you will receive an email with a link to reset your password.',$escaper->escapeHtml($email));
    }	
}