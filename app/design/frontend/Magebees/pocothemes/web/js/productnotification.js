/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
*/
define([
    "jquery",
    "jquery-ui-modules/core",
	"jquery-ui-modules/widget",
	"jquery/jquery.cookie"
], function (jQuery,core,widget) {
    'use strict';
	jQuery.widget('Magebees.productnotification', {
		 _create: function() {
			var salesnotification_interval_time = this.options.salesnotification_interval_time;
			var salesnotification_display_device = this.options.salesnotification_display_device;
			
			var salesNotificationProducts = Object.values(this.options.sales_notification_products);
			var salesNotificationLocations = Object.values(this.options.sales_notification_locations);
			
			var sales_notification_cookie = this.getCookie("sales_notification_cookie");
			if((document.querySelector(".sales_ntf"))&&((salesNotificationProducts.length)>0)){
			if (sales_notification_cookie !== 'close_notification') {
				if(salesnotification_display_device == "both"){
				// Call the showSalesNotification function every 10 seconds
				setInterval(jQuery.proxy(this.showSalesNotification,this), salesnotification_interval_time);
				}else{
					if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					  if(salesnotification_display_device == "only_mobile"){
						// Call the showSalesNotification function every 10 seconds
						setInterval(jQuery.proxy(this.showSalesNotification,this), salesnotification_interval_time);
					  }
					}
					else{
					  if(salesnotification_display_device == "only_desktop"){
						// Call the showSalesNotification function every 10 seconds
						setInterval(jQuery.proxy(this.showSalesNotification,this), salesnotification_interval_time);
					  }
					}
				}
			}
			
		}
		},
		showSalesNotification: function () {
			// Select the notification elements
			var self = this;
		  var notification = document.querySelector('.sales_ntf');
		  var salesNumber = document.querySelector('.sls_no');
			var productName = document.querySelector('.sales-ntf-product-name');
			
			var productImage = document.querySelector('.sales-ntf-product-image');
			var productLink = document.querySelector('.sales-ntf-product-link');
			
			var salesNotificationProducts = Object.values(this.options.sales_notification_products);
			var salesNotificationLocations = Object.values(this.options.sales_notification_locations);
			var salesnotification_enable_locations = this.options.salesnotification_enable_locations;
		  // Generate a random product object, number of sales, and location
		    var product = salesNotificationProducts[this.getRandomInt(0, salesNotificationProducts.length - 1)];
		  var sales = this.getRandomInt(1, 60);
		  
		  if((salesnotification_enable_locations=="1")&&((salesNotificationLocations.length)>0)){
			var location = document.querySelector('.location');
			var loc = salesNotificationLocations[this.getRandomInt(0, salesNotificationLocations.length - 1)];  
		  }
		
		  var sales_notification_cookie = this.getCookie("sales_notification_cookie");
		  if (sales_notification_cookie === 'close_notification') {
			notification.classList.add('hide');
			return;
		  }
			var close_sales_notification = document.querySelector('.cls_ntf');
		  close_sales_notification.addEventListener('click', function(event) {
			
			//this.setCookie("sales_notification_cookie","close_notification");
			jQuery.proxy(self.setCookie("sales_notification_cookie","close_notification",1),self);
			notification.classList.add('hide');
		  });
  
		  // Update the notification message
		  salesNumber.innerHTML = sales;
		  productName.querySelector('.sales-ntf-product-link').innerHTML = product.name;
		  productName.querySelector('.sales-ntf-product-link').href = product.url;
		  productName.querySelector('.sales-ntf-product-link').setAttribute('aria-label', product.name);
		  if((salesnotification_enable_locations=="1")&&((salesNotificationLocations.length)>0)){
			jQuery('.sales_ntf_from').show();
		  location.innerHTML = loc.name;
		  }
		  productImage.src = product.image;
		  productLink.href = product.url;
	  
		  // Show the notification with slide in effect
		  notification.classList.add('show');
		  notification.classList.remove('hide');
	  
	  // Hide the notification with slide out effect after 5 seconds
	  setTimeout(function() {
		notification.classList.add('hide');
		notification.classList.remove('show');
	  }, 5000);
},

		getRandomInt: function (min, max) {
			return Math.floor(Math.random() * (max - min + 1)) + min;
		},
		
		setCookie: function (cname, cvalue, exdays) {
			var d = new Date();
		    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		    var expires = "expires="+d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/;SameSite=Strict";
		},
		getCookie: function (cname) {
			
			
			if(jQuery.cookie("sales_notification_cookie") === null ) {
				return "";
			}
				 
				var name = cname + "=";
				
			   var ca = document.cookie.split(';');
			  for(var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') {
				  c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					console.log(c);
				  return c.substring(name.length, c.length);
				}
			  }
			  return "";
		}
	});

	return jQuery.Magebees.productnotification;

});

