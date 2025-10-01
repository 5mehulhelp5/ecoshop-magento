<?php
namespace Magebees\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Source of option values in a form of value-label pairs
 */
class ImageResizetype implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
		return [
            ['value' => 'resizeonly', 'label' => __('Resize Only')],
            ['value' => 'cropandresize', 'label' => __('Crop & Resize')]
        ];
    }
}
