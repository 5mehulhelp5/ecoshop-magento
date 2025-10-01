<?php
namespace Magebees\Finder\Block\Adminhtml\Import\Edit\Renderer;

/**
 * CustomFormField Customformfield field renderer
 */
class ImportContent extends \Magento\Framework\Data\Form\Element\AbstractElement
{
 
    public function getElementHtml()
    {
		
		$loader = "test";
        $import_content = "<div id='import_content'></div>";
        return $import_content;
    }
}
