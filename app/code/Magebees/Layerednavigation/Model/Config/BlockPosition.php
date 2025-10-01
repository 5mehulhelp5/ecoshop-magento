<?php

namespace Magebees\Layerednavigation\Model\Config;

class BlockPosition implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $blockpos_arr['0']='Sidebar';
        $blockpos_arr['1']='Top';
        $blockpos_arr['2']='Both';
            return $blockpos_arr ;
    }
}
