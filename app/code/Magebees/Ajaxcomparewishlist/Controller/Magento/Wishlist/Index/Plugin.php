<?php
namespace Magebees\Ajaxcomparewishlist\Controller\Magento\Wishlist\Index;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Store\Model\ScopeInterface;

class Plugin extends \Magento\Wishlist\Controller\Index\Plugin
{
    protected $customerSession;
    protected $authenticationState;
    protected $config;
    protected $redirector;
    private $messageManager;
	
    public function __construct(
        CustomerSession $customerSession,
        \Magento\Wishlist\Model\AuthenticationStateInterface $authenticationState,
        ScopeConfigInterface $config,
        RedirectInterface $redirector,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->customerSession = $customerSession;
        $this->authenticationState = $authenticationState;
        $this->config = $config;
        $this->redirector = $redirector;
        $this->messageManager = $messageManager;
    }
	
    public function beforeDispatch(\Magento\Framework\App\ActionInterface $subject, RequestInterface $request)
    {
        if ($this->authenticationState->isEnabled() && !$this->customerSession->authenticate()) {
            $subject->getActionFlag()->set('', 'no-dispatch', true);
            if (!$this->customerSession->getBeforeWishlistUrl()) {
                $this->customerSession->setBeforeWishlistUrl($this->redirector->getRefererUrl());
            }
            $data = $request->getParams();
            unset($data['login']);
            $this->customerSession->setBeforeWishlistRequest($data);
            $this->customerSession->setBeforeRequestParams($this->customerSession->getBeforeWishlistRequest());
            $this->customerSession->setBeforeModuleName('wishlist');
            $this->customerSession->setBeforeControllerName('index');
            $this->customerSession->setBeforeAction('add');

            //if ($request->getActionName() == 'add') {
			if ($request->getActionName() == 'add' && !$request->isAjax() && isset($sessVal)) {
                $this->messageManager->addErrorMessage(__('You must login or register to add items to your wishlist.'));
            }
        }
        if (!$this->config->isSetFlag('wishlist/general/active', ScopeInterface::SCOPE_STORES)) {
            throw new NotFoundException(__('Page not found.'));
        }
    }
}

	