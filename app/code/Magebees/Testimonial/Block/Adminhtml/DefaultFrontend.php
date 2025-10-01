<?php

namespace Magebees\Testimonial\Block\Adminhtml;

class DefaultFrontend extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    
        return '<div style="background:#efefef;border:1px solid #d8d8d8;padding:10px;margin-bottom:10px;"><span>&lt?php echo 
$this->getLayout()->createBlock("Magebees\Testimonial\Block\Testimonial")->setTemplate("testimonial_detail.phtml")->toHtml(); ?&gt;</span></div>';
    }
}
