<?php
namespace Magebees\PocoThemes\Model\Config\Source;

class RowPerSlide {
	
	public function toOptionArray() {
		
        return [
			['value' =>'4', 'label' => __('4')],
			['value' =>'5', 'label' => __('5')],
			['value' =>'6', 'label' => __('6')]
		];
    }
}

