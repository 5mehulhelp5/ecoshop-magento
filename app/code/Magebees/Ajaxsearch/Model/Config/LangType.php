<?php

namespace Magebees\Ajaxsearch\Model\Config;

class LangType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $langtype_arr['0']='Left-to-Right';
        $langtype_arr['1']='Right-to-Left';
        return $langtype_arr ;
    }
}
