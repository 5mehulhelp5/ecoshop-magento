<?php
namespace Magebees\Onepagecheckout\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Checkout\Model\Session as CheckoutSession;

class Store extends \Magento\Framework\App\Action\Action
{
	public function execute()
    {
		$selectedstorecode = $this->getRequest()->getParam('selectedstorecode');
		if(isset($selectedstorecode)){			
			$checkoutSession = $this->_objectManager->create('\Magento\Checkout\Model\Session');
			$checkoutSession->setData('pickupLocationCode', $selectedstorecode);
		}
		return;		
    }
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Onepagecheckout::store');
    }
}