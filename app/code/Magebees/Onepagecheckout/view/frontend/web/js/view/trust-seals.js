define(
    [
        'uiComponent'
    ],
    function (Component) {
        'use strict';

        var tsEnabled  = window.checkoutConfig.tsEnabled;
        var tsLabel  = window.checkoutConfig.tsLabel;
        var tsText  = window.checkoutConfig.tsText;
        var tsBadges  = window.checkoutConfig.tsBadges;

        return Component.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/trust-seals'
            },
            isEnabled: function () {
                if(tsEnabled == 0){
                    return "";
                }else{
                    return tsEnabled;    
                }
            },
            getLabel: function () {
                return tsLabel;
            },
            getText: function () {
                return tsText;
            },
            getBadges: function () {
                return tsBadges;
            }
        });
    }
);
