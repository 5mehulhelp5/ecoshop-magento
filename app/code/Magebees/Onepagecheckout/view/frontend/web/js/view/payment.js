define(
    [
        'jquery',
        "underscore",
        'ko',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'mage/translate',
        'Magento_Checkout/js/view/payment',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/method-list',
        'Magebees_Onepagecheckout/js/action/save-default-payment'
    ],
    function (
        $,
        _,
        ko,
        paymentService,
        methodConverter,
        $t,
        Payment,
        quote,
        methodList,
        saveDefaultPayment
    ) {
        'use strict';
        paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));
        return Payment.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/payment',
                activeMethod: ''
            },
            initialize: function () {
                this.beforeInitPayment();
                this._super();
                this.navigate();
                methodList.subscribe(function () {
                    saveDefaultPayment();
                });
                return this;
            },
            beforeInitPayment: function(){
                quote.shippingAddress.subscribe(function(){
                    if(quote.shippingAddress() && !quote.shippingAddress().street){
                        quote.shippingAddress().street = ['',''];
                    }
                });
            }
        });
    }
);
