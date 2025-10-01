<?php
namespace Magebees\Blog\Block\Adminhtml\Import;
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'import_id';
        $this->_controller = 'adminhtml_import';
        $this->_blockGroup = 'Magebees_Blog';
     
         parent::_construct();


        $this->buttonList->remove('back');
        $this->buttonList->remove('delete');
		$this->buttonList->remove('save');
		$this->buttonList->remove('reset');
		
		$this->addButton(
                'import',
                [
                'label' => __('Import Blog'),
                'class' => 'import-blog',
                'level' => -1
                ]
            );
		
        $this->buttonList->update('save', 'label', __('Save Import'));
        $this->buttonList->update('delete', 'label', __('Delete Import'));
       $this->_formScripts[] = "
		 
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'banner_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'banner_content');
                }
            }
	;";
		
		;
    }
	 public function getFormHtml()
    {
       // get the current form as html content.
        $html = parent::getFormHtml();
        //Append the phtml file after the form content.
        $html .= $this->setTemplate('Magebees_Blog::importblog.phtml')->toHtml(); 
        return $html;
    }
 
}
