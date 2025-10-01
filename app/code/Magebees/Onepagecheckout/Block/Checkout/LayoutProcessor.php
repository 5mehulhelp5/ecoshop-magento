<?php
namespace Magebees\Onepagecheckout\Block\Checkout;
class LayoutProcessor 
{	 
	protected $_helperConfig;
	
    public function __construct(
        \Magebees\Onepagecheckout\Helper\Configurations $helperConfig
    ) {
        $this->_helperConfig = $helperConfig;
    }	 
    public function afterProcess(
    \Magento\Checkout\Block\Checkout\LayoutProcessor $subject, array $jsLayout
    ) {
		
       if ($this->_helperConfig->getEnable()) {
            if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount']);
            }
            if(isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
                $childs = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = $this->processShippingInput($childs);
            }
            if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'])) {
                $childs = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] = $this->processBillingInput($childs);
            }	 
            if(isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['component'])) {
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['component'] = "Magebees_Onepagecheckout/js/view/summary";
            }
            if(isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['component'])) {
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['component'] = "Magebees_Onepagecheckout/js/view/summary/totals";
            }
            if(isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['config']['template'])) {
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['config']['template'] = "Magebees_Onepagecheckout/summary/totals";
            }
            if(isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['config']['template'])) {
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['config']['template'] = "Magebees_Onepagecheckout/summary/totals";
            }
            if(isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['component'])) {
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['component'] = "Magebees_Onepagecheckout/js/view/summary/cart-items";
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['displayArea'] = "item-review";
            }
            if(isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['children']['details']['component'])) {
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['children']['details']['component'] = 'Magebees_Onepagecheckout/js/view/summary/item/details';
            }
            if(isset($jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['children']['details']['children']['thumbnail']['component'])) {
                $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['children']['details']['children']['thumbnail']['component'] = 'Magebees_Onepagecheckout/js/view/summary/item/details/thumbnail';
            }
			$NewsletterLabel = $this->_helperConfig->newsletterLabel();
			$checked = $this->_helperConfig->newsletterChecked();
			$visible = $this->_helperConfig->enableNewsletter();
			
			 $jsLayoutSubscribe = [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'billing-step' => [
                                    'children' => [
                                        'payment' => [
                                            'children' => [
                                                'customer-email' => [
                                                    'children' => [
                                                        'newsletter-subscribe' => [
                                                            'config' => [
                                                                'checkoutLabel' => $NewsletterLabel,
                                                                'checked' => $checked,
                                                                'visible' => $visible
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => [
                                                'customer-email' => [
                                                    'children' => [
                                                        'newsletter-subscribe' => [
                                                            'config' => [
                                                               'checkoutLabel' => $NewsletterLabel,
																'checked' => $checked,
                                                                'visible' => $visible
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
		$jsLayout = array_merge_recursive($jsLayout, $jsLayoutSubscribe);
        }
        return $jsLayout;
    }	
    public function processShippingInput($childs)
	{	
        if(count($childs) > 0){
            foreach($childs as $key => $child){
                if(isset($child['config']['template']) && $child['config']['template'] == 'ui/group/group' && isset($child['children'])){
                    $childs[$key]['component'] = "Magebees_Onepagecheckout/js/view/form/components/group";
                }
                if(isset($child['config']) && isset($child['config']['elementTmpl']) && $child['config']['elementTmpl'] == "ui/form/element/input"){
                    $childs[$key]['config']['elementTmpl'] = "Magebees_Onepagecheckout/form/element/input";
                }
                if(isset($child['config']) && isset($child['config']['template']) && $child['config']['template'] == "ui/form/field"){
                    $childs[$key]['config']['template'] = "Magebees_Onepagecheckout/form/field";
                }
                $sortOrder = $this->_helperConfig->getFieldSortOrder($key);
                if($sortOrder !== false){
                    $childs[$key]['sortOrder'] = strval($sortOrder);
                }
            }
        }
        return $childs;
    }
    public function processBillingInput($payments){
        if(count($payments) > 0){
            foreach($payments as $paymentCode => $paymentComponent){
                if (isset($paymentComponent['component']) && $paymentComponent['component'] != "Magento_Checkout/js/view/billing-address") {
                    continue;
                }
                $paymentComponent['component'] = "Magebees_Onepagecheckout/js/view/billing-address";
                if(isset($paymentComponent['children']['form-fields']['children'])){
                    $childs = $paymentComponent['children']['form-fields']['children'];
                    foreach($childs as $key => $child){
                        if(isset($child['config']['template']) && $child['config']['template'] == 'ui/group/group' && isset($child['children'])){
                            $childs[$key]['component'] = "Magebees_Onepagecheckout/js/view/form/components/group";
                        }
                        if(isset($child['config']) && isset($child['config']['elementTmpl']) && $child['config']['elementTmpl'] == "ui/form/element/input"){
                            $childs[$key]['config']['elementTmpl'] = "Magebees_Onepagecheckout/form/element/input";
                        }
                        if(isset($child['config']) && isset($child['config']['template']) && $child['config']['template'] == "ui/form/field"){
                            $childs[$key]['config']['template'] = "Magebees_Onepagecheckout/form/field";
                        }
                        $sortOrder = $this->_helperConfig->getFieldSortOrder($key);
                        if($sortOrder !== false){
                            $childs[$key]['sortOrder'] = $sortOrder;
                        }
                    }
                    $paymentComponent['children']['form-fields']['children'] = $childs;
                    $payments[$paymentCode] = $paymentComponent;
                }
            }
        }
        return $payments;
    }
}
