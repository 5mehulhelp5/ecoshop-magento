define(
    [
        'uiComponent',
        'ko',
        'jquery',
        'Magebees_Onepagecheckout/js/model/sidebar'
    ],
    function(Component, ko, $, sidebarModel) {
        'use strict';
        return Component.extend({
            setModalElement: function(element) {
                sidebarModel.setPopup($(element));
            }
        });
    }
);
