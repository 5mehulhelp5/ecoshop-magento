<?php
namespace Magebees\Testimonial\Model;

class Formfield implements \Magento\Framework\Option\ArrayInterface
{
     /**
      * Options getter
      *
      * @return array
      */
    public function toOptionArray()
    {
        return [
            ['value' =>'0', 'label' => __('Address')],
            ['value' =>'1', 'label' => __('Company Name')],
            ['value' =>'2', 'label' => __('Company Website')],
            ['value' =>'3', 'label' => __('Youtube Video URL')],
            ['value' =>'4', 'label' => __('Upload Profile Image')],
            ['value' =>'5', 'label' => __('Rating')]
        ];
    }
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    
    public function toArray()
    {
        return [
        '0'=> __('Address'),
        '1'=> __('Company Name'),
        '2'=> __('Company Website'),
        '3'=> __('Youtube Video URL'),
        '4'=> __('Upload Profile Image'),
        '5'=> __('Rating'),
        ];
    }
}
