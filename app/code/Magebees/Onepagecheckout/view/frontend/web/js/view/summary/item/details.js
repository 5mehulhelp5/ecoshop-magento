define(
    [
        'jquery',
        'uiComponent',
        'mage/storage',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/quote',
        'Magebees_Onepagecheckout/js/action/reload-shipping-method',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Ui/js/modal/confirm',
        'Magento_Ui/js/modal/alert',
        'mage/translate',
        'Magento_Catalog/js/price-utils',
		'mage/url',
		'mage/cookies'
    ],
    function (
        $,
        Component,
        storage,
        customerData,
        getTotalsAction,
        totals,
        quote,
        reloadShippingMethod,
        getPaymentInformation,
        confirm,
        alertPopup,
        Translate,
        priceUtils,
		urlBuilder
    ) {
        "use strict";
        return Component.extend({
            params: '',
            defaults: {
                template: 'Magebees_Onepagecheckout/summary/item/details'
            },
            showhidebtn: function(data) {
				if(jQuery('.mbopcqty #'+data.item_id).val() != ""){
					jQuery('.mbopcqty #'+data.item_id).parent().next(".mbupdateBtn").show();
				}else{
					jQuery('.mbopcqty #'+data.item_id).parent().next(".mbupdateBtn").hide();
				}
            },
            getValue: function(quoteItem) {
                return quoteItem.name;
            },
            incQty: function (data) {
                this.updateOscQty(data.item_id, 'update', data.qty, 'inc');
            },
            decQty: function (data) {
                this.updateOscQty(data.item_id, 'update', data.qty, 'dec');
            },
            updateNewQty: function (data) {
                this.updateOscQty(data.item_id, 'update', data.qty, 'upqty');
            },
            deleteItem: function (data) {
                var self = this;
                confirm({
                    content: Translate('Are you sure you would like to remove this item from the shopping cart?'),
                    actions: {
                        confirm: function () {
                            self.updateOscQty(data.item_id, 'delete', '');
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });
            },
            showLoaderImg: function () {
				$('.magebeesLoader').show();
            },
            hideLoaderImg: function () {
				$('.magebeesLoader').hide();
            },
            updateOscQty: function (itemId, type, qty, flag) {
				var params = {
                    itemId: itemId,
                    qty: qty,
                    updateType: type,
					flag: flag
                };
				var self = this;
                this.showLoaderImg();
				$.extend(params, {
                    'form_key': $.mage.cookies.get('form_key')
                });
                $.ajax({
                    url: urlBuilder.build('onepage/quote/update'),
                    data: params,
                    type: 'post',
                    dataType: 'json',
                    context: this,
                }).done(function (result) {
                    }
                ).fail(
                    function (result) {

                    }
                ).always(
                    function (result) {
                        if (result.error) {
                            alertPopup({
                                content: Translate(result.error),
                                autoOpen: true,
                                clickableOverlay: true,
                                focus: "",
                                actions: {
                                    always: function(){

                                    }
                                }
                            });
                        }
                        if(result.cartEmpty || result.is_virtual){
							$('[data-block="minicart"]').replaceWith(result.minicart);
							$('[data-block="minicart"]').trigger('contentUpdated');
                            window.location.reload();
                        }else{
                            reloadShippingMethod();
                            getPaymentInformation().done(function () {
                                self.hideLoaderImg();
                            });
                        }
                    }
                );
            }			
        });
    }
);