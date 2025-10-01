define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'ko',
    "jquery-ui-modules/core",
	"jquery-ui-modules/widget",
    'mage/validation/validation'
    
], function ($,$t,modal,ko) {
    "use strict";

    $.widget('magebees_ajaxcart.ajaxToCart', {
    
        ajaxSubmit: function (actionUrl,params,livetime) {
         /** For close quick view popup*/
            document.getElementById('loadingImage').style['display']='block';
            if ($('#product_quickview_content').length) {
                $('#product_quickview_content').modal('closeModal');
            }
            if ($('#ajax_submit_start').length) {
                return;
            }
            $('body').append('<div id="ajax_submit_start"></div>');
            var self = this;
            $.ajax({
                url: actionUrl,
                data: params,
                type: 'post',
                dataType: 'json',
                success: function (result) {

                    
                    document.getElementById('loadingImage').style['display']='none';
                    if (result.html_popup) {
                        $('.quickViewDetails').remove();
                        $('body').append('<div id="popup_content"></div>');
                        // bind popup content in model
                        self.popupModal(result,livetime);
                    }
                    
                    if (result.popup_content) {
                        $('body').append('<div id="confirm_content"></div>');
                        self.popupModal(result,livetime);
                    }
                    
                    if (result.wishlist_content) {
           // content update after wishlist add to cart product
                        self.updateWishlist(result);
                    }
                    
                    if (result.cart_content) {
                          if ($('.cart-container').length) {
                            /*start update total and estimate method when cart item delete*/      require(["Magento_Checkout/js/action/get-totals"], function (getTotalsAction) {
                                    var deferred = $.Deferred();
                                    getTotalsAction([], deferred);
                                 });
                                require([
                                'Magento_Checkout/js/model/quote',
                                'Magento_Checkout/js/model/shipping-rate-registry'
                            ], function(quote, rateRegistry){

                                var address = quote.shippingAddress();
                                address.trigger_reload = new Date().getTime();
                                rateRegistry.set(address.getKey(), null);
                                rateRegistry.set(address.getCacheKey(), null);
                                quote.shippingAddress(address);
                                location.reload(); 
                            });
                            /*end update total and estimate method when cart item delete*/               
                          }
                        // content update after checkout cart remove product
                        if (result.is_empty) {
                        $('.cart-container').html(result.cart_content);
                        } else {
                            $(".crosssell").html("");
                            $("#form-validate").replaceWith(("#form-validate",result.cart_content));
                        }
                    }
                    $('body #ajax_submit_start').remove();
                }
            });
        },
        ajaxPopup: function (actionUrl,livetime) {
            document.getElementById('loadingImage').style['display']='block';
            var self = this;
            $.ajax({
                url: actionUrl,
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    document.getElementById('loadingImage').style['display']='none';
                    if (result.html_popup) {
                        $('.quickViewDetails').remove();
                        $('body').append('<div id="popup_content"></div>');
                        // bind popup content in model
                        self.popupModal(result,livetime);
                    }
                    
                    if (result.popup_content) {
                        $('body').append('<div id="confirm_content"></div>');
                        self.popupModal(result,livetime);
                    }
                    
                    if (result.wishlist_content) {
                        self.updateWishlist(result);
                    }
                }
            });
        },
         ajaxSubmitForm: function (form,livetime) {
                document.getElementById('loadingImage').style['display']='block';
             var self = this;
             $('#popup_content').modal('closeModal');
             
             /** For close quick view popup*/
             if ($('#product_quickview_content').length) {
            $('#product_quickview_content').modal('closeModal');
            }
            
             /* if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== ''){
                   var params = new FormData($('#product_addtocart_form')[0]);
              } else {
                  var params=form.serialize();
              }*/
          
             if ($('#ajax_add_start').length) {
             return;
             }
             $('body').append('<div id="ajax_add_start"></div>');

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    document.getElementById('loadingImage').style['display']='none';
                    if (result.html_popup) {
                        $('.quickViewDetails').remove();
                        $('body').append('<div id="popup_content"></div>');
                        // bind popup content in model
                        self.popupModal(result,livetime);
                    }

                    if (result.popup_content) {
                        $('body').append('<div id="confirm_content"></div>');
                        self.popupModal(result,livetime);
                    }

                    if (result.wishlist_content) {
                        self.updateWishlist(result);
                    }
                    if (result.cart_content) {
                          if ($('.cart-container').length) {
                         require(["Magento_Checkout/js/action/get-totals"], function (getTotalsAction) {
                            var deferred = $.Deferred();
                            getTotalsAction([], deferred);
                         });
                          }
                        // content update after checkout cart remove product
                        if (result.is_empty) {
                        $('.cart-container').html(result.cart_content);
                        } else {
                            $(".crosssell").html("");
                            $("#form-validate").replaceWith(("#form-validate",result.cart_content));
                        }
                    }
                     $('body #ajax_add_start').remove();
                }
            });
        },
        updateWishlist: function (result) {
            if (result.is_empty) {
                $("#wishlist-view-form").replaceWith(("#wishlist-view-form",result.wishlist_content));
            } else {
                if (result.item_id) {
                    var item = result.item_id;
                    $("#item_"+item).remove();
                }
            }
        },
        shopContinue: function () {
            // $('#confirm_content').modal('closeModal');
        },
        popupModal: function (result,livetime) {
            var self = this,
                modelClass = "cartDetails";
                if (result.popup_content) {
                    modelClass = "cartDetails cartBox";
                }
                
            var options =
            {
                type: 'popup',
                modalClass: modelClass,
                responsive: true,
                innerScroll: true,
                clickableOverlay: false,
                title: false,
                buttons: false
            };

            if (result.popup_content) {
                var popup = modal(options, $('#confirm_content'));
                $('#confirm_content').html(result.popup_content);
                $('#confirm_content').modal('openModal');

                /*start close popup as per configuration and display timer*/
                var sec= parseInt(livetime);
                var remain='';
                var timer=null;
                var timer = setInterval(function () {
                    remain=sec--;
                    $("#shopcontinue").text("Continue Shopping ("+ remain +")");

                    if (remain == 0) {
                        $('#confirm_content').modal('closeModal');
                        clearInterval(timer);
                    }
                    $('body').on('click','.cartDetails .action-close',function () {
                        
                  
                        $('#confirm_content').remove();
                        $('#confirm_content').modal('closeModal');
                       clearInterval(timer);
                         
                    });
                    
                    $('body').on('click','#shopcontinue',function () {
               
                        $('#confirm_content').modal('closeModal');
                       clearInterval(timer);
                        $('#confirm_content').remove();
                    });
                }, 1000);
                /*end close popup as per configuration and display timer*/
            } else if (result.html_popup) {
                var popup = modal(options, $('#popup_content'));
                $('#popup_content').html(result.html_popup);
                $('#popup_content').modal('openModal');
            }
        }
    });

    return $.magebees_ajaxcart.ajaxToCart;
});