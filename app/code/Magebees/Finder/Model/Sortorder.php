<?php
namespace Magebees\Finder\Model;

class Sortorder implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Deafult Sort Order')],['value' => 2, 'label' => __('Search Products')], ['value' => 3, 'label' => __('Universal Products')]];
    }

    public function toArray()
    {
        return [1 => __('Deafult Sort Order'), 2 => __('Search Products'),3 =>__('Universal Products')];
    }
}
