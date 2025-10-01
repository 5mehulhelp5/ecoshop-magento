<?php
namespace Magebees\AjaxCategoryProducts\Block\Adminhtml;

class TemplateCode extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<div style="background:#efefef;border:1px solid #d8d8d8;padding:10px;margin-bottom:10px;">
		<span>{{block class="Magebees\AjaxCategoryProducts\Block\CategoryProducts"  template="products.phtml"}}</span>	
		</div>';
    }
}
