<?php
namespace Magebees\CategoryImage\Model;

class Rowselect implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 2, 'label' => __('2')], ['value' =>3, 'label' => __('3')],['value' =>4, 'label' => __('4')],['value' =>5, 'label' => __('5')]];
    }
    
    public function toArray()
    {
        return [5 => __('5'),4 => __('4'),3 => __('3'), 2 => __('2')];
    }
}
