<?php
namespace Magebees\PocoBase\Model\Config\Source;
class ScrollPosition implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>'left', 'label' => __('Left')],
			['value' =>'right', 'label' => __('Right')]
		];
    }
	
}

