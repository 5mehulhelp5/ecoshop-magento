<?php

namespace Magebees\Testimonial\Block\Adminhtml;

class Testimonial extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_testimonial';
        $this->_blockGroup = 'Magebees_Testimonial';
        $this->_headerText = __('Testimonials');
        $this->_addButtonLabel = __('Add Testimonial');
        parent::_construct();
    }
}
