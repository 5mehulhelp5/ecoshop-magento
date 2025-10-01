<?php
namespace Magebees\MPM\Model\Config\Source; 
class Popuptype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
       return [
            ['value' => 0, 'label' => __('Custom Message Popup')],
            ['value' => 1, 'label' => __('Magento Default Message Popup')],
        ];
    }
}
