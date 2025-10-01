<?php
namespace Magebees\Blog\Block\Adminhtml;
class Comment extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_comment';
        $this->_blockGroup = 'Magebees_Blog';
        $this->_headerText = 'Comment';
        $this->_addButtonLabel = __('Add New Comment');
        parent::_construct();
	$this->removeButton('add'); // Add this code to remove the button
    }
}
