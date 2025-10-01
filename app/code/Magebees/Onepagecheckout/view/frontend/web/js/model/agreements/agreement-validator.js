define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';
        var agreementsConfig = window.checkoutConfig.checkoutAgreements;
        return {
            validate: function() {
                if (!agreementsConfig.isEnabled) {
                    return true;
                }

                var form = $('form#magebees-checkout-agreements-form');
                form.validation();
                return form.validation('isValid');
            }
        }
    }
);
