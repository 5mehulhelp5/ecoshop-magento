<?php
/***************************************************************************
 Extension Name : Product Label
 Extension URL  : https://www.magebees.com/product-label-extension-magento-2.html
 Copyright      : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email  : support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Model;

class Pages implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' =>'product', 'label' => __('Product Detailed Page')],
            ['value' =>'category', 'label' => __('Product Listing Page')],
            ['value' =>'result', 'label' => __('Search Page')],
            ['value' =>'advanced', 'label' => __('Advanced Search Page')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return  [
            'product'=> __('Product Detailed Page'),
            'category'=> __('Product Listing Page'),
            'result'=> __('Search Page'),
            'advanced'=> __('Advanced Search Page')
        ];
    }
}
