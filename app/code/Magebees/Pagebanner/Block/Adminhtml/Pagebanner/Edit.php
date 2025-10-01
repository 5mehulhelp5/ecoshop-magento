<?php
namespace Magebees\Pagebanner\Block\Adminhtml\Pagebanner;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Pagebanner';
        $this->_controller = 'adminhtml_pagebanner';

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
			jQuery('#specified_page_layout_handle').hide();
			pagetypeoption();
			function pagetypeoption(){
				var e = document.getElementById('page_type_options');
				var pagetype = e.options[e.selectedIndex].value;
				pageoptions(pagetype);
			}
			
			});
			function pageoptions(value){
				var specified_page_layout = document.getElementById('specified_page_layout_handle').value;
				
				if(value!=''){
					
					if(value=='specifiedpage'){
						var layout_handle = document.getElementById('layout_handle');
						layout_handle.classList.add('required-entry');
						layout_handle.classList.add('_required');
						var parent1 = document.getElementById('layout_handle').parentNode;
						var parent2 = parent1.parentNode;
						var parent3 = parent2.parentNode;
						var parent4 = parent3.parentNode;
						parent4.style.display = 'block';
						if(specified_page_layout!=''){
						document.getElementById('layout_handle').value = specified_page_layout; //Option 10
						}
					}else{
						var layout_handle = document.getElementById('layout_handle');
						layout_handle.classList.remove('required-entry');
						layout_handle.classList.remove('_required');
						var parent1 = document.getElementById('layout_handle').parentNode;
						var parent2 = parent1.parentNode;
						var parent3 = parent2.parentNode;
						var parent4 = parent3.parentNode;
						parent4.style.display = 'none';
						if(specified_page_layout!=''){
						document.getElementById('layout_handle').value = specified_page_layout; //Option 10
						}
					}
						
				}
				else
				{	
					var parent1 = document.getElementById('layout_handle').parentNode;
					var parent2 = parent1.parentNode;
					var parent3 = parent2.parentNode;
					var parent4 = parent3.parentNode;
					//parent4.style.display = 'block';
				}
			}
			
			";
    }
}
