/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
*/
define([
    'jquery',
	"jquery-ui-modules/core",
	"jquery-ui-modules/widget",
	"alignProductSection",
	"blog"
], function (jQuery,core,widget,alignProducts,blog) {
    'use strict';
	jQuery.widget('magebees.ajaxloading', {
		 _create: function(config, element) {
			
			var widget = this;
			var options = this.getOptions();
			
			var url = this.options.url;
			var self = this;
			var randon_number = this.options.randon_number;
			var res_div = this.options.res_div;
			var type = self.options.type;
			var buttonClick = false;
				
				if(type=='Magebees\\CategoryImage\\Block\\Widget\\CategoryImagewidget'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.categoryThumbanilList();
					}
				}else if(type=='Magebees\\Productlisting\\Block\\Widget\\ProductlistingWidget'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.productListing();
					}
				}else if(type=='Magebees\\Blog\\Block\\Widget\\LatestPosts'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.blogList();
					}
				}else if(type=='Magebees\\Imagegallery\\Block\\Widget\\Gallery'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.galleryList();
					}
				}else if(type=='Magebees\\PocoBase\\Block\\Widget\\NewsLetter'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.newsLetter();
					}
				}else if(type=='Magebees\\PocoBase\\Block\\Widget\\Content'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.contentLoad();
					}
				}else if(type=='Magebees\\Advertisementblock\\Block\\Widget\\Advertisementwidget'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.advertisementBlock();
					}
				}else if(type=='Magebees\\TodayDealProducts\\Block\\Widget\\DealProductsWidget'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.todayDealBlock();
					}
				}
			jQuery(window).scroll(function () {
				var type = self.options.type;
				
				if(type=='Magebees\\CategoryImage\\Block\\Widget\\CategoryImagewidget'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.categoryThumbanilList();
					}
				}else if(type=='Magebees\\Productlisting\\Block\\Widget\\ProductlistingWidget'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.productListing();
					}
				}else if(type=='Magebees\\Blog\\Block\\Widget\\LatestPosts'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.blogList();
					}
				}else if(type=='Magebees\\Imagegallery\\Block\\Widget\\Gallery'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.galleryList();
					}
				}else if(type=='Magebees\\PocoBase\\Block\\Widget\\NewsLetter'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.newsLetter();
					}
				}else if(type=='Magebees\\PocoBase\\Block\\Widget\\Content'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.contentLoad();
					}
				}else if(type=='Magebees\\Advertisementblock\\Block\\Widget\\Advertisementwidget'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.advertisementBlock();
					}
				}else if(type=='Magebees\\TodayDealProducts\\Block\\Widget\\DealProductsWidget'){
					
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.todayDealBlock();
					}
				}
				
				
				
			});
			
			
			jQuery('body').find('.tab-title').on('click',function(){
				
				if(type=='Magebees\\Productlisting\\Block\\Widget\\ProductlistingWidget'){
					if(!jQuery(res_div).hasClass('ajax_send'))
					{
					self.productListingTab();
					}
					}
			});
			
		},
		getOptions: function(){
			return this.options; 
		},
		categoryThumbanilList: function() {
			
			
			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'wd_enable':this.options.wd_enable ,'wd_categories':this.options.wd_categories,'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing ,'wd_show_heading':this.options.wd_show_heading,'wd_heading':this.options.wd_heading,'wd_heading_position':this.options.wd_heading_position,'wd_heading_logo':this.options.wd_heading_logo ,'wd_short_desc':this.options.wd_short_desc,'wd_shopnow':this.options.wd_shopnow,'store_id':this.options.store_id,'wd_show_viewall':this.options.wd_show_viewall,'wd_viewall_txt':this.options.wd_viewall_txt,'wd_viewall_url':this.options.wd_viewall_url,'wd_no_of_items':this.options.wd_no_of_items,'wd_bgimage':this.options.wd_bgimage,'wd_description':this.options.wd_description,'wd_slider':this.options.wd_slider,'wd_autoscroll':this.options.wd_autoscroll,'wd_navarrow':this.options.wd_navarrow,'wd_pagination':this.options.wd_pagination,'wd_pagination_type':this.options.wd_pagination_type,'wd_infinite_loop':this.options.wd_infinite_loop,'wd_scrollbar':this.options.wd_scrollbar,'wd_grap_cursor':this.options.wd_grap_cursor,'wd_slide_auto_height':this.options.wd_slide_auto_height,'wd_centered':this.options.wd_centered,'wd_items_per_slide':this.options.wd_items_per_slide,'wd_auto_play_delaytime':this.options.wd_auto_play_delaytime,'wd_autoplayoff':this.options.wd_autoplayoff,'wd_items_per_row':this.options.wd_items_per_row,'wd_bgcolor':this.options.wd_bgcolor,'wd_shopnow_text':this.options.wd_shopnow_text,'template':this.options.template},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
					});
			}
		},
		productListing: function() {
			
			
			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'listing_id':this.options.listing_id,'wd_load_ajax':this.options.wd_load_ajax,'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing,
						'wd_bgcolor':this.options.wd_bgcolor,'wd_bgimage':this.options.wd_bgimage,'wd_view_more_position':this.options.wd_view_more_position},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
						jQuery(res_div).trigger('contentUpdated'); /*trigger for init ajax scroll js*/
						jQuery(res_div).alignProducts({
						container : ".products-grid",
						item : '.item',
						elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.buynow-btn,.swatches-listing,.product-item-details,.product-brand,.p_ctgry'
						});
					});
			}
		},
		productListingTab: function() {
			
			
			var res_div = this.options.res_div;
			
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'listing_id':this.options.listing_id,'wd_load_ajax':this.options.wd_load_ajax,'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing,
						'wd_bgcolor':this.options.wd_bgcolor,'wd_bgimage':this.options.wd_bgimage,'wd_view_more_position':this.options.wd_view_more_position},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
						jQuery(res_div).trigger('contentUpdated'); /*trigger for init ajax scroll js*/
						jQuery(res_div).alignProducts({
						container : ".products-grid",
						item : '.item',
						elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.buynow-btn,.swatches-listing,.product-item-details,.product-brand,.p_ctgry'
						});
						
					});
			
		},
		blogList: function() {
			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'template': this.options.template,'wd_spacing': this.options.wd_spacing,'wd_bottom_spacing': this.options.wd_bottom_spacing,'wd_custom_link': this.options.wd_custom_link,'wd_custom_title': this.options.wd_custom_title,'wd_custom_link_title': this.options.wd_custom_link_title,'wd_custom_link_url': this.options.wd_custom_link_url,'wd_show_heading': this.options.wd_show_heading,'wd_heading': this.options.wd_heading,'wd_show_description': this.options.wd_show_description,'wd_description': this.options.wd_description,'wd_post_show_feature_image': this.options.wd_post_show_feature_image,'feature_image_height': this.options.feature_image_height,'feature_image_width': this.options.feature_image_width,
						'resize_type': this.options.resize_type,'wd_post_type': this.options.wd_post_type,'wd_post_limit': this.options.wd_post_limit,'wd_slider': this.options.wd_slider,'wd_items_per_slide': this.options.wd_items_per_slide,'wd_autoscroll': this.options.wd_autoscroll,'wd_auto_play_delaytime': this.options.wd_auto_play_delaytime,'wd_autoplayoff': this.options.wd_autoplayoff,'wd_slide_auto_height': this.options.wd_slide_auto_height,'wd_navarrow': this.options.wd_navarrow,
						'wd_pagination': this.options.wd_pagination,'wd_infinite_loop': this.options.wd_infinite_loop,'wd_scrollbar': this.options.wd_scrollbar,'wd_grap_cursor': this.options.wd_grap_cursor,'wd_sort_by': this.options.wd_sort_by,'wd_category': this.options.wd_category,'wd_comment_count': this.options.wd_comment_count,'wd_tags': this.options.wd_tags,'wd_author': this.options.wd_author,'wd_add_this': this.options.wd_add_this,'wd_post_readmore': this.options.wd_post_readmore,'wd_post_show_view_all': this.options.wd_post_show_view_all,'store_id': this.options.store_id,'wd_bgimage': this.options.wd_bgimage,'wd_bgcolor': this.options.wd_bgcolor,'wd_items_per_row': this.options.wd_items_per_row,'wd_post_content': this.options.wd_post_content,'wd_post_view_all_text': this.options.wd_post_view_all_text,'wd_post_show_view_all_url': this.options.wd_post_show_view_all_url,'wd_post_ids': this.options.wd_post_ids},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
						jQuery(res_div).blog();
					});
			}
		},
		galleryList: function() {

			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
				
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing,'wd_show_heading':this.options.wd_show_heading,'wd_heading':this.options.wd_heading,'wd_show_description':this.options.wd_show_description,'wd_description_position':this.options.wd_description_position,'wd_description':this.options.wd_description,'no_of_image':this.options.no_of_image,'wd_slider':this.options.wd_slider,'wd_items_per_slide':this.options.wd_items_per_slide,'wd_items_per_row':this.options.wd_items_per_row,'wd_autoscroll':this.options.wd_autoscroll,'wd_auto_play_delaytime':this.options.wd_auto_play_delaytime,'wd_autoplayoff':this.options.wd_autoplayoff,'wd_slide_auto_height':this.options.wd_slide_auto_height,'wd_navarrow':this.options.wd_navarrow,	'wd_pagination':this.options.wd_pagination,'wd_pagination_type':this.options.wd_pagination_type,		'wd_infinite_loop':this.options.wd_infinite_loop,'wd_scrollbar':this.options.wd_scrollbar,'wd_grap_cursor':this.options.wd_grap_cursor,'template':this.options.template,'store_id':this.options.store_id},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
					});
					
			}
		},
		newsLetter: function() {

			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'wd_enable':this.options.wd_enable,'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing,'wd_bgcolor':this.options.wd_bgcolor,'wd_bgimage':this.options.wd_bgimage,'wd_show_heading':this.options.wd_show_heading,'wd_newsletter_title':this.options.wd_newsletter_title,'wd_heading_logo':this.options.wd_heading_logo,'wd_newsletter_text_placeholder':this.options.wd_newsletter_text_placeholder,'wd_newsletter_text':this.options.wd_newsletter_text,'wd_newsletter_button_text':this.options.wd_newsletter_button_text,'template':this.options.template},
					}).done(function (data) {
						
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
						jQuery(res_div).trigger('contentUpdated');
					});
			}
		},
		contentLoad: function() {
			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
			
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing,'wd_bgcolor':this.options.wd_bgcolor,'wd_content_position':this.options.wd_content_position,'wd_content_type':this.options.wd_content_type,'wd_block':this.options.wd_block,'wd_title':this.options.wd_title,'wd_description':this.options.wd_description,'wd_view_more':this.options.wd_view_more,'wd_view_more_text':this.options.wd_view_more_text,'wd_view_more_link':this.options.wd_view_more_link,'wd_additional_content':this.options.wd_additional_content,'wd_additional_content_type':this.options.wd_additional_content_type,'wd_additional_block':this.options.wd_additional_block,'wd_image_section':this.options.wd_image_section,'wd_video_on_text':this.options.wd_video_on_text,'wd_video_on_popup':this.options.wd_video_on_popup,'wd_video_url':this.options.wd_video_url,'store_id':this.options.store_id,'template':this.options.template},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
						jQuery(res_div).trigger('contentUpdated');
					});
			}
		},
		advertisementBlock: function() {
			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
			
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing,'wd_custom_class':this.options.wd_custom_class,'wd_advertisement':this.options.wd_advertisement,'wd_style':this.options.wd_style,'store_id':this.options.store_id,'enabled':this.options.enabled,'title':this.options.title,'subtitle':this.options.subtitle,'advertisement':this.options.advertisement,'style':this.options.style,'template':this.options.template},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
						jQuery(res_div).trigger('contentUpdated');
					});
			}
		},
		todayDealBlock: function() {
			var res_div = this.options.res_div;
			var elementTop = jQuery(res_div).offset().top;
			var elementBottom = elementTop + jQuery(res_div).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			var isVisibleElement = elementBottom > viewportTop && elementTop < viewportBottom;
			if(isVisibleElement){
			
				var url = this.options.url;
				var res_div = this.options.res_div;
				var loading_div = this.options.loading_div;
				var loaderUrl = this.options.loaderUrl;
				jQuery(res_div).addClass('ajax_send');
				jQuery(loading_div).prepend(jQuery('<img>',{id:'contentLoad',src:loaderUrl,width:90,height:90,alt:'Loading...'}));
				jQuery(loading_div).show();
				jQuery.ajax({
						context: res_div,
						url: url,
						type: "POST",
						data: {'wd_spacing':this.options.wd_spacing,'wd_bottom_spacing':this.options.wd_bottom_spacing,'wd_bgcolor':this.options.wd_bgcolor,'wd_deal':this.options.wd_deal,'wd_show_viewall':this.options.wd_show_viewall,'wd_viewall_txt':this.options.wd_viewall_txt,'wd_viewall_url':this.options.wd_viewall_url,'template':this.options.template},
					}).done(function (data) {
						jQuery(loading_div).hide();
						jQuery(loading_div).empty();
						if(jQuery(res_div).hasClass('ajx_loader'))
						{
							 jQuery(res_div).removeClass('ajx_loader');
						}
						jQuery(res_div).html(data.result);
						jQuery(res_div).trigger('contentUpdated');
						jQuery(res_div).alignProducts({
						container : ".products-grid",
						item : '.item',
						elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.buynow-btn,.swatches-listing,.product-item-details,.product-brand,.p_ctgry'
						});
					});
			}
		}
	});
	return jQuery.magebees.ajaxloading;

});