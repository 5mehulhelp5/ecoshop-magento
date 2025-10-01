define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magebees_Onepagecheckout/js/action/set-coupon-code',
        'Magebees_Onepagecheckout/js/action/cancel-coupon',
        'Magebees_Onepagecheckout/js/magebees/components'
    ],
    function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction) {
        'use strict';
        var totals = quote.getTotals();
        var couponCode = ko.observable(null);
        if (totals()) {
            couponCode(totals()['coupon_code']);
        }
        var isApplied = ko.observable(couponCode() != null);
        var isLoading = ko.observable(false);
        return Component.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/payment/discount'
            },
            couponCode: couponCode,
			isShowDiscount: ko.observable(window.checkoutConfig.show_discount),
            isApplied: isApplied,
            isLoading: isLoading,
            apply: function () {
                if (this.validate()) {
                    isLoading(true);
                    setCouponCodeAction(couponCode(), isApplied, isLoading);
                }
            },
            cancel: function () {
                if (this.validate()) {
                    isLoading(true);
                    couponCode('');
                    cancelCouponAction(isApplied, isLoading);
                }
            },
            validate: function () {
                var form = '#discount-form';
                return $(form).validation() && $(form).validation('isValid');
            }

        });
    }
);
