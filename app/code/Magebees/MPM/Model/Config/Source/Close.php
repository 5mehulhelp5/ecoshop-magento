<?php
namespace Magebees\MPM\Model\Config\Source; 
class Close implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
       /*return [
            ['value' => 0, 'label' => __('Auto')],
            ['value' => 1, 'label' => __('Click On Message')],
			['value' => 2, 'label' => __('Click On Windows')],
            ['value' => 3, 'label' => __('Do Not Close')],
        ];*/
		return [
            ['value' => 0, 'label' => __('Auto')],
            ['value' => 1, 'label' => __('Click On Message')],
        ];
    }
}
