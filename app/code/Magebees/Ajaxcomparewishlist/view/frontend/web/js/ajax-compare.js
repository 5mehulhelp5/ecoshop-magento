define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
   "jquery-ui-modules/core","jquery-ui-modules/widget",
], function ($, modal, $t) {
    'use strict';
    $.widget('magebees.ajaxcomparewishlist',{
        options: {
            popupWrapperSelector: '#mbcomparepopupwrapper',
            popupBlankSelector: '#mbcompareblank',
            //closePopupModal: '.action-close',
			closePopupModal: '.mbcomparemodalpopup .action-close',
			processStart: 'processStart',
            processStop : 'processStop',
            addToCompareButtonSelector: '.tocompare',
            addToCompareButtonDisabledClass: 'disabled',
            addToCompareButtonTextWhileAdding: '',
            addToCompareButtonTextAdded: '',
            addToCompareButtonTextDefault: '',
            btnCloseSelector: '#mbcomparebtnclose',
            showLoader: false
        },
         _create: function () {
            var self = this;
            self._init();
            $('body').on('contentUpdated', function () {
                self._init();
            });
			//self.closePopup();
			//self.autoClosePopup(self.options.popupWrapperSelector);
		},

        autoClosePopup: function (wrapper) {
            var self = this;
            window.autocloseCountdown = setInterval(function (wrapper) {
                    var leftTimeNode = $('body').find('#mbcomparebtnclose .mbcompareautoclosetime');
                    var leftTime = parseInt(leftTimeNode.text()) - 1;                   
                    leftTimeNode.text(leftTime);
                    if (leftTime == 0) {
						clearInterval(window.autocloseCountdown);
                        $(self.options.closePopupModal).trigger('click');
						 
                    }
					$('body').on('click',self.options.closePopupModal,function (event) {
						event.stopImmediatePropagation();
						clearInterval(window.autocloseCountdown);
					});
					$(self.options.btnCloseSelector).click(function(event) {
						clearInterval(window.autocloseCountdown);
					});
			}, 1000);
        },
        closePopup: function () {
			
			var self = this;
			
			$(self.options.btnCloseSelector).click(function(event) {
				event.stopImmediatePropagation();
				$(self.options.closePopupModal).trigger('click');
				clearInterval(window.autocloseCountdown);
			});
			$('body').on('click',self.options.closePopupModal,function () {
                $(self.options.popupWrapperSelector).remove();
                $(self.options.popupWrapperSelector).modal('closeModal');
                clearInterval(window.autocloseCountdown);
            });
			
		},
        _init: function () {
            var self = this;
            self.element.find(self.options.addToCompareButtonSelector).off('click').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
				clearInterval(window.autocloseCountdown);
                self.addCompare($(this));
            });
        },
        showPopup: function() {
            var self = this,
                comparePopup = $(self.options.popupWrapperSelector);
            var modaloption = {
                type: 'popup',
                modalClass: 'mbcomparemodalpopup',
                responsive: true,
                innerScroll: true,
                clickableOverlay: true,
                closed: function(){
                   $('.mbcomparemodalpopup').remove();
                }
            };
            modal(modaloption, comparePopup);
            comparePopup.modal('openModal');
        },

        addCompare: function (el) {
            var self = this, 
                comparePopup = $(self.options.popupWrapperSelector),
                body   = $('body'),
                parent = el.parent(),
                post   = el.data('post');
            var params = post.data;
            if(parent.hasClass(self.options.addToCompareButtonDisabledClass)) return;
            $.ajax({
                url: post.action,
                data: params,
                type: 'POST',
                dataType: 'json',
                showLoader: self.options.showLoader,
                beforeSend: function () {
                    self.disableAddToCompareButton(parent);
                    if (self.options.showLoader) body.trigger(self.options.processStart);
                },
                success: function (res) {
                    if (self.options.showLoader) body.trigger(self.options.processStop);
                    if (res.popup) {
                        if (!comparePopup.length) {
                            body.append('<div class="mbcomparepopupwrapper" id="' + self.options.popupWrapperSelector.replace(/^#/, "") +'" >'+res.popup+'</div>');
                        }
                        self.showPopup();  
						$(self.options.popupWrapperSelector).trigger('contentUpdated');
						self.closePopup();
                        self.autoClosePopup(self.options.popupWrapperSelector);                      
                    } else {
                        //alert($t('No response from server'));
						location.reload(true);
                    }
                }
            }).done(function(){
                 self.enableAddToCompareButton(parent);
            });
        },

        /**
         * @param {String} form
         */
        disableAddToCompareButton: function (form) {
            var addToCompareButtonTextWhileAdding = this.options.addToCompareButtonTextWhileAdding || $t('Adding...'),
                addToCompareButton = $(form).find(this.options.addToCompareButtonSelector);

            addToCompareButton.addClass(this.options.addToCompareButtonDisabledClass);
            addToCompareButton.find('span').text(addToCompareButtonTextWhileAdding);
            addToCompareButton.attr('title', addToCompareButtonTextWhileAdding);
        },

        /**
         * @param {String} form
         */
        enableAddToCompareButton: function (form) {
            var addToCompareButtonTextAdded = this.options.addToCompareButtonTextAdded || $t('Added'),
                self = this,
                addToCompareButton = $(form).find(this.options.addToCompareButtonSelector);

            addToCompareButton.find('span').text(addToCompareButtonTextAdded);
            addToCompareButton.attr('title', addToCompareButtonTextAdded);

            setTimeout(function () {
                var addToCompareButtonTextDefault = self.options.addToCompareButtonTextDefault || $t('Add to Compare');

                addToCompareButton.removeClass(self.options.addToCompareButtonDisabledClass);
                addToCompareButton.find('span').text(addToCompareButtonTextDefault);
                addToCompareButton.attr('title', addToCompareButtonTextDefault);
            }, 1000);
        }
    });
    return $.magebees.ajaxcomparewishlist;
});