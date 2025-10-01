<?php

namespace Magebees\Layerednavigation\Block\Adminhtml;

class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_attribute';
        $this->_blockGroup = 'Magebees_Layerednavigation';
        $this->_headerText = __('Attribute');
        parent::_construct();
         $this->removeButton('add');
    }
}
