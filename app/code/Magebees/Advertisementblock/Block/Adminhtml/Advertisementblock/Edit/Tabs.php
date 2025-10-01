<?php

namespace Magebees\Advertisementblock\Block\Adminhtml\Advertisementblock\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('advertisementblock_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Advertisement Block'));
    }
}
