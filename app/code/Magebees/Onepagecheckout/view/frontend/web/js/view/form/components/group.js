define([
    'Magento_Ui/js/form/components/group'
], function (Group) {
    'use strict';
    return Group.extend({
        defaults: {
            visible: true,
            label: '',
            showLabel: true,
            required: false,
            template: 'ui/group/group',
            fieldTemplate: 'Magebees_Onepagecheckout/form/field',
            breakLine: true,
            validateWholeGroup: false,
            additionalClasses: {}
        },
        initialize: function () {
            this._super()
                ._setClasses();
            return this;
        }
    });
});
