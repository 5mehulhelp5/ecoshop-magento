define([
    'jquery',
    'ko',
    'uiComponent',
	'Magebees_Onepagecheckout/js/model/shipping-save-processor/default',
	'Magento_Checkout/js/model/quote',
	'Magento_Checkout/js/model/resource-url-manager',
	'Magento_Checkout/js/model/error-processor',
	'Magento_Checkout/js/model/full-screen-loader',
	'mage/storage'
], function ($, ko, Component, saveshipinfo, quote, resourceUrlManager, errorProcessor, fullScreenLoader, storage) {
    'use strict';

		var show_hide_deldate = window.checkoutConfig.enable_delivery_date;
		var delivery_date_required = window.checkoutConfig.delivery_date_required;
		
		if(show_hide_deldate == 1){
			show_hide_deldate = true;
		}else{
			show_hide_deldate = false;
		}
	
		var deldate_label = window.checkoutConfig.deldate_label;
		if(deldate_label == "" || deldate_label == null){
			deldate_label = "Delivery Date";
		}
		if(delivery_date_required == 1){
			deldate_label = deldate_label+" *";
		}
	
    return Component.extend({
        defaults: {
            template: 'Magebees_Onepagecheckout/delivery-date-block'
        },
		canVisibileDelDate: show_hide_deldate,
		deldatelabel : deldate_label,
		
        initialize: function () {
            this._super();
            var disabled = window.checkoutConfig.disabled;
            var noday = window.checkoutConfig.noday;
            var hourMin = parseInt(window.checkoutConfig.hourMin);
            var hourMax = parseInt(window.checkoutConfig.hourMax);
            var format = window.checkoutConfig.format;
			var deldate_available_from = parseInt(window.checkoutConfig.deldate_available_from);
			var deldate_available_to = parseInt(window.checkoutConfig.deldate_available_to);
			
			
			var show_del_time = window.checkoutConfig.show_del_time;
			
			if(isNaN(deldate_available_from)){	
				deldate_available_from = 3;
			}
			if(isNaN(deldate_available_to)){
				deldate_available_to = 7;
			}
			
			if(!format) {
                format = 'yy-mm-dd';
            }
            if(disabled != null){
				var disabledDay = disabled.split(",").map(function(item) {
					return parseInt(item, 10);
				});
			}

            ko.bindingHandlers.datetimepicker = {
                init: function (element, valueAccessor, allBindingsAccessor) {
                    var $el = $(element);
                    if(noday) {
						
						if(show_del_time == 1){
							var options = {
								minDate: deldate_available_from,
								maxDate: deldate_available_to,
								dateFormat:format
							};
						}else{
							var options = {
								minDate: deldate_available_from,
								maxDate: deldate_available_to,
								dateFormat:format,
								hourMin: hourMin,
								hourMax: hourMax
							};						
						}
                    } else {
						if(show_del_time == 1){
							var options = {
								minDate: deldate_available_from,
								maxDate: deldate_available_to,
								dateFormat:format,
								beforeShowDay: function(date) {
									var day = date.getDay();
									if(disabledDay.indexOf(day) > -1) {
										return [false];
									} else {
										return [true];
									}
								}
							};
						}else{
							var options = {
								minDate: deldate_available_from,
								maxDate: deldate_available_to,
								dateFormat:format,
								hourMin: hourMin,
								hourMax: hourMax,
								beforeShowDay: function(date) {
									var day = date.getDay();
									if(disabledDay.indexOf(day) > -1) {
										return [false];
									} else {
										return [true];
									}
								}
							};
						}
                    }

					if(show_del_time == 1){
						$el.datetimepicker(options);						
					}else{
						$el.datepicker(options);
					}
					
                    var writable = valueAccessor();
                    if (!ko.isObservable(writable)) {
                        var propWriters = allBindingsAccessor()._ko_property_writers;
                        if (propWriters && propWriters.datetimepicker) {
                            writable = propWriters.datetimepicker;
                        } else {
                            return;
                        }
                    }
                    writable($(element).datetimepicker("getDate"));
                },
                update: function (element, valueAccessor) {
                    var widget = $(element).data("DateTimePicker");
                    if (widget) {
                        var date = ko.utils.unwrapObservable(valueAccessor());
                        widget.date(date);
                    }
                }
            };

            return this;
        },setDelDate: function () {
			var payloadDel;
			payloadDel = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code,
						extension_attributes:{
							delivery_date: $('#magebees_delivery_date').val()
                     	}
                    }
                };
			fullScreenLoader.startLoader();
                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payloadDel)
                ).done(
                    function (response) {
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
						errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
		}
    });
});
