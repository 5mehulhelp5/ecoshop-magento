<?php

namespace Magebees\Layerednavigation\Model\Config;

class ReplaceCharAll implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
		$inputtype_arr=[];
        $inputtype_arr['0']='Replace all special character';
        $inputtype_arr['1']='Replace only white space';        
            return $inputtype_arr ;
    }
}
