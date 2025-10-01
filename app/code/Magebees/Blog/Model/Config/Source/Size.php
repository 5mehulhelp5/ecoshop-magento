<?php

namespace Magebees\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Source of option values in a form of value-label pairs
 */
class Size implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
		return [
            ['value' => 'compact', 'label' => __('Compact')],
            ['value' => 'normal' , 'label' => __('Normal')]
        ];
    }
}
