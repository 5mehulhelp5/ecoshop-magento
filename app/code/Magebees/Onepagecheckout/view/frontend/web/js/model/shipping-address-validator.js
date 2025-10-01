define(
    [
        'jquery',
		'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magebees_Onepagecheckout/js/model/validate-shipping',
        'Magebees_Onepagecheckout/js/view/shipping'

    ],
    function (
        $,
        addressList,
        quote,
        customer,
        ValidateShipping,
        Shipping
    ) {	
        'use strict';
        return {
            validate: function (form) {
				var loginFormSelector = 'form[data-role=email-with-possible-login]',
				emailValidationResult = customer.isLoggedIn();
				if (!quote.shippingMethod()) {
					ValidateShipping.errorValidationMessage('Please specify a shipping method.');
					return false;
				}

				if (!customer.isLoggedIn()) {
					$(loginFormSelector).validation();
					emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
				}

				if (addressList().length == 0) {
					ValidateShipping.validating(true);
					var checkoutProvider = Shipping().source;
					ValidateShipping.validating(false);
					checkoutProvider.set('params.invalid', false);
					checkoutProvider.trigger('shippingAddress.data.validate');

					if (checkoutProvider.get('shippingAddress.custom_attributes')) {
						checkoutProvider.trigger('shippingAddress.custom_attributes.data.validate');
					}

					if (checkoutProvider.get('params.invalid') ||
							!quote.shippingMethod().method_code ||
							!quote.shippingMethod().carrier_code ||
							!emailValidationResult
					) {
						return false;
					}
				}

				if (!emailValidationResult) {
					$(loginFormSelector + ' input[name=username]').focus();
					return false;
				}
				return true;
            }

        };
    }
);
