<?php
namespace Magebees\CacheWarmer\Model\Config\Source;

class PageType implements \Magento\Framework\Option\ArrayInterface
{
    public const TYPE_INDEX    = 'index';
    public const TYPE_CMS      = 'cms';
    public const TYPE_PRODUCT  = 'product';
    public const TYPE_CATEGORY = 'category';

    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Home Page'),
                'value' => self::TYPE_INDEX
            ],
            [
                'label' => __('CMS pages'),
                'value' => self::TYPE_CMS
            ],
            [
                'label' => __('Product pages'),
                'value' => self::TYPE_PRODUCT
            ],
            [
                'label' => __('Category pages'),
                'value' => self::TYPE_CATEGORY
            ],
        ];

        return $options;
    }

    public function toArray()
    {
        $options = $this->toOptionArray();

        $result = array_combine(
            array_column($options, 'value'),
            array_column($options, 'label')
        );

        return $result;
    }
}
