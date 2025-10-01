<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml;
class Slidergroup extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected function _construct() {
		$this->_controller = 'adminhtml_slidergroup';
        $this->_blockGroup = 'Magebees_Responsivebannerslider';
        $this->_headerText = __('Responsivebannerslider');
        $this->_addButtonLabel = __('Add New Group');
		parent::_construct();
      
    }
}
