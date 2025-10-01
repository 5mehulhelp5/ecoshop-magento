<?php
namespace Magebees\PocoBase\Model\Config\Source;
class Device implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'both', 'label' => __('Both')],
			['value' => 'only_mobile', 'label' => __('Only Mobile')],
			['value' => 'only_desktop', 'label' => __('Only Desktop')]
		];
	}
}