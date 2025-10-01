define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'Magebees_Onepagecheckout/js/magebees/plugins/jquery.nicescroll.min'
    ],
    function ($, Component, quote, riceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Magebees_Onepagecheckout/summary/grand-total-button'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode();
            },
            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('grand_total').value;
                }
                return this.getFormattedPrice(price);
            },
            getBaseValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_grand_total;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            },
            getGrandTotalExclTax: function() {
                var totals = this.totals();
                if (!totals) {
                    return 0;
                }
                return this.getFormattedPrice(totals.grand_total);
            },
            isBaseGrandTotalDisplayNeeded: function() {
                var totals = this.totals();
                if (!totals) {
                    return false;
                }
                return totals.base_currency_code != totals.quote_currency_code;
            },
            showDetails:function(){
                $('.magebees-minicart-items-wrapper-two').niceScroll({cursorcolor:"#e5e5e5",railpadding: { top: 0, right: 7, left: 0, bottom: 0 }});
                var summary_button = $('.magebees-summary-cart');
                var arrow_button = $('.magebees-arrow-down');
                var table = $('.magebees-Onepagecheckout-summary-wrapper');

                if(table.hasClass('show') && summary_button.hasClass('magebees-summary-cart-arrow') && arrow_button.hasClass('magebees-arrow-down')){
                    table.removeClass('show');
                    summary_button.removeClass('magebees-summary-cart-arrow');
                    arrow_button.removeClass('magebees-arrow-up');
                } else {
                    table.addClass('show');
                    summary_button.addClass('magebees-summary-cart-arrow');
                    arrow_button.addClass('magebees-arrow-up');
                }
            }
        });
    }
);