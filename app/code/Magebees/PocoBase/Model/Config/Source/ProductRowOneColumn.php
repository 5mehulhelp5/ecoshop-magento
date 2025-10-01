<?php
namespace Magebees\PocoBase\Model\Config\Source;

class ProductRowOneColumn {
	
	public function toOptionArray() {
		
        return [
			['value' =>'3', 'label' => __('3')],
			['value' =>'4', 'label' => __('4')],
			['value' =>'5', 'label' => __('5')]
		];
    }
}

