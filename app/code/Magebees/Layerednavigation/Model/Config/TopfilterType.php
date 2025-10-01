<?php

namespace Magebees\Layerednavigation\Model\Config;

class TopfilterType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $filtertype_arr=[];
        $filtertype_arr['0']='Horizontal Drop-Downs';
        $filtertype_arr['1']='Horizontal Boxes';
            return $filtertype_arr ;
    }
}
