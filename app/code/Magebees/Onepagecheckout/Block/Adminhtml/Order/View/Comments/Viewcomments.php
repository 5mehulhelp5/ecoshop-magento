<?php
namespace Magebees\Onepagecheckout\Block\Adminhtml\Order\View\Comments;
class Viewcomments extends \Magento\Backend\Block\Template
{			
	public function getOrderFor($orderId)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);
		return $order->getOrderFor();
	}
	public function getOrderComments($orderId)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);
		return $order->getOrderComments();
	}
}