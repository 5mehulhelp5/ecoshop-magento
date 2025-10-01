<?php
namespace Magebees\Blog\Block\Adminhtml;

class Category extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Magebees_Blog';
        $this->_headerText = 'Category';
        $this->_addButtonLabel = __('Add New Category');
        parent::_construct();
    }
}
