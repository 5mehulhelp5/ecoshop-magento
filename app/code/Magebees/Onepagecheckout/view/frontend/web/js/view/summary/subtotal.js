define(
    [
        'Magebees_Onepagecheckout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function (Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/summary/subtotal'
            },
            getPureValue: function() {
                var totals = quote.getTotals()();
                if (totals) {
                    return totals.subtotal;
                }
                return quote.subtotal;
            },
            getValue: function () {
                return this.getFormattedPrice(this.getPureValue());
            }

        });
    }
);
