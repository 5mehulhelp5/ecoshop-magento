<?php
namespace Magebees\PocoBase\Model\Config\Source;
class PopupNotification implements \Magento\Framework\Option\ArrayInterface
{	
	protected $_promotionsnotification;
	public function __construct(
		\Magebees\Promotionsnotification\Model\Promotionsnotification $promotionsnotification
	) {
		$this->_promotionsnotification = $promotionsnotification;
	}
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
    	$res = $this->_promotionsnotification->getCollection()->addFieldToFilter('notification_style','popup');
		return $res->toOptionArray();
	}
}
