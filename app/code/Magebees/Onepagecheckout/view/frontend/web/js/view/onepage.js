define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'mage/translate',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/modal/alert'
    ],
    function (
        $,
        Component,
        ko,
        $t,
        quote,
        alertPopup
    ) {
        'use strict';

        var mblayout = window.checkoutConfig.checkout_page_layout;
        return Component.extend({
            mbcpl: ko.observable(window.checkoutConfig.checkout_page_layout),
            getclass: function () {
            if (mblayout) {
                return "opc-wrapper magebees-one-page-checkout-wrapper mageb-onecolumn-layout";
            }else{
                return "opc-wrapper magebees-one-page-checkout-wrapper";
            }
                return "opc-wrapper magebees-one-page-checkout-wrapper";
            },
            chklayout: function(){
                return mblayout;
            },
            mbsticky: function(){

            }
        });
    }
);
