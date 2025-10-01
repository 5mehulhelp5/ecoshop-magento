/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
*/
define([
    'jquery',
	"jquery-ui-modules/core","jquery-ui-modules/widget",
	'magebees.swiper',
    'domReady!'
], function ($,core,widget,Swiper) {
    'use strict';
	$.widget('Magebees.swiperinit', {
		 _create: function() {
			 var self = this;
			 this._swiperInit();
			
		},
		_swiperInit:function(){
			var self=this;
			var sliderSelector = '.swiper-container';
			var nSlider = document.querySelectorAll(sliderSelector);
			[].forEach.call(nSlider, function( slider, index, arr )
	{
		var carouseldata = slider.getAttribute('data-carousel-swiper') || {};
		var data = slider.getAttribute('data-swiper') || {};
		var swiperloaded = slider.getAttribute('swiperloaded');
		if((!jQuery.isEmptyObject(data))&&(!jQuery.isEmptyObject(carouseldata))){
		if (data)
		{
			var dataOptions = JSON.parse(data);
		}
		if (carouseldata)
		{
			var carouselDataOptions = JSON.parse(carouseldata);
		}
		var swiperSliderDefault = {
          breakpointsInverse: true,observer: false
		};
		console.log(carouselDataOptions);
		console.log(carouselDataOptions);
		console.log(carouselDataOptions);
		if(carouselDataOptions.slider_id)
		{
			carouselDataOptions.options = Object.assign({}, swiperSliderDefault, carouselDataOptions);
			var carouselSwiper = new Swiper('#'+carouselDataOptions.slider_id, carouselDataOptions.options);	
			var swiperSliderDefault = {
			breakpointsInverse: true,observer: false,thumbs: {swiper: carouselSwiper,}
			};
			
		}
		slider.options = Object.assign({}, swiperSliderDefault, dataOptions);
		
		console.log(slider.options);
		if(slider.options.slider_id){
		var swiper = new Swiper(slider, slider.options);	
		
		jQuery("#"+slider.options.slider_id).attr('swiperLoaded',true);
		}
		
		}else if((!jQuery.isEmptyObject(data))&&(swiperloaded != 'true')){
		if (data)
		{
			var dataOptions = JSON.parse(data);
		}
		if(dataOptions.pagination_type=='custom'){
		var swiperSliderDefault = {
          breakpointsInverse: true,observer: true,
			pagination: { 
			el: dataOptions.pagination_id,
			clickable: true,
			renderBullet: function (index, className) 
				{
					return '<span class="' + className + '">' + (index + 1) + "</span>";
				}
			}			
        };
		
		}else if(dataOptions.pagination_type=='dynamic'){
		var swiperSliderDefault = {
          breakpointsInverse: true,observer: true,
			pagination: { 
			el: dataOptions.pagination_id,
			clickable: true,
			dynamicBullets: true
			}			
        };
		
		}else if(dataOptions.pagination_type=='progressbar'){
		var swiperSliderDefault = {
          breakpointsInverse: true,observer: true,
			pagination: { 
			el: dataOptions.pagination_id,
			type:"progressbar",
			clickable: true
			}			
			};
		}else if(dataOptions.pagination_type=='fraction'){
		var swiperSliderDefault = {
          breakpointsInverse: true,observer: true,
			pagination: { 
			el: dataOptions.pagination_id,
			type:"fraction",
			clickable: true
			}			
			};
		}else if(dataOptions.pagination_type=='default'){
		var swiperSliderDefault = {
          breakpointsInverse: true,observer: true,
			pagination: { 
			el: dataOptions.pagination_id,
			clickable: true
			}			
			};
		}else {
		
		var swiperSliderDefault = {
          breakpointsInverse: true,observer: false
		};
		
		}
		//slider.options = Object.assign({}, defaultOptions, dataOptions);
		slider.options = Object.assign({}, swiperSliderDefault, dataOptions);
		
		console.log(slider.options);
		if(slider.options.slider_id){
		var swiper = new Swiper(slider, slider.options);	
		
		jQuery("#"+slider.options.slider_id).attr('swiperLoaded',true);
		
		/* stop on hover */
		if ( typeof slider.options.autoplay !== 'undefined' && slider.options.autoplay !== false )
		{
			slider.addEventListener('mouseenter', function(e) {
				swiper.autoplay.stop();
				console.log('stop')
			});

			slider.addEventListener('mouseleave', function(e) {
				swiper.autoplay.start();
				console.log('start')
			});
		}
		if ( typeof slider.options.slide_mode !== 'undefined' && slider.options.slide_mode !== false && slider.options.slide_mode == 'vertical')
		{
			swiper.init();
			swiper.on('init', function () {
				self.setSlideHeight(this);
			});
			swiper.on('slideChange', function () {
				self.setSlideHeight(this);
			
			});
		}
		if ( typeof slider.options.isVideoAvailable !== 'undefined' && slider.options.isVideoAvailable !== false)
		{
			swiper.on('slideChange', function () {
				self.youtubeVideo(this);
			
			});
			
			
		}
		
		}
		
		
		}
		
		});
		
		},
		setSlideHeight:function(slide){
			$('.swiper-slide').css({height:'auto'});
			var currentSlide = slide.activeIndex;
			var newHeight = $(slide.slides[currentSlide]).height();

			$('.swiper-wrapper,.swiper-slide').css({ height : newHeight })
			slide.update();
		},
		youtubeVideo:function(slide){
			
			var currentSlide = slide.activeIndex + 1;
			var sid = slide.passedParams.slider_id + "-"+ currentSlide;
			
			console.log(slide.passedParams.slider_id); 
			 if(currentSlide){
				
					 
				

					var youtubeIframes = document.querySelectorAll('[videocws][src*="youtube.com"]');

					youtubeIframes.forEach(iframe => {
						// Send a 'pauseVideo' postMessage command to the YouTube iframe
						iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
					});
					 
					 var iframes = document.querySelectorAll('[videocws][src*="vimeo.com"]');

					iframes.forEach(iframe => {
						// Send a 'pause' postMessage command to the Vimeo iframe
						iframe.contentWindow.postMessage('{"method":"pause"}', '*');
					});
					 				
				 
				 
				 
                / Stop the video /
                var alliFrame = document.querySelectorAll("#"+ sid);
				console.log(alliFrame);
				
                if (alliFrame.length > 0) {
                 
                  
                  / Start current video /
                  var currentiFrame = document.querySelector("#" + sid);
                  if (currentiFrame) 
                  {
					  
				
                    if (currentiFrame.classList.contains("js-youtube")) {
                      if (currentiFrame.getAttribute('src') === null) {
                        currentiFrame.setAttribute('src', currentiFrame.getAttribute('data-src'));
                        currentiFrame.addEventListener('load', function () {
                          currentiFrame.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', "*");
                        });
                      }else{
                        currentiFrame.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', "*");
                      }
                    }
                    if (currentiFrame.classList.contains("js-vimeo")) {
                      if (currentiFrame.getAttribute('src') === null) {
                        currentiFrame.setAttribute('src', currentiFrame.getAttribute('data-src'));
                        currentiFrame.addEventListener('load', function () {
                          currentiFrame.contentWindow.postMessage('{"method":"play"}',"https://player.vimeo.com");
                        });
                      }else{
                          currentiFrame.contentWindow.postMessage('{"method":"play"}',"https://player.vimeo.com");
                      }
                    }
                  }
                }
			
              }
			
			
			
			
		}
		

	});

	return $.Magebees.swiperinit;

});

