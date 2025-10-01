<?php
namespace Magebees\Blog\Block\Adminhtml\Comment;
class Edit extends \Magento\Backend\Block\Widget\Form\Container {
    protected function _construct() {
        $this->_objectId = 'comment_id';
        $this->_controller = 'adminhtml_comment';
        $this->_blockGroup = 'Magebees_Blog';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Comment'));
        $this->buttonList->update('delete', 'label', __('Delete Comment'));
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

