<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slide\Edit;
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		parent::_construct();
        $this->setId('slide_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Responsive Banner Slider'));
    }
}