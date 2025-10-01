<?php
namespace Magebees\MPM\Model\Config\Source; 
class Position implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
       return [
            ['value' => 0, 'label' => __('Top')],
            ['value' => 1, 'label' => __('Top Left')],
            ['value' => 2, 'label' => __('Top Right')],
            ['value' => 3, 'label' => __('Bottom')],
            ['value' => 4, 'label' => __('Bottom Left')],
            ['value' => 5, 'label' => __('Bottom Right')],
			['value' => 6, 'label' => __('Middle')],
            ['value' => 7, 'label' => __('Middle Left')],
            ['value' => 8, 'label' => __('Middle Right')],
        ];
    }
}
