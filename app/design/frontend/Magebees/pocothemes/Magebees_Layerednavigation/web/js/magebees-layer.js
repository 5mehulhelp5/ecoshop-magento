define([ "jquery","Magento_Ui/js/modal/alert", "mage/apply/main" , "jquery/ui"], function ( $,alert,mage,ul) {
    "use strict";
    $.widget('magebees.layeredNav', {        
        _create: function () {
            this.initAjaxLayerNavigation();
            this.initFilterNavigation();
			this.gridSwitchList();
			this.gridSorterOptions();
			this.gridSorterAction();
			this.gridLimiterAction();
			$("#loadingLayer").insertBefore(".page-header");
		},
        initAjaxLayerNavigation: function () {
            var self=this;			
             $("body .toolbar-products").find('[ data-mage-init]')
            .each(function (index, element) {
                var ele_id=$(element).attr('id');
                if (ele_id=="limiter") {
                $(element).removeAttr('data-mage-init');
                     $(element).change(function () {
                         var url=$(this).val();                        
                     });
                }
            });
        /*start for expand and collapse filter item according to configuration */
        $("div").on('click', function () {
            var always_expand=$(this).data('expand');
            if (!always_expand) {
            var expand_class=$(this).attr('id');
                if (expand_class) {
                var code=expand_class.substr(expand_class.indexOf("_") + 1);
                    if (expand_class=='filter-options-title_'+code) {
                    
                        var filter_html=$(this).html();
                        var header = $(this);                                    
                        var content = $("#layered-filter-block #filter-options-content_"+code);
                        var layercollapse=$(this).data('layercollapse');
                        if (layercollapse==1) {
                        if ($(this).data('position')=='top') {
                            if ($(this).data('filtertype')=='0') {
								$("#magebees_navigation_top_before")
								.nextUntil("#magebees_navigation_top_after")
								.find(".filter-options-title").each(function () {
										$(this).removeClass("active");
									});
								}
                            }
                        $(this).toggleClass("active");
                        content.slideToggle(500, function () {
                            header.text(function () {                              
                                $(this).html(filter_html);
                            });
                        });
                        }
                    }
                }
            }
        });
            $("input").on('keyup', function () {
				var self=this;
				var search_id=$(this).attr('id');
					if (search_id) {
						var code=search_id.substr(search_id.indexOf("_") + 1);
						if (code!='price') {
								var string=$(this).val();
								var searchString = $(this).val().toUpperCase();
								$('ol li#item_'+code).each(function () {
								var first_child=$(this).children().first();
						if (!(first_child.html())) {
							var a=first_child.next();
						} else {
							var a=$(this).children().first();
						}
						/* clone the element - select all the children - remove all the children -again go back to selected element */
						var item_text = a.clone().children() .remove().end().text();						
						var li_text=a.text().toUpperCase();
						var a = a;
						if (li_text.indexOf(searchString) >= 0) {   
							var matchStart = a.text().toLowerCase().indexOf("" + string.toLowerCase() + "");
							var matchEnd = matchStart + string.length - 1;
							var beforeMatch = a.text().slice(0, matchStart);
							var matchText = a.text().slice(matchStart, matchEnd + 1);
							var afterMatch = a.text().slice(matchEnd + 1);
							a.html(beforeMatch + "<span style=background:yellow>" + matchText + "</span>" + afterMatch);
							$(this).css('display','block');
						} else {
							$(this).css('display','none');
						}
					
						});
							
						/* manage for more/less button on searchbox value*/
						if ($(this).val()) {
						$('#more_'+code).hide();
							$('#less_'+code).hide();
						} else {
							$('#less_'+code).text('More');
							$('#less_'+code).attr('id','more_'+code);
							$('#more_'+code).show();
							var count=0;
							var unfold=$('#more_'+code).val();
							if (!unfold) {
							var unfold=$('#unfold_'+code).val();
							}
							$('ol li#item_'+code).each(function () {
								count++;
								if (count<=unfold) {
								$(this).css('display','block');
								} else {
									$(this).css('display','none');
								}
							});
						}
					}
                }
         });
         /*end for searchbox output and manage more/less button according to result */
        
        /* start for expand/collapse on more/less button click */
            $("button").on('click', function () {
            
            var btn_id=$(this).attr('id');
            if (btn_id) {
            var code=btn_id.substr(btn_id.indexOf("_") + 1);
         
                if (btn_id=='more_'+code) {
                $('ol li#item_'+code).each(function () {
                        $(this).css('display','block');
                    });
                    $(this).text('Less');
                    $(this).attr('id','less_'+code);
                }
                if (btn_id=='less_'+code) {
                var count=0;
                    var unfold=$(this).val();
                    $('ol li#item_'+code).each(function () {
                    count++;
                    if (count<=unfold) {
                    $(this).css('display','block');
                    } else {
                        $(this).css('display','none');
                    }
                
                    });
                    $(this).text('More');
                    $(this).attr('id','more_'+code);
                }
                if (btn_id=='expand_'+code) {             
                    $(this).toggleClass("active");
                    var collapse=$(this).data('collapse');
                   
                  if($(this).hasClass('expand_cat'))
				  {
					var level=$(this).parent().data('level');
					  if((level==2)&&($(this).parent().hasClass('advanced')))
					  {
						  if($(this).hasClass('active'))
						  {
							$(this).parent().nextUntil( '.level_'+level, "li" ).css('display','block');
						  }else{
							$(this).parent().nextUntil( '.level_'+level, "li" ).css('display','none');
						  }
					  }else{
						  $(this).parent().nextAll().slice(0,collapse).toggle();
					  }
				  }else{
					  $(this).parent().nextAll().slice(0,collapse).toggle();
				  }					
                }
            }
        });
        /* end for expand/collapse on more/less button click */
            /* start for set ajax on pagination*/
            $("#magebees_product_list_before")
            .nextUntil("#magebees_product_list_after")
            .find("a").each(function () {
                var link = $(this);
                var link_class= $(this).attr("class");
				var classes = [ "page","action  previous","action  next","action previous","action next"];
                var found_class = $.inArray(link_class,classes);
                if (found_class >-1) {
                    link.attr("onclick", "return false;");
                    var url = link.attr("href");
                    link.click(function () {
                        self.ajaxLayerNav(url);						
						var stickyHeader = jQuery(".page-header").height() + 50;
						if(jQuery("#mode-grid").length){
							var scrollPosition = jQuery("#mode-grid").offset().top - stickyHeader ;
						}
						if(jQuery("#mode-list").length){
							var scrollPosition = jQuery("#mode-list").offset().top - stickyHeader ;
						}
						jQuery([document.documentElement, document.body]).animate({
							scrollTop: scrollPosition
						}, 2000);
						
                    });
                }
            });/*end for set ajax on pagination*/
                        
			/* start for set ajax request on default filter apply in layer nav */
            $("#magebees_navigation_before")
            .nextUntil("#magebees_navigation_after")
            .find("a").each(function () {
                var link = $(this);
                if (link.attr("href") !== "#" ) {
                    if ($(this).closest('li').find('input[type=checkbox]').length) {
                            $(this).closest('li').find('input[type=checkbox]').click(function () {
                                var checkbox_url=$(this).closest('li').find('input[type=checkbox]').val();
                                /* ?p=1  remove parameter from URL */
								checkbox_url = self.removeURLParameter(checkbox_url,'p');
								self.ajaxLayerNav(checkbox_url);
                            });
                    }
                    if ($(this).closest('li').find('input[type=radio]').length) {
                        $(this).closest('li').find('input[type=radio]').click(function () {
							var radio_url=$(this).closest('li').find('input[type=radio]').val();
							/* ?p=1  remove parameter from URL */
							radio_url = self.removeURLParameter(radio_url,'p');
							self.ajaxLayerNav(radio_url);
						});
                    }
                    if (link.data('cat')=='category') {
                    link.click(function () {
                            window.location.href=link.attr("href");
                        });
                    } else {												   
                        link.attr("onclick", "return false;");
                        var url = link.attr("href");
                        /* link.click(function (e) { */
                        link.on('click', function (e) {							
							if($('#enable_varnish').attr('value'))
						   	{
								if($(this).hasClass('filter-clear'))
								{
									window.location.href=link.attr("href");
									return false;
								}
							}
                            e.preventDefault();
                            if ($(this).closest('li').find('input[type=checkbox]').length) {
                                if ($(this).closest('li').find('input[type=checkbox]').prop('checked')) {
                                    $(this).closest('li').find('input[type=checkbox]').prop('checked', false);
                                } else {
                                    $(this).closest('li').find('input[type=checkbox]').prop('checked', true);
                                }
                            }
                            
                            if ($(this).closest('li').find('input[type=radio]').length) {
                                if ($(this).closest('li').find('input[type=radio]').prop('checked')) {
                                    $(this).closest('li').find('input[type=radio]').prop('checked', false);
                                } else {
                                    $(this).closest('li').find('input[type=radio]').prop('checked', true);
                                }
                            }
							/* ?p=1  remove parameter from URL */
                            // url = url.replace(/&p=[^&;]*/,'');   
							url = self.removeURLParameter(url,'p');							
                            self.ajaxLayerNav(url);
                            
                        });
                    }
                }
            });
        /*end for set ajax request on default filter apply in layer nav */
        
        /*start for set ajax request on top filter apply in layer nav */
            $("#magebees_navigation_top_before")
            .nextUntil("#magebees_navigation_top_after")
            .find("a").each(function () {                
                var link = $(this);
                if (link.attr("href") !== "#" ) {
                    if ($(this).closest('li').find('input[type=checkbox]').length) {
                            $(this).closest('li').find('input[type=checkbox]').click(function () {
                                var checkbox_url=$(this).closest('li').find('input[type=checkbox]').val();
                                 /* ?p=1  remove parameter from URL */
								 checkbox_url = self.removeURLParameter(checkbox_url,'p');							
								self.ajaxLayerNav(checkbox_url);
                            });
                    }
                    if ($(this).closest('li').find('input[type=radio]').length) {
                        $(this).closest('li').find('input[type=radio]').click(function () {                        
                                var radio_url=$(this).closest('li').find('input[type=radio]').val();
                                /* ?p=1  remove parameter from URL */
								radio_url = self.removeURLParameter(radio_url,'p');							
								self.ajaxLayerNav(radio_url);
                            });
                    }
                    if (link.data('cat')=='category') {
                    link.click(function () {
                            window.location.href=link.attr("href");
                        });
                    } else {
                        link.attr("onclick", "return false;");
                        var url = link.attr("href");
                        link.click(function (e) {
                             e.preventDefault();
                            if ($(this).closest('li').find('input[type=checkbox]').length) {
                                if ($(this).closest('li').find('input[type=checkbox]').prop('checked')) {
                                    $(this).closest('li').find('input[type=checkbox]').prop('checked', false);
                                } else {
                                    $(this).closest('li').find('input[type=checkbox]').prop('checked', true);
                                }
                            }                            
                            if ($(this).closest('li').find('input[type=radio]').length) {
                                if ($(this).closest('li').find('input[type=radio]').prop('checked')) {
                                    $(this).closest('li').find('input[type=radio]').prop('checked', false);
                                } else {
                                    $(this).closest('li').find('input[type=radio]').prop('checked', true);
                                }
                            }
                            /* ?p=1  remove parameter from URL */
							url = self.removeURLParameter(url,'p');														
                            self.ajaxLayerNav(url);                            
                        });
                    }
                }
            });
        /*end for set ajax request on top filter apply in layer nav */
        },		
        highlight: function (string , obj) {
            var matchStart = obj.text().toLowerCase().indexOf("" + string.toLowerCase() + "");
            var matchEnd = matchStart + string.length - 1;
            var img1 = '';
            var img2 = '';
            var beforeMatch = obj.text().slice(0, matchStart);
            if (obj.children('img').get(0) !== undefined) {
                img1 = obj.children('img').get(0).outerHTML
            }
            if (obj.children('img').get(1) !== undefined) {
                img2 = obj.children('img').get(1).outerHTML
            }
            var matchText = obj.text().slice(matchStart, matchEnd + 1);
            var afterMatch = obj.text().slice(matchEnd + 1);
            obj.html(img1 + img2 + beforeMatch + "<em>" + matchText + "</em>" + afterMatch);
        },        
        initFilterNavigation: function () {
            var self=this;        
            /*start for set ajax request on dropdown value select in layer nav */
            var dropdown = $(".magebees_select").attr("dropdown");
            var dropdownurl = $(".magebees_select").attr("dropdown_url");            
            if (dropdown=='on') {
            if ($(".magebees_select").data('cat')=='category') {
                window.location.href=dropdownurl;
                } else {
					/* ?p=1  remove parameter from URL */
					dropdownurl = self.removeURLParameter(dropdownurl,'p');
                    self.ajaxLayerNav(dropdownurl);
                }
            }            
            /*end for set ajax request on dropdown value select in layer nav */
        },
		gridSorterOptions: function () {
			var self=this;
			jQuery("#sorter").change(function (e) {
				 e.stopImmediatePropagation();
				var order = this.value;
				var currentURL =  window.location.href;
				var params = new window.URLSearchParams(currentURL);
				var isExists = params.get('product_list_order');
				currentURL = self.removeURLParameter(currentURL,'product_list_order');
				if (currentURL.indexOf("?") > -1) {
					currentURL = currentURL+"&product_list_order="+order;
				}else{
					currentURL = currentURL+"?product_list_order="+order;
				}
				currentURL = currentURL.replace('#','');
				currentURL = currentURL.replace(';','');
				history.pushState({}, "", currentURL);				
				self.ajaxLayerNav(currentURL);
				}); 
		},
		gridSorterAction: function () {
			var self=this;
			jQuery(".sorter-action").prop("href", "#;");
			jQuery(".sorter-action").click(function (e) {
				e.stopImmediatePropagation();
				var currentURL =  window.location.href;
				var dataValue = jQuery(this).attr('data-value');
				var hrefValue = jQuery(this).attr('href');
				currentURL = currentURL.replace('/'+hrefValue+'/', '');
				currentURL = self.removeURLParameter(currentURL,'product_list_dir');
				if (currentURL.indexOf("?") > -1) {
					currentURL = currentURL+"&product_list_dir="+dataValue;
				}else{
					currentURL = currentURL+"?product_list_dir="+dataValue;	
				}
				currentURL = currentURL.replace('/'+hrefValue+'/', '');
				currentURL = currentURL.replace('#','');
				currentURL = currentURL.replace(';','');
				history.pushState({}, "", currentURL);
				self.ajaxLayerNav(currentURL);
				}); 				
		},
		gridLimiterAction: function () {
			var self=this;
			jQuery(".toolbar-products > .limiter > .control > #limiter").change(function (e) {
				 e.stopImmediatePropagation();
				var limit = this.value;
				var currentURL =  window.location.href;
				var params = new window.URLSearchParams(currentURL);
				jQuery(".toolbar-products > .limiter > .control > #limiter option").each(function(){
					var limiter_option = jQuery(this).val();
					var search_option_question = '?product_list_limit='+limiter_option;
					var search_option_and = '&product_list_limit='+limiter_option;				 
					if (currentURL.indexOf(search_option_question) > -1) {
						currentURL = currentURL.replace(search_option_question, "");
					}else if (currentURL.indexOf(search_option_and) > -1) {
						currentURL = currentURL.replace(search_option_and, "");
					}				
				});
				if (currentURL.indexOf("?") > -1) {
					currentURL = currentURL+"&product_list_limit="+limit;
				}else{
					currentURL = currentURL+"?product_list_limit="+limit;
				}
				/* ?p=1  remove parameter from URL */
				currentURL = self.removeURLParameter(currentURL,'p');
				currentURL = currentURL.replace('#','');
				currentURL = currentURL.replace(';','');
				history.pushState({}, "", currentURL);
				self.ajaxLayerNav(currentURL);
				});
		},
		removeURLParameter: function (url, parameter) {
				var urlparts= url.split('?');   
				if (urlparts.length >= 2) {
				var prefix= encodeURIComponent(parameter)+'=';
				var pars= urlparts[1].split(/[&;]/g);
				for (var i= pars.length; i-- > 0;) {    
					if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
						pars.splice(i, 1);
					}
				}
				url= urlparts[0]+'?'+pars.join('&');
				return url;
			} else {
				return url;
			}
		},
		gridSwitchList: function () {
            var self=this;
			if(jQuery("#mode-grid").length){
				jQuery("#mode-grid").prop("href", "#;");
			}
			if(jQuery("#mode-list").length){
				jQuery("#mode-list").prop("href", "#;");
			}			
            jQuery(".mode-grid").click(function (e) {
				e.stopImmediatePropagation();
				var currentURL =  window.location.href;
				var hrefValue = jQuery(this).attr('href');
				currentURL = currentURL.replace('/'+hrefValue+'/', '');
				currentURL = currentURL.replace('#','');
				currentURL = currentURL.replace(';','');
				history.pushState({}, "", currentURL);
				if (!jQuery(this).hasClass('active')){
					jQuery('.mode-grid').addClass('active');
					jQuery('.mode-list').removeClass('active');
					var hrefValue = jQuery(this).attr('href');
					currentURL = currentURL.replace('/'+hrefValue+'/', '');
					currentURL = currentURL.replace('#','');
					currentURL = currentURL.replace(';','');
					history.pushState({}, "", currentURL);
					currentURL =  window.location.href;
					if (window.location.href.indexOf("?product_list_mode=list") > -1) {
						var currentURL =  window.location.href.replace('?product_list_mode=list','');
					}
					if (window.location.href.indexOf("&product_list_mode=list") > -1) {
						var currentURL = window.location.href.replace('&product_list_mode=list','');
					}					
					currentURL = currentURL.replace('/&/', '?');
					self.ajaxLayerNav(currentURL);
					} 
				});
				jQuery(".mode-list").click(function (e) {
				e.stopImmediatePropagation();
				var currentURL =  window.location.href;
				var hrefValue = jQuery(this).attr('href');
				currentURL = currentURL.replace('/'+hrefValue+'/', '');
				currentURL = currentURL.replace('#','');
				currentURL = currentURL.replace(';','');
                
                if (!jQuery(this).hasClass('active')){
					jQuery('.mode-list').addClass('mode-list active');
					jQuery('.mode-grid').removeClass('mode-grid active');
					window.location.href.replace('#;','');
					var currentURL = window.location.href;					
					var hrefValue = jQuery(this).attr('href');
					currentURL = currentURL.replace('/'+hrefValue+'/', '');
					currentURL = currentURL.replace('#','');
					currentURL = currentURL.replace(';','');
					currentURL =  window.location.href;
					currentURL = currentURL.replace('/&/', '?');
						if((currentURL.includes('?'))||(currentURL.includes('&'))){
						  currentURL = currentURL + '&product_list_mode=list';
						}else{
							currentURL = currentURL + '?product_list_mode=list';
						}
					self.ajaxLayerNav(currentURL);
				}
			});
		},
		
		errorLayer:function()
		{
			document.getElementById('loadingLayer').style['display']='none';
			  $("body").append("<div id=filter_error style=display:none;>Something went wrong when applying this filter, please contact administrator of this website.</div>");
				 $("#filter_error").alert({
					title: "Warning",
					content: " ",
					autoOpen:true
				});	
		},
		dropChange:function()
		{
			$(document).on('change', '.magebees_select', function() {
				$(".magebees_select").attr( "dropdown", "on" );			
				$(".magebees_select").attr("dropdown_url",this.value);
				var widget = $(this).layeredNav({});
			});	
		},
		
		/*getInfiniteScrollConfig: function () {
            var configElement = document.getElementById('infinite-scroll-config');
            return JSON.parse(configElement.getAttribute('data-config'));
        },*/

        applyInfiniteScroll: function () {
			var configElement = document.getElementById('infinite-scroll-config');
			if (!configElement) return;

			var config = JSON.parse(configElement.dataset.config);

			$(".pocotheme.products-grid, .pocotheme.products-list").magebeesInfiniteScroll({
				loading_type: config.loading_type,
				product_container: config.product_container,
				page_number: config.show_page_no,
				load_next_text: config.label_next_button,
				load_prev_text: config.label_prev_button,
				load_button_style: config.button_style,
				threshold: config.threshold,
				text_no_more: config.text_no_more
			});
		},

        ajaxLayerNav: function (url) {
            var self=this;	
            document.getElementById('loadingLayer').style['display']='block';
            $('body').addClass('stop-scrolling');
            var productListBlock = $("#magebees_product_list_before").attr("data-block-name");            
            if (productListBlock==null) {
                  window.location.href=url;
            }
            var navigationBlock = $("#magebees_navigation_before").attr("data-block-name");
            var blockNameParams = "&productListBlock=" + productListBlock +
                "&navigationBlock=" + navigationBlock;
            var ajaxUrl=url;            
           if (ajaxUrl.indexOf("?") > -1 ) {
                ajaxUrl += "&magebeesAjax=1" + blockNameParams;
            } else {
                ajaxUrl += "?magebeesAjax=1" + blockNameParams;
            }
            $.ajax({
                url:url,
                cache:true,
				method: 'GET',
				data: {magebeesAjax:1,productListBlock:productListBlock,navigationBlock:navigationBlock},
                dataType: "json",
				success: function(data){  
                document.getElementById('loadingLayer').style['display']='none';
				$('body').removeClass('stop-scrolling');
				if(data!=null)
				{
					history.pushState({}, "", url);
					var productListContent = data.list_product;
					var leftNavContent = data.left_nav_content;
					$("#magebees_product_list_before")
                    .nextUntil("#magebees_product_list_after")
                    .remove();
					$(productListContent).insertAfter($("#magebees_product_list_before"));
					$(".product-items").trigger('contentUpdated');
					
					// Trigger Infinite Scroll after AJAX Success
					if($('#enable_ajaxscroll').attr('value')=="1"){
						self.applyInfiniteScroll();
					}	
					
					/*ajax scroll attribute assign */
					/****** Start Add For Compatibility With Product Label Extension************/
					if ($('.prodLabel').length) {
						$(".products > .product").each(function () {
							var labeldiv = $(this).find(".prodLabel");
							var parentdiv = $(this).find(".product-item-photo");
							$(parentdiv).append(labeldiv);
							$('.prodLabel').show();
						});
					}
					/*******End Add For Compatibility With Product Label Extension*************/
					$("#magebees_navigation_before")
						.nextUntil("#magebees_navigation_after")
						.remove();
					$(leftNavContent).insertAfter($("#magebees_navigation_before"));
					if (data.top_nav_content) {
					var topNavContent = data.top_nav_content;
						$("#magebees_navigation_top_before")
						.nextUntil("#magebees_navigation_top_after")
						.remove();
						$(topNavContent).insertAfter($("#magebees_navigation_top_before"));
					}
					$("#magebees_navigation_top_before")
					.nextUntil("#magebees_navigation_top_after").find('.filter-current').hide();
					$("#magebees_navigation_top_before")
					.nextUntil("#magebees_navigation_top_after").find('.filter-clear').hide();
					$(document).attr("title",data.title);
					$(mage.apply);
					
						self.initAjaxLayerNavigation();
					
				
					self.gridSwitchList();
					self.gridSorterOptions();
					self.gridSorterAction();
					self.gridLimiterAction();
					
						 }else{
						 self.errorLayer();
					 }
					if($('#enable_varnish').attr('value'))
					{
						var count=0;
						var refreshHref='';
						$('body').find('a').each(function() {
						var lhref = $(this).attr('href');
						if(typeof(lhref)!="undefined")
						{
							lhref = lhref.replace('?magebeesAjax=1&', '?');
							lhref = lhref.replace('?magebeesAjax=1;', '');
							lhref = lhref.replace('&magebeesAjax=1&', '');
							$(this).attr('href',lhref); 
						}
						var aclass = $(this).attr('class');
						if(typeof(aclass)!="undefined")
						{							
							if($(this).hasClass('filter-clear'))
							{
								count++;
								var refreshHref=$(this).attr('href');
								$(this).removeAttr("onclick");	
							}
						}
					});
				 	if(count==0)
					{
						window.location.href=refreshHref;
					}
				 }                
            },
			 error: function(XMLHttpRequest, textStatus, errorThrown) { 
				$('body').removeClass('stop-scrolling');  
				self.errorLayer();
			}
		});
        }
    });
	return $.magebees.layeredNav;
});