<?php

namespace Magebees\Layerednavigation\Model\Config;

class SpecialChar implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $inputtype_arr['0']='-';
        $inputtype_arr['1']='_';        
            return $inputtype_arr ;
    }
}
