<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\System\Config\Form;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
	protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('refreshslider.phtml');
    }
  	
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
         return $this->_toHtml();
    }
    public function getAjaxCheckUrl()
    {
        return $this->getUrl('responsivebannerslider/slidedata/refreshslider');
    }
	public function getGenerateStaticMenuUrl()
    {
        return $this->getUrl('responsivebannerslider/slidedata/generateStatichtml');
    }
	public function isDeveloperModeEnable(){
	$optimized = $this->_scopeConfig->getValue('responsivebannerslider/optimize_performance', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $optimized;
	}
	public function getDynamicStaticTemplateDirectoryPath(){
		$dir_for_static_file = 'pub/media/responsivebannerslider/files';
		return $dir_for_static_file;
	}
	
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
          ->setData([
          'id'        => 'refresh_button',
          'label'     => __('PUBLISH'),
          'onclick'   => 'javascript:publishMenu(); return false;'
          ]);
 
        return $button->toHtml();
    }
}
