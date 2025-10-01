<?php
namespace Magebees\PocoThemes\Model\Config\Source;

class CookieStyle implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
			['value' => 'style1', 'label' => __('Style 1')],
			['value' => 'style2', 'label' => __('Style 2')]
		];
	}
}