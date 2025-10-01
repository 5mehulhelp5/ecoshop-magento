<?php
namespace Magebees\Onepagecheckout\Controller\Ordercommentdelete;
class Index extends \Magebees\Onepagecheckout\Controller\Index
{
    public function execute()
    {
        $checkoutSession = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        $result = $this->_objectManager->create('\Magento\Framework\Controller\Result\JsonFactory')->create();
        $checkoutSession->setOrdercommentsstatus('');
        $checkoutSession->setOrderCommentstext('');

        $result->setData(['success'=>'true']);
       	return $result;
    }
}
