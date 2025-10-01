define([
    "jquery",
    "mage/apply/main",
     "jquery-ui-modules/core","jquery-ui-modules/widget",
	"alignProductSection",
	"magebees/ajaxcomparewishlist",
	"magebees.countdown"
], function ($,mage,ui,alignProducts,ajaxcomparewishlist) {
    "use strict";
    //creating jquery widget
    $.widget('magebees.ajaxloadnext',{
        _init:function () {
            
        },
        
        _create: function () {
			var self = this;
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
                            var res = $(data).find('#category-product-id-'+default_cat_id+' > .products-grid > div.product-items > div.product-item');
                            $("#category-product-id-"+default_cat_id+" > .products-grid > div.product-items").append(res);
							$("#category-product-id-"+default_cat_id).trigger('contentUpdated');
							$(mage.apply);
							$("#category-product-id-"+default_cat_id).alignProducts({
							container : ".products-grid",
							item : '.item',
							elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.buynow-btn,.swatches-listing,.product-item-details,.product-brand,.p_ctgry'
							});
							$("#category-product-id-"+default_cat_id).ajaxcomparewishlist({popupTTL:10,showLoader:true});	self.dealTimer();							
							}
                    });
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
    return $.magebees.ajaxloadnext;
});
