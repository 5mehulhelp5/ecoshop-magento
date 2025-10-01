<?php
namespace Magebees\PocoBase\Model\Config\Source;

class ProductRowTwoColumns {
	
	public function toOptionArray() {
		
        return [
			['value' =>'2', 'label' => __('2')],
			['value' =>'3', 'label' => __('3')],
			['value' =>'4', 'label' => __('4')]
		];
    }
}

