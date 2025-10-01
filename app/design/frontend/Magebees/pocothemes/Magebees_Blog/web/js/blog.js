/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'domReady!'
], function (jQuery) {
    'use strict';
    
    jQuery.widget('Magebees.blog', {
		_init: function () {
				var self = this;
				
				self.limiter();
				
				self.showMoreLess();
				
				self.paginationAjax();
				
				//for align blog posts
				if (jQuery('.mb-post-gridview').length) {
					self.alignBlogPostsListActions();
				}
				var resizeTimer;
				jQuery(window).resize(function (e) {
					
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(function () {
						self.alignBlogPostsListActions();
					}, 250);
				});
		},
         _create: function() {
				var self = this;
				
				
				//for align blog posts
				if (jQuery('.mb-post-gridview').length) {
					self.alignBlogPostsListActions();
				}
				
				
				var resizeTimer;
				jQuery(window).resize(function (e) {
					
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(function () {
						self.alignBlogPostsListActions();
					}, 250);
				});
				
				
				 
				
		},
		limiter: function(){
			var self = this;
			jQuery(".mb-blog-toolbar > .pager > .limiter > .limiter-options > option").each(function(event) {
					
					var current_url = self.urldecode(this.value);
					this.value = current_url;
				});
			jQuery(".mb-blog-toolbar > .pager > .limiter > .limiter-options").change(function(event){
					
					var current_url = self.urldecode(this.value);
					//window.location.href = current_url;
					self.ajaxBlogPagination(current_url);
					//return false;
				});
		},
		showMoreLess: function(){
			
			jQuery(".show-more-cat").click(function(event){
						
						event.stopPropagation();
						if(jQuery(this).hasClass('showmorecat'))
						{
							
							jQuery(this).prevAll("a.hide-cat-more").addClass("show-cat-more");
							jQuery(this).prevAll("a.hide-cat-more").removeClass("hide-cat-more");
							jQuery(this).removeClass('showmorecat');
							jQuery(this).addClass('hidemorecat');
							jQuery(this).text(' Less');
							
							return false;
						}
						if(jQuery(this).hasClass('hidemorecat'))
						{
							
							jQuery(this).prevAll("a.show-cat-more").addClass("hide-cat-more");
							jQuery(this).prevAll("a.show-cat-more").removeClass("show-cat-more");
							jQuery(this).removeClass('hidemorecat');
							jQuery(this).addClass('showmorecat');
							jQuery(this).text(' More');
							
							return false;
						}
				});
				jQuery(".show-more-tag").click(function(event){
					event.stopPropagation();
			
				if(jQuery(this).hasClass('showmoretag'))
				{
			
					jQuery(this).prevAll("a.hide-tag-more").addClass("show-tag-more");
					jQuery(this).prevAll("a.hide-tag-more").removeClass("hide-tag-more");
					jQuery(this).removeClass('showmoretag');
					jQuery(this).addClass('hidemoretag');
					jQuery(this).text(' Less');
					return false;
				}
				if(jQuery(this).hasClass('hidemoretag'))
				{
			
					jQuery(this).prevAll("a.show-tag-more").addClass("hide-tag-more");
					jQuery(this).prevAll("a.show-tag-more").removeClass("show-tag-more");
					jQuery(this).removeClass('hidemoretag');
					jQuery(this).addClass('showmoretag');
					jQuery(this).text(' More');
					return false;
				}
			
				});
		},
		paginationAjax: function(){		
				var self = this;
				/*Start for set ajax on pagination*/	
				jQuery(".mb-blog-toolbar").find("a").each(function () {	
				var link = jQuery(this);						
				var link_class= jQuery(this).attr("class");	
				var classes = [ "page","action previous","action next","action  next","action  previous"]; 
				var found_class = jQuery.inArray(link_class,classes);	
				if (found_class >-1) {					        
				link.attr("onclick", "return false;");		
				var url = link.attr("href");		
				link.click(function () {		
				self.ajaxBlogPagination(url);			
				});            
				}else{
					
					
					
				}	
				});	
				/*end for set ajax on pagination*/	
		},	
		ajaxBlogPagination: function (url) {
			
			var self=this;        
			document.getElementById('loadingImage').style['display']='block';	
			//jQuery('body').addClass('stop-scrolling');   
			var ajaxUrl=url;   
			
			
				var stickyHeader = jQuery(".page-header").height() + 50;
				var scrollPosition = jQuery(".mbb-blog-category-page").offset().top - stickyHeader ;
				
				
				jQuery([document.documentElement, document.body]).animate({
					scrollTop: scrollPosition
				}, 2000);
				
		
			jQuery.get(ajaxUrl, function(html) {
				document.getElementById('loadingImage').style['display']='none';	
			//	jQuery('body').removeClass('stop-scrolling');	
				history.pushState({}, "", ajaxUrl);	
				// Loop through elements you want to scrape content from
			  var updatedBlogList = jQuery(html).find(".mbb-blog-category-page").html();
			  var updatedSidebarmain = jQuery(html).find(".sidebar-main").html();
			  var updatedSidebaradditional = jQuery(html).find(".sidebar-additional").html();
			  jQuery('body').find('.mbb-blog-category-page').html(updatedBlogList);
			  jQuery('body').find('.sidebar-main').html(updatedSidebarmain);
			  jQuery('body').find('.sidebar-additional').html(updatedSidebaradditional);
			  jQuery(".mbb-blog-category-page").trigger('contentUpdated');
			   
			  self.alignBlogPostsListActions();
			  self.paginationAjax();
			  self.limiter();
			  self.showMoreLess();
			  
			});			
		},
		alignBlogPostsListActions: function() {
			var bloggridRows = []; 
			var tempRow = [];
			var blogGridElements = jQuery('.mb-post-gridview .item');
			blogGridElements.each(function (index) {

				if (jQuery(this).css('clear') != 'none' && index != 0) {
					bloggridRows.push(tempRow); 
					tempRow = []; 
				}
				tempRow.push(this);

				if (blogGridElements.length == index + 1) {
					bloggridRows.push(tempRow);
				}
			});
			
			jQuery.each(bloggridRows, function () {
				var tallestHeight = 0;
				var tallestHeight1 = 0;
				var tallestHeight2 = 0;
				jQuery.each(this, function () {

					jQuery(this).find('.mb-post-title').css('min-height', '');
					jQuery(this).find('.mb-post-text').css('min-height', '');
					jQuery(this).find('.mb-post-meta').css('min-height', '');

					var elHeight = parseInt(jQuery(this).find('.mb-post-title').css('height'));
					var elHeight1 = parseInt(jQuery(this).find('.mb-post-text').css('height'));
					var elHeight2 = parseInt(jQuery(this).find('.mb-post-meta').css('height'));

					if (elHeight > tallestHeight) {
						tallestHeight = elHeight;
					}
					 if (elHeight1 > tallestHeight1) {
						tallestHeight1 = elHeight1;
					}
					if (elHeight2 > tallestHeight2) {
						tallestHeight2 = elHeight2;
					}
				});
				jQuery.each(this, function () {
					jQuery(this).find('.mb-post-title').css('minHeight', tallestHeight);
				});
				jQuery.each(this, function () {
					jQuery(this).find('.mb-post-text').css('minHeight', tallestHeight1);
				});
				jQuery.each(this, function () {
					jQuery(this).find('.mb-post-meta').css('minHeight', tallestHeight2);
				});
			});
			
		},
		urldecode: function(url) {
			var map ={'&amp;': '&','?amp;': '?','&lt;': '<','&gt;': '>','&quot;': '"','&#039;': "'",};return url.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
        }
	});
    return jQuery.Magebees.blog;
    
});
