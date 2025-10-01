/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
*/
define([
    'jquery',
	"jquery-ui-modules/core",
	"jquery-ui-modules/widget",
	"cwsmenu"
], function (jQuery,core,widget,cwsmenu) {
    'use strict';
	jQuery.widget('magebees.menuinit', {
		 _create: function(config, element) {
			var widget = this;
			var options = this.getOptions();
			jQuery(this.options.group_id).cwsmenu(this.options);
		},
		getOptions: function(){
			return this.options; 
		},
	});
	return jQuery.magebees.menuinit;
});