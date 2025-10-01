<?php

namespace Magebees\PocoBase\Model\Config\Source;

class Resolution implements \Magento\Framework\Option\ArrayInterface
{

    protected $_options;
	
    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray() {

        $this->_options = [
            /*['label' => '150px x 150px', 'value' => '150'],
            ['label' => '240px x 240px', 'value' => '240'],*/
			['label' => '320px x 320px', 'value' => '320'],
			['label' => '480px x 480px', 'value' => '480'],
			['label' => '612px x 612px', 'value' => '640'],
            
        ];
        return $this->_options;
    }

}