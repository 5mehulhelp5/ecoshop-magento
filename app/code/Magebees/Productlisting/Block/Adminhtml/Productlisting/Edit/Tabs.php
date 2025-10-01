<?php
namespace Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productlisting_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Productlisting Information'));
    }
    
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'general_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab\General'
                )->toHtml(),
                'active' => true
            ]
        );
        
		$this->addTab(
			'slider_section',
			[
				'label' => __('Pagination and Slider Options'),
				'title' => __('Pagination and Slider Options'),
				'content' => $this->getLayout()->createBlock(
					'Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab\Slider'
				)->toHtml()
			]
		);
		
		/* $this->addTab(
			'design_section',
			[
				'label' => __('Design Options'),
				'title' => __('Design Options'),
				'content' => $this->getLayout()->createBlock(
					'Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab\Design'
				)->toHtml()
			]
		);*/
	
		$this->addTab(
			'display_section',
			[
				'label' => __('Display Settings'),
				'title' => __('Display Settings'),
				'content' => $this->getLayout()->createBlock(
					'Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab\Display'
				)->toHtml()
			]
		);
		
		$this->addTab(
			'products_section',
			[
				'label' => __('Select Products'),
				'title' => __('Select Products'),
				'url' => $this->getUrl('prodlist/manage/producttab', ['_current' => true]),
				'class' => 'ajax'
			]
		);
		
		$this->addTab(
                'code_section',
                [
                    'label' => __('Use Code Inserts'),
                    'title' => __('Use Code Inserts'),
                    'content' => $this->getLayout()->createBlock(
                        'Magebees\Productlisting\Block\Adminhtml\Productlisting\Edit\Tab\Code'
                    )->toHtml()
                ]
            );
               
        return parent::_prepareLayout();
    }
}
