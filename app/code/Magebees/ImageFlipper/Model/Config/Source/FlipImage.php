<?php

namespace Magebees\ImageFlipper\Model\Config\Source;

class FlipImage implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('None')],['value' => 'Y', 'label' => __('Horizontal')], ['value' =>'X', 'label' => __('Vertical')],['value' =>1, 'label' => __('Rotate')]];
    }
    
    public function toArray()
    {
        return [0 => __('None'),'Y' => __('Horizontal'),'X'=> __('Vertical'),1 => __('Rotate')];
    }
}
