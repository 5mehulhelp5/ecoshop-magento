<?php

namespace Magebees\Layerednavigation\Model\Config;

class IncludeAttr implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		 $inputtype_arr=[];
        $inputtype_arr['0']='Include Attribute Code';
        $inputtype_arr['1']='Include Attribute Label';        
            return $inputtype_arr ;
    }
}
