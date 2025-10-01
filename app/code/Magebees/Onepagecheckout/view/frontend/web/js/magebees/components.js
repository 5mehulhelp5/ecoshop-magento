define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, ko, quote) {
        'use strict';
        ko.bindingHandlers.myTestHandler = {
            init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            },
            update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            }
        }
    });