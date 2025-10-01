require([
    "jquery"
], function($){
    "use strict";
    $(document).ready(function(){
        $('body').append(
            '<div id="magebees_free_Onepagecheckout_locked_popup_container">' +
            '<div class="magebees_free_Onepagecheckout_locked_mask"></div>' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up">' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_close"><i class="fa fa-times" aria-hidden="true"></i></div>' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_title_img"></div>' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_content">' +
            'Magebees’s Checkout Suite enhances your checkout experience to make the process quicker, while still offering a suite of robust features to beef up your orders. Upgrade to Pro today for these great features:' +
            '</div>' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_features">' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_one_feature">In-Store Pickup</div>' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_one_feature">Store Credits</div>' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_one_feature">Customer Support</div>' +
            '<div class="magebees_free_Onepagecheckout_locked_pop_up_one_feature">Installation & Upgrades</div>' +
            '</div>' +
            '<div class="clear"></div>' +
            '<a title="Unlock Pro" href="https://www.magebeesagency.com/extensions/one-step-page-checkout.html?add" target="_blank" class="magebees_free_Onepagecheckout_locked_button_small"><i class="fa fa-lock" aria-hidden="true"></i>Unlock Pro</a>' +
            '</div>' +
            '</div>'
        );
        $(document).on('click', '.magebees_free_Onepagecheckout_locked_button', function () {
            $('#magebees_free_Onepagecheckout_locked_popup_container').css('height', $('html').height()+'px');
            $('#magebees_free_Onepagecheckout_locked_popup_container').show();
            $('html, body').animate({
                scrollTop: $(".magebees_free_Onepagecheckout_locked_pop_up").offset().top -
                ($('.page-actions._fixed').length?($('.page-actions._fixed').height()+5):85)
            }, 500);
        });
        $(document).on('click', '.magebees_free_Onepagecheckout_locked_pop_up_close', function () {
            $('#magebees_free_Onepagecheckout_locked_popup_container').hide();
        });
        $(window).on('keydown', function(e){
            if(e.keyCode == 27){
                $('#magebees_free_Onepagecheckout_locked_popup_container').hide();
            }
        });
    });
});