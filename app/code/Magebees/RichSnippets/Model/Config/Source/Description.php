<?php
namespace Magebees\RichSnippets\Model\Config\Source;

class Description implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => '0', 'label' => __('None')],
			['value' => '1', 'label' => __('Product Full Description')],	
			['value' => '2', 'label' => __('Product Short Description')],	
			['value' => '3', 'label' => __('Product Meta Description')],
		];
	}
}