<?php
namespace Magebees\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Source of option values in a form of value-label pairs
 */
class Posttype implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
		return [
            ['value' => 'featured', 'label' => __('Featured Post')],
            ['value' => 'latest', 'label' => __('Latest Post')],
            ['value' => 'custom', 'label' => __('Custom Post List')]
        ];
    }
}
