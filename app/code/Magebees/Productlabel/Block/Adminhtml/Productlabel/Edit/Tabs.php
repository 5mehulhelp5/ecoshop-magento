<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Block\Adminhtml\Productlabel\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productlabel_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Label Information'));
    }
    
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'general_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Productlabel\Block\Adminhtml\Productlabel\Edit\Tab\General'
                )->toHtml(),
                'active' => true
            ]
        );
        
        $this->addTab(
            'labels_section',
            [
                'label' => __('Labels'),
                'title' => __('Labels'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Productlabel\Block\Adminhtml\Productlabel\Edit\Tab\Labels'
                )->toHtml()
            ]
        );
        
        $this->addTab(
            'conditions_section',
            [
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Productlabel\Block\Adminhtml\Productlabel\Edit\Tab\Conditions'
                )->toHtml()
            ]
        );
    
        return parent::_prepareLayout();
    }
}
