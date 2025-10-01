define([
    "jquery",
    "jquery-ui-modules/core","jquery-ui-modules/widget",
	"alignProductSection",
	"magebees/ajaxcomparewishlist",
	"magebees.countdown"
], function ($,ui,alignProducts,ajaxcomparewishlist) {
    "use strict";
    //creating jquery widget
    $.widget('magebees.ajaxloadcategory',{
        _init:function () {
            $('.mage-ajaxpro-page .toolbar').hide();
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
					
					$('.mage-ajaxpro-page .toolbar').hide();					
					$("#category-product-id-"+default_cat).trigger('contentUpdated');
					$("#category-product-id-"+default_cat).alignProducts({
					container : ".products-grid",
					item : '.item',
					elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.buynow-btn,.swatches-listing,.product-item-details,.product-brand,.p_ctgry'
					});
					$("#category-product-id-"+default_cat).ajaxcomparewishlist({popupTTL:10,showLoader:true});
					}

            });
        },
        dealTimer: function(){
			 $('.magebees-timecountdown-container').each(function () {
				 
                if($(this).data('timerformat')== 1){
                    //based on from and to date timer
                    var totime = $(this).data('totime');
                    if (totime != '') {
                        //var jsnowTime = Math.round((new Date()).getTime()/1000);
                        var fromtime = $(this).data('currenttime');
                        var remainTime = totime - fromtime ;
                        if (remainTime > 0) {
							$(this).children('#category-timer-countbox').countdown({
								until: remainTime,
								isRTL: 0,
								labels: ['YR', 'MTH', 'WK', 'DY', 'HRS', 'MIN', 'SEC'],
								significant:0});
						}
                    }
                }else{
                    //24 hours timer format
                    var totime = $(this).data('totime');
                    if (totime != '') {
                        //var jsnowTime = Math.round((new Date()).getTime()/1000);
                        var remainTime = 0;
                        var fromtime = $(this).data('fromtime');
                        var currenttime = $(this).data('currenttime');
                        var totaldays = (currenttime - fromtime);
                        var one_day_time = 60 * 60 * 24;
                            
                        // calculate (and subtract) whole days
                        var days = Math.floor(totaldays / one_day_time);
                        totaldays -= days * one_day_time;

                        var last_day_diff = totime - currenttime;
                        if(last_day_diff < one_day_time){
                            remainTime = totime - currenttime;
                        }else{
                            days += 1;
                            var nextdaytime = (fromtime + (one_day_time * days));
                            remainTime = nextdaytime - currenttime;
                        }
                        if (remainTime > 0) {
							$(this).children('#category-timer-countbox').countdown({
								until: remainTime,
								isRTL: 0,
								labels: ['YR', 'MTH', 'WK', 'DY', 'HRS', 'MIN', 'SEC'],
								significant:0});
							
						}
                    }
                }
            });	
			
		},    
      
    });
    return $.magebees.ajaxloadcategory;
});
