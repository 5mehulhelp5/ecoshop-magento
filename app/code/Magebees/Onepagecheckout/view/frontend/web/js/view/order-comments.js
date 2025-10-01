define([
    'ko',
    'jquery',
    'uiComponent'
], function (ko, $, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magebees_Onepagecheckout/order-comments'
        },
         getEnabled: function () {
            return window.checkoutConfig.enabled;
         },
         getEnabledOrdercomment: function () {
            return window.checkoutConfig.enabledordercomment;
         },
         getEnabledFileupload: function () {
			return window.checkoutConfig.enabledfileupload;
         },
         getfileuploadStatus: function () {
			return window.checkoutConfig.fileuploadstatus;
         },
         getOrdercommentsStatus: function () {
			return window.checkoutConfig.ordercommentsstatus;
         },
         getfileuploadvalue: function () {
			return window.checkoutConfig.fileuploadvalue;
         },		 
		 getfileuploadvalueone: function () {
			var file = window.checkoutConfig.fileuploadvalue.split(',');
			if(file[0] != "" && file[0] != null){
				return file[0];
			}else{
				$("#rmfile1").hide();
			}
		 },
		 getfileuploadvaluetwo: function () {
            var file = window.checkoutConfig.fileuploadvalue.split(',');
			if(file[1] != "" && file[1] != null){
				return file[1];
			}else{
				$("#rmfile2").hide();
			}
         },
		 getfileuploadvaluethree: function () {
            var file = window.checkoutConfig.fileuploadvalue.split(',');
			if(file[2] != "" && file[2] != null){
				return file[2];
			}else{
				$("#rmfile3").hide();
			}
         },
		 getfileuploadvaluefour: function () {
			var file = window.checkoutConfig.fileuploadvalue.split(',');
			if(file[3] != "" && file[3] != null){
				return file[3];
			}else{
				$("#rmfile4").hide();
			}
         },
		 getfileuploadvaluefive: function () {
			var file = window.checkoutConfig.fileuploadvalue.split(',');
			if(file[4] != "" && file[4] != null){
				return file[4];
			}else{
				$("#rmfile5").hide();
			}
         },
         getOrdercommentTitle: function () {
            return window.checkoutConfig.ordercommenttitle;
         },
         getOrdercommentTexttitle: function () {
            return window.checkoutConfig.ordercommenttexttitle;
         },
         getOrderfileTexttitle: function () {
            return window.checkoutConfig.orderfiletexttitle;
         },
         getOrdercommentbaseurl: function () {
            return window.checkoutConfig.ordercommentbaseurl;
         },
         getFileuploadvalustatus: function () {
            return window.checkoutConfig.fileuploadvaluestatus;
         },
         getOrdercommentfield: function () {
            return window.checkoutConfig.ordercommentfield;
         },
         getOrdercommentfieldNo: function () {
            return window.checkoutConfig.ordercommentfieldno;
         },
         getOrdercommentfile: function () {
            return window.checkoutConfig.ordercommentfile;
         },
         getOrdercommentfileNo: function () {
            return window.checkoutConfig.ordercommentfileno;
         },
         getorderCommentstext: function () {
			return window.checkoutConfig.getordercommentstext;
         },
		 getorderCommentstextone: function () {
			var comments = window.checkoutConfig.getordercommentstext.split('|');
			if(comments[0] != "" && comments[0] != null){
				return comments[0];
			}else{
				$("#submitfordelete1").hide();
			}
         },
		 getorderCommentstexttwo: function () {
			var comments = window.checkoutConfig.getordercommentstext.split('|');
			if(comments[1] != "" && comments[1] != null){
				return comments[1];
			}else{
				$("#submitfordelete2").hide();
			}
         },
		 getorderCommentstextthree: function () {
			var comments = window.checkoutConfig.getordercommentstext.split('|');
			if(comments[2] != "" && comments[2] != null){
				return comments[2];
			}else{
				$("#submitfordelete3").hide();
			}
         },
		 getorderCommentstextfour: function () {
			var comments = window.checkoutConfig.getordercommentstext.split('|');
			if(comments[3] != "" && comments[3] != null){
				return comments[3];
			}else{
				$("#submitfordelete4").hide();
			}
         },
		 getorderCommentstextfive: function () {
			var comments = window.checkoutConfig.getordercommentstext.split('|');
			if(comments[4] != "" && comments[4] != null){
				return comments[4];
			}else{
				$("#submitfordelete5").hide();
			}
         },
         getOrdercommentsFiletype: function () {
            return window.checkoutConfig.order_comments_file_type;
         },
		 numberofattachments: function(){
			return window.checkoutConfig.numberofattachment;
		 }
    });
});
