<?php
namespace Magebees\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Source of option values in a form of value-label pairs
 */
class CaptchaType implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
		return [
            ['value' => 'visible', 'label' => __('Visible')],
            ['value' => 'invisible', 'label' => __('In Visible')]
        ];
    }
}
