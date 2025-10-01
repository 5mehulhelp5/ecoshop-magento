<?php
namespace Magebees\Finder\Block\Adminhtml\Import;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Finder';
        $this->_controller = 'adminhtml_import';

        parent::_construct();
		
		
        $this->buttonList->update('save', 'label', __('Upload File'));
        $this->buttonList->remove('back');
        
		$this->addButton(
            'import_data',
            [
                'label' => __('Import'),
				'class' => 'scalable primary save',
                'onclick' => "importData()",
            ]
        );
		
		
        $this->addButton('import_back', [
            'label' => __('Back'),
            'class' =>'back',
            'onclick' => "setLocation('{$this->getUrl('*/finder/edit', array('id' => $this->getRequest()->getParam('fid')))}')",
        ]);
                
        $this->_formScripts[] = "
			var imported = 0;
			function importData(){
				require(
				[
					'jquery',
					'Magento_Ui/js/modal/modal'
				],
				
				function(jQuery,modal) {
				 	imported = 0;
					var options = {
						type: 'popup',
						responsive: true,
						innerScroll: true,
						title: 'Import',
						buttons: [{
							text: jQuery.mage.__('Close'),
							class: '',
							click: function () {
								this.closeModal();
								window.location='".$this->getUrl('finder/import/index',array('fid'=>$this->getRequest()->getParam('fid')))."';	
							}
						}]
					};
					
										
					var file = jQuery( '#import_file option:selected' ).val();
					
					if (file == 0) {
						alert('Please select file first.');
						return false;
					}
					start = performance.now();
        
        
					var popup = modal(options, jQuery('#import_content')); //initialize popup
					jQuery('#import_content').modal('openModal');
					if(imported == 0){
						jQuery('#import_content').html('<img src=".$this->getViewFileUrl('Magebees_Finder::images/loader-1.gif')." />');
					}
					
					var delete_old = document.getElementById('existing_data').value;
					if(delete_old == 1){
						jQuery('#import_content').html('<img src=".$this->getViewFileUrl('Magebees_Finder::images/loader-1.gif')." /><br\>Deleting Existing Finder Records.');
						deleteRecursive(delete_old);
					}else{
						jQuery('#import_content').html('Import Process Starts.');
						importRecursive(1);
					}
					
					end = performance.now();
					timeTaken = end - start;
					console.log('Function took ' +
							timeTaken + ' milliseconds');
				});
			}
			
			function importRecursive(next){
				var file = jQuery( '#import_file option:selected' ).text();
				var range_enable = document.getElementById('range_enable').value;
				jQuery.ajax({
					url : '".$this->getUrl('finder/import/import')."',
					data: { 
						import_file: file,
						finder_id : ".$this->getRequest()->getParam('fid').",
						pointer_next:next,
						range_enable:range_enable,
					} ,

					dataType: 'json',
					type: 'post',
					showLoader:false,
					success: function(response){
						pointer_next = response.pointer_last;
						console.log(response.count);
						imported = imported + response.count;
						if(response.fail){
							window.location='".$this->getUrl('finder/import/index',array('fid'=>$this->getRequest()->getParam('fid')))."';	
						}else if(!response.no_more){
							jQuery('#import_content').html('<img src=".$this->getViewFileUrl('Magebees_Finder::images/loader-1.gif')." /><br\>'+imported+' Product(s) Assigned to Finder.');
							importRecursive(pointer_next);
						}else{
							jQuery('#import_content').html(imported+' Product(s) Assigned to Finder. Import Process Completed');
							setTimeout(function(){jQuery('#import_content').modal('closeModal');window.location='".$this->getUrl('finder/finder/edit',array('id'=>$this->getRequest()->getParam('fid'),'active_tab'=>'products_section'))."'; }, 5000);
								
						}
					}
				});
			}
			
			function deleteRecursive(delete_old){
				jQuery.ajax({
					url : '".$this->getUrl('finder/import/deleteold')."',
					data: { 
							delete_old:delete_old,
							finder_id : ".$this->getRequest()->getParam('fid')."
						} ,

					dataType: 'json',
					type: 'post',
					showLoader:false,
					success: function(response){
						var res_next = response.delete_old;
						if(res_next == 1){
							deleteRecursive(delete_old);
						}else{
							jQuery('#import_content').html('Import Process Starts.');
							importRecursive(1);
						}
					}
				});
			}
						
		";
    }
}
