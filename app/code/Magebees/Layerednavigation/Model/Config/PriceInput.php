<?php

namespace Magebees\Layerednavigation\Model\Config;

class PriceInput implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $inputtype_arr['0']='Default';
        $inputtype_arr['1']='Range Slider';
        $inputtype_arr['2']='Dropdown';
        $inputtype_arr['3']='From-To Only';
        
        
        return $inputtype_arr ;
    }
}
