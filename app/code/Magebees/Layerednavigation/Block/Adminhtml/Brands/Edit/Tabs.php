<?php
namespace Magebees\Layerednavigation\Block\Adminhtml\Brands\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('main_section');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Brand'));
    }
}
