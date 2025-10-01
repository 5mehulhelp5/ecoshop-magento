define(['uiComponent','ko','Magento_Customer/js/customer-data','Magento_Catalog/js/price-utils'], function (Component,ko,customerData,priceUtils) {
    "use strict";
	var cartObservable = customerData.get('cart');
	var subtotalAmount;
	//var maxPrice = 100;
	var maxPrice = maxpriceShipping;
	var successmessageShipping = successmessage;
	var remainmessageShipping = remainmessage;
	
	var percentage;
	var remainingAmount;
	var currentCurrencyPriceFormat = currentpriceFormat;
    return Component.extend({
		displaySubtotal: ko.observable(true),
		//maxprice: '$' + maxPrice.toFixed(2),
		maxprice: maxPrice.toFixed(2),
        initialize: function () {
            this._super();
			subtotalAmount = cartObservable().subtotalAmount;
			remainingAmount = maxPrice - subtotalAmount;
			var remainingAmountPriceFormat = priceUtils.formatPrice(remainingAmount, currentCurrencyPriceFormat);
			var remainingMessage = remainmessageShipping.replace("%s",remainingAmountPriceFormat);
			this.message = ko.observable(cartObservable().subtotalAmount);
			
			var txt = document.createElement("textarea");
					txt.innerHTML = remainingMessage;
					//this.remainmessage = txt.value;
			this.message(txt.value);
var txt1 = document.createElement("textarea");
					txt1.innerHTML = successmessage;
					
					
					this.successmessage =  txt1.value;
					
			this.message(txt.value);
			
			
			
			this.remainmessage = remainmessageShipping;
			this.cart = customerData.get('cart');
			
			
			
          
			this.maxprice = maxPrice.toFixed(2);
			cartObservable.subscribe((function(newCart) {
                   
					subtotalAmount = cartObservable().subtotalAmount;
					remainingAmount = maxPrice - subtotalAmount;
					
					var remainingAmountPriceFormat = priceUtils.formatPrice(remainingAmount, currentCurrencyPriceFormat);
					
					this.remainmessage = remainmessageShipping; 
					var remainingMessage = this.remainmessage.replace("%s",remainingAmountPriceFormat);
				
					var txt = document.createElement("textarea");
					txt.innerHTML = remainingMessage;
					
					this.message(txt.value);
					
                }).bind(this));
				
        },
		getTotalCartItems: function () {
                
				return cartObservable().summary_count;
         },
		 isFreeShipping: function(){
			 
			 
			subtotalAmount = cartObservable().subtotalAmount;
			 if(subtotalAmount >= maxPrice){
				 return true;
			 }
		 },
		 remainingAmount: function(){
				
				var subtotal_incl_tax = jQuery.parseHTML(cartObservable().subtotal_incl_tax); 
				var subtotal_incl_tax = jQuery(subtotal_incl_tax).text();
				subtotalAmount = cartObservable().subtotalAmount;
			  remainingAmount = maxPrice - subtotalAmount;
			  var remainingAmountPriceFormat = priceUtils.formatPrice(remainingAmount, currentCurrencyPriceFormat);
			 var remainingMessage = this.remainmessage.replace("%s",remainingAmountPriceFormat);
			  
			
			 return remainingAmountPriceFormat;
			  
			  
		 },
		 getpercentage: function () {
              subtotalAmount = customerData.get('cart')().subtotalAmount;
              if (subtotalAmount >= maxPrice) {
                   subtotalAmount = maxPrice;
             }
			 remainingAmount = maxPrice - subtotalAmount;
			  var remainingAmountPriceFormat = priceUtils.formatPrice(remainingAmount, currentCurrencyPriceFormat);
			
			 var remainingMessage = this.remainmessage.replace("%s",remainingAmountPriceFormat);
			this.remainmessage = remainingMessage;
			if(maxPrice>0){
				percentage = ((subtotalAmount * 100) / maxPrice);
			}else{
				percentage = 100;
			}
			  
			  
                      return percentage;
              },
			  getRoundpercentage: function () {
              subtotalAmount = customerData.get('cart')().subtotalAmount;
              if (subtotalAmount >= maxPrice) {
                   subtotalAmount = maxPrice;
             }
			 if(maxPrice > 0){
				percentage = ((subtotalAmount * 100) / maxPrice); 
			 }else{
				 return 100;
			 }
			  
					if(percentage >= 100){
						return 100;
					}else{
					return percentage.toPrecision(2);	
					}
					
                      
              },
			  getFormattedPrice: function (price) {
				return priceUtils.formatPrice(price, currentCurrencyPriceFormat);
            },
			getRemainMessage: function () {
				
				remainingAmount = maxPrice - subtotalAmount;
				var remainingAmountPriceFormat = priceUtils.formatPrice(remainingAmount, currentCurrencyPriceFormat);
				var remainingMessage = this.remainmessage.replace("%s",remainingAmountPriceFormat);
				var txt = document.createElement("textarea");
				txt.innerHTML = remainingMessage;
				return remainingMessage;
				
            },getSuccessMessage: function () {
				return this.successmessage;
            }
    });
});