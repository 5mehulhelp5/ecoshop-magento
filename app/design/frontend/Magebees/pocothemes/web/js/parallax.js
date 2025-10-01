/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'domReady!'
], function (jQuery) {
    'use strict';
    
    jQuery.widget('Magebees.parallax', {
         _create: function() {
            var self = this;
            this.resizeAllParallax();
            jQuery(window).on('resize', function(){
                self.resizeAllParallax();
            });
        },
        
        resizeAllParallax: function() {
            var div_id_opp = 'half-col'; /* opposite div class */
            var div_id = 'img_parallax'; /* the ID of the div that you're resizing */
            var img_w = 860; /* the width of your image, in pixels */
            var img_h = 860; /* the height of your image, in pixels */
            this.resizeParallax(div_id,div_id_opp, img_w,img_h);
        },

        resizeParallax: function(div_id,div_id_opp,img_w,img_h) {
            var div = jQuery('.' + div_id);
            var divwidth = div.width();

            var div_id_opp = jQuery('.' + div_id_opp);
            var div_id_opp_width = div_id_opp.width();
            
            if (divwidth < 769) { var pct = (img_h/img_w) * 112; } /* show full image, plus a little padding, if on static mobile view */
            else { var pct = 90; } /* this is the HEIGHT as percentage of the current div WIDTH. you can change it 
            to show more (or less) of your image */

            var newheight = Math.round(divwidth * (pct/100));
            var newheight_opp = div_id_opp.height();
            
            newheight = newheight  + 'px';
            newheight_opp = 860; //newheight_opp + 'px';
            div.height(newheight_opp);

            if(newheight_opp > divwidth){
                newheight_opp = newheight_opp;
            }else{
                if(divwidth > 860){
                    newheight_opp = divwidth;  
                }else{
                    newheight_opp = 860;
                }
                div.css("height",'860px');
               
            }

            if(div_id_opp_width > newheight_opp){
              //newheight_opp = 960;
            }

            div.css("background-size", newheight_opp + 20 + 'px');

            var windowwidth = jQuery(window).width();
            var flagfortextheight = true;

            if(windowwidth > 768 && windowwidth < 1025 ){
                div.css("height",'860px');
            }
            if(windowwidth < 1000 ){
                jQuery(".parallax-image").css("display","none");
                jQuery(".content-text-right").css("width","100%");
                jQuery(".content-text-left").css("width","100%");
                jQuery( ".parallax-text-right" ).each(function( index ) {
                    jQuery(".parallax-text-right").eq(index).css("background-image",  jQuery(".parallax-img-right").eq(index).css("background-image"));
                
                });
                jQuery( ".parallax-text-left" ).each(function( index ) {
                    jQuery(".parallax-text-left").eq(index).css("background-image",   jQuery(".parallax-img-left").eq(index).css("background-image"));
                });
                jQuery(".parallax-text-right").css("background-size",  "cover");
                jQuery(".parallax-text-left").css("background-size",  "cover");
              
                jQuery(".feature-row__text").css("min-height","auto");
                div_id_opp.css("height",'');
            }else{
                jQuery(".parallax-image").css("display","");
                jQuery(".content-text-right").css("width","50%");
                jQuery(".content-text-left").css("width","50%");
                jQuery(".parallax-text-right").css("background-image",  "");
                jQuery(".parallax-text-left").css("background-image",   "");
          
                if(flagfortextheight){
                    if(windowwidth > 1436 ){
                        jQuery(".feature-row__text").css("height","860");
                    }else{
                        jQuery(".feature-row__text").css("height","860");
                    }
                }else{
                    jQuery(".feature-row__text").css("height","860");
                }
            }
        }
 
    });
    return jQuery.Magebees.parallax;
    
});
