<?php

namespace Magebees\Ajaxaddtocart\Block\Adminhtml;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $wysiwygConfig;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context);
    }
    
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $element->style= 'width:700px; height:300px;';
        $element->setConfig($this->wysiwygConfig->getConfig($element));
        return parent::_getElementHtml($element);
    }
}
