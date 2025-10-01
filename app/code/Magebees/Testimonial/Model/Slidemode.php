<?php
namespace Magebees\Testimonial\Model;

class Slidemode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' =>'0', 'label' => __('Horizontal')],
            ['value' =>'1', 'label' => __('Vertical')],
            ['value' =>'2', 'label' => __('Fade')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    
    
    public function toArray()
    {
        return ['0'=> __('Horizontal'), '1'=> __('Vertical'),'2'=> __('Fade')];
    }
}
