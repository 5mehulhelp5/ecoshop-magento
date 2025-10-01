<?php
namespace Magebees\PocoBase\Model\Config\Source;
class LayoutWidth implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>'full', 'label' => __('Full Width')],			['value' =>'custom', 'label' => __('Custom Width')],			['value' =>'boxed', 'label' => __('Boxed Width')]
		];
    }
	
}

