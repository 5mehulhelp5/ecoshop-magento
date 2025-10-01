<?php
namespace Magebees\Blog\Block\Adminhtml\Category;
class Edit extends \Magento\Backend\Block\Widget\Form\Container {
    protected function _construct() {
        $this->_objectId = 'category_id';
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Magebees_Blog';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Category'));
        $this->buttonList->update('delete', 'label', __('Delete Category'));
        $this->buttonList->add('saveandcontinue', ['label' => __('Save and Continue Edit'), 'class' => 'save', 'data_attribute' => ['mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]]], -100);
        $this->_formScripts[] = "
		 
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'banner_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'banner_content');
                }
            }
	;";;
    }
}

