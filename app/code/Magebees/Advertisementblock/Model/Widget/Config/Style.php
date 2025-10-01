<?php
namespace Magebees\Advertisementblock\Model\Widget\Config;

class Style implements \Magento\Framework\Option\ArrayInterface
{
     /**
      * Options getter
      *
      * @return array
      */
    public function toOptionArray()
    {
        $style_arr=[];
        $style_arr['0']='Box(Default)';
        $style_arr['1']='Full Width';
        return $style_arr;
    }
}
