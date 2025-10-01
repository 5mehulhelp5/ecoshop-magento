<?php
namespace Magebees\Pagebanner\Block\Adminhtml\Pagebanner\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pagebanner_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Pagebanner Information'));
    }
    
    protected function _prepareLayout()
    {
        
        $this->addTab(
            'general_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Magebees\Pagebanner\Block\Adminhtml\Pagebanner\Edit\Tab\General'
                )->toHtml(),
                'active' => true
            ]
        );
		/*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_widgetFactory = $objectManager->get('\Magento\Widget\Model\Widget\InstanceFactory');
		$widgetInstance = $_widgetFactory->create();
		$code = 'cms_page_link';
		$themeId = '4';
		$type = $code != null ? $widgetInstance->getWidgetReference('code', $code, 'type') : null;
		$code = $widgetInstance->getWidgetReference('type', $this->getType(), 'code');
		$widgetInstance->setType($type)->setCode($code)->setThemeId($themeId);
		$chooserBlock = $this->getLayout()->createBlock('\Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout')->setName(
            'page_type'
        )->setId(
            'page_type'
        )->setClass(
            'required-entry select'
        )->setArea(
            $widgetInstance->getArea()
        )->setTheme(
            $widgetInstance->getThemeId()
        );
		
		$this->addTab(
            'page_section',
            [
                'label' => __('Page'),
                'title' => __('Page'),
                'content' => $chooserBlock->toHtml(),
                'active' => true
            ]
        );*/
        return parent::_prepareLayout();
    }
}
