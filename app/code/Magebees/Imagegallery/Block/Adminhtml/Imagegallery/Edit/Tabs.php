<?php
namespace Magebees\Imagegallery\Block\Adminhtml\Imagegallery\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('imagegallery_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Imagegallery Information'));
    }
    
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'general_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Imagegallery\Block\Adminhtml\Imagegallery\Edit\Tab\General'
                )->toHtml(),
                'active' => true
            ]
        );
	
        return parent::_prepareLayout();
    }
}
