<?php
namespace Magebees\AjaxCategoryProducts\Model;

class PageLayout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' =>'top', 'label' => __('Top')],
            ['value' =>'left', 'label' => __('Left')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
       
    public function toArray()
    {
        return ['top'=> __('Top'), 'left'=> __('Left')];
    }
}
