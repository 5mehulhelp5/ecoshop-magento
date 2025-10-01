define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'ko',
    "jquery-ui-modules/core",
    "jquery-ui-modules/widget",
    'mage/validation/validation',
    "magebees/ajaxcomparewishlist",
	"Magento_Customer/js/customer-data"
], function ($,$t,modal,ko,core,widget,validation,ajaxcomparewishlist,customerData) {
    "use strict";
    $.widget('magebees_ajaxcart.ajaxToCart', {
		_init: function () {
			
        },
		updateCate:function (){
			require([
				'Magento_Customer/js/customer-data'], function (customerData) {
				var sections = ['cart'];
				customerData.invalidate(sections);
				customerData.reload(sections, true);
			});
		},
		prepeareSidebarCart: function () {
		if ($('#poco-sidebar-minicart').length) {
            var $sidebarCart = $('#poco-sidebar-minicart');
            if (!$sidebarCart.data('prepared')) {
                $sidebarCart.on('click', '[data-role=cart-trigger]', function() {
                    $sidebarCart.toggleClass('opened');
                    if ($sidebarCart.hasClass('opened')) $sidebarCart.trigger('dropdowndialogopen');
                }).on('click', '[data-action=close]', function() {
                    $sidebarCart.removeClass('opened');
                }).on('cartLoading', function() {
                	$sidebarCart.addClass('ajaxcart-loading');
				}).on('cartLoaded', function() {
                    $sidebarCart.removeClass('ajaxcart-loading');
				}).data('prepared', true);
            }
		}	
		},
		prepareInformedPopup: function (show_popup) {
			var sections = ['cart'];
			var self = this;
			customerData.reload(sections, true);
			$(document).on('customer-data-reload',function(e, arg){
			if (show_popup=="1") {
				if ($('#poco-minicart-popup').length) {
					var $popupCart = $('#poco-minicart-popup');
					if (!$popupCart.data('prepared')) {
						$popupCart.on('cartLoading', function() {
						$popupCart.addClass('ajaxcart-loading');
					}).on('cartLoaded', function() {
						$popupCart.removeClass('ajaxcart-loading');
					}).data('prepared', true);
				}
				if (!$popupCart.find('.minicart-items-wrapper .section-content-inner').hasClass('nice-scroll')) {
					$popupCart.find('.minicart-items-wrapper .section-content-inner').addClass('nice-scroll');
				}
				// The node to be monitored
				
				if($(".modals-wrapper").length)
				{	
				var target = $(".modals-wrapper");
			var reOpenCartPoupUp = false;
			var observer = new MutationObserver(function( mutations ) {
			 mutations.forEach(function( mutation ) {
				var ChildrenLength = mutation.target.children.length;
				var ChildrenCount = parseInt(ChildrenLength);
				if((mutation.target.children.length > 0)&(mutation.target.children.length < 3)){
					var $childrens = $(mutation.target.children);
					$childrens.each(function() {
						var $children = $( this );
						var className = $children[0].className;
						var QuickViewPopupClass = 'modal-popup quickViewDetails';
						var modalsOverlay = 'modals-overlay';
						if($('body').hasClass('cart-informed-modal-opened') && 
						(($children[0].className!='modal-popup confirm _show') || 
						(className.indexOf(QuickViewPopupClass) != -1)&&(className.indexOf(modalsOverlay) != -1)))
						{
							if(ChildrenCount == 2){
								reOpenCartPoupUp = true;
							}else{
								reOpenCartPoupUp = false;
							}
						}
					});
				}else{
					reOpenCartPoupUp = false;
				}
				var newNodes = mutation.addedNodes; // DOM NodeList
				if( newNodes !== null ) { // If there are new nodes added
					var $nodes = $( newNodes ); // jQuery set
					$nodes.each(function() {
						var $node = $( this );
						if( $node.hasClass("modal-popup confirm _show")) {
							$('body').on("click",".action-dismiss",function(e) {
							});				
							$('body').on("click",".action-accept",function(e) {
								//I want to do Ajax stuff   
							});	
							$(document).on("click",".action-dismiss",function(e) {
										//I want to do Ajax stuff   
							});	
							$(document).on("click",".action-accept",function(e) {
								//I want to do Ajax stuff   
							});	
						}else{
						}
					});
				}
			  });  
				if (reOpenCartPoupUp) {
						$("#autoClose").click();
						clearInterval(window.CartPopuptimer);
						self.prepareInformedPopup(show_popup);
						self.reOpenInformedPopup();
					}
			});
			// Configuration of the observer:
				var config = { 
					attributes: true, 
					childList: true, 
					characterData: true 
				};
				// Pass in the target node, as well as the observer options
			observer.observe(target[0], config);
			}
			
			//observer.disconnect(); 
			// Later, you can stop observing
				$popupCart.on('click', '[data-action=close]', function() {
					if(typeof observer != 'undefined')
					{
						observer.disconnect(); 
					}
					
					clearInterval(window.CartPopuptimer);
					$popupCart.modal('closeModal');
				});
				if ($popupCart.data('prepared')) {
					self.showInformedPopup(observer);
				}
			
			}
			show_popup = false;
				}


			
			
		});
		},
		prepeareFooterCart: function () {
		 if ($('#poco-footer-minicart').length) {
            var $ftrCart = $('#poco-footer-minicart');
            if (!$ftrCart.data('prepared')) {
                $ftrCart.css('display', '');
                var $cartTrigger, $itemTrigger, $cartContent = $('[data-role=cart-content]', $ftrCart).first();
                $ftrCart.on('click', '[data-role=cart-trigger]', function() {
                    //alert('line 203');
					var fly_cart_time = parseInt(window.ajaxShoppingCart.fly_cart_time);
					//alert('line 205 :'+fly_cart_time);
					$cartContent.slideToggle(fly_cart_time, 'linear', function() {
                        $ftrCart.toggleClass('opened');
                    });
                }).on('click', '[data-role=item-trigger]', function() {
                    var $trigger = $(this), $item = $trigger.parents('.product-item').first(), $actions = $('[data-role=item-actions]', $item).first();
                    $item.toggleClass('active').siblings().removeClass('active');
                }).data('prepared', true);
            }
            $ftrCart.on('cartLoading', function() {
                $ftrCart.find('[data-role=cart-count]').hide();
                $ftrCart.find('[data-role=cart-processing]').show();
            });
            $ftrCart.on('cartLoaded', function() {
                $ftrCart.find('[data-role=cart-count]').show();
                $ftrCart.find('[data-role=cart-processing]').hide();
            });
        }	
		},
        ajaxSubmit: function (actionUrl,params,livetime,show_popup,currentElement) {
		/** For close quick view popup*/
            document.getElementById('loadingImage').style['display']='block';
            /* 
			if ($('#product_quickview_content').length) {
                $('#product_quickview_content').modal('closeModal');
            }
			*/
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
                    if ((result.html_popup)) {
                        $('.quickViewDetails').remove();
                        $('body').append('<div id="popup_content"></div>');
                        // bind popup content in model
                        self.popupModal(result,livetime);
                    }
                    if((result.popup_content)) {
						
						//self.updateCate();
						if ((show_popup == "1")&&(!result.error)) {
                        	/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							self.prepareInformedPopup(show_popup);
							
							 //self.showInformedPopup();
						}else if ((show_popup == "2")&&(!result.error)) {
							self.prepeareFooterCart();
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							if(parseInt(window.ajaxShoppingCart.auto_close_flycart)){
							clearInterval(window.flycart_timer);	
							}
							self.flyingCartWithoutForm(currentElement, 'footer');
						}else if ((show_popup == "3")&&(!result.error)) {
							$('body').append('<div id="confirm_content"></div>');
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}
							*/
							self.popupModal(result,livetime);	
						}else if(result.error){
							$('body').append('<div id="confirm_content"></div>');
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							self.popupModal(result,livetime);	
						}
					}
                    if (result.wishlist_content) {
						// content update after wishlist add to cart product
                        self.updateWishlist(result);
                    }
                    if (result.cart_content) {
                        $('.cart-container').remove();
                        $(result.cart_content).insertBefore(".crosssell");
                        $(".crosssell").eq(1).remove();
                        $('.cart-container').trigger('contentUpdated');
                        $('.product-items').ajaxcomparewishlist({popupTTL:10,showLoader:true});     
                          if ($('.cart-container').length) {
                            /*start update total and estimate method when cart item delete*/    
                            require(["Magento_Checkout/js/action/get-totals","Magento_Customer/js/customer-data"], function (getTotalsAction,customerData) {
                                    var sections = ['cart'];
                                     /* Minicart reloading */
                                    customerData.reload(sections, true);
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
                        /* if (result.is_empty) {
                        $('.cart-container').html(result.cart_content);
                        } else {
                           $("#form-validate").replaceWith(("#form-validate",result.cart_content));
                        }*/
                    }
                    $('body #ajax_submit_start').remove();
					/** For close quick view popup start*/
					if ($('#product_quickview_content').length) {
						$('#product_quickview_content').modal('closeModal');
					}
					/** For close quick view popup end*/
                }
            });
        },
        ajaxPopup: function (actionUrl,livetime,show_popup) {
            document.getElementById('loadingImage').style['display']='block';
            var self = this;
            $.ajax({
                url: actionUrl,
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    document.getElementById('loadingImage').style['display']='none';
                    if ((result.html_popup)) {
                        $('.quickViewDetails').remove();
                        $('body').append('<div id="popup_content"></div>');
                        // bind popup content in model
                        self.popupModal(result,livetime);
                    }
					if((result.popup_content)) {
						
                        //self.updateCate();
						var sections = ['cart'];
						customerData.reload(sections, true);
						if ((show_popup == "1")&&(!result.error)) {
							/* 
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}
							*/
							 self.prepareInformedPopup(show_popup);
							 //self.showInformedPopup();
						}else if ((show_popup == "2")&&(!result.error)) {
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}
							*/
							self.prepeareFooterCart();
							if(parseInt(window.ajaxShoppingCart.auto_close_flycart)){
							clearInterval(window.flycart_timer);	
							}
							self.flyingCart(form, 'footer');
						}else if ((show_popup == "3")&&(!result.error)) {
							$('body').append('<div id="confirm_content"></div>');
							/*if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							self.popupModal(result,livetime);	
						}else if(result.error){
							$('body').append('<div id="confirm_content"></div>');
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							self.popupModal(result,livetime);	
						}
                    }
                    if (result.wishlist_content) {
                        self.updateWishlist(result);
                    }
					/** For close quick view popup start*/
					if ($('#product_quickview_content').length) {
						$('#product_quickview_content').modal('closeModal');
					}
					/** For close quick view popup end*/
                }
            });
        },
         ajaxSubmitForm: function (form,livetime,show_popup) {
			  document.getElementById('loadingImage').style['display']='block';
             var self = this;
             $('#popup_content').modal('closeModal');
             /** For close quick view popup*/
         /*     if ($('#product_quickview_content').length) {
            $('#product_quickview_content').modal('closeModal');
            }
			*/
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
                    if ((result.html_popup)) {
					    $('.quickViewDetails').remove();
                        $('body').append('<div id="popup_content"></div>');
                        // bind popup content in model
                        self.popupModal(result,livetime);
                    }
					
					
					if((result.popup_content)) {
						
						//self.updateCate();
						var sections = ['cart'];
						customerData.reload(sections, true);
						if ((show_popup == "1")&&(!result.error)) {
							 /*
							 if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}
							*/
							 self.prepareInformedPopup(show_popup);
							 //self.showInformedPopup();
						}else if ((show_popup == "2")&&(!result.error)) {
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							self.prepeareFooterCart();
							if(parseInt(window.ajaxShoppingCart.auto_close_flycart)){
							clearInterval(window.flycart_timer);	
							}
							self.flyingCart(form, 'footer');
						}else if ((show_popup == "3")&&(!result.error)) {
							$('body').append('<div id="confirm_content"></div>');
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							self.popupModal(result,livetime);	
						}else if(result.error){
							$('body').append('<div id="confirm_content"></div>');
							/*
							if (($('#exit-intent-products').is(":visible"))&&($("body").hasClass("_has-modal"))) {
								   $('.exitIntentPopup .action-close').trigger('click');
							}*/
							self.popupModal(result,livetime);	
						}
					}
                    if (result.wishlist_content) {
                        self.updateWishlist(result);
                    }
                    if (result.cart_content) {
                        $('.cart-container').remove();
                        $(result.cart_content).insertBefore(".crosssell");
                        $(".crosssell").eq(1).remove();;
                        $('.cart-container').trigger('contentUpdated');
                        $('.product-items').ajaxcomparewishlist({popupTTL:10,showLoader:true});     
                          if ($('.cart-container').length) {
                         require(["Magento_Checkout/js/action/get-totals"], function (getTotalsAction) {
                            var deferred = $.Deferred();
                            getTotalsAction([], deferred);
                         });
                          }
                        /* if (result.is_empty) {
                        $('.cart-container').html(result.cart_content);
                        } else {
                           $("#form-validate").replaceWith(("#form-validate",result.cart_content));
                        }*/
                    }
                     $('body #ajax_add_start').remove();
					 /** For close quick view popup start*/
					if ($('#product_quickview_content').length) {
						$('#product_quickview_content').modal('closeModal');
					}
					/** For close quick view popup end*/
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
                title: false,
                clickableOverlay: false,
                buttons: false
            };
            // for quick view on cart page
            /* POCO THEME CUSTOMIZATION START */
            //if (result.cart_content) {
                  if ($('.cart-container').length) {
                            /*start update total and estimate method when cart item delete*/    
                            require(["Magento_Checkout/js/action/get-totals","Magento_Customer/js/customer-data"], function (getTotalsAction,customerData) {
                                    var sections = ['cart'];
                                     /* Minicart reloading */
                                    customerData.reload(sections, true);
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
                }   
           // }
            /* POCO THEME CUSTOMIZATION END */
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
					var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
					$("#shopcontinue").text(ctn_btn_label+" ("+ remain +")");
                    
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
        },
		flyingCart: function(form, type) {
				var fly_cart_time = parseInt(window.ajaxShoppingCart.fly_cart_time);
                var $container = $('[data-block=minicartpro]'),  $img, $effImg, $parent, src, $destination, $panelContent;
                $container.trigger('cartLoading');
                if ((window.innerWidth < 768) &&  ($('.js-footer-cart a').length)) {
                    $destination = $('.js-footer-cart a').first();
                } else {
                    if (type == 'footer') {
                        $destination = $('[data-block=minicartpro] [data-role=flying-destination]').first();
                    } else {
                        $destination = $('#desk_cart-wrapper');
                    }
                }
                $panelContent = $('[data-block=minicartpro] .block-minicartpro');
                if (form.parents('.product-item').length) {
                    $parent = form.parents('.product-item').first();
                    $img = $parent.find('.product-item-photo img').first();
                }else if (form.parents('.item').length) {
                    $parent = form.parents('.item').first();
                    $img = $parent.find('.product-image > img').first();
                }else if (form.parents('#product_quickview_content').length) {
                    $parent = form.parents('#product_quickview_content').first();
                    $img = $parent.find('.fotorama__active img.fotorama__img').first();
                }else if (form.parents('#popup_content').length) {
                    $parent = form.parents('#popup_content').first();
                    $img = $parent.find('.pImageBox > img.pImage').first();
                }else if (form.parents('.exitIntentPopup-item').length) {
                    $parent = form.parents('.exitIntentPopup-item').first();
                    $img = $parent.find('.product-item-photo img').first();
                }else {
                    $img = $('.fotorama__active img.fotorama__img');
                }
                if ($img.length) {
                    $effImg = $('<img style="display: none; position:absolute; z-index:100000"/>');
                    $('body').append($effImg);
                    src = $img.attr('src');
                    var width = $img.width(), height = $img.height();
                    var step01Css = {
                        top: (($img.offset().top > $(window).scrollTop()) ? $img.offset().top : ($(window).scrollTop() + 10)),
                        left: $img.offset().left,
                        width: width,
                        height: height
                    }
                    $effImg.attr('src', src).css(step01Css);
                    var flyImage = function () {
                        $effImg.show();
                        var newWidth = 0.1*width, newHeight = 0.1*height;
                        var step02Css = {
                            top: $destination.offset().top,
                            left: $destination.offset().left,
                            width: newWidth,
                            height: newHeight
                        }
                      //alert('line 625 :'+fly_cart_time);
						$effImg.animate(step02Css, fly_cart_time, 'linear', function () {
                            $effImg.fadeOut(100, 'swing', function () {
                                $effImg.remove();
                                if (type == 'sidebar') {
                                    $container.addClass('opened');
                                }
                            });
                        });
						//alert('line 634 :'+fly_cart_time);
                    }
                    if (type == 'footer') {
						var $ftrCart = $('#poco-footer-minicart');
						if ( !$panelContent.is('*:visible') ) {
                            $panelContent.css({minHeight:'none'}).slideDown(1000, 'swing', flyImage);
							$ftrCart.toggleClass('opened');
                        } else {
                            flyImage();
                        }
						if (($panelContent.is('*:visible'))&&(parseInt(window.ajaxShoppingCart.auto_close_flycart))) {
								/*start close flycart as per configuration and display timer*/
								var flycart_sec= parseInt(window.ajaxShoppingCart.flycart_showing_time);
								var flycart_remain='';
								window.flycart_timer=null;
								window.flycart_timer = setInterval(function () {
									flycart_remain=flycart_sec--;
									if (flycart_remain == 0) {
										$panelContent.css({minHeight:'none'}).slideUp(1000, 'swing', flyImage);
										$ftrCart.toggleClass('opened');
										clearInterval(window.flycart_timer);
									}
								}, 1000);
								/*end close flycart as per configuration and display timer*/
						}
                    } else {
                        flyImage();
                    }
                }
            },
			flyingCartWithoutForm: function(current, type) {
				var fly_cart_time = parseInt(window.ajaxShoppingCart.fly_cart_time);
                var $container = $('[data-block=minicartpro]'),  $img, $effImg, $parent, src, $destination, $panelContent;
                $container.trigger('cartLoading');
                if ((window.innerWidth < 768) &&  ($('.js-footer-cart a').length)) {
                    $destination = $('.js-footer-cart a').first();
                } else {
                    if (type == 'footer') {
                        $destination = $('[data-block=minicartpro] [data-role=flying-destination]').first();
                    } else {
                        $destination = $('#desk_cart-wrapper');
                    }
                }
                $panelContent = $('[data-block=minicartpro] .block-minicartpro');
				if (current.parents('.product-item').length) {
                    $parent = current.parents('.product-item').first();
                    $img = $parent.find('.product-item-photo img').first();
                } else if (current.parents('.item').length) {
                    $parent = current.parents('.item').first();
                    $img = $parent.find('.product-image > img').first();
                } else if (current.parents('#product_quickview_content').length) {
                    $parent = current.parents('#product_quickview_content').first();
                    $img = $parent.find('.fotorama__active img.fotorama__img').first();
                }else if (current.parents('#popup_content').length) {
                    $parent = current.parents('#popup_content').first();
                    $img = $parent.find('.pImageBox > img.pImage').first();
                }else if (current.parents('.exitIntentPopup-item').length) {
                    $parent = current.parents('.exitIntentPopup-item').first();
                    $img = $parent.find('.product-item-photo img').first();
                }else {
                    $img = $('.fotorama__active img.fotorama__img');
                }
                if ($img.length) {
                    $effImg = $('<img style="display: none; position:absolute; z-index:100000"/>');
                    $('body').append($effImg);
                    src = $img.attr('src');
                    var width = $img.width(), height = $img.height();
                    var step01Css = {
                        top: (($img.offset().top > $(window).scrollTop()) ? $img.offset().top : ($(window).scrollTop() + 10)),
                        left: $img.offset().left,
                        width: width,
                        height: height
                    }
                    $effImg.attr('src', src).css(step01Css);
                    var flyImage = function () {
                        $effImg.show();
                        var newWidth = 0.1*width, newHeight = 0.1*height;
                        var step02Css = {
                            top: $destination.offset().top,
                            left: $destination.offset().left,
                            width: newWidth,
                            height: newHeight
                        }
						//alert('line 720 :'+fly_cart_time);
                        $effImg.animate(step02Css, fly_cart_time, 'linear', function () {
                            $effImg.fadeOut(100, 'swing', function () {
                                $effImg.remove();
                                if (type == 'sidebar') {
                                    $container.addClass('opened');
                                }
                            });
                        });
						//alert('line 728 :'+fly_cart_time);
                    }
                    if (type == 'footer') {
						var $ftrCart = $('#poco-footer-minicart');
                        if ( !$panelContent.is('*:visible') ) {
                            $panelContent.css({minHeight:'none'}).slideDown(1000, 'swing', flyImage);
							$ftrCart.toggleClass('opened');
                        } else {
                            flyImage();
                        }
						if (($panelContent.is('*:visible'))&&(parseInt(window.ajaxShoppingCart.auto_close_flycart))) {
								/*start close flycart as per configuration and display timer*/
								var flycart_sec= parseInt(window.ajaxShoppingCart.flycart_showing_time);
								var flycart_remain='';
								window.flycart_timer=null;
								window.flycart_timer = setInterval(function () {
									flycart_remain=flycart_sec--;
									if (flycart_remain == 0) {
										$panelContent.css({minHeight:'none'}).slideUp(1000, 'swing', flyImage);
										$ftrCart.toggleClass('opened');
										clearInterval(window.flycart_timer);
									}
								}, 1000);
								/*end close flycart as per configuration and display timer*/
						}
                    } else {
                        flyImage();
                    }
                }
            },
			showInformedPopup: function(observer) {
				var self = this;
                //var popupId = 'poco-minicart-popup';
				var popupId = window.ajaxShoppingCart.popupId;
				var isAutoClose = window.ajaxShoppingCart.is_auto_close;
				var livetime = window.ajaxShoppingCart.auto_close_time;
				
				var $popup = $('#' + popupId);
			    if ($popup.length) {
                    if ($('.cart-informed-modal').length == 0) {
                        modal({
                            innerScroll: true,
							clickableOverlay: true,
							overlay: true,
                            buttons: [],
                            wrapperClass: 'cart-informed-modal',
                            opened: function() {
                                $('body').addClass('cart-informed-modal-opened');
                                $('[data-block=\'minicartpro\']').trigger('dropdowndialogopen');
                                $('.cart-informed-modal .modal-content').addClass('nice-scroll');  
                            },
                            closed: function(event) {
								event.stopImmediatePropagation();
								
								var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
								$("#autoClose").text(ctn_btn_label);
                    
								clearInterval(window.CartPopuptimer);
								$('body').removeClass('cart-informed-modal-opened');
								//observer.disconnect(); 
                            }
                        }, $popup); 
                    }
                    $popup.trigger('cartLoading');
                    if ((!$('body').hasClass('cart-informed-modal-opened'))&&(!$('body').hasClass('ajaxcart-loading'))) {
                        $popup.modal('openModal');
                    } else {
                        $('[data-block=\'minicartpro\']').trigger('dropdowndialogopen');
                    }
				window.CartPopuptimer=null;
				var isAutoClose = window.ajaxShoppingCart.is_auto_close;
				if(isAutoClose){
					var livetime = window.ajaxShoppingCart.auto_close_time;	
					var CartPopupsec= parseInt(livetime);
					var CartPopupremain='';
					window.CartPopuptimer=null;
					window.CartPopuptimer = setInterval(function () {
                    CartPopupremain=CartPopupsec--;
					var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
					$("#autoClose").text(ctn_btn_label+" ("+ CartPopupremain +")");	
                    if (CartPopupremain == 0) {
						var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
						$("#autoClose").text(ctn_btn_label);	
						$popup.modal('closeModal');
						//observer.disconnect(); 
                        clearInterval(window.CartPopuptimer);
                    }
                }, 1000);
				/*end close popup as per configuration and display timer*/
				}
				$('body').on('click','.cart-informed-modal .action-close',function (event) {
                    	event.stopImmediatePropagation();
						//observer.disconnect(); 
						clearInterval(window.CartPopuptimer);
					});
				$('body').on('click','#autoClose',function (event) {
					event.stopImmediatePropagation();
					$popup.modal('closeModal');
					//observer.disconnect(); 
					
					var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
					$("#autoClose").text(ctn_btn_label);	
                    clearInterval(window.CartPopuptimer);
                });
                }
            },
			reOpenInformedPopup: function() {
				var self = this;
                var popupId = window.ajaxShoppingCart.popupId;
				var $popup = $('#' + popupId);
			    if ($popup.length) {
                        modal({
                            innerScroll: true,
							clickableOverlay: true,
							overlay: true,
                            buttons: [],
                            wrapperClass: 'cart-informed-modal',
                            opened: function() {
                                $('body').addClass('cart-informed-modal-opened');
                                $('[data-block=\'minicartpro\']').trigger('dropdowndialogopen');
                                $('.cart-informed-modal .modal-content').addClass('nice-scroll');  
                            },
                            closed: function(event) {
								event.stopImmediatePropagation();
								
								var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
								$("#autoClose").text(ctn_btn_label);	
					
								clearInterval(window.CartPopuptimer);
								$('body').removeClass('cart-informed-modal-opened');
                            }
                        }, $popup); 
                    $popup.trigger('cartLoading');
					$popup.modal('openModal');
					window.CartPopuptimer=null;
					var isAutoClose = window.ajaxShoppingCart.is_auto_close;
					if(isAutoClose){
						var livetime = window.ajaxShoppingCart.auto_close_time;	
						var CartPopupsec= parseInt(livetime);
						var CartPopupremain='';
						window.CartPopuptimer=null;
						window.CartPopuptimer = setInterval(function () {
						CartPopupremain=CartPopupsec--;
						var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
						$("#autoClose").text(ctn_btn_label+" ("+ CartPopupremain +")");		
						if (CartPopupremain == 0) {
							var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
							$("#autoClose").text(ctn_btn_label);		
							$popup.modal('closeModal');
							//observer.disconnect(); 
							clearInterval(window.CartPopuptimer);
						}
					}, 1000);
					/*end close popup as per configuration and display timer*/
					}
					$('body').on('click','.cart-informed-modal .action-close',function (event) {
                    	event.stopImmediatePropagation();
						//observer.disconnect(); 
						clearInterval(window.CartPopuptimer);
					});
				$('body').on('click','#autoClose',function (event) {
					event.stopImmediatePropagation();
					$popup.modal('closeModal');
					//observer.disconnect(); 
					
					var ctn_btn_label = window.ajaxShoppingCart.continue_shopping_label;
					$("#autoClose").text(ctn_btn_label);		
							
                    clearInterval(window.CartPopuptimer);
                });
				}
            }
    });
    return $.magebees_ajaxcart.ajaxToCart;
});