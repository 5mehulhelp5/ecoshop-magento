
define(['ko'],function (ko) {
    'use strict';
    return function (config) {
        return {
            title: ko.observable("Module Name will return"),
            getModulename : function () {
                return config;
            }
        }
    }
});
