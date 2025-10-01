define([
    'jquery',
    'underscore',
    'mage/template',
    'jquery-ui-modules/core',
	'jquery-ui-modules/widget',
    'mage/translate',
	"alignProductSection"
], function ($, _, mageTemplate,alignProducts) {
    'use strict';
    var myTimeout;
   
    function isEmpty(value)
    {
        
        return (value.length === 0) || (value == null) || /^\s+$/.test(value);
    }
    
     $.widget('magebees_ajaxquicksearch.ajaxQuickSearch', {
  
        options: {
            autocomplete: 'off',
            //minSearchLength: 2,
            responseFieldElements: 'ul li',
            selectClass: 'selected',
            submitBtn: 'button[type="submit"]',
			autoheight:true,
            searchLabel: '[data-role=minisearch-label]'
        },

        _create: function () {
                
    var self=this;
            var enable_recent_click=this.options.enableRecentClick;
            var enable_recent=this.options.enableRecent;
            var cat_sel=this.options.selectedCategory;
            var rtl=this.options.rightToLeft;
            
            if (rtl==1) {
            $('#search_mini_form').addClass('rtl');
            }
            
            
            document.getElementById('search_loading').style['display']='none';
            $('#category').val(cat_sel).attr("selected", "selected");
            $('#category').change(function () {
                    $('#search').val("");
                    
            });
            
           /* $("#category").each(function () {
            $(this).wrap("<span class='select-wrapper'></span>");
            $(this).after("<span class='holder'>All</span>");
            });*/
            
            $("#category").change(function () {
                var selectedOption = $(this).find(":selected").text();
              
			   // $(".holder").text($.trim(selectedOption));
			   
			   if (selectedOption == null || $.trim(selectedOption) === ''){
  
			   }else{
				   $("#cat_holder").text($.trim(selectedOption));
			   }
			   
			   
			   
			   
            }).trigger('change');
            
            
            this.responseList = {
                indexList: null,
                selected: null
            };
            this.autoComplete = $(this.options.destinationSelector);
            this.searchForm = $(this.options.formSelector);
            this.submitBtn = this.searchForm.find(this.options.submitBtn)[0];
            this.searchLabel = $(this.options.searchLabel);
           
            _.bindAll(this, '_onKeyDown', '_onPropertyChange', '_onSubmit');

            
            this.submitBtn.disabled = true;
             this.element.click(function () {
                 if (enable_recent==1) {
                 if (enable_recent_click==1) {
                     var recent_content=$(".recent_content").html();
                        
                        var searchtext=$(this).val();
                        if (!searchtext) {
                        $('#search_autocomplete').html(recent_content);
                        }
                         if ($('#search_autocomplete').find('#ajax_recent').length) {
                         document.getElementById("search_autocomplete").style['display']='block';
                         }
                     }
                 }
            });

            this.element.attr('autocomplete', this.options.autocomplete);

            $("body").click(function (e) {
                if ((e.target.className == "action tocart primary") || (e.target.id == "search")) {
                if ($('#search_autocomplete').find('#ajax_recent').length) {
                    self.autoComplete.show();
                        self._updateAriaHasPopup(true);
                    }
                } else {
                self.autoComplete.hide();
                   self._updateAriaHasPopup(false);
                }
              });

           this.element.on('focus', $.proxy(function () {
                this.searchLabel.addClass('active');
            }, this));
            this.element.on('keydown', this._onKeyDown);
            this.element.on('input propertychange', this._onPropertyChange);

            this.searchForm.on('submit', $.proxy(function () {
               
                this._updateAriaHasPopup(false);
            }, this));
            
            var searchLabel = $('#search_mini_form .label');
            searchLabel.on('click', function f(event)
            {
                searchLabel.toggleClass('active');
                event.preventDefault();
                event.stopPropagation();
                
                
            });
        },
       
        /**
         * @private
         * @param {Boolean} show Set attribute aria-haspopup to "true/false" for element.
         */
        _updateAriaHasPopup: function (show) {
            
            if (show) {
                this.element.attr('aria-haspopup', 'true');
            } else {
                this.element.attr('aria-haspopup', 'false');
            }
        },
        /**
         * Executes when the search box is submitted. Sets the search input field to the
         * value of the selected item.
         * @private
         * @param {Event} e - The submit event
         */
        _onSubmit: function (e) {
            
            var value = this.element.val();
            if (isEmpty(value)) {
                e.preventDefault();
            }
            if (this.responseList.selected) {
                this.element.val(this.responseList.selected.find('.qs-option-name').text());
            }
        },

        /**
         * Executes when keys are pressed in the search input field. Performs specific actions
         * depending on which keys are pressed.
         * @private
         * @param {Event} e - The key down event
         * @return {Boolean} Default return type for any unhandled keys
         */
        _onKeyDown: function (e) {
            
            var keyCode = e.keyCode || e.which;

            switch (keyCode) {
                case $.ui.keyCode.ESCAPE:
                    this.autoComplete.hide();
                    break;
                case $.ui.keyCode.ENTER:
                    this.searchForm.trigger('submit');
                    break;
                default:
                    return true;
            }
        },
        
        /**Use for sort the ul element as per configuration*/
        sortUsingNestedText: function (parent, childSelector, keySelector) {
            var items = parent.children(childSelector).sort(function (a, b) {
            var vA = $(keySelector, a).text();
            var IA=parseInt(vA);
             
            var vB = $(keySelector, b).text();
            var IB=parseInt(vB);
            return (IA < IB) ? -1 : (IA > IB) ? 1 : 0;
            });
                parent.append(items);
         },
         
         highlightText: function (searchtext,divSelector) {
         var array = searchtext.split(' ');
            var word='';
            for (var i=0; i<array.length; i++) {
                if (array[i]) {
                    $(divSelector).html(function (index,searchtext) {
                        var sametext =$(this).text();
                        
                        if (sametext.indexOf("searchtext")<=0) {
                            if ($(this).text()) {
                                var encode_array=array[i].replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
                                var regex = new RegExp('(' + encode_array + ')', 'g');
                                return searchtext.replace(regex,'<span class="searchText">'+ encode_array +'</span>');
                            } else {
                                return searchtext;
                            }
                        } else {
                            var rest =searchtext.substring(0, searchtext.lastIndexOf(">") + 1);
                            var last = searchtext.substring(searchtext.lastIndexOf(">") + 1, searchtext.length);
                            var encode_array=array[i].replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
                            var regex = new RegExp('(' + encode_array + ')', 'g');
                            var laststring= last.replace(regex,'<span class="searchText">'+ encode_array +'</span>');
                            var final=rest+laststring;
                            return final;
                        }
                    });
                }
            }
             
         },
		 autoHeightProductSection: function () {
			 if ($('body').find('.mbAutoSearch').length) {

			var self=this;
			self.alignProductGridActions();

				$(document).bind('DOMNodeInserted',function(e) {	
					setTimeout(function () {
						if(self.options.autoheight)
						{
					 $(window).trigger('delayed-resize', e);
						}
						self.options.autoheight=false;
						}, 1000);
				 });
				 
			$(window).on('load delayed-resize', function (e, resizeEvent) {
				self.alignProductGridActions();				
			});
			}
			 var resizeTimer;
		$(window).resize(function (e) {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function () {
				$(window).trigger('delayed-resize', e);
			}, 250);
		});
		
		 },
		 alignProductGridActions:function (){
		 	
			
			 $('#search_autocomplete').alignProducts({
					container : "#ajax_ul",
					item : '.item',
					elementlist: '.product-name,.ajxSku,.ajxDescription,.review,.price-box,.primary,.ajxRightDetails'
					});
				
				/*$('#search_autocomplete').alignProducts({
					container : "#ajax_ul",
					item : '.item',
					elementlist: '.ajxRightDetails,.product-name,.ajxDescription,.review'
					});
				*/	
						
			
	 },
        /**
         * Executes when the value of the search input field changes. Executes a GET request
         * to populate a suggestion list based on entered text. Handles click (select), hover,
         * and mouseout events on the populated suggestion list dropdown.
         * @private
         */
        _onPropertyChange: function () {
              document.getElementById("search_autocomplete").style.display = "none";
            var enable_product=this.options.enableProduct;
            var enable_suggested=this.options.enableSuggested;
            var enable_popular=this.options.enablePopular;
            var enable_recent=this.options.enableRecent;
            var enable_category=this.options.enableCategory;
            var enable_cms=this.options.enableCms;
            var suggest_title=this.options.suggestTitle;
            var suggest_limit=this.options.suggestLimit;
            var suggest_order=this.options.suggestOrder;
            var product_title=this.options.productTitle;
            var product_order=this.options.productOrder;
            var category_title=this.options.categoryTitle;
            var cms_title=this.options.cmsTitle;
            if (!suggest_title) {
            var suggest_title= "Suggested Search";
            }
            
            if (!product_title) {
            var product_title= "Products";
            }
			
			
            document.getElementById("search_autocomplete").innerHTML = "";
            clearTimeout(myTimeout);
            var searchField = this.element,
                clonePosition = {
                    position: 'absolute',
                    width: searchField.outerWidth()
                },
                            
                value = this.element.val();

            this.submitBtn.disabled = isEmpty(value);

            if (value.length >= parseInt(this.options.minSearchLength, 10)) {
                var self=this;
                var cat_id=$('#category').val();
                    if (cat_id==undefined) {
                        var ajax_url=self.options.url+'?q='+value;
                    } else {
                        var ajax_url=self.options.url+'?q='+value+'&cat_id='+cat_id;
                    }
                 myTimeout = setTimeout(function () {
                     
                    document.getElementById('search_loading').style['display']='block';
                $.ajax({
                url: ajax_url,
                data: {q: value,cat:cat_id},
                type: 'post',
                dataType: 'json',
                success: function (data) {
					self.options.autoheight=true;
					 var products=data.products.trim();
                    document.getElementById("search_autocomplete").innerHTML = "";
                        var querytext=value.replace(/ /g,"+");
					 if (products) {
                        if (enable_product==1) {
                    
              				 $('#search_autocomplete').append('<div id="mageb-search-auto-inner">');	
							if((enable_suggested==1)||(enable_popular==1)||(enable_recent==1)||(enable_category==1)||(enable_cms)==1){
							
                 			$('#mageb-search-auto-inner').append('<div id="search_autocomplete_first">');
							var search_result=$('#search_autocomplete_first');
							}
							else
							{								
								var search_result=$('#search_autocomplete'); 
							}
						}
						 else
						 {
							var search_result=$('#search_autocomplete'); 
						 }
					 }
					else
					{						
						var search_result=$('#search_autocomplete');
					}
					
                    document.getElementById('search_loading').style['display']='none';
                    if (enable_recent==1) {
                    if (data.recent) {
                        $(search_result).append(data.recent);                      
                            self.highlightText(value,'#search_autocomplete .recent_item>a');
                        }
                    }
                    
                    if (data.update_recent) {
                    $('.mbAutoSearch .recent_content').html(data.update_recent);
                    }
                    if (enable_popular==1) {
                    if (data.popular) {
                        $(search_result).append(data.popular);                     
                            self.highlightText(value,'#search_autocomplete .popular_item>a');
                        }
                    }
                    
                    if (enable_suggested==1) {
                    if (!jQuery.isEmptyObject(data.suggest)) {
                        if (suggest_limit!=0) {
                $(search_result).append('<ul id="ajax_suggest" class="searchTags"><li class="titleRow"><span class="order" style="display:none;">'+suggest_order+'</span><h6 id="suggested" class="mbSecTitle">'+$.mage.__(suggest_title)+'</li></ul>');
                             var key,count=0;
                            
                            for (key in data.suggest) {
                            if (data.suggest.hasOwnProperty(key)) {
                            var suggest_data=data.suggest[key];
                            var suggest_text=suggest_data.title.replace(/ /g,"+");
                            var suggest_url=data.url+'?q='+suggest_text;
                                
                                if (count<suggest_limit) {
                                $('#ajax_suggest').append('<li class="searchTag"><a href='+suggest_url+' id="title">'+suggest_data.title+'<span class="searchCount">'+suggest_data.num_results+'</span></a></h6></li></ul>');
                                                        self.highlightText(value,'#search_autocomplete .searchTag>a');
                                }
                            }
                            count++;
                            }
                            }
                        }
                    }
                    
                    if (enable_category==1) {
                        if (data.category) {
                            $(search_result).append(data.category);                      
                            self.highlightText(value,'#search_autocomplete .category_item>a');
                        }
                    }
                    if (enable_cms==1) {
                        if (data.cms) {
                            $(search_result).append(data.cms);                           
                            self.highlightText(value,'#search_autocomplete .cms_item>a');
                        }
                    }
					 $('#mageb-search-auto-inner').append('</div>');
					
                    
                     if (products) {
                        if (enable_product==1) {
                            if (cat_id==undefined) {
                                var result_url=data.url+'?q='+querytext;
                            } else {
                                var result_url=data.url+'?q='+querytext+'&category='+cat_id;
                            }
							 $('#mageb-search-auto-inner').append('<div id="search_autocomplete_second">');                         
                             $('#search_autocomplete_second').append('<ul><li class="titleRow"><span class="order" style="display:none;">'+product_order+'</span><h6 class="mbSecTitle product_title">'+$.mage.__(product_title)+'<span><a href='+result_url+' id="title">'+$.mage.__("See All")+'</a></span></h6></li></ul>');
                            $('#search_autocomplete_second').append(data.products);
                            var searchtext=value;
							self.autoHeightProductSection();
                            self.highlightText(searchtext,'#search_autocomplete .ajxRightDetails > p');
                        }
                     }
                    if (!data.recent && !data.popular && !products && !data.title && !data.cms && !data.category) {
                        $('#search_autocomplete').append('<ul><li id="no_record">'+$.mage.__("No record Found")+'</li></ul>');
                     } 
					else if (!products && !data.title && !data.cms && !data.category) {
                    $('#search_autocomplete').append('<ul><li id="no_record">'+$.mage.__("No Search Results Found")+'</li></ul>');
                    }
                    self.sortUsingNestedText($('#search_autocomplete'),"ul","span.order");
                    self.sortUsingNestedText($('#search_autocomplete_first'),"ul","span.order");
                     $('#search_autocomplete').trigger('contentUpdated');
                    self.element.removeAttr('aria-activedescendant');
                    document.getElementById("search_autocomplete").style.display = "block";
                    
                    var searchinput = $(".searchField #search").width();
                    var searchautobox = $(".mbAutoSearch .search-autocomplete").width();
                    if (searchautobox > searchinput ) {
                        $(".mbAutoSearch").addClass("right");
                    }
                },				error: function (jqXHR, textStatus, errorThrown) {                  if (jqXHR.status == 500) {                      					  alert('Unexpected error, please check your search term.');                  } else {                      alert('Unexpected error, please check your search term.');                  }				  document.getElementById('search_loading').style['display']='none';				}
                });
                }, 1000);
            } else {
                this.autoComplete.hide();
                this._updateAriaHasPopup(false);
                this.element.removeAttr('aria-activedescendant');
            }
        }
    });

    return $.magebees_ajaxquicksearch.ajaxQuickSearch;
});

