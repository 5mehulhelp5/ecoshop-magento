<?php
namespace Magebees\Blog\Block\Adminhtml;

class Import extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_import';
        $this->_blockGroup = 'Magebees_Blog';
        $this->_headerText = 'Blog Import From Wordpress';
        $this->_addButtonLabel = __('Start Import Blogs');
        parent::_construct();
    }
}
