<?php
namespace Magebees\Onepagecheckout\Observer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Captcha\Observer\CaptchaStringResolver;

class CheckUserLoginObserver extends \Magento\Captcha\Observer\CheckUserLoginObserver
{
    protected $_helper;
    protected $_actionFlag;
    protected $messageManager;
    protected $_session;
    protected $captchaStringResolver;
    protected $_customerUrl;
    protected $customerRepository;
    protected $authentication;

    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Session\SessionManagerInterface $customerSession,
        CaptchaStringResolver $captchaStringResolver,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->_session = $customerSession;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->_customerUrl = $customerUrl;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formId = 'user_login';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $loginParams = $controller->getRequest()->getPost('login');
        $login = (is_array($loginParams) && array_key_exists('username', $loginParams)) ? $loginParams['username'] : null;
        if ($captchaModel->isRequired($login)) {
            $word = $this->captchaStringResolver->resolve($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word)) {
                try {
                    $customer = $this->getCustomerRepository()->get($login);
                    $this->getAuthentication()->processAuthenticationFailure($customer->getId());
                } catch (NoSuchEntityException $e) {

                }
				if($controller->getRequest()->getPost('mbopcinfo')){
					$this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
					$this->_session->setUsername($login);
					$this->_session->setOpcflag("invalidcaptcha");					
					$beforeUrl = $this->_session->getBeforeAuthUrl();
					$url = $beforeUrl ? $beforeUrl : $this->_customerUrl->getLoginUrl();
					$controller->getResponse()->setRedirect($url);
				}else{
					$this->messageManager->addErrorMessage(__('Incorrect CAPTCHA'));
					$this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
					$this->_session->setUsername($login);
					$beforeUrl = $this->_session->getBeforeAuthUrl();
					$url = $beforeUrl ? $beforeUrl : $this->_customerUrl->getLoginUrl();
					$controller->getResponse()->setRedirect($url);
				}
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }
	private function getCustomerRepository()
    {
        if (!($this->customerRepository instanceof \Magento\Customer\Api\CustomerRepositoryInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Customer\Api\CustomerRepositoryInterface::class
            );
        } else {
            return $this->customerRepository;
        }
    }
    private function getAuthentication()
    {
        if (!($this->authentication instanceof AuthenticationInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                AuthenticationInterface::class
            );
        } else {
            return $this->authentication;
        }
    }
}