<?php
namespace Magebees\AjaxCategoryProducts\Model;

class Sortby implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' =>'position', 'label' => __('Position')], ['value' =>'name', 'label' => __('Product Name')], ['value' =>'price', 'label' => __('Price')], ['value' =>'random', 'label' => __('Random')]];
    }

    public function toArray()
    {
        return [0 => __('Name'), 1 => __('Price'),2=>__('Position'),3=>__('Random')];
    }
} 
