<?php
namespace Magebees\Ajaxquickview\Block\Adminhtml;

class DefaultFrontend extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {    
        return '<textarea style="background:#efefef;border:1px solid #d8d8d8;padding:10px;margin-bottom:10px;" onclick="this.focus();this.select()">&lt?php echo $this->helper("Magebees\Ajaxquickview\Helper\Data")->addQuickViewButton($_item);?&gt;</textarea>';
    }
}
