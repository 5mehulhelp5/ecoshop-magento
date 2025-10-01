<?php
namespace Magebees\PocoBase\Model\Config\Source;
class Tabs implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'timeline', 'label' => __('Timeline')],
			['value' => 'events', 'label' => __('Events')],
			['value' => 'messages', 'label' => __('Message')],
		];
	}
}

