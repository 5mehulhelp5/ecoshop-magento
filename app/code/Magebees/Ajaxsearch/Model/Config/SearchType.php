<?php

namespace Magebees\Ajaxsearch\Model\Config;

class SearchType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $searchtype_arr['0']='Like';
        $searchtype_arr['1']='Fulltext';
        $searchtype_arr['2']='Combine(Like and Fulltext)';
        
        return $searchtype_arr ;
    }
}
