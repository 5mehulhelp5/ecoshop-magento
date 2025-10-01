<?php

namespace Magebees\Layerednavigation\Model\Config;

class UrlType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $inputtype_arr['0']='With GET Parameters';
        $inputtype_arr['1']='Long With URL Key';
        $inputtype_arr['2']='Short Without URL Key';
            return $inputtype_arr ;
    }
}
