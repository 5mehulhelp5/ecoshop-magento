<?php
namespace Magebees\Blog\Block\Adminhtml;

class UrlRewrite extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_Urlrewrite';
        $this->_blockGroup = 'Magebees_Blog';
        $this->_headerText = 'URL Rewrite';
        $this->_addButtonLabel = __('Add New URL Rewrite');
        parent::_construct();
    }
}
