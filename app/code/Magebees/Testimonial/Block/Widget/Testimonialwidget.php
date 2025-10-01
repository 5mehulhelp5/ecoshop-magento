<?php

namespace Magebees\Testimonial\Block\Widget;

class Testimonialwidget extends \Magebees\Testimonial\Block\Testimonial implements \Magento\Widget\Block\BlockInterface
{
    
    
    public function addData(array $arr)
    {
        
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        
        $this->_data[$key] = $value;
    }
 
    public function _toHtml()
    {
        
        if ($this->getData('template')) {
            $this->setTemplate($this->getData('template'));
        }
        return parent::_toHtml();
    }
}
