<?php

namespace Magebees\Layerednavigation\Model\Config;

class CategoryMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $category_mode['0']='Default';
        $category_mode['1']='Dropdown';
        $category_mode['2']='With Sub-Categories';
        $category_mode['3']='Static 2 level Tree';
        $category_mode['4']='Advanced Categories';
        return $category_mode ;
    }
}
