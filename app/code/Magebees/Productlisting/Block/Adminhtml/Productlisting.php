<?php
namespace Magebees\Productlisting\Block\Adminhtml;

class Productlisting extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_productlisting';
        $this->_blockGroup = 'Magebees_Productlisting';
        $this->_headerText = __('Manage Product Listing');
        $this->_addButtonLabel = __('Add New List');
        parent::_construct();
    }
}
