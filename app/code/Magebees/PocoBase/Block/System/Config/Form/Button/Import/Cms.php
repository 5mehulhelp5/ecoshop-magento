<?php
namespace Magebees\PocoBase\Block\System\Config\Form\Button\Import;

class Cms extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_buttonLabel = 'Import';

    protected $_actionUrl;
    
    protected $_importType;

    protected $_themeName;
    
    public function setButtonLabel($buttonLabel)
    {
        $this->_buttonLabel = $buttonLabel;
        return $this;
    }

    public function getActionUrl()
    {
        return $this->_actionUrl;
    }

    public function setActionUrl($actionUrl)
    {
        $this->_actionUrl = $actionUrl;
        return $this;
    }
    
    public function getImportType()
    {
        return $this->_importType;
    }

    public function setImportType($importType)
    {
        $this->_importType = $importType;
        return $this;
    }
    
    public function getThemeName()
    {
        return $this->_themeName;
    }

    public function setThemeName($themeName)
    {
        $this->_themeName = $themeName;
        return $this;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/cms_button.phtml');
        }
        return $this;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_buttonLabel;
        $action = !empty($originalData['action_url']) ? $originalData['action_url'] : '';
        if($action) {
            $this->setActionUrl($action);
        }
		$type = !empty($originalData['import_type']) ? $originalData['import_type'] : '';
        if($type) {
            $this->setImportType($type);
        }

        $theme_name = !empty($originalData['theme_name']) ? $originalData['theme_name'] : '';
        if($theme_name) {
            $this->setThemeName($theme_name);
        }
        
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'import_type' => $type,
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl($action),
            ]
        );

        return $this->_toHtml();
    }
}