<?php
namespace Magebees\PocoBase\Model\Config\Source;
class BlockPosition implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>'sidebar', 'label' => __('Sidebar')],
			['value' =>'bottom', 'label' => __('Bottom')]
		];
    }
	
}

