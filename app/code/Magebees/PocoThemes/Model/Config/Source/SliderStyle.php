<?php
namespace Magebees\PocoThemes\Model\Config\Source;

class SliderStyle implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'style1', 'label' => __('Style 1')]
		
		];
	}
}