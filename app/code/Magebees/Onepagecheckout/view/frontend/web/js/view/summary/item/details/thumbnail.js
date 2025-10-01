define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/item/details/thumbnail'
    ],
    function (ko, Component) {
		var imageData = window.checkoutConfig.imageData;
        return Component.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/summary/item/details/thumbnail'
            },
			imageData: imageData,
            showImage: ko.observable(window.checkoutConfig.product_image_enabled),
			getWidth: function(item) {
                imgwidth = ko.observable(window.checkoutConfig.product_image_width);
                if (imgwidth) {
                    return this.imageData[item.item_id]['width'] = imgwidth;
                }else{
					return this.imageData[item.item_id]['width'] = 150;
				}
            },
            getHeight: function(item) {
                imgheight = ko.observable(window.checkoutConfig.product_image_height);
                if (imgheight) {
                    return this.imageData[item.item_id]['height'] = imgheight;
                }else{
					return this.imageData[item.item_id]['height'] = 150;
				}
            },
        });
    }
);
