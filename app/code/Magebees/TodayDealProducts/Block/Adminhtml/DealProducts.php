<?php
namespace Magebees\TodayDealProducts\Block\Adminhtml;

class DealProducts extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_dealProducts';
        $this->_blockGroup = 'Magebees_TodayDealProducts';
        $this->_headerText = __('Manage Today\'s Deal Products');
        $this->_addButtonLabel = __('Add New Deal');
        parent::_construct();
    }
}
