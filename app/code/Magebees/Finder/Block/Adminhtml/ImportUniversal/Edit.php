<?php
namespace Magebees\Finder\Block\Adminhtml\ImportUniversal;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Finder';
        $this->_controller = 'adminhtml_importUniversal';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Import Universal Products'));
        $this->buttonList->remove('back');
        
        $this->addButton('import_back', [
            'label' => __('Back'),
            'class' =>'back',
            'onclick' => "setLocation('{$this->getUrl('*/finder/edit', array('id' => $this->getRequest()->getParam('fid')))}')",
        ]);
                
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'finder_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'finder_content');
                }
            }
					
			function importUniversalData(){
				alert('import');
				require([
					'jquery'
				],
				function($) {
					var form = $('#filename').val();
					if(form){
						$.ajax({
								url : '". $this->getUrl('*/*/importuniversal'). "',
								type: 'post',
								showLoader:true,
						});
					}
       			});
			}
			
		";
    }
}
