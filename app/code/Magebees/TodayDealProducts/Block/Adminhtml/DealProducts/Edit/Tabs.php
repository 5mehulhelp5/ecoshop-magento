<?php
namespace Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
   
    protected function _construct()
    {
        parent::_construct();
        $this->setId('dealproducts_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Today\'s Deal Information'));
    }
    
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'general_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab\General'
                )->toHtml(),
                'active' => true
            ]
        );
        
        $this->addTab(
            'conditions_section',
            [
                'label' => __('Product Conditions'),
                'title' => __('Product Conditions'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab\Conditions'
                )->toHtml(),
            ]
        );
        
        $this->addTab(
            'layout_section',
            [
                'label' => __('Layout Options'),
                'title' => __('Layout Options'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab\LayoutOptions'
                )->toHtml(),
            ]
        );
        
        if ($this->getRequest()->getParam('id')) {
            $this->addTab(
                'code_section',
                [
                    'label' => __('Use Code Inserts'),
                    'title' => __('Use Code Inserts'),
                    'content' => $this->getLayout()->createBlock(
                        'Magebees\TodayDealProducts\Block\Adminhtml\DealProducts\Edit\Tab\Code'
                    )->toHtml()
                ]
            );
        }
        
        return parent::_prepareLayout();
    }
}
