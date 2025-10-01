define([
    "jquery",
    "jquery-ui-modules/core",
	"jquery-ui-modules/widget",
    "mage/apply/main",
    "catalogAddToCart",
    "mage/cookies",
	"alignProductSection",
	"magebees/ajaxcomparewishlist",
	"magebees.countdown"
], function($,core,ui, mage,catalogAddToCart,cookie,alignProducts,ajaxcomparewishlist) {

    $.widget('magebees.magebeesInfiniteScroll', {
        options: {},
        is_loading: 0,
        last_scroll: 0,
		listing_id : 0,
		pageparam : 0,
		toolbar_class : '',
        //totalAmountSelector: '.toolbar-amount',

        _create: function(options) {
			
			this.listing_id = this.options['listing_id'];
			if(this.listing_id){
				this.pageparam = 'list'+this.listing_id;
			}
			this.toolbar_class = this.options['toolbar_class'];
            var check_cookie = $.cookie('clicked_id'); // Get Cookie Value
            $(check_cookie).focus();
            this._initialize();
        },
        _initialize: function() {

            var self = this;
            var offset = 220;
            var duration = 500;
            var options = self.options;
			var pageparam = 'list'+this.listing_id;
            var toolbar_class = options['toolbar_class'];
			var container = options['product_container'];
			//alert(container);
            var show_page = options['page_number'];
            var loading_type = self.options['loading_type'];
            var button_style = self.options['load_button_style'];
            var load_next_text = self.options['load_next_text'];
            var load_prev_text = self.options['load_prev_text'];
            var text_no_more = self.options['text_no_more'];
            var img_src = self.options['loading_image_src'];
            var category_page = $('body').find('.catalog-category-view');
			
			//for autoscroll to clicked position if clicked on back from browser
			var elems = document.querySelectorAll('.product-item-info');
            elems.forEach(
                function(elem) {
                    elem.addEventListener('click', function(evt) {
                        //ga('send', 'event', 'button', 'visit', 'click_visit_zone1');
                        var clicked_id = this.id;
                        $.cookie('clicked_id', clicked_id); // Set Cookie Value

                    }, true); // <-- the `true` allows this event to happen in the capture phase.
                }
            );


            /****Start for set scroll_page attribute on page load for set page number on scroll****/
            $('body').find(container).each(function() {
                //find the product grid ot list container from the body mentioned in configuration 
                if ((!$(this).hasClass('ajaxcategory-products')) && (!$(this).hasClass('ajaxcategory-products-slider')) && (!$(this).hasClass('mage-featured-slider')) && (!$(this).hasClass('products-related')) && (!$(this).hasClass('products-upsell')) && (!$(this).hasClass('products-crosssell')) && (!$(this).hasClass('mage-bestseller-slider')) && (!$(this).hasClass('mage-mostviewed-slider')) && (!$(this).hasClass('mage-new-slider')) && (!$(this).hasClass('auto_load_bestseller')) && (!$(this).hasClass('auto_load_featured')) && (!$(this).hasClass('auto_load_new')) && (!$(this).hasClass('auto_load_mostviewed'))) {
					var parentElemetClass = $(this).parent().attr('class');
                    if ($(this).parent().attr('class') == 'cwsNew') {
                        var current_page = $(".mageNewToolbar .current .page  span:last-child").last().text();
                        var toolbar_class = ".mageNewToolbar";
                        var container_parent = ".cwsNew";
                    } else if ($(this).parent().attr('class') == 'cwsBestSeller') {
                        var current_page = $(".mageBestsellerToolbar .current .page  span:last-child").last().text();
                        var toolbar_class = ".mageBestsellerToolbar";
                        var container_parent = ".cwsBestSeller";
                    } else if ($(this).parent().attr('class') == 'cwsFeatured') {
                        var current_page = $(".mageFeaturedToolbar .current .page  span:last-child").last().text();
                        var toolbar_class = ".mageFeaturedToolbar";
                        var container_parent = ".cwsFeatured";
                    } else if ($(this).parent().attr('class') == 'cwsMostviewed') {
                        var current_page = $(".mageMostviewedToolbar .current .page  span:last-child").last().text();
                        var toolbar_class = ".mageMostviewedToolbar";
                        var container_parent = ".cwsMostviewed";
                    } else if ($(this).parent().attr('class') == 'prodlist') {
						//added for prodlist
						var toolbar_class = self.options['toolbar_class'];
						var current_page = $(toolbar_class).find( "li.current span:last-child" ).last().text();
						var main_con = '.'+pageparam;
						var container_parent = main_con+" .prodlist";
					} else {
                        var current_page = $(".current .page  span:last-child").last().text();
                        var toolbar_class = ".toolbar";
                        var container_parent = ".column";
                    }

                    if (!current_page) {
                        var current_page = 1;
                    }

                    /* Start For set Scroll attribute and page no on page load*/
                    if (show_page == 1) {
                        if (!$(this).find('.magebees_page').length) {
                            $(this).prepend('<div class="magebees_page mageb-paging">Page : ' + current_page + '</div>');
                        }
                    }
                    /* End For set Scroll attribute and page no on page load*/
					console.log(this);
                    $(this).attr("scroll_page", current_page);
                    self.hideToolbarElement(container_parent, $(this), toolbar_class);

                    if (loading_type == 1) {
                        //for On Button Click Page Load Mode
                        /****Start for add LOAD NEXT BUTTON on page load for load next page content ****/
                        if ($(toolbar_class).find('.next').length) {
                            if (!$(toolbar_class).find('.load_next').length) {
                                if (self.options['load_next_text']) {
                                    $(toolbar_class).find(".pages-items").after('<div class="loading-btn"><button class=load_next id=' + toolbar_class + ' style=' + button_style + '>' + self.options['load_next_text'] + '</button></div>');
                                } else {
                                    $(toolbar_class).find(".pages-items").after('<div class="loading-btn"><button class=load_next id=' + toolbar_class + ' style=' + button_style + '>Load Next</button></div>');
                                }

                                $(toolbar_class).find('.load_next').click(function() {
									$('body').find('.load_next').attr('disabled', true);
                                    var next_toolbar = $(this).attr("id");
									$(this).css('display', 'none');
                                    var next_url = $(next_toolbar).find(".next").attr("href");

                                    var last_content = $(container_parent).find(self.options['product_container']).last().attr("scroll_page");

                                    var next_page = parseInt(last_content);
                                    ++next_page;
									
									if(self.pageparam){
										next_url = new URL(next_url);
										next_url.searchParams.set(self.pageparam, next_page); // setting your param
									} else {
										next_url = next_url.replace(/(p=)[^\&]+/, '$1' + next_page);
									}
                                    if (next_url) {
                                        if (self.is_loading == 0) {
                                            self._loadNextPageContent(next_url, container_parent, toolbar_class);
                                        }
                                    }
                                });
                            }
                        }
                        /****End for add LOAD NEXT BUTTON on page load for load next page content ****/

                        /****Start for add LOAD PREVIOUS BUTTON on page load for load previous page content ****/
                        if ($(toolbar_class).find('.previous').length) {
                            if (!$(container_parent).find('.load_previous').length) {
                                if (self.options['load_prev_text']) {
                                    $(this).before('<div class="loading-btn"><button id=' + toolbar_class + ' class=load_previous style=' + button_style + '>' + self.options['load_prev_text'] + '</button></div>');
                                } else {
                                    $(this).before('<div class="loading-btn"><button id=' + toolbar_class + ' class=load_previous style=' + button_style + '>Load Previous</button></div>');
                                }
								
                                $(container_parent).find('.load_previous').click(function() {
                                    $('body').find('.load_previous').attr('disabled', true);

                                    var previous_toolbar = $(this).attr("id");
									
                                    $(this).css('display', 'none');

                                    var previous_url = $(previous_toolbar).find(".previous").attr("href");

                                    var first_content = $('body').find(self.options['product_container']).first().attr("scroll_page");

                                    var previous_page = parseInt(first_content);

                                    --previous_page;
									
									if(self.pageparam){
										previous_url = new URL(previous_url);
										previous_url.searchParams.set(self.pageparam, previous_page); // setting your param
									} else {
										previous_url = previous_url.replace(/(p=)[^\&]+/, '$1' + previous_page);
									}
									
                                    if (previous_url) {
                                        if (self.is_loading == 0) {
                                            self._loadPreviousPageContent(previous_url, container_parent, toolbar_class);
                                        }
                                    }
                                });
                            }
                        }
                    }
                    /****End for add LOAD PREVIOUS BUTTON on page load for load previous page content ****/

                    if (!$(toolbar_class).find('.next').length) {
						if (!$('.magebees_no_content').length) {
                            $('body').find(this).last().append('<div class="magebees_no_content mageb-no-more-load">' + text_no_more + '</div>');
                        }
                    }
                }
            });
            /**** End for set scroll_page attribute on page load for set page number on scroll****/

            /****Start for add BACK TO TOP BUTTON on page scroll and set functionality for go to top of page***/
            $('.back-to-top').click(function(event) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, duration);
                return false;
            });
            /****End for add BACK TO TOP BUTTON on page scroll and set functionality for go to top of page***/




            var lastScrollTop = 0;
            var st;
            $(window).scroll(function() {
                var st = $(this).scrollTop();
                /*For BACK TO TOP BUTTON */
                if ($(this).scrollTop() > offset) {
                    $('.back-to-top').fadeIn(duration);
                } else {
                    $('.back-to-top').fadeOut(duration);
                }
                /*For BACK TO TOP BUTTON */

                var loading_type = self.options['loading_type'];
                if (loading_type == 0) {
                    // on page scroll
                    self._initScrollPagination(st, lastScrollTop);
                } else {
                    var doc_length = 0;
                    // set page no for on button click mode when page scroll
                    var scroll_pos = $(window).scrollTop();
                    self.CurrentScrollPageUrl(scroll_pos, st, lastScrollTop);
                }
                lastScrollTop = st;
            });
            setTimeout(function() {
                if (loading_type == 0) {
                    self._initScrollPagination(st, lastScrollTop);
                }
            }, 7000);
        },

        _initScrollPagination: function(st, lastScrollTop) {
            /******* For initailise function for load next content on page scroll*/
            var self = this;
            var scroll_pos = $(window).scrollTop();
            /*find current page and change url and scroll-bar*/
            this.CurrentScrollPageUrl(scroll_pos, st, lastScrollTop);
        },

        CurrentScrollPageUrl: function(scroll_pos, st, lastScrollTop) {
            /*find current page and change url and scroll-bar*/
            var self = this;
            var loading_type = self.options['loading_type'];
            var threshold = self.options['threshold'];


            if (Math.abs(scroll_pos - self.last_scroll) > $(window).height() * 0.1) {
                self.last_scroll = scroll_pos;
				
                $(self.options['product_container']).each(function(index) {
                    if ((!$(this).hasClass('ajaxcategory-products')) && (!$(this).hasClass('ajaxcategory-products-slider')) && (!$(this).hasClass('mage-featured-slider')) && (!$(this).hasClass('products-related')) && (!$(this).hasClass('products-upsell')) && (!$(this).hasClass('products-crosssell')) && (!$(this).hasClass('mage-bestseller-slider')) && (!$(this).hasClass('mage-mostviewed-slider')) && (!$(this).hasClass('mage-new-slider')) && (!$(this).hasClass('auto_load_bestseller')) && (!$(this).hasClass('auto_load_featured')) && (!$(this).hasClass('auto_load_new')) && (!$(this).hasClass('auto_load_mostviewed'))) {
                        if (self.findVisibleElement(this)) {
                            var page = $(this).attr('scroll_page');
                            page = parseInt(page);
							var parentElemetClass = $(this).parent().attr('class');
							
							
		
                            if ($(this).parent().attr('class') == 'cwsNew') {
                                var toolbar_class = ".mageNewToolbar";
                                var container_parent = ".cwsNew";
                            } else if ($(this).parent().attr('class') == 'cwsBestSeller') {
                                var toolbar_class = ".mageBestsellerToolbar";
                                var container_parent = ".cwsBestSeller";
                            } else if ($(this).parent().attr('class') == 'cwsFeatured') {
                                var toolbar_class = ".mageFeaturedToolbar";
                                var container_parent = ".cwsFeatured";
                            } else if ($(this).parent().attr('class') == 'cwsMostviewed') {
                                var toolbar_class = ".mageMostviewedToolbar";
                                var container_parent = ".cwsMostviewed";
                            } else if ($(this).parent().attr('class') == 'prodlist') {
								var toolbar_class = self.options['toolbar_class'];
								var main_con = '.'+self.pageparam;
								var container_parent = main_con+" .prodlist";
							}else {
                                var toolbar_class = ".toolbar";
                                var container_parent = ".column";
                            }

                            if ($(toolbar_class).find('.next').length) {
                                var next_url = $(toolbar_class).find(".next").attr("href");
                                var last_content = $(container_parent).find(self.options['product_container']).last().attr("scroll_page");
                                var next_page = parseInt(last_content);
                                ++next_page;
								
								if(self.pageparam){
									next_url = new URL(next_url);
									next_url.searchParams.set(self.pageparam, next_page); // setting your param
								} else {
									next_url = next_url.replace(/(p=)[^\&]+/, '$1' + next_page);
								}
                            }
                            var container = $(this);
                            var scroll_pos = $(window).scrollTop();
                            var diff = $(container).height() - threshold - $(window).height();
                            var blockAfterProducts = $(".main .products ~ .block-static-block");
                            if (blockAfterProducts.length) {
                                diff = diff - blockAfterProducts.height();
                            }
                            diff = 0.9 * diff;


                            if (st > lastScrollTop) {
                                if (scroll_pos >= diff) {
                                    if (self.is_loading == 0) {
                                        $(toolbar_class).find("a").each(function() {
											var page_link_url = $(this).attr("href");
											var page_results = new RegExp('[\?&]p=([^&#]*)').exec(page_link_url);
											
											if (page_results) {
                                                var page_param = decodeURI(page_results[1]) || 0;
                                            } else {
												if(self.pageparam){
													turl = new URL(page_link_url);
													var page_param = turl.searchParams.get(self.pageparam); // setting your param
												}else{
													var page_param = self._getPageParam(page_link_url);
												}
											}
																						
											var next_page_results = new RegExp('[\?&]p=([^&#]*)').exec(next_url);
											
                                            if (next_page_results) {
                                                var next_page_param = decodeURI(next_page_results[1]) || 0;
                                            } else {
                                                if (next_url) {
                                                    var next_page_param = self._getPageParam(next_url);
                                                }
                                            }
											
                                            if (next_page_param == page_param) {
                                                if (!$(container_parent).find('.magebees_no_next').length) {
                                                    if (loading_type == 0) {
                                                        self._loadNextPageContent(next_url, container_parent, toolbar_class);
                                                    }
                                                }
                                            }
                                        });
                                    }

                                }
                            } else {
                                var diff = $(document).height() - $(window).height();
                                if (scroll_pos <= diff) {
                                    if (self.is_loading == 0) {
                                        if ($(toolbar_class).find('.previous').length) {
                                            var previous_url = $(toolbar_class).find(".previous").attr("href");
                                            if (previous_url) {
                                                var first_content = $(container_parent).find(self.options['product_container']).first().attr("scroll_page");
                                                var previous_page = parseInt(first_content);
                                                --previous_page;
												if(self.pageparam){
													previous_url = new URL(previous_url);
													previous_url.searchParams.set(self.pageparam, previous_page); // setting your param
												} else {
													previous_url = previous_url.replace(/(p=)[^\&]+/, '$1' + previous_page);
												}
                                            }
                                        }
                                        if (previous_page > 0) {
                                            if (loading_type == 0) {
                                                self._loadPreviousPageContent(previous_url, container_parent, toolbar_class);
                                            }
                                        }
                                    }

                                    // upscroll code
                                }
                            }
                            

                            if (scroll_pos <= self._getTopContainersHeight($(this))) {

                            }
                            var url = $(toolbar_class).find(".next").attr("href");
                            if (!url) {
                                var url = $(toolbar_class).find(".previous").attr("href");
                            }
                            if (url) {								
								
								if(self.pageparam){
									url = url.replace(/#.*$/, '').replace(/\?.*$/, '');
									url = new URL(url);
									url.searchParams.set(self.pageparam, page); // setting your param
								}else{
									url = url.replace(/(p=)[^\&]+/, '$1' + page);
								}								
							}
							
                            //$(toolbar_class).find(".current .page  span:last-child").last().text(page);
							//commented this for solve update toolar after load content
							history.pushState({}, "", url);
                        }
                    }
                });
            }
        },

        _setAjaxLoad: function() {
            this.is_loading = 0;
        },

        _getPageParam: function(url) {
			if(typeof url == 'string'){
				if (url.indexOf("bp") > -1) {
					var page_results = new RegExp('[\?&]bp=([^&#]*)').exec(url);
					var page_param = decodeURI(page_results[1]) || 0;
				} else if (url.indexOf("fp") > -1) {
					var page_results = new RegExp('[\?&]fp=([^&#]*)').exec(url);
					var page_param = decodeURI(page_results[1]) || 0;
				} else if (url.indexOf("np") > -1) {
					var page_results = new RegExp('[\?&]np=([^&#]*)').exec(url);
					var page_param = decodeURI(page_results[1]) || 0;
				} else if (url.indexOf("mp") > -1) {
					var page_results = new RegExp('[\?&]mp=([^&#]*)').exec(url);
					var page_param = decodeURI(page_results[1]) || 0;
				} 
			}else{
				url = new URL(url);
				var page_param = url.searchParams.get(this.pageparam); // setting your param
			}
            return page_param;
        },
		
        _loadNextPageContent: function(url, container_parent, toolbar_class) {

            var self = this;
            var scroll_ele = $('#scroll_loading');
            var container = $(self.options['product_container']);
            var container_ele_body = $('body').find(container_parent);
            var img_src = self.options['loading_image_src'];
            if (!$(scroll_ele).html()) {
                $(scroll_ele).html(img_src);
            }
            if (container.data('nextrequestRunning')) {
                return;
            }
            container.data('nextrequestRunning', true);
            if (url && (this.is_loading == 0)) {
                this.is_loading = 1;

                setTimeout(function() {
                    $.ajax({
                        url: url,
                        cache: true,
                        type: "GET",
                        beforeSend: function() {
                            if (container_parent == null) {
                                $(self.options['product_container']).last().after($(scroll_ele).css('display', 'block'));
                            } else {
                                $(container_ele_body).find(self.options['product_container']).last().after($(scroll_ele).css('display', 'block'));
                            }
                        },
                        success: function(data) {
                            if (toolbar_class == null) {
                                var next_length = $(data).find('.next').length;
                            } else {
                                var toolbar_ele = $(data).find(toolbar_class);
                                var next_length = $(toolbar_ele).find('.next').length;
                            }
                            self._setAjaxLoad();
                            if (container_parent == null) {
                                $(self.options['product_container']).last().after($(scroll_ele).css('display', 'none'));
                            } else {
                                $(container_ele_body).find(self.options['product_container']).last().after($(scroll_ele).css('display', 'none'));
                            }
                            history.pushState({}, "", url);
                            if (container_parent == null) {
                                var current_page = $(data).find(".current .page  span:last-child").last().text();
                                var result = $(data).find(self.options['product_container']);
                                var toolbar = $(data).find('.pages-items').html();
                                $(".pages-items").html(toolbar);
                            } else {
                                var toolbar_ele = $(data).find(toolbar_class);
                                var current_page = $(toolbar_ele).find(".current .page  span:last-child").last().text();
                                var container_ele = $(data).find(container_parent);
                                var result = $(container_ele).find(self.options['product_container']);
							 
                                var toolbar = $(container_ele).find('.pages-items').html();
                                $(toolbar_ele).find(".pages-items").html(toolbar);
                            }
                            $(result).each(function() {
                                if ((!$(this).hasClass('ajaxcategory-products')) && (!$(this).hasClass('ajaxcategory-products-slider')) && (!$(this).hasClass('mage-featured-slider')) && (!$(this).hasClass('products-related')) && (!$(this).hasClass('products-upsell')) && (!$(this).hasClass('products-crosssell')) && (!$(this).hasClass('mage-bestseller-slider')) && (!$(this).hasClass('mage-mostviewed-slider')) && (!$(this).hasClass('mage-new-slider')) && (!$(this).hasClass('auto_load_bestseller')) && (!$(this).hasClass('auto_load_featured')) && (!$(this).hasClass('auto_load_new')) && (!$(this).hasClass('auto_load_mostviewed'))) {
                                    if (self.options['page_number'] == 1) {
                                        $(this).prepend('<div class="magebees_page mageb-paging">Page : ' + current_page + '</div>');
                                    }
                                    if (container_parent == null) {
                                        $(self.options['product_container']).last().after($(this));
                                    } else {
                                        $(container_ele_body).find(self.options['product_container']).last().after($(this));
                                    }
                                    $(this).attr("scroll_page", current_page);
                                    if (!next_length) {
                                        var no_content_msg = self.options['text_no_more'];
										var no_content_class = '.magebees_no_content';
										if(self.pageparam){
											var no_content_class = '.'+self.pageparam+' .magebees_no_content';
										}
										
                                        if (!$(no_content_class).length) {
                                            $(this).append('<div class="magebees_no_content mageb-no-more-load">' + no_content_msg + '</div>');
                                        }
                                        $(this).append('<div class="magebees_no_next" style="display:none;">test</div>');
                                    }
                                }

                            });
                            if (!next_length) {
                                $(container_ele_body).find('.load_next').css('display', 'none');
                            } else {
                                $(container_ele_body).find('.load_next').css('display', 'inline-block');
                            }
                            $(mage.apply);
                            $("form[data-role='tocart-form']").catalogAddToCart();
                            $(self.options['product_container']).trigger('contentUpdated');
							$(container_ele_body).alignProducts({
							container : ".products-grid",
							item : '.item',
							elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.swatches-listing,.product-item-details,.product-brand,.p_ctgry'
					});	
					$(container_ele_body).ajaxcomparewishlist({popupTTL:10,showLoader:true});								
					self.dealTimer();
					
                        },
                        complete: function() {
                            $('body').find('.load_next').attr('disabled', false);
                            container.data('nextrequestRunning', false);
                            $("#scroll_loading").insertBefore(".page-header");
                            $(mage.apply);
							$(container_ele_body).alignProducts({
							container : ".products-grid",
							item : '.item',
							elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.swatches-listing,.product-item-details,.product-brand'
							});	
					$(container_ele_body).ajaxcomparewishlist({popupTTL:10,showLoader:true});
					self.dealTimer();
					},
                        error: function() {}
                    });
                }, 100);
                $(mage.apply);
				$(container_ele_body).alignProducts({
							container : ".products-grid",
							item : '.item',
							elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.swatches-listing,.product-item-details,.product-brand'
							});	
					$(container_ele_body).ajaxcomparewishlist({popupTTL:10,showLoader:true});
					self.dealTimer();
            }
        },

        _loadPreviousPageContent: function(url, container_parent, toolbar_class) {
            var self = this;
            var scroll_ele = $('#scroll_loading');
            var img_src = self.options['loading_image_src'];
            var container = $(self.options['product_container']);
            var container_ele_body = $('body').find(container_parent);
            if (!$(scroll_ele).html()) {
                $(scroll_ele).html(img_src);
            }
            if (container.data('previousrequestRunning')) {
                return;
            }
            container.data('previousrequestRunning', true);
            if (url && (this.is_loading == 0)) {

                this.is_loading = 1;
                setTimeout(function() {
                    $.ajax({
                        url: url,
                        cache: true,
                        type: "GET",
                        beforeSend: function() {

                            if (container_parent == null) {
                                $(self.options['product_container']).first().before($(scroll_ele).css('display', 'block'));
                            } else {
                                $(container_ele_body).find(self.options['product_container']).first().before($(scroll_ele).css('display', 'block'));
                            }
                        },
                        success: function(data) {
                            if (toolbar_class == null) {
                                var previous_length = $(data).find('.previous').length;
                            } else {
                                var toolbar_ele = $(data).find(toolbar_class);
                                var previous_length = $(toolbar_ele).find('.previous').length;
                            }
                            self._setAjaxLoad();
                            if (container_parent == null) {
                                $(self.options['product_container']).first().before($(scroll_ele).css('display', 'none'));
                            } else {
                                $(container_ele_body).find(self.options['product_container']).first().before($(scroll_ele).css('display', 'none'));
                            }
                            history.pushState({}, "", url);

                            if (container_parent == null) {
                                var current_page = $(data).find(".current .page  span:last-child").last().text();
								var result = $(data).find(self.options['product_container']);
                                var toolbar = $(data).find('.pages-items').html();
                                $(".pages-items").html(toolbar);
                            } else {
                                var toolbar_ele = $(data).find(toolbar_class);
                                var current_page = $(toolbar_ele).find(".current .page  span:last-child").last().text();
								var container_ele = $(data).find(container_parent);
								
                                var result = $(container_ele).find(self.options['product_container']);
								
                                var toolbar = $(container_ele).find('.pages-items').html();
								$(container_ele_body).find(".pages-items").html(toolbar);
								
                            }
                            $(result).each(function() {

                                if ((!$(this).hasClass('ajaxcategory-products')) && (!$(this).hasClass('ajaxcategory-products-slider')) && (!$(this).hasClass('mage-featured-slider')) && (!$(this).hasClass('products-related')) && (!$(this).hasClass('products-upsell')) && (!$(this).hasClass('products-crosssell')) && (!$(this).hasClass('mage-bestseller-slider')) && (!$(this).hasClass('mage-mostviewed-slider')) && (!$(this).hasClass('mage-new-slider')) && (!$(this).hasClass('auto_load_bestseller')) && (!$(this).hasClass('auto_load_featured')) && (!$(this).hasClass('auto_load_new')) && (!$(this).hasClass('auto_load_mostviewed'))) {
                                    if (self.options['page_number'] == 1) {
                                        $(this).prepend('<div class="magebees_page mageb-paging">Page : ' + current_page + '</div>');
                                    }
                                    if (container_parent == null) {
                                        $(self.options['product_container']).first().before($(this));
                                    } else {
                                        $(container_ele_body).find(self.options['product_container']).first().before($(this));
                                    }
                                    $(this).attr("scroll_page", current_page);
                                }
                            });
                            if (!previous_length) {
                                $(container_ele_body).find('.load_previous').css('display', 'none');
                            } else {
                                $(container_ele_body).find('.load_previous').css('display', 'inline-block');
                            }
                            $(mage.apply);
                            $("form[data-role='tocart-form']").catalogAddToCart();
                            $(self.options['product_container']).trigger('contentUpdated');
							$(container_ele_body).alignProducts({
							container : ".products-grid",
							item : '.item',
							elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.swatches-listing,.product-item-details,.product-brand'
							});	
					$(container_ele_body).ajaxcomparewishlist({popupTTL:10,showLoader:true});
					self.dealTimer();
				    },
                        complete: function() {
                            container.data('previousrequestRunning', false);
                            $('body').find('.load_previous').attr('disabled', false);
                            $("#scroll_loading").insertBefore(".page-header");
                            $(mage.apply);
							$(container_ele_body).alignProducts({
							container : ".products-grid",
							item : '.item',
							elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.swatches-listing,.product-item-details,.product-brand'
							});	
					$(container_ele_body).ajaxcomparewishlist({popupTTL:10,showLoader:true});
					self.dealTimer();
					},
                        error: function() {}
                    });
                }, 100);
                $(mage.apply);
				$(container_ele_body).alignProducts({
							container : ".products-grid",
							item : '.item',
							elementlist: '.product-item-name,.product-reviews-summary,.price-box,.product-item-name,.product-item-description,.product-name,.detail-left,.productTitle,.swatches-listing,.product-item-details,.product-brand'
							});	
					$(container_ele_body).ajaxcomparewishlist({popupTTL:10,showLoader:true});
					self.dealTimer();
			}
        },
        hideToolbarElement: function(container_parent, container, toolbar_class) {

            var self = this;
            var options = self.options;
            var show_page = options['page_number'];
            if (show_page == 1 || show_page == 0) {
                var toolbar_ele = $('body').find(toolbar_class);
                $(toolbar_ele).find('.toolbar-amount').hide();
                $(toolbar_ele).find('.items.pages-items').hide();
                $(toolbar_ele).find('.toolbar-number').hide();
                $(toolbar_ele).find('.limiter').hide();
            }
        },

        findVisibleElement: function(element) {

            element = $(element);
            var visible = element.is(":visible");
            var scroll_pos = $(window).scrollTop();
            var window_height = $(window).height();
            var el_top = element.offset().top;
            var el_height = element.height();
            var el_bottom = el_top + el_height;
            var result = (el_bottom - el_height * 0.25 > scroll_pos) &&
                (el_top < (scroll_pos + 0.5 * window_height)) &&
                visible;
            return result;
        },

        _getTopContainersHeight: function(container) {
            var self = this;
            var threshold = self.options['threshold'];
            var scrollTop = $(window).scrollTop(),
                elementOffset = $(container).offset().top + threshold;
            
            var result = 0.9 * elementOffset;
            return result;
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
    return $.magebees.magebeesInfiniteScroll;
});