<?php
namespace Magebees\Onepagecheckout\Block\System\Config;
class Separator extends \Magento\Config\Block\System\Config\Form\Field
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $htmlId = $element->getHtmlId();
        $html = '<tr id="row_' . $htmlId . '">'
            . '<td class="label" colspan="3">';
        $marginTop = '30px';
        $customStyle = 'text-align:left;';
        $html .= '<div style="margin-top: ' . $marginTop . '; padding-bottom:10px; font-weight: bold; border-bottom: 1px solid #dfdfdf;'
            . $customStyle . '">';
        $html .= $element->getLabel();
        $html .= '</div></td></tr>';
        return $html;
    }
}

