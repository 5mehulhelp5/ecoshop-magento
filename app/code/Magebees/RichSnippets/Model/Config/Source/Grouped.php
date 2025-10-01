<?php
namespace Magebees\RichSnippets\Model\Config\Source;

class Grouped implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => '0', 'label' => __('Main Offer')],
			['value' => '1', 'label' => __('List of Associated Products Offers')],		
			['value' => '2', 'label' => __('Aggregate Offer')],	
		];
	}
}