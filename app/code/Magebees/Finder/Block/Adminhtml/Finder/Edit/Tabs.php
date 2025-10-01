<?php
namespace Magebees\Finder\Block\Adminhtml\Finder\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('finder_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Finder Information'));
    }
    
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'general_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Finder\Block\Adminhtml\Finder\Edit\Tab\General'
                )->toHtml(),
                'active' => true
            ]
        );
        
        if ($this->getRequest()->getParam('id')) {
            $this->addTab(
                'dropdowns_section',
                [
                    'label' => __('Dropdowns'),
                    'title' => __('Dropdowns'),
                    'content' => $this->getLayout()->createBlock(
                        'Magebees\Finder\Block\Adminhtml\Finder\Edit\Tab\Dropdowns'
                    )->toHtml()
                ]
            );
            
            $this->addTab(
                'category_section',
                [
                    'label' => __('Categories'),
                    'title' => __('Categories'),
                    'content' => $this->getLayout()->createBlock(
                        'Magebees\Finder\Block\Adminhtml\Finder\Edit\Tab\Categories'
                    )->toHtml()
                ]
            );
            
            $this->addTab(
                'products_section',
                [
                    'label' => __('Products'),
                    'title' => __('Products'),
                    'content' => $this->getLayout()->createBlock(
                        'Magebees\Finder\Block\Adminhtml\Product\Grid'
                    )->toHtml()
                ]
            );
            
            $this->addTab(
                'universal_products_section',
                [
                    'label' => __('Universal Products'),
                    'title' => __('Universal Products'),
                    'url' => $this->getUrl('finder/finder/universaltab', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
			
			$this->addTab(
                'import_history_section',
                [
                    'label' => __('Import History'),
                    'title' => __('Import History'),
                    'content' => $this->getLayout()->createBlock(
                        'Magebees\Finder\Block\Adminhtml\Import\HistoryGrid'
                    )->toHtml()
                ]
            );
            
            $this->addTab(
                'code_section',
                [
                    'label' => __('Use Code Inserts'),
                    'title' => __('Use Code Inserts'),
                    'content' => $this->getLayout()->createBlock(
                        'Magebees\Finder\Block\Adminhtml\Finder\Edit\Tab\Code'
                    )->toHtml()
                ]
            );
        }
                
        return parent::_prepareLayout();
    }
}
