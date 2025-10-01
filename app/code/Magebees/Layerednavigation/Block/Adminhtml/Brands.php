<?php
namespace Magebees\Layerednavigation\Block\Adminhtml;

class Brands extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_brands';
        $this->_blockGroup = 'Magebees_Layerednavigation';
         $this->_headerText = __('Brands');
        $this->addButton('Load', [
            'label' => 'Load Brand',
            'class'=>'action-default scalable add primary',
            'onclick' => "setLocation('".$this->getUrl('layerednavigation/brands/saveattribute')."flag/1')",
        ]);
        
        parent::_construct();
        $this->removeButton('add');
    }
}
