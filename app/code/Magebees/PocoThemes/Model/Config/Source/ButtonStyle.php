<?php
namespace Magebees\PocoThemes\Model\Config\Source;
class ButtonStyle implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>'square', 'label' => __('Square')],
			['value' =>'radius', 'label' => __('Radius')],
			['value' =>'rounded', 'label' => __('Rounded')],
			['value' =>'outlined', 'label' => __('Outlined')]
		];
    }
	
}