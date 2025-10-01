<?php
namespace Magebees\Promotionsnotification\Block\View;
class Bar extends \Magebees\Promotionsnotification\Block\View
{
    public function getNotificationCollection($mode = "bar")
    {
        $notification_collection = parent::getNotificationCollection($mode);
        return $notification_collection;
    }
	
	public function getNotifictionBarByStore($notification_ids, $mode = "bar")
	{
		$notification_collection = $this->_notificationFactory->create()->getCollection();
		$notification_collection->addFieldToFilter('status', 1);
		$notification_collection->addFieldToFilter('notification_style', $mode);

		$now = $this->_localeDate->date()->format('Y-m-d H:i:s');
		$notification_collection->addFieldToFilter('from_date', ['lt' => $now]);
		$notification_collection->addFieldToFilter('to_date', ['gt' => $now]);

		// store filter
		$store_id = $this->_storeManager->getStore()->getId();
		if (!$this->_storeManager->isSingleStoreMode()) {
			$notification_collection->storeFilter($store_id);
		}

		// customer group filter
		$customer_id = $this->_customerSession->getCustomerGroupId();
		$notification_collection->customerFilter($customer_id);

		// notification_ids filter (if provided)
		if (!empty($notification_ids)) {
			$notification_collection->addFieldToFilter('main_table.notification_id', ['in' => $notification_ids]);
		}
		
		return $notification_collection;
	}
	
    public function addTop()
    {
        if ($this->getDisplayPosition()=="top") {
            $this->setTemplate('Magebees_Promotionsnotification::bar.phtml');
        }
    }
    public function addBottom()
    {	
		if ($this->getDisplayPosition()=="bottom") {
            $this->setTemplate('Magebees_Promotionsnotification::bar.phtml');
        }
    }
}