<?php

namespace Magebees\Advertisementblock\Block\Adminhtml;

class Advertisementblock extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_Advertisementblock';
        $this->_blockGroup = 'Magebees_Advertisementblock';
        $this->_headerText = __('Advertisementblocks');
        $this->_addButtonLabel = __('Add Advertising Block');
        parent::_construct();
    }
}
