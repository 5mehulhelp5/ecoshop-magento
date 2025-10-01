var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Magebees_Onepagecheckout/js/model/agreements/place-order-mixin': true,
                'Magebees_Onepagecheckout/js/model/place-order-with-comments-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magebees_Onepagecheckout/js/model/payment/place-order-mixin': true
            },
			'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Magebees_Onepagecheckout/js/model/shipping-rates-validation-rules-mixin': true
            }
        }
    },
    map: {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default": "Magebees_Onepagecheckout/js/model/shipping-save-processor/default",
            /*"Magento_Checkout/js/view/payment/list": "Magebees_Onepagecheckout/js/view/payment/list",*/
            "Magento_Braintree/js/view/payment/method-renderer/paypal": "Magebees_Onepagecheckout/js/view/payment/braintree/method-renderer/paypal",
            "Magento_Braintree/js/view/payment/method-renderer/vault": "Magebees_Onepagecheckout/js/view/payment/braintree/method-renderer/vault",
			'cwsopc' : 'Magebees_Onepagecheckout/js/cwsopc',
        }
    },
	deps:[
        'Magebees_Onepagecheckout/js/ordercomment'
    ]
};