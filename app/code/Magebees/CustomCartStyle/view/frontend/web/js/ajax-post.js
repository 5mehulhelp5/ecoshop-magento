/**
 * Copyright © 2019 Magebees, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/confirm',
    'jquery-ui-modules/widget',
    'defaultMageDataPost'
], function($, mageTemplate, uiConfirm) {
    if (typeof window.pocoBuilAjaxPost == 'undefined') {
        window.pocoBuilAjaxPost = true;
        var dataPost = $(document).data('mageDataPost');
        if (typeof dataPost != 'undefined') {
            var isCompareIndexPage = function() {
                return $('body').hasClass('catalog-product_compare-index');
            }
            
            var $loginPopup = $('<div class="ajax-lgfm-popup md-dialog-container account-popup" id="ajax-lgfm-popup" data-pocopopup>').appendTo('body');
            $('body').trigger('pocoBuildPopup');
            $loginPopup.parents('.popup-ajax-lgfm-popup').first().addClass('popup-account-popup');
            dataPost.ajaxActions = {};
            dataPost.$msgContainer = $('<div class="poco-msg-container fixed">').appendTo('body');
            dataPost.isAjaxAction = function(action) {
                if (typeof this.ajaxActions[action] == 'undefined') {
                    var self = this;
                    self.ajaxActions[action] = false;
                    $.each(pocoAjaxPost.replacedActions, function(needSearch, replacement) {
                        if (action.search(needSearch) > -1) {
                            self.ajaxActions[action] = true;
                            return false;
                        }
                    });
                }
                return this.ajaxActions[action];
            };
            dataPost.oldPostData = dataPost.postData;
            dataPost.filterAction = function(action) {
                $.each(pocoAjaxPost.replacedActions, function(needSearch, replacement) {
                    if (action.search(needSearch) > -1) {
                        action = replacement;
                        return false;
                    }
                });
                return action;
            };
            dataPost.updateMessages = function(messages) {
                var self = this;
                if (messages) {
                    self.displayMessages(messages);
                } else {
                    $.get(pocoAjaxPost.updateMsgUrl, {_: $.now()}, function(rs) {
                        if (rs.messages) {
                            if (rs.messages.messages) {
                                self.displayMessages(rs.messages.messages);
                            }
                        }
                    });
                }
            };
            dataPost.displayMessages = function(messages) {
                var self = this;
                self.$msgContainer.show();
                $.each(messages, function(i, msg) {
                    var $message = $('<div class="message poco-translator">').addClass(msg.type).html('<span>' + msg.text + '</span>').prependTo(self.$msgContainer);
                    setTimeout(function() {
                        $message.fadeOut(2000, 'swing', function() {
                            $message.remove();
                            if (!self.$msgContainer.children().length) {
                                self.$msgContainer.hide();
                            }
                        });
                    }, 3000);
                });
            };
            dataPost.ajaxPost = function(params) {
                var self = this;
                var formKey = $(this.options.formKeyInputSelector).val();
                if (formKey) {
                    params.data['form_key'] = formKey;
                }
                var action = self.filterAction(params.action);
                var postParam = params;
                if (self.notLogin && (action.search('wishlist') > -1)) {
                    self._displayLoginForm(false, postParam);
                } else {
                    postParam.data.currentUrl = document.URL;
                    if (isCompareIndexPage()) {
                        postParam.data.isCompareIndexPage = true;
                    }
                    $('body').addClass('poco-ajaxpost-proccessing');
                    $.ajax({
                        url: action,
                        data: postParam.data,
                        type: 'POST',
                        showLoader: true,
                        success: function(rs) {
                            if (rs.message) {
                                self.displayMessages([{
                                    type: (rs.success?'success':'error'),
                                    text: rs.message
                                }]);
                            } else {
                                self.updateMessages();
                            }
                            if (rs.login_form_html) {
                                self.notLogin = true;
                                self._displayLoginForm(rs.login_form_html, postParam, rs.after_login_url);
                            }
                            
                            if (action.search('wishlist/remove') > -1) {
                                $('body').trigger('pocoWishlistItemRemoved', [params, rs]);
                            }
                            if ((action.search('wishlist/fromcart') > -1) || (action.search('wishlist/moveallfromcart') > -1)) {
                                $('body').trigger('pocoWishlistItemMovedFromCart', [params, rs]);
                            }
                            if (action.search('compare/remove') > -1) {
                                $('body').trigger('pocoCompareItemRemoved', [params, rs]);
                            }
                            $('body').trigger('materialUpdated');
                            $('body').trigger('contentUpdated');
                        }
                    }).always(function() {
                        $('body').removeClass('poco-ajaxpost-proccessing');
                    });
                }
            };
            dataPost.postData = function(params) {
                var self = this;
                if (this.isAjaxAction(params.action)) {
                    if (params.data.confirmation) {
                        uiConfirm({
                            content: params.data.confirmationMessage,
                            actions: {
                                confirm: function () {
                                    self.ajaxPost(params);
                                }
                            }
                        });
                    } else {
                        self.ajaxPost(params);
                    }
                } else {
                    this.oldPostData(params);
                }
            };
            
            dataPost._displayLoginForm = function(html, params, action) {
                referer = btoa(referer);
                if (html) {
                    $loginPopup.data('action', action);
                    var referer = action + 'product/' + params.data.product + '/referer/' + btoa(document.URL) + '/form_key/' + params.data.form_key;
                    $loginPopup.html(html); var $form = $loginPopup.find('form');
                    $form.append($('<input type="hidden" name="referer">').val(btoa(referer)));
                } else {
                    action = $loginPopup.data('action');
                    var referer = action + 'product/' + params.data.product + '/referer/' + btoa(document.URL) + '/form_key/' + params.data.form_key;
                    $loginPopup.find('form [name="referer"]').val(btoa(referer));
                }
                $loginPopup.trigger('triggerPopup');
            }
            
            $.widget('magebees.customDataPost', $.mage.dataPost, {
                postData: function(params) {
                    dataPost.postData(params);
                }
            });
            $.mage.dataPost = $.magebees.customDataPost;           
            
            $('body').on('pocoWishlistItemRemoved', function(e, params, rs) {
                var id = params.data.item;
                var $wishlistForm = $('#wishlist-view-form');
                if ($wishlistForm.length) {
                    $wishlistForm.find('.product-item[id="item_' + id +'"]').fadeOut(500, 'swing', function() {
                        $(this).remove();
                        if ($wishlistForm.find('.product-item').length == 0) {
                            var $msg = $('<div class="message info empty">').html('<span>' + pocoAjaxPost.wishlistEmptyMsg + '</span>').prependTo($wishlistForm);
                            $wishlistForm.find('.actions-toolbar, .products-grid.wishlist').remove();
                        }
                    });
                }
            });
            $('body').on('pocoWishlistItemMovedFromCart', function(e, params, rs) {
                if (rs.success) {
                    setTimeout(function() {
                        document.location.reload();
                    }, 1000);
                }
            });
            
            $('body').on('pocoCompareItemRemoved', function(e, params, rs) {
                if (rs.compare_list_html && isCompareIndexPage()) {
                    var $oldCompareList = $('.table-wrapper.comparison').first();
                    if ($oldCompareList.length) {
                        $('.action.print.hidden-print').remove();
                        var $newCompareList = $('<div>').html(rs.compare_list_html);
                        $oldCompareList.hide().before($newCompareList);
                        $oldCompareList.remove();
                        $newCompareList.children().first().unwrap();
                    }
                }
            });
        }
    }
});