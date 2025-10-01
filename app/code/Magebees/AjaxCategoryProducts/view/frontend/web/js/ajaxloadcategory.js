define([
    "jquery",
    "jquery-ui-modules/core",
	"jquery-ui-modules/widget"
], function ($) {
    "use strict";
    //creating jquery widget
    $.widget('magebees.ajaxloadcategory',{
        _init:function () {
            $('.toolbar').hide();
            $('body').addClass("page-products");
            var paramValue = 0;
            paramValue = this.options.default_cat_id;
            var id = '#'+paramValue;
            $(id).parent().addClass("cat-active");
            
        },
        
        _create: function () {
            var self = this;
            var default_cat = this.options.default_cat_id;
            $('#cat-filter-'+default_cat).on("click",'a', function () {
                var paramValue = $(this).attr('id');
                $('#cat-filter-'+default_cat).find('li.cat-active').removeClass("cat-active");
                $(this).parent().addClass("cat-active");
                
                self._filterCategory(paramValue);
            });
        },
        
        _filterCategory: function (paramValue) {
            var self = this;
            var default_cat = self.options.default_cat_id;
            
            var hash = window.location.hash;
            //for remove hash value from URL
            var loc = window.location.href,
            index = loc.indexOf('#');

            if (index > 0) {
              window.location = loc.substring(0, index);
            }
            var url = window.location.href;
            if (url.indexOf('cat_ids' + "=") >= 0) {
                var prefix = url.substring(0, url.indexOf('cat_ids'));
                var suffix = url.substring(url.indexOf('cat_ids'));
                suffix = suffix.substring(suffix.indexOf("=") + 1);
                suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
                url = prefix + 'cat_ids' + "=" + paramValue + suffix;
            } else {
                if (url.indexOf("?") < 0) {
                    url += "?" + 'cat_ids' + "=" + paramValue; } else {
                    url += "&" + 'cat_ids' + "=" + paramValue; }
            }
            var ajaxurl = url + hash;
           
            $.ajax({
                url:ajaxurl,
                    showLoader:true,
                    //dataType: "html",
                success: function (data) {
                    var newcur = $(data).find("#category-products-pager-"+default_cat);
                    var nextavail = newcur.find('.pager > .pages > ul.pages-items > li.pages-item-next > a').length;
                    var res = $(data).find('#category-product-id-'+default_cat);
                                        
                    $("#category-product-id-"+default_cat).replaceWith(res);
                    if (nextavail <= 0) {
                        $("#load-more-text-"+default_cat).replaceWith("No more Items");
                    }
                    
                    $('.toolbar').hide();
                    $("#category-product-id-"+default_cat).trigger('contentUpdated');
                   
                }

            });
        },
            
      
    });
    return $.magebees.ajaxloadcategory;
});
