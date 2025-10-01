<?php
namespace Magebees\ProductsSlider\Block\Widget;

class ProductsSliderWidget extends \Magebees\ProductsSlider\Block\ProductsSlider implements \Magento\Widget\Block\BlockInterface

{
    public function addData(array $arr)
    {
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        $this->_data[$key] = $value;
	}

    
    

}