define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/postcode-validator',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/quote',
        'mage/translate'
    ],
    function ($, ko, shippingRatesValidationRules, addressConverter, selectShippingAddress, postcodeValidator, shippingRatesValidator, setShippingInformation, quote, $t) {
        'use strict';
        var checkoutConfig = window.checkoutConfig,
            validators = [],
            observedElements = [],
            postcodeElement = null;
        return {
            validateAddressTimeout: 0,
            validateDelay: 2000,
            registerValidator: function (carrier, validator) {
                if (checkoutConfig.activeCarriers.indexOf(carrier) != -1) {
                    validators.push(validator);
                }
            },
            validateAddressData: function (address) {
                return shippingRatesValidator.validateAddressData(address);
            },
            bindChangeHandlers: function (elements, force, delay) {
                var self = this,
                    observableFields = shippingRatesValidationRules.getObservableFields();

                $.each(elements, function (index, elem) {
                    self.bindHandler(elem);

                    if (elem.index === 'postcode') {
                        postcodeElement = elem;
                    }
                });
            },
            bindHandler: function (element, delay) {
                var self = this;
                delay = typeof delay === "undefined" ? self.validateDelay : delay;
                if (element.component.indexOf('/group') != -1) {
                    $.each(element.elems(), function (index, elem) {
                        self.bindHandler(elem);
                    });
                } else {
                    element.on('value', function () {
                        clearTimeout(self.validateAddressTimeout);
                        self.validateAddressTimeout = setTimeout(function () {
                            if (self.postcodeValidation()) {
                                self.validateFields();
                            }
                        }, delay);
                    });
                    observedElements.push(element);
                }
            },
            postcodeValidation: function () {
                var countryId = $('select[name="country_id"]').val(),
                    validationResult = postcodeValidator.validate(postcodeElement.value(), countryId),
                    warnMessage;
                if (postcodeElement == null || postcodeElement.value() == null) {
                    return true;
                }
                postcodeElement.warn(null);
                if (!validationResult) {
                    warnMessage = $t('Provided Zip/Postal Code seems to be invalid.');
                    if (postcodeValidator.validatedPostCodeExample.length) {
                        warnMessage += $t(' Example: ') + postcodeValidator.validatedPostCodeExample.join('; ') + '. ';
                    }
                    warnMessage += $t('If you believe it is the right one you can ignore this notice.');
                    postcodeElement.warn(warnMessage);
                }

                return validationResult;
            },
            validateFields: function () {
                var addressFlat = addressConverter.formDataProviderToFlatData(
                        this.collectObservedData(),
                        'shippingAddress'
                    ),
                    address;
                if (this.validateAddressData(addressFlat)) {
                    address = addressConverter.formAddressDataToQuoteAddress(addressFlat);
                    selectShippingAddress(address);
                    setShippingInformation();
                }
            },
            collectObservedData: function () {
                var observedValues = {};
                $.each(observedElements, function (index, field) {
                    observedValues[field.dataScope] = field.value();
                });
                return observedValues;
            }
        };
    }
);
