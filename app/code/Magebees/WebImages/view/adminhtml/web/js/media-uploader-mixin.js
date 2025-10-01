/**
 * Copyright © 2021 Magebees. All rights reserved.
 * See LICENSE.txt for license details.
 */


define([
    'jquery'
], function ($) {
    'use strict';

    return function (mediaUploader) {
        $.widget('mage.mediaUploader', mediaUploader, {
            // Extend or override functions from the original module here
            _initUploader: function () {
                // Add or modify initialization logic
                console.log('Custom initialization logic added.');
                this._super();
            },

            // Add more custom functions or overrides as needed
            customFunction: function () {
                // Custom functionality
            }
        });

        return $.mage.mediaUploader;
    };
});