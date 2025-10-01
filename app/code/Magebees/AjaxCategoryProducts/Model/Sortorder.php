<?php
namespace Magebees\AjaxCategoryProducts\Model;

class Sortorder implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' =>'ASC', 'label' => __('Ascending')],['value' =>'DESC', 'label' => __('Descending')]];
    }

    public function toArray()
    {
        return [1 => __('Descending'),2=>__('Ascending')];
    }
}
