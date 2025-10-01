<?php
namespace Magebees\Finder\Block\Adminhtml\Finder;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magebees_Finder';
        $this->_controller = 'adminhtml_finder';

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
        
        if ($this->getRequest()->getParam('id')) {
            $this->addButton('adminhtml_finder', [
                'label' => __('Add New Record'),
                'class' =>'add',
                'onclick' => "setLocation('{$this->getUrl('*/product/new', array('fid' => $this->getRequest()->getParam('id')))}')",
            ]);
            
            $this->addButton('adminhtml_import', [
                'label' => __('Import Finder Records'),
                'class' =>'add',
                'onclick' => "setLocation('{$this->getUrl('*/import/index', array('fid' => $this->getRequest()->getParam('id')))}')",
            ]);
            
            $this->addButton('adminhtml_import_universal', [
                'label' => __('Import Universal Products'),
                'class' =>'add',
                'onclick' => "setLocation('{$this->getUrl('*/importuniversal/index', array('fid' => $this->getRequest()->getParam('id')))}')",
            ]);
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'finder_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'finder_content');
                }
            }
			
			function validate(){
				var name=document.getElementById('filename').value;
				if(name==''){
					alert('Please upload the file first.');
					return false;
				}else{
					Element.show('loading-mask');
					return true;
				}
			}
			
		";
    }
}
