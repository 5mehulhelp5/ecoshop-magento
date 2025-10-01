<?php
namespace Magebees\Blog\Block\Adminhtml;

class Post extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_post';
        $this->_blockGroup = 'Magebees_Blog';
        $this->_headerText = 'Post';
        $this->_addButtonLabel = __('Add New Post');
        parent::_construct();
    }
}
