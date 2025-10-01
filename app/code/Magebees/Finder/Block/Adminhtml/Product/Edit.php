<?php
namespace Magebees\Finder\Block\Adminhtml\Product;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Finder';
        $this->_controller = 'adminhtml_product';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Assign Product'));
        $this->buttonList->remove('back');
        $redirectUrl = $this->getRequest()->getServer('HTTP_REFERER');
                
        $this->addButton('product_back', [
            'label' => __('Back'),
            'class' =>'back',
            //'onclick' => "setLocation('{$this->getUrl('*/finder/edit', array('id' => $this->getRequest()->getParam('fid')))}')",
            'onclick' => "setLocation('{$redirectUrl}')",
        ]);
    }
}
