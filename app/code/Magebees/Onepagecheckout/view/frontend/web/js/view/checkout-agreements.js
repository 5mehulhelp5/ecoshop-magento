define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'Magebees_Onepagecheckout/js/model/agreements/agreements-modal',
        'Magebees_Onepagecheckout/js/magebees/plugins/jquery.nicescroll.min'
    ],
    function (ko, $, Component, agreementsModal) {
        'use strict';
		if(window.checkoutConfig.enable_terms == 1){
			var enable_terms = true;
		}else{
			var enable_terms = false;
		}
        return Component.extend({
            defaults: {
                template: 'Magebees_Onepagecheckout/checkout/checkout-agreements'
            },
            isVisible: enable_terms,
            checkboxTitle: ko.observable(window.checkoutConfig.term_checkboxtitle),
			termtitle: window.checkoutConfig.term_title,			
			modalTitle: ko.observable(null),
            modalContent: ko.observable(null),
            modalWindow: null,
            isAgreementRequired: function(element) {
				return element.mode == 1;
            },
            showContent: function (element) {
                this.modalTitle(window.checkoutConfig.term_title);
                this.modalContent(window.checkoutConfig.term_html);
                agreementsModal.showModal();
                $('.magebees-checkout-agreements-modal').closest('.modal-content').niceScroll({cursorcolor:"#e5e5e5",cursorwidth:"8px",railpadding: { top: 0, right: 7, left: 0, bottom: 0 }});
            },
            initModal: function(element) {
                agreementsModal.createModal(element);
            }
        });
    }
);
