<?php
namespace Magebees\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer;

/**
 * CustomFormField Customformfield field renderer
 */
class MappingContent extends \Magento\Framework\Data\Form\Element\AbstractElement
{
 
    /**
     * Get the after element html.
     *
     * @return mixed
     */
    public function getElementHtml()
    {
       // $mappingDiv = '<div id="cat_mapping" class="category-mapping-content"></div>';
        
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$template = $objectManager->create('Magento\Framework\View\Element\Template');
		return $template->setTemplate("Magebees_Blog::postfeatureimage.phtml")->toHtml();
		
		//return $mappingDiv;
		
    }
}
