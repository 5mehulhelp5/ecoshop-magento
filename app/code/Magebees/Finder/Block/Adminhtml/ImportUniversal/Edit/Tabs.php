<?php
namespace Magebees\Finder\Block\Adminhtml\ImportUniversal\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('import_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Universal Products Import Information'));
    }
    
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'import_section',
            [
                'label' => __('Universal Products Import Information'),
                'title' => __('Universal Products Import Information'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Finder\Block\Adminhtml\ImportUniversal\Edit\Tab\Import'
                )->toHtml(),
                'active' => true
            ]
        );
        
        return parent::_prepareLayout();
    }
}
