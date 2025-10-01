<?php

namespace Magebees\EmailEditor\Model\Config;

class FontStyle implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
    	$fontstyle_arr=[];
        $fontstyle_arr['normal']='Normal';
        $fontstyle_arr['italic']='Italic';
         $fontstyle_arr['oblique']='Oblique';
        return $fontstyle_arr ;
    }
}
