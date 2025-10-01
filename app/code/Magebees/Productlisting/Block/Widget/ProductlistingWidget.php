<?php
namespace Magebees\Productlisting\Block\Widget;

class ProductlistingWidget extends \Magebees\Productlisting\Block\Productlisting implements \Magento\Widget\Block\BlockInterface
{
	//public $pagination_class;
    public function addData(array $arr)
    {
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        $this->_data[$key] = $value;
    }
}