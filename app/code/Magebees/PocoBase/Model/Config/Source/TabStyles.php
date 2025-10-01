<?php
namespace Magebees\PocoBase\Model\Config\Source;

class TabStyles implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'default', 'label' => __('Horizontal')],
			['value' => 'vertical', 'label' => __('Vertical')],
			['value' => 'accordion', 'label' => __('Accordion')],
		];
	}
}

