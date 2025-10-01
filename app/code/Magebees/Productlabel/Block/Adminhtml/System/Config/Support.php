<?php
namespace Magebees\Productlabel\Block\Adminhtml\System\Config;

class Support extends \Magento\Config\Block\System\Config\Form\Field
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';
        $html .= '<div style="float: left;">
<a href="https://www.magebees.com" target="_blank"><img src="https://www.magebees.com/skin/frontend/default/magentoextensiondesign/images/logo.gif" style="float:left; padding-right: 35px; margin-top: 30px;" /></a></div>
<div style="float:left">
<h2><b>MageBees Product Labels Extension</b></h2>
<p>
<b>Installed Version: v1.1.1</b><br>
Do you need Extension Support? Please create support ticket from <a href="http://support.magebees.com" target="_blank">here</a> or <br> please contact us on <a href="mailto:support@magebees.com">support@magebees.com</a> for quick reply.

</p>
</div>';
        return $html;
    }
}
