<?php

namespace Magebees\Layerednavigation\Block\Adminhtml\Attribute\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Manage Attribute'));
    }
    protected function _prepareLayout()
    {
        
            
        $this->addTab(
            'attribute_section',
            [
                'label' => __('Attribute Information'),
                'title' => __('Attribute Information'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Layerednavigation\Block\Adminhtml\Attribute\Edit\Tab\Attributeinfo'
                )->toHtml()
            ]
        );
        $this->addTab(
            'option_section',
            [
                'label' => __('Attribute Option Information'),
                'url' => $this->getUrl('*/*/optiongrid', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        return parent::_prepareLayout();
    }
}
