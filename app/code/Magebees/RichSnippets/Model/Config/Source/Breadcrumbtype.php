<?php
namespace Magebees\RichSnippets\Model\Config\Source;

class Breadcrumbtype implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => '0', 'label' => __('Default(Long)')],
			['value' => '1', 'label' => __('Short')],		
		];
	}
}