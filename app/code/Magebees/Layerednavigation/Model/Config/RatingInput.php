<?php

namespace Magebees\Layerednavigation\Model\Config;

class RatingInput implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $inputtype_arr['0']='Default';
        $inputtype_arr['1']='Dropdown';
        
        return $inputtype_arr ;
    }
}
