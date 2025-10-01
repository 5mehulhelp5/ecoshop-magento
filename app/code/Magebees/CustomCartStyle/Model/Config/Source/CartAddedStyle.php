<?php
/**
 *
 * Copyright © 2018 Magebees, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\CustomCartStyle\Model\Config\Source;

class CartAddedStyle implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Show informed popup')],
            ['value' => '2', 'label' => __('Make product image fly to footer cart panel')],
            ['value' => '3', 'label' => __('Show Ajax Add To Cart DialogBox')]
        ];
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}