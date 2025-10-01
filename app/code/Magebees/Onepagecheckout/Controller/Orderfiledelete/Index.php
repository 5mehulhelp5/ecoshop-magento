<?php
namespace Magebees\Onepagecheckout\Controller\Orderfiledelete;
class Index extends \Magebees\Onepagecheckout\Controller\Index
{
    public function execute()
    {
        $checkoutSession = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        $result = $this->_objectManager->create('\Magento\Framework\Controller\Result\JsonFactory')->create();
        $checkoutSession->setFileuploadvalue('');
        $checkoutSession->setOrderForFile('');
        $checkoutSession->setFileuploadvaluestatus('0');
        $result->setData(['success'=>'true']);
       	return $result;
    }
}
