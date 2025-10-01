<?php
/***************************************************************************
 Extension Name  : Magento2 Responsive Banner Slider with Lazy Load Extension
 Extension URL   : http://www.magebees.com/magento2-responsive-banner-slider-with-lazy-load-extension.html
 Copyright    : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email   : support@magebees.com 
 ***************************************************************************/
 ?>
<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
	    parent::_construct();
        $this->setId('main_section');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Responsive Banner Slider'));
    }
	
	
	 protected function _prepareLayout()
        {
			
			$this->addTab('main_section',
				[
					'label' => __('General Information'),
					'title' => __('General Information'),
					'content' => $this->getLayout()->createBlock(
                    'Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit\Tab\Main')->toHtml(),
					'active' => false
				]
			);
			$this->addTab('page_section',
				[
					'label' => __('Display on Pages'),
					'title' => __('Display on Pages'),
					'content' => $this->getLayout()->createBlock(
                    'Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit\Tab\Pages')->toHtml(),
					'active' => false
				]
			);
			$this->addTab('categories_section',
				[
					'label' => __('Display on Categories'),
					'title' => __('Display on Categories'),
					'content' => $this->getLayout()->createBlock(
                    'Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit\Tab\Categories')->toHtml(),
					'active' => false
				]
			);
			
			
            $this->addTab(
                'slidergroup_product_section',
                [
                    'label' => __('Display on Product Pages'),
                    'url' => $this->getUrl('responsivebannerslider/slidergroup/grids', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
            return parent::_prepareLayout();
        }
		
		

 

      

    
}