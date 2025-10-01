<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml;
class Slide extends \Magento\Backend\Block\Widget\Grid\Container
{
	protected function _construct()
    {
		$this->_controller = 'adminhtml_slide';
        $this->_blockGroup = 'Magebees_Responsivebannerslider';
        $this->_headerText = __('Slide');
        $this->_addButtonLabel = __('Add New Slide');
        parent::_construct();
    }
}
