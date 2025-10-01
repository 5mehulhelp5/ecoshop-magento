define([
	"jquery",
	"underscore",
	"ko",
	"uiComponent",
	"uiRegistry",
	"Magento_Checkout/js/model/step-navigator",
	"Magento_Checkout/js/model/quote",
	"Magento_Ui/js/modal/alert",
	"Magento_Customer/js/model/customer"
],
function ($, _, ko, Component, registry, stepNavigator, quote, alertmsg, customer) {
    "use strict"; 
	
	var telephoneval = window.checkoutConfig.telephoneval;
	var company_show = window.checkoutConfig.company_show;
	var delivery_date_required = window.checkoutConfig.delivery_date_required;
	
    return Component.extend({
        defaults: {
			template: "Magebees_Onepagecheckout/placeorder"
		},
        initialize: function () {
            $(function () {
                $("body").on("click", ".payment-method._active :input", function () {
                    $("#place-order-trigger").html($(".payment-method._active").find(".action.primary.checkout").html());
                });
            });
            this._super();
            var self = this;
            return this;
        },
        isVisible: function () {
           // return stepNavigator.isProcessed("shipping");
		   return true;
        },
        placeOrder: function () {
            $(function () {
                //$("body").on("click", "#place-order-trigger", function (event) {
                    //event.stopImmediatePropagation();
                    var errorMsg = "";
                    if (!quote.isVirtual()) {
						if ($("#mbselectedpickupstore").length > 0 && $("#checkout-step-store-selector").is(":visible")) {
							if($.trim($("#mbselectedpickupstore").val()) != "") {
								var selectedstorecode = $("#mbselectedpickupstore").val();
								var storeactionurl = $("#storeactionurl").val();								
								$.ajax({
									url:storeactionurl,
									type:'POST',
									showLoader: false,
									dataType:'json',
									data: {selectedstorecode:selectedstorecode},
									success:function(response){
										try {
											response = response;
										}catch(e){
											alert("Error: Store Code");
										}
									}
								});
							}else{
								alertmsg({
									title: $.mage.__("Alert"),
									content: $.mage.__("Please Select the Pickup Store."),
									modalClass: "mbopcalert",
									actions: { always: function () {} },
									buttons: [
										{
											text: $.mage.__("OK"),
											class: "action primary accept",
											click: function () {
												this.closeModal(true);
											},
										},
									],
								});
								return;
							}
						}
						//return;
                        if ($(".magebees-checkout-shipping-address .shipping-address-items").length == 0) {
							
							if(!customer.isLoggedIn()){
								if ($.trim($(".form-login #customer-email").val()) == "") {
									errorMsg += $.mage.__("Please Enter Email Address");
									errorMsg += "<br>";
								}
							}
                            if ($.trim($("#co-shipping-form input[name=firstname]").val()) == "") {
								errorMsg += $.mage.__("Please Enter Firstname");
								errorMsg += "<br>";
						   }
                            if ($.trim($("#co-shipping-form input[name=lastname]").val()) == "") {
								errorMsg += $.mage.__("Please Enter Lastname");
								errorMsg += "<br>";
							}
							if ($.trim($('#co-shipping-form input[name="street[0]"]').val()) == "") {
								errorMsg += $.mage.__("Please Enter Street");
								errorMsg += "<br>";
							}
							if(telephoneval == "req"){
								if ($.trim($("#co-shipping-form input[name=telephone]").val()) == "") {
									errorMsg += $.mage.__("Please Enter Phone Number");
									errorMsg += "<br>";
								}
							}
                            if ($.trim($("#co-shipping-form select[name=country_id]").val()) == "") {
                                errorMsg += $.mage.__("Please Select Country");
								errorMsg += "<br>";
							}
                            if ($.trim($("#co-shipping-form input[name=city]").val()) == "") {
								errorMsg += $.mage.__("Please Enter City");
								errorMsg += "<br>";
							}
                            if ($.trim($("#co-shipping-form input[name=postcode]").val()) == "") {
								errorMsg += $.mage.__("Please Enter Postcode");
								errorMsg += "<br>";
							}
							if ($("#co-shipping-form select[name=region_id]").is(":visible")){
								if($.trim($("#co-shipping-form select[name=region_id]").val()) == "") {
									errorMsg += $.mage.__("Please Select Region");
									errorMsg += "<br>";
								}
							}
							if ($("#co-shipping-form input[name=region]").is(":visible")){
								if($.trim($("#co-shipping-form input[name=region]").val()) == "") {
									errorMsg += $.mage.__("Please Select Region");
									errorMsg += "<br>";
								}
							}
							if(company_show == "req"){
								if ($.trim($("#co-shipping-form input[name=company]").val()) == "") {
									errorMsg += $.mage.__("Please Enter Company");
									errorMsg += "<br>";
								}
							}							
                            if ($("#termconditon").length && $("#termconditon").is(":visible")) {
                                if ($("#termconditon:checked").length <= 0) {
									errorMsg += $.mage.__("Please Accept Terms and Condition");
									errorMsg += "<br>";
								}
                            }
							
							if ($("#magebees_delivery_date").length && $("#magebees_delivery_date").is(":visible") && delivery_date_required == 1) {
                                if ($.trim($("#magebees_delivery_date").val()) == "") {
									errorMsg += $.mage.__("Please Enter Delivery Date");
									errorMsg += "<br>";
								}
                            }
							
                            if ($.trim(errorMsg) != "") {
								//var buttontxt = $.mage.__("OK");
                                alertmsg({
                                    title: $.mage.__("Alert"),
                                    content: errorMsg,
                                    modalClass: "mbopcalert",
                                    actions: { always: function () {} },
                                    buttons: [
                                        {
                                            text: $.mage.__("OK"),
                                            class: "action primary accept",
                                            click: function () {
                                                this.closeModal(true);
                                            },
                                        },
                                    ],
                                });
                                return;
                            }
                        }
                    }
                    $(".payment-method._active").find(".action.primary.checkout").trigger("click");
                //});
            });
        },
    });
});
