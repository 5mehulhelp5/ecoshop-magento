<?php
namespace Magebees\PocoBase\Model\Config\Source;
class StickyOptions implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>'0', 'label' => __('No Sticky')],
			['value' =>'1', 'label' => __('Reverce Sticky')],
			['value' =>'2', 'label' => __('Sticky')]
		];
    }
	
}

