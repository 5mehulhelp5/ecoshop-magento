<?php

namespace Magebees\AjaxInfiniteScroll\Model\Config;

class LoadingType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $loading_type=[];
        $loading_type['0']='On page scroll';
        $loading_type['1']='On button click';
        
        return $loading_type ;
    }
}
