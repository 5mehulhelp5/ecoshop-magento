define([
        'jquery',
        'Magento_Ui/js/modal/modal',
        'jquery-ui-modules/core','jquery-ui-modules/widget',
        'jquery/validate',
        'Magebees_Onepagecheckout/js/magebees/plugins/jquery.nicescroll.min'
    ],
    function ($, modal) {
        'use strict';
        $.widget('mage.magebeesOnestepcheckout', {
            popup: null,
            init: function () {
                this.showModal();
                this.inputText();
                this.cvvText();
                this.sendForm();
                this.newModal();
            },

            inputText: function () {
                $(document).off('blur', '#authorizenet_directpost_cc_number');
                $(document).on('blur', '#authorizenet_directpost_cc_number', function (e) {
                    if ($('#authorizenet_directpost_cc_number').val() == 0) {
                        $(this).closest('div.number').find('label').removeClass('focus');
                    }
                });

                $(document).off('focus', '#authorizenet_directpost_cc_number');
                $(document).on('focus', '#authorizenet_directpost_cc_number', function (e) {
                    $(this).closest('div.number').find('label').addClass('focus');
                });
            },
            cvvText: function () {
                $(document).off('blur', '#authorizenet_directpost_cc_cid');
                $(document).on('blur', '#authorizenet_directpost_cc_cid', function (e) {
                    if ($('#authorizenet_directpost_cc_cid').val() == 0) {
                        $(this).closest('div.cvv').find('label').removeClass('focus');
                    }
                });
                $(document).off('focus', '#authorizenet_directpost_cc_cid');
                $(document).on('focus', '#authorizenet_directpost_cc_cid', function (e) {
                    $(this).closest('div.cvv').find('label').addClass('focus');
                });
            },
            showModal: function () {
                var _self = this;
                $(document).off('click touchstart', '.actions-toolbar .remind');
                $(document).on('click touchstart', '.actions-toolbar .remind', function (e) {
                    e.preventDefault();
                    $('.magebees-Onepagecheckout-forgot-response-message').hide();
                    _self.displayModal();
                });
            },
            newModal: function(){
                var _self = this;
                $(document).on('click touchstart', '.actions-toolbar .remind', function (e) {
                    e.preventDefault();
                    _self.reopenModal();
                });
            },
            reopenModal: function () {
                $(".magebees-Onepagecheckout-forgot-main-wrapper").modal('openModal');
            },
            displayModal: function () {
                var modalContent = $(".magebees-Onepagecheckout-forgot-main-wrapper");
                this.popup = modalContent.modal({
                    autoOpen: true,
                    type: 'popup',
                    modalClass: 'magebees-Onepagecheckout-forgot-wrapper',
                    title: '',
                    buttons: [{
                        class: "magebees-hidden-button-for-popup",
                        text: 'Back to Login',
                        click: function () {
                            $('.magebees-Onepagecheckout-forgot-response-message').hide();
                            this.closeModal();
                        }
                    }]
                });
            },
            sendForm: function(){
                $('.magebees-forgot-password-submit').click(function(e){
                    e.preventDefault();
                    var email = $('.magebees-Onepagecheckout-forgot-email').val();
                    var validator = $(".magebees-Onepagecheckout-forgot-form").validate();
                    var status = validator.form();
                    if (!status) {
                        return;
                    }
                    if (typeof(postUrl) != "undefined") {
                        var sendUrl = postUrl;
                    } else {
                        console.log("Magebees post url error.");
                    }
                    $.ajax({
                        type: "POST",
                        data: {email: email},
                        url: sendUrl,
                        showLoader: true
                    }).done(function (response) {
                        if(typeof(response.message != "undefined")){
                            $('.magebees-Onepagecheckout-forgot-response-message').html(response.message);
                            $('.magebees-Onepagecheckout-forgot-email').val('');
                            $('.magebees-Onepagecheckout-forgot-response-message').show();
                        }
                    });
                });
            }
        });
        return $.mage.magebeesOnestepcheckout;
    });