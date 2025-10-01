define(
    [
        'ko',
        'uiComponent'
    ],
    function(ko, Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/order-comment'
            },
            isShowComment: ko.observable(window.checkoutConfig.show_comment)
        });
    }
);
