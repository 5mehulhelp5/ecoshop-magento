define(
    [
        'jquery',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/action/create-billing-address',
        'underscore'
    ],
    function (
        $,
        setShippingInformationAction,
        stepNavigator,
        addressList,
        quote,
        checkoutData,
        createShippingAddress,
        selectShippingAddress,
        selectShippingMethodAction,
        paymentService,
        selectPaymentMethodAction,
        addressConverter,
        selectBillingAddress,
        createBillingAddress,
        _
    ) {
        'use strict';
		var ShippingCalled = true;
        return {
            resolveEstimationAddress: function () {
                var address;

                if (checkoutData.getShippingAddressFromData()) {
                    address = addressConverter.formAddressDataToQuoteAddress(checkoutData.getShippingAddressFromData());
                    selectShippingAddress(address);
                } else {
                    this.resolveShippingAddress();
                }

                if (quote.isVirtual()) {
                    if (checkoutData.getBillingAddressFromData()) {
                        address = addressConverter.formAddressDataToQuoteAddress(
                            checkoutData.getBillingAddressFromData()
                        );
                        selectBillingAddress(address);
                    } else {
                        this.resolveBillingAddress();
                    }
                }

            },
            resolveShippingAddress: function () {
                var newCustomerShippingAddress = checkoutData.getNewCustomerShippingAddress();

                if (newCustomerShippingAddress) {
                    createShippingAddress(newCustomerShippingAddress);
                }
                this.applyShippingAddress();

            },
            applyShippingAddress: function (isEstimatedAddress) {
                var address,
                    shippingAddress,
                    isConvertAddress,
                    addressData,
                    isShippingAddressInitialized;

                if (addressList().length == 0) {
                    address = addressConverter.formAddressDataToQuoteAddress(
                        checkoutData.getShippingAddressFromData()
                    );
                    selectShippingAddress(address);
                }
                shippingAddress = quote.shippingAddress();
                isConvertAddress = isEstimatedAddress || false;

                if (!shippingAddress) {
                    isShippingAddressInitialized = addressList.some(function (addressFromList) {
                        if (checkoutData.getSelectedShippingAddress() == addressFromList.getKey()) {
                            addressData = isConvertAddress ?
                                addressConverter.addressToEstimationAddress(addressFromList)
                                : addressFromList;
                            selectShippingAddress(addressData);

                            return true;
                        }

                        return false;
                    });

                    if (!isShippingAddressInitialized) {
                        isShippingAddressInitialized = addressList.some(function (address) {
                            if (address.isDefaultShipping()) {
                                addressData = isConvertAddress ?
                                    addressConverter.addressToEstimationAddress(address)
                                    : address;
                                selectShippingAddress(addressData);

                                return true;
                            }

                            return false;
                        });
                    }

                    if (!isShippingAddressInitialized && addressList().length == 1) {
                        addressData = isConvertAddress ?
                            addressConverter.addressToEstimationAddress(addressList()[0])
                            : addressList()[0];
                        selectShippingAddress(addressData);
                    }
                }
            },			
			resolveShippingRates: function (ratesData) {
				$('.magebeesLoader').show();
                var selectedShippingRate = checkoutData.getSelectedShippingRate(),
                    availableRate = false;
					
				if(ShippingCalled == true){
					 if (ratesData.length >= 1) {
						for (var i = 0; i < ratesData.length; i++) {
							if(ratesData[i].carrier_code == window.checkoutConfig.default_shipping_method){
								selectShippingMethodAction(ratesData[i]);
								setShippingInformationAction();
								ShippingCalled = false;
								return;
							}
						}
					}					
				}
                if (ratesData.length == 1) {
                    selectShippingMethodAction(ratesData[0]);
                    setShippingInformationAction();
                    return;
                }
                if (quote.shippingMethod()) {
                    availableRate = _.find(ratesData, function (rate) {
                        return rate.carrier_code == quote.shippingMethod().carrier_code &&
                            rate.method_code == quote.shippingMethod().method_code;
                    });
                }
                if (!availableRate && selectedShippingRate) {
                    availableRate = _.find(ratesData, function (rate) {
                        return rate.carrier_code + '_' + rate.method_code === selectedShippingRate;
                    });
                }
                if (!availableRate && window.checkoutConfig.selectedShippingMethod) {
                    availableRate = true;
                    selectShippingMethodAction(window.checkoutConfig.selectedShippingMethod);
                    stepNavigator.next();
                }
                if (!availableRate) {
                    selectShippingMethodAction(null);
                    if(!quote.isVirtual()){
                        paymentService.setPaymentMethods([]);
                    }
                } else {
                    if(typeof(availableRate) === 'object'){
                        selectShippingMethodAction(availableRate);
                        setShippingInformationAction();
                    }else{
                        selectShippingMethodAction(availableRate);
                    }
                }
				
            },
            resolvePaymentMethod: function () {				
                var availablePaymentMethods = paymentService.getAvailablePaymentMethods(),
                    selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();
                if (selectedPaymentMethod) {
                    availablePaymentMethods.some(function (payment) {
                        if (payment.method == selectedPaymentMethod) {
                            selectPaymentMethodAction(payment);
                        }
                    });
                }
            },
            resolveBillingAddress: function () {
                var selectedBillingAddress = checkoutData.getSelectedBillingAddress(),
                    newCustomerBillingAddressData = checkoutData.getNewCustomerBillingAddress();
                if (selectedBillingAddress) {
                    if (selectedBillingAddress == 'new-customer-address' && newCustomerBillingAddressData) {
                        selectBillingAddress(createBillingAddress(newCustomerBillingAddressData));
                    } else {
                        addressList.some(function (address) {
                            if (selectedBillingAddress == address.getKey()) {
                                selectBillingAddress(address);
                            }
                        });
                    }
                } else {
                    this.applyBillingAddress();
                }
            },
            applyBillingAddress: function () {
                var shippingAddress;
                if (quote.billingAddress()) {
                    selectBillingAddress(quote.billingAddress());
                    return;
                }
                shippingAddress = quote.shippingAddress();

                if (shippingAddress &&
                    shippingAddress.canUseForBilling() &&
                    (shippingAddress.isDefaultShipping() || !quote.isVirtual())
                ) {
                    selectBillingAddress(quote.shippingAddress());
                }
            }
        };
    }
);
