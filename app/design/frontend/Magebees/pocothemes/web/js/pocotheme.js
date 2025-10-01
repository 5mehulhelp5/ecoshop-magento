/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
*/
define([
    'jquery',
    'domReady!',
	'Magento_Catalog/js/price-utils',
	'Magento_Ui/js/modal/modal',
	'magebees.wow',
	'mage/cookies',
], function ($,domready,priceUtils,modal,WOW) {
    'use strict';
	$.widget('Magebees.pocotheme', {
		 _create: function() {
			var exitIntentPopupEnable = this.options.enable_exit_intent_popup;
			
			var sticky_menu_enable = this.options.sticky_menu;
			var breakPoint = this.options.breakPoint;
			this.headerSection();
			var theme_option_rtl = this.options.theme_option_rtl;
			
			if(exitIntentPopupEnable != 0){
				this.exitIntentPopup();
			}
			var cookiePolicyEnable = this.options.enable_cookie_policy;
			if(cookiePolicyEnable != 0){
				var cookieExpires = new Date(new Date().getTime() + this.options.cookieExpires * 1000);
				var cookieLifetime = this.options.cookieLifetime;
				var cookieName = this.options.cookieName;
				var noCookiesUrl = this.options.noCookiesUrl;
				this.cookiePolicy(cookieExpires,cookieLifetime,cookieName,noCookiesUrl);
			}
			var enableBrowserTabNotification = this.options.enableBrowserTabNotification;
			
			if(enableBrowserTabNotification != 0){
				
			var browserTabNotificationTimeInterval = this.options.browserTabNotificationTimeInterval;
			var browserTabNotificationMessage = this.options.browserTabNotificationMessage;
			var dataDefault = {
				titleTag: document.getElementsByTagName("title")[0]
			};
			var dataDynamic = {
				messages: browserTabNotificationMessage ,
				delay:browserTabNotificationTimeInterval
			};
			var browserdata = Object.assign({}, dataDynamic, dataDefault);
			/* Check if the 'titleTag' property exists in the 'data' object*/
			  if (browserdata.titleTag) {
				/* Set some additional properties*/
				browserdata.originalTitle = browserdata.titleTag.innerText;
				browserdata.isSingleMessage = browserdata.messages.length < 2;
				browserdata.isActive = false;
				this.options.browserdata = browserdata;
				/* Add an event listener for the visibility change event*/
				document.addEventListener('visibilitychange', 
					jQuery.proxy(this.browserTabNotification,this)
					, false);
			  }
			 
				
			}
			
			
			var enableAnimation = this.options.enable_animation;
			var enableMobileAnimation = this.options.enable_animation_on_mobile;
			if(enableAnimation != 0){
				this.animationWow(enableMobileAnimation);
			}
			if(sticky_menu_enable != 0){
				this.stickyMenu();
				jQuery(document).ready(jQuery.proxy(this.stickyMenuResize,this));
				jQuery(window).resize(jQuery.proxy(this.stickyMenuResize,this));
			}else{
				$('.page-header').addClass('no-sticky');
			}
			if(theme_option_rtl != 0){
				this.topoMenuRtl();
			}
			var scroll_top_enable = this.options.scroll_top_enable;
			if(scroll_top_enable != 0){
				this.scrollTop();
			}
			this.macClassAdd();
			this.wishlistcounter();
			jQuery(document).ready(jQuery.proxy(this._footermenu,this));
			jQuery(document).ready(jQuery.proxy(this._footermobilemenu,this));
			jQuery(window).resize(jQuery.proxy(this._footermobilemenu,this));
			if(document.querySelector('.fixbtm_menu_mbl')){
            if (jQuery(window).width() < breakPoint ) {
					var bar_menubar_mobile = document.querySelector('.fixbtm_menu_mbl'); 
					document.querySelector('body').style.paddingBottom = bar_menubar_mobile.offsetHeight  + "px";
				}
			}
		
		},
		animationWow: function(enableMobileAnimation){
			
			var wow = new WOW({boxClass:'wow',animateClass: 'animate__animated', offset:30,
				mobile:enableMobileAnimation   });
			wow.init();
				jQuery('div.productsListing-section').each(function(index){
				var productListing = this;
				jQuery('body').find('.tab-title').on('click',function(){
					jQuery(productListing).find('.wow').each(function(){
						wow.show(this);
					});
				});
				});
				
				jQuery(document).ajaxSuccess(function( event, xhr, settings ) {
					var ajaxRequestURL = settings.url;
					var isAjaxLoadProducts = ajaxRequestURL.includes("/prodlist/index/ajaxload");  
					if(isAjaxLoadProducts){
						jQuery('div.productsListing-section').each(function(index){
						var productListing = this;
						jQuery('body').find('.tab-title').on('click',function(){
							jQuery(productListing).find('.wow').each(function(){
								wow.show(this);
							});
						});
						});
					
					}
				});

		},
		browserTabNotification: function(){
			if (document.visibilityState === 'visible') {
				var browserdata = this.options.browserdata;
				
				/* If the page is visible, clear the timer and reset the title tag*/
			  if (!browserdata.isActive) return;
			  clearInterval(browserdata.timerId);
			  browserdata.titleTag.innerText = browserdata.originalTitle;
			} else {
				var browserdata = this.options.browserdata;
				
			 /* If the page is hidden, set the new title tag and start a timer*/
			  let messageIndex = 1;
			  let currentMessage = browserdata.messages[messageIndex];
			  if (browserdata.isActive = true, browserdata.titleTag.innerText = currentMessage, browserdata.isSingleMessage) return;
			  browserdata.timerId = setInterval(function () {
				let nextMessage = browserdata.messages[++messageIndex];
				if (!nextMessage) {
				  messageIndex = 1;
				  nextMessage = currentMessage;
				}
				browserdata.titleTag.innerText = nextMessage;
			  }, browserdata.delay);
			}
		},
		cookiePolicy: function(cookieExpires,cookieLifetime,cookieName,noCookiesUrl){
			if(!jQuery.cookie(cookieName)){
				jQuery("#cookie_bar").slideDown();
				
			}
			jQuery("#close_cookie_bar").bind("click", function() {
				jQuery("#cookie_bar").slideUp();
				jQuery.cookie(cookieName, true, {expires : cookieExpires,SameSite : 'Strict',path    : '/',secure  : true});
				
				if(!jQuery.cookie(cookieName)){
					window.location.href = noCookiesUrl;
				}
			});
		},
		exitIntentPopup: function(){
			var exitIntentShown = null;
			var exitIntentPopupModalClass = this.options.exitIntentPopupModalClass;
            var exitIntentPopup = {
                type: 'popup',
                responsive: true,
                innerScroll: false,
				clickableOverlay: true,
				backdrop: 'static',
				keyboard: false,
				modalClass: exitIntentPopupModalClass,
				closePopupModal: '.exitIntentPopup .action-close',
                title: $.mage.__('Exit Intent Popup'),
				closed: function(){
					if(!jQuery.cookie('exitIntentShown')){
					var now = new Date();
					now.setTime(now.getTime() + (1 *  60 * 60 * 1000));
					jQuery.cookie('exitIntentShown', true, {expires : now,SameSite : 'Strict',path    : '/',secure  : true});
					}else{
						var exitIntentShown = jQuery.cookie('exitIntentShown');
					}
                },
				buttons: []
            };
			var isMobile = false; 
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				 var isMobile = true; 
			}
			// Show the popup and overlay when the user tries to leave the page
			jQuery(document).on('mouseleave', function(event) {
				event.stopImmediatePropagation();
				if (event.clientY < 0) {
						var exitIntentShown = null;
						if(jQuery.cookie('exitIntentShown')){
							var exitIntentShown = jQuery.cookie('exitIntentShown');
						}
						
						if((!exitIntentShown)&&(!isMobile)&&(!$("body").hasClass("_has-modal")))
						{
							var exitIntent = modal(exitIntentPopup, $('#exit-intent-products'));
							$("#exit-intent-products").modal("openModal");
						}
				}
				});
		},
		_footermenu: function () {
			var responsive_breakpoint = this.options.breakPoint;
			jQuery('.footer-links .menuTitle').click(function(){
				if (jQuery(window).width() < responsive_breakpoint ) {
					jQuery(this).next('.footer-links ul').slideToggle(300);
					jQuery(this).toggleClass("active");
				}
			});
			
		},
		_footermobilemenu: function () {
			var responsive_breakpoint = this.options.breakPoint;
			var resizeDelay = 200;
		   var doResize = true;
		   var resizer = function () {
			  if (doResize) {
				//your code that needs to be executed goes here
				if (jQuery(window).width() > responsive_breakpoint ) {
					jQuery(".footer-links ul").show();
				}else { 
					jQuery(".footer-links ul").hide(); 
					if(jQuery(".footer-links ul").prev().hasClass('active')){
					jQuery(".footer-links ul").prev().removeClass('active');
					}
				}
				doResize = false;
			  }
			};
			var resizerInterval = setInterval(resizer, resizeDelay);
			resizer();
			jQuery(window).resize(function() {
			  doResize = true;
			});
			
		},
		headerSection:function(){
			
			var header_style = this.options.header_style;

			//Ensure dropdown is hidden and inactive on page load
			jQuery(document).ready(function () {
				jQuery('.setting-links .setting-dropdown').hide().removeClass('active');
			});

			if ((header_style != 'header-5') || (header_style != 'header-6')) {
				jQuery(document).click(function (event) {
					var container = $(".setting-links");
					if (!container.is(event.target) && !container.has(event.target).length) {
						if (jQuery('.setting-links .setting-dropdown').is(":visible")) {
							// Close only, don't toggle
							jQuery('.setting-links .setting-dropdown').hide();
							jQuery('.setting-links .setting-dropdown').removeClass('active');
						}
					}
				});
			}

			jQuery('.setting-links .setting-icon').on('click', function (e) {
				e.stopPropagation(); // prevent immediate document click
				jQuery('.setting-links .setting-dropdown').toggle();

				if ((header_style != 'header-5') || (header_style != 'header-6')) {
					jQuery('.setting-links .setting-dropdown').toggleClass('active');
				}
			});


			
			/* Search Drawer */
			jQuery('.search-toggle .topSearch').on('click', function(e){
				jQuery('.search-drawer').toggleClass("search-drawer-open");
					setTimeout(function() { jQuery('input[name="q"]').focus() }, 1000);
					e.preventDefault();
					var add_html =  '<div class="mask-overlay">';
					jQuery(add_html).hide().appendTo('body').fadeIn('200');
					jQuery('.mask-overlay, .search-drawer .close').on('click',function(){
						jQuery('.mask-overlay').remove();
						jQuery('.search-drawer').removeClass("search-drawer-open");
					});
				});
			jQuery('.search-drawer .close').on('click', function(e){
				jQuery('.search-drawer').toggleClass("search-drawer-open");
			});	
			
		},
		wishlistcounter: function(){
			if($('.wishlist-counter').length){
			$('.wishlist-counter').hide(); 
			setTimeout(function() {
				var wishlistCounterStr = null;
				wishlistCounterStr = $('.wishlist-counter').html(); 
				if(wishlistCounterStr){
				var wishlistCounter = wishlistCounterStr.replace ( /[^\d.]/g, '' );
				$('.wishlist-counter').html(wishlistCounter); 
				$('.wishlist-counter').show(); 	
				}
				
			}, 5000);	
			}
		},
		scrollTop: function() {
			var scroll_top_position = this.options.scroll_top_position;
			 $('.scroll-top').css(scroll_top_position, "20px");
			//Check to see if the window is top if not then display button
			$(window).scroll(function(){
				if ($(this).scrollTop() > 100) {
					$('.scroll-top').fadeIn();
				} else {
					$('.scroll-top').fadeOut();
				}
			});
			// Click event to scroll to top
			$('.scroll-top').click(function(){
				$('html, body').animate({scrollTop : 0},800);
				return false;
			});
		},
		macClassAdd: function() {
			if (navigator.userAgent.indexOf('Mac OS X') != -1) {
			  $("body").addClass("mac-os");
			} else {
			  $("body").addClass("pc");
			}
		},
		stickyMenu: function() {
			var sticky_style = this.options.sticky_menu;
			var previousScroll = $(window).scrollTop(); 

			$(window).scroll(function () {
				var currentScroll = $(window).scrollTop();
				if ($(window).scrollTop() > 100 ) {
					var header_height = $('.page-header').height();
					var top_bar_height  = parseInt($('.top-header').height());
					if(top_bar_height > 0)
					{
					var top_bar_padding  = parseInt(20);
					var top_bar_height = 0 - (top_bar_height+top_bar_padding);	
					}else{
						var top_bar_height = 0 - (top_bar_height);	
					}
					if(currentScroll > previousScroll) {
						if(sticky_style == 1){
							$('.page-header').removeClass('sticky-header in-down-anm');
						}else{
							$('.page-header').addClass('sticky-header in-down-anm');
							$('.page-header').css("height",header_height);
						}
					} else {
						if(sticky_style == 1){
							$('.page-header').addClass('sticky-header in-down-anm');
							$('.page-header').css("height",header_height);
						}
					}
				} else if ($(window).scrollTop() == 0) {
					$('.page-header').removeClass('sticky-header in-down-anm');
					$('.page-header').css("height",'auto');
					if(($('#notification_slider').length ) && ($("#notification_slider").is(":visible")))        // use this if you are using id to check
						{
							var notification_height = $('#bxslider').height();
							$('.sticky-product').css("top",notification_height);
						}else{
							$('.page-header').css("top", "0");
						}

					
				}
				previousScroll = currentScroll;
			});
		},
		stickyMenuResize: function() {
			var sticky_menu = this.options.sticky_menu;
			jQuery('.page-header').css("height",'auto');
			jQuery('.page-header').css("top", "0");
			var header_height = jQuery('.page-header').height();
			if(sticky_menu != 0){
				var previousScroll = jQuery(window).scrollTop(); 
				var currentScroll = jQuery(window).scrollTop();
				if (jQuery(window).scrollTop() > 100 ) {
					
					if(currentScroll > previousScroll) {
						if(sticky_menu == 1){
							jQuery('.page-header').removeClass('sticky-header in-down-anm');
						}else{
							jQuery('.page-header').addClass('sticky-header in-down-anm');
							jQuery('.page-header').css("height",header_height);
						}
					} else {
						if(sticky_menu == 1){
							jQuery('.page-header').addClass('sticky-header in-down-anm');
							jQuery('.page-header').css("height",header_height);
						}
					}
				} else if (jQuery(window).scrollTop() == 0) {
					jQuery('.page-header').removeClass('sticky-header in-down-anm');
					jQuery('.page-header').css("height",'auto');
					jQuery('.page-header').css("top", "0");
					
				}
				previousScroll = currentScroll;
				}else if(sticky_menu == 0){
					jQuery('.page-header').addClass('no-sticky');
				}
		},
		topoMenuRtl: function() {
			var theme_option_rtl = this.options.theme_option_rtl;
			var topmenu = $("ul.cwsMenu").parent();
			$(topmenu).toggleClass('ltr rtl');
		}

	});

	return $.Magebees.pocotheme;

});

