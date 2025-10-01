/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'ko',
    'jquery-ui-modules/core',
	'jquery-ui-modules/widget',
    'mage/validation/validation'
    
], function ($,$t,modal,ko) {
    "use strict";

    $.widget('magebees_ajaxquickview.ajaxQuickView', {
    
        _create: function () {
            $("#loadingImage").insertBefore(".page-header");
        },

        productQuickView: function (actionUrl) {
            var self = this;
            $.ajax({
                url: actionUrl,
                dataType: 'json',
                success: function (result) {
                    document.getElementById('loadingImage').style['display']='none';
                    if (result.product_detail) {
                        $('.cartDetails').remove();
                        $('body').append('<div id="product_quickview_content"></div>');
                        // bind popup content in model
                        self.popupModal(result);
                        //$('#product_quickview_content').remove();
                    }
                }
            });
        },
        
        popupModal: function (result) {
            var self = this,
            modelClass = "quickViewDetails";
            if (result.product_detail) {
                modelClass = "quickViewDetails viewBox";
            }
                
            var options =
            {
                type: 'popup',
                modalClass: modelClass,
                responsive: true,
                innerScroll: true,
                title: false,
                buttons: false
            };

            if (result.product_detail) {
                var popup = modal(options, $('#product_quickview_content'));
                $('#product_quickview_content').html(result.product_detail);
                $('.quickview-product-name').html('<h3>'+result.title+'</h3>');
                $('#product_quickview_content').trigger('contentUpdated');
               // $('#product_quickview_content').modal('openModal');
                
                $('#product_quickview_content').modal('openModal').on('modalclosed', function() { 
                  /*insert code here*/ 
                  $('#product_quickview_content').remove();
                });
            }
        }
    });

    return $.magebees_ajaxquickview.ajaxQuickView;
});