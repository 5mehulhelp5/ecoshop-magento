<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup;

class Color extends \Magento\Config\Block\System\Config\Form\Field {

/**
 * @param \Magento\Backend\Block\Template\Context $context
 * @param Registry $coreRegistry
 * @param array $data
 */

public function __construct(
\Magento\Backend\Block\Template\Context $context, array $data = []
) {
    parent::__construct($context, $data);
}

protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
    $html = $element->getLabelHtml() . $element->getElementHtml();
    $value = $element->getData('value');
	$html .= '<br/><script>
		require([
		"jquery","jquery/colorpicker/js/colorpicker",
		], function ($) {
			"use strict";
			console.log("required the colorpicker. Be free!");
			var $el = $("#' . $element->getHtmlId() . '"); 
			$el.css("backgroundColor", "'. $value .'");
			$el.ColorPicker({	
				color: "'. $value .'",
				onChange: function (hsb, hex, rgb) {					
					$el.css("backgroundColor", "#" + hex).val("#" + hex);
				}
			});	
			
		});
		
		</script>';	
		return $html.'<br/>';
    //return '<p>'.$html.'</p>';
}
}
