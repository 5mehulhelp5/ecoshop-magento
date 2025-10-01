<?php
namespace Magebees\PocoThemes\Model\Config\Source;

class SliderPaginationStyle implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'default', 'label' => __('Default')],
			['value' => 'dynamic', 'label' => __('Dynamic')],
			['value' => 'progress', 'label' => __('Progress')],
			['value' => 'fraction', 'label' => __('Fraction')],
			['value' => 'custom', 'label' => __('Custom')]
		
		];
	}
}