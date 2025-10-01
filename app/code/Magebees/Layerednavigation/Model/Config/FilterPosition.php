<?php

namespace Magebees\Layerednavigation\Model\Config;

class FilterPosition implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $filterpos_arr['0']='At the top';
        $filterpos_arr['1']='At the bottom';
        
        return $filterpos_arr ;
    }
}
