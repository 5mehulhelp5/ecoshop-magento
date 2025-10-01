define([
    'jquery',
    "jquery-ui-modules/core",
	"jquery-ui-modules/widget"
], function ($,ui) {
    'use strict';
	
	var defaultShippingMethod;
	var isCustomerLogged;
	var numberofAttachment;
	
    $.widget('Magebees.cwsopc', {
		
		_init: function () {
			defaultShippingMethod = this.options.defaultShippingMethod;
			isCustomerLogged = this.options.isCustomerLogged;
			numberofAttachment = this.options.numberofAttachment;

			var self = this;
			
			setTimeout(function(){
				self.checkContainer();
				self.checkCommentsBlock();
				
				/* For update the Shipping Method */
				$(document).on( 'blur', '#co-shipping-form input[name="telephone"]', function() {
					self.checkContainer();
					$('.payment-method._active input[type=checkbox]').attr('checked', false);
					setTimeout(function(){ 
						$('.payment-method._active input[type=checkbox]').trigger('click');
					}, 5000);
				});
				$(document).on( 'blur', '#co-shipping-form input[name="street[0]"]', function() {
					self.checkContainer();
				});
				$(document).on( 'blur', '#co-shipping-form input[name="city"]', function() {
					self.checkContainer();
				});
				$(document).on( 'blur', '#co-shipping-form input[name="postcode"]', function() {
					self.checkContainer();
				});
				$(document).on( 'blur', '#co-shipping-form input[name="region"]', function() {
					self.checkContainer();
				});
				$(document).on( 'blur', '#co-shipping-form select[name="region_id"]', function() {
					self.checkContainer();
				});
				if(isCustomerLogged){
					$(document).on( 'click', '.magebees-shipping-address-item', function() {
						self.checkContainer();
					});
				}
				$('#billing-address-same-as-shipping-checkmo').prop("checked", true).trigger("change");
				/* End of Update Shipping Method Code */


				if($('.magebees-billing-address-details').is(':visible')){
					$('.magebees-billing-address-details').hide();
				}
				$(document).on('click', '#billing-address-same-as-shipping-checkmo', function(){
					if ($(this).is(":checked")) {
						$(".magebees-billing-address-details").hide();
					} else {
						$(".magebees-billing-address-details").show();
					}
				});

			}, 5000);
		},
		_create: function () {
			
        },
		checkContainer: function() {
			if($('.mageship').is(':visible')){
				if(defaultShippingMethod != ""){
					$(".mageship").each(function(index) {						
						var str1 = this.value;
						var str2 = defaultShippingMethod;
						if(str1.indexOf(str2) != -1){
							$('.mageship').eq(index).trigger('click');
							$('.loading-mask').css("background","transparent");
							/* $('.loading-mask .loader').hide(); */
						}
					});
				}else{
					$('.mageship').eq(0).trigger('click');
					$('.loading-mask').css("background","transparent");
					/* $('.loading-mask .loader').hide(); */
				}
          } else {
            setTimeout(self.checkContainer, 3000);
          }
		},
		checkCommentsBlock: function () {
			if($('.attachCommentBlock').is(':visible')){
				$('.attachCommentBlock').hide();
				$('.attachCommentBlock:first').show();
				$('.addMorebtn').click(function() {
					$('.attachCommentBlock:visible').next().show();
					if($('.attachCommentBlock:visible').last().attr('id') == numberofAttachment){
						$(".addMorebtn").hide();
					}
				});
				$('.block-order-comments').on('click','.attComRmv',function() {
					$(this).parent().hide();
					if($('.attachCommentBlock:visible').last().attr('id') < numberofAttachment){
						$(".addMorebtn").show();
					}
				});  
			} else {
				setTimeout(self.checkCommentsBlock, 3000);
			}
        },
	});

    return $.Magebees.cwsopc;
});