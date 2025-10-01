<?php
namespace Magebees\Pagebanner\Block\Adminhtml;

class Pagebanner extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_pagebanner';
        $this->_blockGroup = 'Magebees_Pagebanner';
        $this->_headerText = __('Manage Pagebanner');
        $this->_addButtonLabel = __('Add New Banner');
        parent::_construct();
    }
}
