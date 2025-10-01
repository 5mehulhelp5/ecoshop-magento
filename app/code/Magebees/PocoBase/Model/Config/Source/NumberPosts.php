<?php
namespace Magebees\PocoBase\Model\Config\Source;
class NumberPosts implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * Options getter
     *
     * @return array
     */
    
	public function toOptionArray() {
        return [
			['value' =>2, 'label' => 2],
			['value' =>3, 'label' => 3]
		];
    }
	
}


