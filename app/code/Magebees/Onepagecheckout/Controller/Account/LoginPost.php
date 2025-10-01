<?php
namespace Magebees\Onepagecheckout\Controller\Account;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;

class LoginPost extends \Magento\Customer\Controller\Account\LoginPost 
{
    public function execute() 
	{	
		if($this->getRequest()->getPost('mbopcinfo')){
			$response = array();
			if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('home');
				return $resultRedirect;
			}
			
			if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
			
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                        $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                        $metadata->setPath('/');
                        $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                    }
                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                    if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectUrl) {
                        $this->accountRedirect->clearRedirectCookie();
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setUrl($this->_redirect->success($redirectUrl));
                        return $resultRedirect;
                    }
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __('This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',$value);
					$response = ['error' => true, 'type'=> 'login', 'message' => $message];
					
                } catch (UserLockedException $e) {
                    $message = __('The account sign-in was incorrect or your account is disabled temporarily. '. 'Please wait and try again later.');
					$response = ['error' => true, 'type'=> 'login', 'message' => $message];
                } catch (AuthenticationException $e) {
                    $message = __('The account sign-in was incorrect or your account is disabled temporarily. '. 'Please wait and try again later.');
					$response = ['error' => true, 'type'=> 'login', 'message' => $message];
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
					$message = __('An unspecified error occurred. Please contact us for assistance.');
					$response = ['error' => true, 'type'=> 'login', 'message' => $message];
                } finally {
                    if (isset($message)) {
						$response = ['error' => true, 'message' => $message];
                        $this->session->setUsername($login['username']);
                    }
                }
            } else {
				$message = __('A login and a password are required.');
				$response = ['error' => true, 'type'=> 'login', 'message' => $message];
            }
        }
		$config= $this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory');
		return $config->create()->setData($response);
		}else{
			return parent::execute();
		}
    }
	private function getCookieManager()
	{
		if (!$this->cookieMetadataManager) {
			$this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Stdlib\Cookie\PhpCookieManager::class);
		}
		return $this->cookieMetadataManager;
	}
}