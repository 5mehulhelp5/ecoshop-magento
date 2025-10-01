define([
    "jquery",
    "mage/apply/main",
    "jquery-ui-modules/core",
	"jquery-ui-modules/widget"
], function ($,mage) {
    "use strict";
    //creating jquery widget
    $.widget('magebees.ajaxloadnext',{
        _init:function () {
            
        },
        
        _create: function () {
            var default_cat_id = this.options.default_cat_id;
            var donetext = this.options.donetext;
            var id = "#category-product-id-"+default_cat_id;
        
            //for load next page
            $(id).on("click", "#load-more", function () {
                var pagerelement = $(id).find('.pager > .pages > ul.pages-items > li.pages-item-next > a');
                if (pagerelement.length > 0) {
                    var url = pagerelement.attr('href');
                    $('#loading-text-'+default_cat_id).show();
                    $('#load-more-text-'+default_cat_id).hide();
                    $.ajax({
                        url:url,
                            cache:true,
                            showLoader:true,
                            //dataType: "html",
                        success: function (data) {
                            $('#loading-text-'+default_cat_id).hide();
                            $('#load-more-text-'+default_cat_id).show();
                            var newcur = $(data).find("#category-products-pager-"+default_cat_id);
                            var nextavail = newcur.find('.pager > .pages > ul.pages-items > li.pages-item-next > a').length;
                            if (nextavail <= 0) {
                                $('#load-more-text-'+default_cat_id).replaceWith(donetext);
                            }
                            $("#category-products-pager-"+default_cat_id).replaceWith(newcur);
                            $('.toolbar').hide();
                            var res = $(data).find('#category-product-id-'+default_cat_id+' > .products-grid > ol.product-items > li');
                            $("#category-product-id-"+default_cat_id+" > .products-grid > ol.product-items").append(res);
                            $(mage.apply);
                        }
                    });
                }
            });
        }
    });
    return $.magebees.ajaxloadnext;
});
