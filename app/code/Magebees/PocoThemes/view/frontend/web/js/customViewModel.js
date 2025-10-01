define(['uiComponent','ko','Magento_Customer/js/customer-data','Magento_Catalog/js/price-utils'], function (Component,ko,customerData,priceUtils) {
    "use strict";
	var cartObservable = customerData.get('cart');
	var subtotalAmount;
	//var maxPrice = 100;
	var maxPrice = maxpriceShipping ;
	var percentage;
	var remainingAmount;
	var currentCurrencyPriceFormat = currentpriceFormat;
    return Component.extend({
		displaySubtotal: ko.observable(true),
		//maxprice: '$' + maxPrice.toFixed(2),
		maxprice: maxPrice.toFixed(2),
        initialize: function () {
            this._super();
			this.message = ko.observable(cartObservable().subtotalAmount);
			this.cart = customerData.get('cart');
          //  alert("initialized the custom component.");
			//this.maxprice = getFormattedPrice(maxPrice.toFixed(2));
			this.maxprice = maxPrice.toFixed(2);
			cartObservable.subscribe((function(newCart) {
                    // Update message when cart section data changes
                    this.message(newCart.subtotalAmount);
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
			  subtotalAmount = cartObservable().subtotalAmount;
			  remainingAmount = maxPrice - subtotalAmount;
			  return priceUtils.formatPrice(remainingAmount, currentCurrencyPriceFormat);
		 },
		 getpercentage: function () {
              subtotalAmount = customerData.get('cart')().subtotalAmount;
              if (subtotalAmount >= maxPrice) {
                   subtotalAmount = maxPrice;
             }
              percentage = ((subtotalAmount * 100) / maxPrice);
                      return percentage;
              },
			  getFormattedPrice: function (price) {
				return priceUtils.formatPrice(price, currentCurrencyPriceFormat);
            }
    });
});