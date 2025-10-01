<?php 
namespace Magebees\Pagebanner\Block\Adminhtml\Pagebanner\Edit\Renderer;
/**
* CustomFormField Customformfield field renderer
*/
class CustomRenderer extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
    * Get the after element html.
    *
    * @return mixed
    */
    public function getAfterElementHtml()
    {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_widgetFactory = $objectManager->get('\Magento\Widget\Model\Widget\InstanceFactory');
		$widgetInstance = $_widgetFactory->create();
		$code = 'cms_page_link';
		$themeId = '1';
		$type = $code != null ? $widgetInstance->getWidgetReference('code', $code, 'type') : null;
		$widgetInstance->setType($type)->setCode($code)->setThemeId($themeId);
		$code = $widgetInstance->getWidgetReference('type', $this->getType(), 'code');
		$content = $objectManager->create('Magento\Framework\View\Element\Template');
		
		$chooserBlock = $content->getLayout()->createBlock('\Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout')->setName(
            'layout_handle'
        )->setId(
            'layout_handle'
        )->setClass(
            'required-entry select'
        )->setArea(
            $widgetInstance->getArea()
        )->setTheme(
            $widgetInstance->getThemeId()
        );
		return $chooserBlock->toHtml();
		// here you can write your code.
        $customDiv = '<div style="width:600px;height:200px;margin:10px 0;border:2px solid #000" id="customdiv"><h1 style="margin-top: 12%;margin-left:40%;">Custom Div</h1></div>';
        return $customDiv;
    }
}
?>