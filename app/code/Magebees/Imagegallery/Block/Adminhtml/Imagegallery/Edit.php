<?php
namespace Magebees\Imagegallery\Block\Adminhtml\Imagegallery;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Imagegallery';
        $this->_controller = 'adminhtml_imagegallery';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ],
            -100
        );
		$this->_formScripts[] = "
			
		 	requirejs(['jquery','domReady!'], function(jQuery){
			urltypeoption();
			function urltypeoption(){
				var e = document.getElementById('isexternal');
				var isExternal = e.options[e.selectedIndex].value;
				urloptions(isExternal);
			}
			
			});
			function urloptions(value){
				if(value==1){
					jQuery('#url').addClass('validate-url');
				}else{
					jQuery('#url').removeClass('validate-url');
				}
			}
			
			";
    }
}
