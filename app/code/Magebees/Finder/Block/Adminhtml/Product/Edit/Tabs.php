<?php
namespace Magebees\Finder\Block\Adminhtml\Product\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product Information'));
    }
    
    protected function _prepareLayout()
    {
        $this->addTab(
            'product_section',
            [
                'label' => __('Product Information'),
                'url' => $this->getUrl('finder/product/tab', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        
        
        return parent::_prepareLayout();
    }
}
