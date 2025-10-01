<?php
namespace Magebees\Testimonial\Model;

class Adminmail implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' =>'default', 'label' => __('Default General Contact')],
            ['value' =>'custom', 'label' => __('Add new email for Admin')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    
    
    public function toArray()
    {
        return ['default'=> __('Default General Contact'), 'custom'=> __('Add new email for Admin')];
    }
}
