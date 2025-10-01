<?php
namespace Magebees\PocoThemes\Model\Config\Source;

class ThumbStyle implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'horizontal', 'label' => __('Horizontal')],
			['value' => 'vertical', 'label' => __('Vertical')],
			['value' => 'dots', 'label' => __('Dots')],
			['value' => 'grid', 'label' => __('Grid View')],
		];
	}
}

