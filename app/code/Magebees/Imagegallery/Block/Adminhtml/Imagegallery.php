<?php
namespace Magebees\Imagegallery\Block\Adminhtml;

class Imagegallery extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_imagegallery';
        $this->_blockGroup = 'Magebees_Imagegallery';
        $this->_headerText = __('Manage Imagegallery');
        $this->_addButtonLabel = __('Add New Image');
        parent::_construct();
    }
}
