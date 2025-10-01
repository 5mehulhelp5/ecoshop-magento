<?php
namespace Magebees\Onepagecheckout\Plugin\Checkout\Controller\Index;
class Index extends \Magento\Checkout\Controller\Index\Index
{
	public function afterExecute()
    {
		$checkoutHelper = $this->_objectManager->get('Magento\Checkout\Helper\Data');
		if (!$checkoutHelper->canOnepageCheckout()) {
			$this->messageManager->addError(__('One-page checkout is turned off.'));
			return $this->resultRedirectFactory->create()->setPath('checkout/cart');
		}
		$quote = $this->getOnepage()->getQuote();
		if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
			return $this->resultRedirectFactory->create()->setPath('checkout/cart');
		}
		if (!$this->_customerSession->isLoggedIn() && !$checkoutHelper->isAllowedGuestCheckout($quote)) {
			$this->messageManager->addError(__('Guest checkout is disabled.'));
			return $this->resultRedirectFactory->create()->setPath('checkout/cart');
		}
		$this->_customerSession->regenerateId();
		$this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
		$this->getOnepage()->initCheckout();
		$resultPage = $this->resultPageFactory->create();
		$checkoutTitle = $this->_objectManager->get('Magebees\Onepagecheckout\Helper\Configurations')->getCheckoutTitle();
		 if ($this->_objectManager->get('Magebees\Onepagecheckout\Helper\Configurations')->getEnable()) {	 
			$resultPage->getLayout()->getUpdate()->addHandle('magebeesosc_layout');
			$resultPage->getConfig()->getTitle()->set(__($checkoutTitle));
		}else{
			$resultPage->getLayout()->getUpdate()->addHandle('checkout_index_index');						$resultPage->getConfig()->getTitle()->set(__('Checkout'));
		}		
		return $resultPage;
    }
}