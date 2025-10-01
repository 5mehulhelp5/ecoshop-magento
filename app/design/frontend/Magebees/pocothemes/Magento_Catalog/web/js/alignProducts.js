
define([
    'jquery',
    "jquery-ui-modules/core","jquery-ui-modules/widget",
	'Magento_Swatches/js/swatch-renderer'
    
], function ($,ui,swatch) {
    'use strict';

    $.widget('Magebees.alignProducts', {
        /**
         * Bind events to the appropriate handlers.
         * @private
         */
		_init: function () {
		var containerElement = this.options.container;
			var itemElement = this.options.item;
			var self = this;
			self.alignProductGridActions(containerElement,itemElement);
			
			$(document).on('swatch.initialized', function() {
				self.alignProductGridActions(containerElement,itemElement);       
            });
				
			
            var resizeTimer;
			$(window).resize(function (e) {
				e.stopImmediatePropagation();
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function () {
					self.alignProductGridActions(containerElement,itemElement);
                }, 250);
			});
		},
		_create: function () {
			
        },
		alignProductGridActions: function(containerElement,itemElement) {
			var ElementList = this.options.elementlist;
			var tallestHeight = {};
			var gridRows = []; // This will store an array per row
            var tempRow = [];
           	var productGridElements = $(containerElement).find(itemElement);
			 
            productGridElements.each(function (index) {
                if ($(this).css('clear') != 'none' && index != 0) {
                    gridRows.push(tempRow); // Add the previous set of rows to the main array
                    tempRow = []; // Reset the array since we're on a new row
                }
                tempRow.push(this);

                if (productGridElements.length == index + 1) {
                    gridRows.push(tempRow);
                }
            });



            $.each(gridRows, function () {
                var self = this;
				
				var ElementListArray = ElementList.split(","); 
					$.each( ElementListArray, function( key, value ) {
						
						tallestHeight[value] = 0;
						
					});
					
                
				$.each(this, function () {
                   var self = this; 
                    $.each( ElementListArray, function( key, value ) {
						
						
						$(self).find(value).css('min-height', '');
						var elementHeight = parseInt($(self).find(value).css('height'));
						if (elementHeight > tallestHeight[value]) {
                        tallestHeight[value] = elementHeight;
						}
						
						
						
						
					});
				});
                $.each(this, function () {
                   var self = this; 
				    $.each( ElementListArray, function( key, value ) {
						
						
						if(tallestHeight[value] > 0){
						var setElementHeight = tallestHeight[value];
						$(self).find(value).css('minHeight', setElementHeight);
						}
						
						
					});
					
				
                });
            });
		}
	});

    return $.Magebees.alignProducts;
});