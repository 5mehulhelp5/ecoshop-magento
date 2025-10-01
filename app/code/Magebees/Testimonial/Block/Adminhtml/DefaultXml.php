<?php

namespace Magebees\Testimonial\Block\Adminhtml;

class DefaultXml extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<div style="background:#efefef;border:1px solid #d8d8d8;padding:10px;margin-bottom:10px;">
		<span>&lt;referenceContainer name="content"&gt;</br>&lt;block class="Magebees\Testimonial\Block\Testimonial" name="testimonial" ifconfig="testimonial/setting/enable" template="testimonial_detail.phtml"/&gt;</br>&lt;/referenceContainer&gt;</span>	
		</div>';
    }
}
