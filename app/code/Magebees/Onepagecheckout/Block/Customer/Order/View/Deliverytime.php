<?php
namespace Magebees\Onepagecheckout\Block\Customer\Order\View;
use Magento\Framework\View\Element\Template;

class Deliverytime extends Template
{	
	protected $mbhelper;

	public function __construct(
		\Magebees\Onepagecheckout\Helper\Data $mbhelper,
		\Magento\Framework\View\Element\Template\Context $context,
		array $data = []
	) {
		$this->mbhelper = $mbhelper;
		parent::__construct($context, $data);
	}
	
	public function getDeliveryDate($orderId)
	{
		$order = $this->mbhelper->getObjectManager()->create('\Magento\Sales\Model\OrderRepository')->get($orderId);
		
		$localeDate = $this->mbhelper->getObjectManager()->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
		if($order->getDeliveryDate() != '0000-00-00 00:00:00') {
			$formattedDate = $localeDate->formatDateTime(
				$order->getDeliveryDate(),
				\IntlDateFormatter::MEDIUM,
				\IntlDateFormatter::MEDIUM,
				null,
				$localeDate->getConfigTimezone(
					\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
					$order->getStore()->getCode()
				)
			);
		} else {
			$formattedDate = __('N/A');
		}
		return $formattedDate;
	}
}