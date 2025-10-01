<?php
namespace Magebees\PocoThemes\Model\Config\Source;
class FreeShippingBarVisible implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>'minicart', 'label' => __('Mini Cart')],
			['value' =>'cartpopup', 'label' => __('Cart Popup')],
			['value' =>'footercart', 'label' => __('Footer Cart Panel')],
			['value' =>'productdetail', 'label' => __('Product Detail Page')],
			['value' =>'cart', 'label' => __('Cart Page')],
			['value' =>'checkout', 'label' => __('Checkout Page')],
		];
    }
	
}