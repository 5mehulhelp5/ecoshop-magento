<?php
namespace Magebees\PocoBase\Block\System\Config\Form\Button\Import;

class DemoCustom extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_buttonLabel = 'Import';

    protected $_actionUrl;
    
    protected $_demoVersion;
    
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
    
    public function getDemoVersion()
    {
        return $this->_demoVersion;
    }

    public function setDemoVersion($demoVersion)
    {
        $this->_demoVersion = $demoVersion;
        return $this;
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/demo_button_custom.phtml');
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
        $demo_version = !empty($originalData['demo_version']) ? $originalData['demo_version'] : '';
        if($demo_version) {
            $this->setDemoVersion($demo_version);
        }
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'demo_version' => $demo_version,
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl($action),
            ]
        );

        return $this->_toHtml();
    }
}