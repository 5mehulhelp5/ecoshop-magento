<?php
namespace Magebees\Finder\Block\Adminhtml\Import\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('import_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Import Information'));
    }
    
    protected function _prepareLayout()
    {
		$this->addTab(
            'upload_section',
            [
                'label' => __('Upload File'),
                'title' => __('Upload File'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Finder\Block\Adminhtml\Import\Edit\Tab\Upload'
                )->toHtml(),
                'active' => true
            ]
        );
        
        $this->addTab(
            'import_section',
            [
                'label' => __('Import Finder Data'),
                'title' => __('Import Finder Data'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Finder\Block\Adminhtml\Import\Edit\Tab\Import'
                )->toHtml(),
               // 'active' => true
            ]
        );
        
        return parent::_prepareLayout();
    }
}
