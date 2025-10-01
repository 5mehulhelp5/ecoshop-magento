<?php
namespace Magebees\PocoThemes\Model\Config\Source;
class HeaderStyle implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>'header-1', 'label' => __('Header Style 1')],
			['value' =>'header-2', 'label' => __('Header Style 2')],
			['value' =>'header-3', 'label' => __('Header Style 3')],
			['value' =>'header-4', 'label' => __('Header Style 4')],
			['value' =>'header-5', 'label' => __('Header Style 5')],
			['value' =>'header-6', 'label' => __('Header Style 6')],
			['value' =>'header-7', 'label' => __('Header Style 7')],
			['value' =>'header-8', 'label' => __('Header Style 8')],
			['value' =>'header-9', 'label' => __('Header Style 9')],
			['value' =>'header-10', 'label' => __('Header Style 10')],
			['value' =>'header-11', 'label' => __('Header Style 11')],
			['value' =>'header-12', 'label' => __('Header Style 12')],
			['value' =>'header-13', 'label' => __('Header Style 13')],
			['value' =>'header-14', 'label' => __('Header Style 14')],
			['value' =>'header-15', 'label' => __('Header Style 15')],
			['value' => 'header-16', 'label' => __('Header Style 16')],
			['value' => 'header-17', 'label' => __('Header Style 17')],
			['value' => 'header-18', 'label' => __('Header Style 18')],
			['value' => 'header-19', 'label' => __('Header Style 19')]

		];
    }
	
}

