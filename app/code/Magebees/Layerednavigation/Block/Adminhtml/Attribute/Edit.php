<?php

namespace Magebees\Layerednavigation\Block\Adminhtml\Attribute;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_attribute';
        $this->_blockGroup = 'Magebees_Layerednavigation';
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Attribute'));
        $this->buttonList->remove('reset');
    }
     /**
      * Prepare layout
      *
      * @return \Magento\Framework\View\Element\AbstractBlock
      */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
    
    public function getDeleteUrl(array $args = [])
    {
        $id=(int) $this->getRequest()->getParam('id');
 
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}
