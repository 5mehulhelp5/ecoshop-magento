define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';
    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function(originalAction, paymentData, redirectOnSuccess, messageContainer) {
			var orderCommentConfig = window.checkoutConfig.enable_comment;			
			if(orderCommentConfig == 1){
				orderCommentConfig = true;
			}else{
				orderCommentConfig = false;
			}
            if(orderCommentConfig) // true
            {
                var order_comments=jQuery('[name="comment-code"]').val();
                if(typeof(paymentData.additional_data) === 'undefined'
                || paymentData.additional_data === null
                ){
                    paymentData.additional_data = {comments:order_comments};
                }else{
                    paymentData.additional_data.comments = order_comments;
                }
            }
            return originalAction(paymentData, redirectOnSuccess, messageContainer);
        });
    };
});