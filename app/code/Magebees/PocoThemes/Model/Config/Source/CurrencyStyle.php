<?php
namespace Magebees\PocoThemes\Model\Config\Source;

class CurrencyStyle implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray() {
		return [
			['value' => 'symbol', 'label' => __('Currency Symbol')],
			['value' => 'code', 'label' => __('Currency Code')],
			['value' => 'symbol-code', 'label' => __('Currency Symbol + Code')],
			['value' => 'code-symbol', 'label' => __('Currency Code + Symbol')],
			
		];
	}
}
