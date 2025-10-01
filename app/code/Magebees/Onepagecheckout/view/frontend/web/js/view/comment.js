define(
    [
        'jquery',
        'ko',
        'uiComponent',
    ],
    function ($, ko, Component) {
        'use strict';
        var show_hide_comments = window.checkoutConfig.enable_comment;
		if(show_hide_comments == 1){
			show_hide_comments = true;
		}else{
			show_hide_comments = false;
		}
        return Component.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/checkout/comment'
            },
            canVisibleBlock: show_hide_comments
        });
    });
