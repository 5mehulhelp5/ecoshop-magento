<?php
namespace Magebees\Blog\Block\Adminhtml;

class Tag extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_tag';
        $this->_blockGroup = 'Magebees_Blog';
        $this->_headerText = 'Post';
        $this->_addButtonLabel = __('Add New Tag');
        parent::_construct();
    }
}
