<?php
/***************************************************************************
 Extension Name : Product Label
 Extension URL  : https://www.magebees.com/product-label-extension-magento-2.html
 Copyright      : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email  : support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Block\Adminhtml;

class Productlabel extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_productlabel';
        $this->_blockGroup = 'Magebees_Productlabel';
        $this->_headerText = __('Manage Labels');
        $this->_addButtonLabel = __('Add New Label');
        parent::_construct();
    }
}
