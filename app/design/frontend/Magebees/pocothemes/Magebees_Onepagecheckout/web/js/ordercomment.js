define(
    [
        'jquery',
        'jquery-ui-modules/core','jquery-ui-modules/widget',
    ],
    function ($) {
        'use strict';
         $(document).on('click', '.order-comment-btn', function (e) {
            e.preventDefault();
            var flag = true;
            if (flag == false) {
                return false;
            } else {
                var form = $('#order-attachment');
                var formData = new FormData(form[0]);
                 $.ajax({
                        showLoader: true,
                        url: window.checkoutConfig.ordercommentbaseurl,
                        data: formData,
                        type: "POST",
                        datatype: "json",
                        cache:false,
                        contentType: false,
                        processData: false
                    }).done(function (data) {
                        if(data.success == 'true'){
                            if(data.fileuploadstauts == 1 && data.filename != "") {
								var filenames = data.filename.split(',');
								/* File-1 */
								if(filenames[0] != "" && filenames[0] != null){
									$('.attachSection1 .fileContent1 .file-uploded').remove();
									$('#order_for1').val('');
									$('.attachSection1 .fileContent1').append('<div class="file-uploded"><span class="file-upload-status" data-bind="text: getfileuploadvalue()">'+ filenames[0] +'</span><a href="javascript:;" id="rmfile1" class="order-file-removelink remove-file-icon"> Remove</a></div>');
								}
								/* File-2 */
								if(filenames[1] != "" && filenames[1] != null){
									$('.attachSection2 .fileContent2 .file-uploded').remove();
									$('#order_for2').val('');
									$('.attachSection2 .fileContent2').append('<div class="file-uploded"><span class="file-upload-status" data-bind="text: getfileuploadvalue()">'+ filenames[1] +'</span><a href="javascript:;" id="rmfile2" class="order-file-removelink remove-file-icon"> Remove</a></div>');
								}
								
								/* File-3 */
								if(filenames[2]  != "" && filenames[2] != null){
									$('.attachSection3 .fileContent3 .file-uploded').remove();
									$('#order_for3').val('');
									
									$('.attachSection3 .fileContent3').append('<div class="file-uploded"><span class="file-upload-status" data-bind="text: getfileuploadvalue()">'+ filenames[2] +'</span><a href="javascript:;" id="rmfile3" class="order-file-removelink remove-file-icon"> Remove</a></div>');
								}
								
								
								/* File-4 */
								if(filenames[3] != "" && filenames[3] != null){
									$('.attachSection4 .fileContent4 .file-uploded').remove();
									$('#order_for4').val('');
									$('.attachSection4 .fileContent4').append('<div class="file-uploded"><span class="file-upload-status" data-bind="text: getfileuploadvalue()">'+ filenames[3] +'</span><a href="javascript:;" id="rmfile3" class="order-file-removelink remove-file-icon"> Remove</a></div>');
								}
								
								/* File-5 */
								if(filenames[4] != "" && filenames[4] != null){
									$('.attachSection5 .fileContent5 .file-uploded').remove();
									$('#order_for5').val('');
									$('.attachSection5 .fileContent5').append('<div class="file-uploded"><span class="file-upload-status" data-bind="text: getfileuploadvalue()">'+ filenames[4] +'</span><a href="javascript:;" id="rmfile4" class="order-file-removelink remove-file-icon"> Remove</a></div>');
								}
                            }

                            if(data.ordercommentstatus == 1 && data.comment != "") {
								var comment = data.comment.split('|');
                                /* Comment1 */
								if(comment[0] != "" && comment[0] != null){
									$('.attachSection1 .attachComments1 .block-title').remove();
									$('#submitfordelete1').remove();
									$('.attachSection1 .attachComments1').append('<p class="block-title">'+ comment[0] +'</p>');
									$(".attachComments1").show();
									$('.block-order-comment .attachComments1').append('<a href="javascript:;" id="submitfordelete1" class="order-comment-removelink remove-comment-icon submitfordelete"> Remove Comment</a>');
									//$("#submitfordelete1").show();
								}
								
								/* Comment2 */
								if(comment[1] != "" && comment[1] != null){
									$('.attachSection2 .attachComments2 .block-title').remove();
									$('#submitfordelete2').remove();
									$('.attachSection2 .attachComments2').append('<p class="block-title">'+ comment[1] +'</p>');
									$(".attachComments2").show();
									$('.block-order-comment .attachComments2').append('<a href="javascript:;" id="submitfordelete2" class="order-comment-removelink remove-comment-icon submitfordelete"> Remove Comment</a>');
									//$("#submitfordelete2").show();
								}
								/* Comment3 */
								if(comment[2] != "" && comment[2] != null){
									$('.attachSection3 .attachComments3 .block-title').remove();
									$('#submitfordelete3').remove();
									$('.attachSection3 .attachComments3').append('<p class="block-title">'+ comment[2] +'</p>');
									$(".attachComments3").show();
									$('.block-order-comment .attachComments3').append('<a href="javascript:;" id="submitfordelete3" class="order-comment-removelink remove-comment-icon submitfordelete"> Remove Comment</a>');
									//$("#submitfordelete3").show();
								}
								/* Comment4 */
								if(comment[3] != "" && comment[3] != null){
									$('.attachSection4 .attachComments4 .block-title').remove();
									$('#submitfordelete4').remove();
									$('.attachSection4 .attachComments4').append('<p class="block-title">'+ comment[3] +'</p>');
									$(".attachComments4").show();
									$('.block-order-comment .attachComments4').append('<a href="javascript:;" id="submitfordelete4" class="order-comment-removelink remove-comment-icon submitfordelete"> Remove Comment</a>');
									//$("#submitfordelete4").show();
									
								}
								/* Comment5 */
								if(comment[4] != "" && comment[4] != null){
									$('.attachSection5 .attachComments5 .block-title').remove();
									$('#submitfordelete5').remove();
									$('.attachSection5 .attachComments5').append('<p class="block-title">'+ comment[4] +'</p>');
									$(".attachComments5").show();
									
									$('.block-order-comment .attachComments5').append('<a href="javascript:;" id="submitfordelete5" class="order-comment-removelink remove-comment-icon submitfordelete"> Remove Comment</a>');
									//$("#submitfordelete5").show();
								}
							}
                        }else{
                            $(".block-title").hide();
                            $(".order-comment-removelink").hide();
                        }
                        validatefrom();
                    });
                return false;
                /*return true;*/
            }
       });
        $(document).on('click', '.order-file-removelink', function (event) {
            event.preventDefault();			
            $.ajax({
                showLoader: true,
                url: window.checkoutConfig.orderfiledelete,
                data: "",
                type: "POST",
                datatype: "json"
            }).done(function (data) {
                if(data.success == 'true'){
                    validatefrom();
					if(event.target.id == "rmfile1"){
						$('.block-order-comments .block-order-for .fileContent1 .file-uploded').remove();
						$('#rmfile1').remove();
					}
					if(event.target.id == "rmfile2"){
						$('.block-order-comments .block-order-for .fileContent2 .file-uploded').remove();
						$('#rmfile2').remove();
					}
					if(event.target.id == "rmfile3"){
						$('.block-order-comments .block-order-for .fileContent3 .file-uploded').remove();
						$('#rmfile3').remove();
					}
					if(event.target.id == "rmfile4"){
						$('.block-order-comments .block-order-for .fileContent4 .file-uploded').remove();
						$('#rmfile4').remove();
					}
					if(event.target.id == "rmfile5"){
						$('.block-order-comments .block-order-for .fileContent5 .file-uploded').remove();
						$('#rmfile5').remove();
					}
                }
            });
        });

        $(document).on('click', '.submitfordelete', function (event) {
                event.preventDefault();
                $.ajax({
                    showLoader: true,
                    url: window.checkoutConfig.ordercommentdelete,
                    data: "",
                    type: "POST",
                    datatype: "json"
                }).done(function (data) {
					if(data.success == 'true'){
					if(event.target.id == "submitfordelete1"){
						$('#order_for1').val('');
						validatefrom();
						$('.attachSection1 .attachComments1 .block-title').html('');
						$('.attachSection1 .attachComments1 #submitfordelete1').html('');
					}
					if(event.target.id == "submitfordelete2"){
						$('#order_for2').val('');
						validatefrom();
						$('.attachSection2 .attachComments2 .block-title').html('');
						$('.attachSection2 .attachComments2 #submitfordelete2').html('');
					}
					if(event.target.id == "submitfordelete3"){
						$('#order_for3').val('');
						validatefrom();
						$('.attachSection3 .attachComments3 .block-title').html('');
						$('.attachSection3 .attachComments3 #submitfordelete3').html('');
					}
					if(event.target.id == "submitfordelete4"){
						$('#order_for4').val('');
						validatefrom();
						$('.attachSection4 .attachComments4 .block-title').html('');
						$('.attachSection4 .attachComments4 #submitfordelete4').html('');
					}
					if(event.target.id == "submitfordelete5"){
						$('#order_for5').val('');
						validatefrom();
						$('.attachSection5 .attachComments5 .block-title').html('');
						$('.attachSection5 .attachComments5 #submitfordelete5').html('');
					}
					
                   }
                });
            });


        function validatefrom() {
            if($('.order_for').length >0 && $('.order_comments').length >0 ){
                if($('.order_for').val() == "" && $.trim($('.order_comments').val()) == ""){
                    $('#order-commentbtn').prop('disabled', true);
                }
                else{
                    $('#order-commentbtn').removeAttr('disabled');
                }
            }else{
                if($('.order_for').length >0){
                    if($('.order_for').val() == ""){
                        $('#order-commentbtn').prop('disabled', true);
                    }
                    else{
                        $('#order-commentbtn').removeAttr('disabled');
                    }
                }
                if($('.order_comments').length >0){
                    if($.trim($('.order_comments').val()) == "") {
                        $('#order-commentbtn').prop('disabled', true);
                    }
                    else{
                        $('#order-commentbtn').removeAttr('disabled');
                    }
                }
            }
        }
        validatefrom();
        $(document).on('input', '.order_comments', function (event) {
            validatefrom();
        });
        $(document).on('change', '.order_for', function (event) {
            validatefrom();
        });
    }
)