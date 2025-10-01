<?php
namespace Magebees\Finder\Block\Adminhtml;

class Finder extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_finder';
        $this->_blockGroup = 'Magebees_Finder';
        $this->_headerText = __('Manage Finders');
        $this->_addButtonLabel = __('Add New Finder');
        parent::_construct();
    }
}
