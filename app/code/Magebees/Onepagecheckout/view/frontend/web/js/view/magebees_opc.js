require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
    'use strict';

    $(function () {  // CHANGED: wrapped everything in DOM ready to ensure elements exist

        /**
         * ========================
         * Login Popup
         * ========================
         */
        var $popupLogin = $('#popup-login');      // CHANGED: cached selector
        var $opcLoginBtn = $('#opcLogin');        // CHANGED: cached selector

        if ($popupLogin.length && $opcLoginBtn.length) {   // CHANGED: only run if popup + button exist
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: [{
                    text: $.mage.__('Close'),
                    class: 'opcloginpopup',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };

            modal(options, $popupLogin);   // CHANGED: safe initialization

            $opcLoginBtn.on('click', function () {
                $popupLogin.modal('openModal');
            });
        }

        /**
         * ========================
         * Forgot Password Toggle
         * ========================
         */
        if ($('#forgotform').length) {  // CHANGED: added existence check
            $('#forgotform').on('click', function () {
                $('#Onepagecheckout-forgot-popup').show();
                $('#Onepagecheckout-login-popup').hide();
            });
        }

        if ($('#loginback').length) {  // CHANGED: added existence check
            $('#loginback').on('click', function () {
                $('#Onepagecheckout-forgot-popup').hide();
                $('#Onepagecheckout-login-popup').show();
            });
        }

        /**
         * ========================
         * Forgot Password Submit
         * ========================
         */
        if ($('#forgot_form').length) {  // CHANGED: added existence check
            var url = $('#forgoturl').val();

            $('#forgot_form').on('click', '#reset_password', function () {
                var email = $('#osc_email_address').val();

                if (!email) {
                    alert('Please enter your email.');
                    return;
                }
                if (!isEmail(email)) {
                    alert('Please enter a valid email address (Ex: johndoe@domain.com).');
                    return;
                }

                $.ajax({
                    url: url,
                    data: {email: email},
                    dataType: 'json',
                    type: 'post',
                    showLoader: true,
                    success: function (data) {
                        $('#result').html(data);
                    }
                });
            });
        }

        /**
         * ========================
         * Login Form Ajax Submit
         * ========================
         */
        if ($('#mg-login-form').length) {  // CHANGED: added existence check
            $('#mg-login-form').on('submit', function (e) {
                e.preventDefault();  // CHANGED: moved preventDefault up for safety

                var $form = $(this);

                $('.opcerrwrap').hide();

                if (!$('#mg-login-form #email').val() || !$('#mg-login-form #pass').val()) {
                    return;
                }

                if ($('#mg-login-form #captcha_user_login').length &&
                    !$('#mg-login-form #captcha_user_login').val()) {
                    return;
                }

                if ($('#mg-login-form #g-recaptcha-response').length &&
                    !$('#mg-login-form #g-recaptcha-response').val()) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    showLoader: true,
                    success: function (response) {
                        if (response.error === true) {
                            if (response.type === 'captcha') {
                                setTimeout(function () {
                                    $('#Onepagecheckout-login-popup .captcha-reload').trigger('click');
                                }, 3000);
                            }
                            $('.opcerrormsg').html(response.message);
                            $('.opcerrwrap').show();
                        } else {
                            location.reload();
                        }
                    }
                });
            });
        }

        /**
         * ========================
         * Email Validation
         * ========================
         */
        function isEmail(email) {  // CHANGED: moved inside require block (was global before)
            var regex = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

    }); // CHANGED: closed DOM ready
});
