define([
        "jquery",
        "mage/template", "jquery-ui-modules/core","jquery-ui-modules/widget", "mage/translate", "Magento_Ui/js/modal/modal"
    ],
    function(jQuery, modal) {
        "use strict";
        var response = "";
        var countOfPosts = 0;
        var countOfTags = 0;
        var countOfComments = 0;
        var countOfCategories = 0;
        var totalPage = 0;
        jQuery.widget('Blog.importblog', {
            _create: function() {
                var self = this;
                
                var loadingimageurl = this.options.loading_image_url;
                var wordpress_connection = this.options.wordpress_connection;
                var wordpress_copytables = this.options.wordpress_copytables;



                jQuery(".action-close").click(function() {
                    location.reload();
                });

                var options = {
                    type: 'popup',
                    title: 'Import Blog',
                    responsive: true,
                    innerScroll: true,
                };
                var Import_form = jQuery('#edit_form')[0];
                var data = new FormData(Import_form);
                jQuery("#import").click(function() {
                    if (jQuery('#edit_form').valid()) {
                        var db_name = jQuery("#import_dbname").val();
                        var db_username = jQuery("#import_username").val();
                        var db_password = jQuery("#import_password").val();
                        var db_host = jQuery("#import_host").val();
                        var db_prefix = jQuery("#import_prefix").val();
                        var selectedStores = jQuery('#import_store_id').val();
                        jQuery.ajax({
                            url: wordpress_connection,
                            data: {
                                db_name: db_name,
                                db_user: db_username,
                                db_password: db_password,
                                db_host: db_host,
                                db_prefix: db_prefix,
                                selectedStores: selectedStores
                            },
                            dataType: 'json',
                            type: 'post',
							showLoader: true,
                            success: function(data) {
                                try {
                                    response = data;
                                    if (response.success) {
                                        jQuery("#loading-icon").html("<img id='loadingicon' src='" + loadingimageurl + "'/>");
                                        jQuery("#import_popup").modal(options).modal('openModal');

                                        jQuery('.steps-export').show();
                                        jQuery("#loading-icon").show();
                                        jQuery('#import_popup').show();

                                        jQuery("#connection").addClass("active");
                                        self.copytables();
                                    }
                                } catch (e) {
                                    alert('Error: Failed connect to wordpress database');
                                    jQuery("#error_message").text('Error: Failed connect to wordpress database');
                                    jQuery("#error_message").show();
                                }
                            },
                            error: function(request, status, error) {
                                alert('Error: Failed connect to wordpress database');
                                jQuery("#error_message").text('Error: Failed connect to wordpress database');
                                jQuery("#error_message").show();
                                location.reload(true);
                            }
                        });


                    } else {
                        return false;
                    }


                });

            },

            copytables: function() {
                var self = this;

                var db_name = jQuery("#import_dbname").val();
                var db_username = jQuery("#import_username").val();
                var db_password = jQuery("#import_password").val();
                var db_host = jQuery("#import_host").val();
                var db_prefix = jQuery("#import_prefix").val();
                var selectedStores = jQuery('#import_store_id').val();
                jQuery.ajax({
                    url: this.options.wordpress_copytables,
                    data: {
                        db_name: db_name,
                        db_user: db_username,
                        db_password: db_password,
                        db_host: db_host,
                        db_prefix: db_prefix,
                        selectedStores: selectedStores
                    },
                    dataType: 'json',
                    type: 'post',

                    success: function(data) {
                        try {
                            response = data;
							var copytableresponse = data;
							
							
                            if (response.success) {
                                var countOfPosts = response.wordpress_blog_post_count;
                                var countOfTags = response.wordpress_blog_tag_count;
                                var countOfComments = response.wordpress_blog_post_comment_count;
                                var countOfCategories = response.wordpress_blog_category_count;
								if(countOfCategories > 0)
								{
									jQuery("#cat-total").text(countOfCategories);
                                	jQuery("#cat-parent-total").text(countOfCategories);
								}
								if(countOfTags > 0)
								{
								jQuery("#tag-total").text(countOfTags);
                                }
								if(countOfComments > 0)
								{
								 jQuery("#comment-total").text(countOfComments);
                                }
								if(countOfPosts > 0)
								{
								jQuery("#post-total").text(countOfPosts);
                                }
								if(copytableresponse.wordpress_blog_category_count > 0)
								{
									if(jQuery("#connection").hasClass('active')){
									    jQuery("#connection").removeClass("active");
									}
									jQuery("#importing-category").addClass("active");
									
									jQuery("#cat-process-status").text('Importing');
									var current_count = 1;
									jQuery("#cat-process").text(current_count);
									jQuery("#create-category").show();
								self.createcategories(current_count,copytableresponse);	
								}else if(copytableresponse.wordpress_blog_tag_count > 0)
								{
									jQuery("#create-tag").show();
									var current_count = 1;
									jQuery("#tag-process").text(current_count);
									jQuery("#tag-process-status").text('Importing');
									if(jQuery("#connection").hasClass('active')){
									    jQuery("#connection").removeClass("active");
									}
									  jQuery("#importing-tags").addClass("active");
									
								self.createtags(current_count,copytableresponse);
								}else if(copytableresponse.wordpress_blog_post_count > 0)
								{
									jQuery("#create-post").show();
									var current_count = 1;
									jQuery("#post-process").text(current_count);
									jQuery("#post-process-status").text('Importing');
									if(jQuery("#connection").hasClass('active')){
									    jQuery("#connection").removeClass("active");
									}
									  jQuery("#importing-posts").addClass("active");
									
									self.createposts(current_count,copytableresponse);
								}else if(copytableresponse.wordpress_blog_post_comment_count > 0)
								{
									var current_count = 1;
									
									jQuery("#create-comment").show();
									jQuery("#comment-process-status").text('Importing');
									
									if(jQuery("#connection").hasClass('active')){
									    jQuery("#connection").removeClass("active");
									}
									 jQuery("#importing-comments").addClass("active");
									jQuery("#comment-process").text(0);
									self.createcomment(current_count,copytableresponse);
								}
								


                            }

                        } catch (e) {

                            jQuery("#error_message").text('Error: Failed to copy wordpress database');
                            jQuery("#error_message").show();
                        }
                    },
                    error: function(request, status, error) {
                        alert('Error: Failed to copy wordpress database');
                        location.reload(true);
                    }
                });

            },
            createcategories: function(current_count,copytableresponse) {
                var self = this;


                var selectedStores = jQuery('#import_store_id').val();


                jQuery.ajax({
                    url: this.options.wordpress_category,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        current_count: current_count,
                        selectedStores: selectedStores
                    },
                    success: function(data) {
                        try {
                            response = data;
                            if ((response.success) && (response.next)) {
                                var current_count = parseInt(response.current_count) + 1;
                                jQuery("#cat-process").text(current_count);
                                self.createcategories(current_count,copytableresponse);
                            } else if ((response.success) && (!response.next)) {
                                if(jQuery("#importing-category").hasClass('active')){
									    jQuery("#importing-category").removeClass("active");
								}
								jQuery("#cat-process-status").text('Completed');
                                jQuery("#category-assing-parent").show();
                                var current_count = 1;
                                jQuery("#cat-parent-process-status").text('Importing');
                                jQuery("#cat-parent-process").text(current_count);
								jQuery("#assing-parent-category").addClass("active");
								
								self.assignparentcategories(current_count,copytableresponse);
                            }


                        } catch (e) {
                            jQuery("#error_message").text('Error: Importing Categories');
                            jQuery("#error_message").show();
                        }
                    },
                    error: function(request, status, error) {

                        jQuery("#error_message").text('Error: Importing Categories');
                        jQuery("#error_message").show();
                        
                    }
                });
            },
            assignparentcategories: function(current_count,copytableresponse) {
                var self = this;
                jQuery.ajax({
                    url: this.options.wordpress_assign_parent_category,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        current_count: current_count
                    },
                    success: function(data) {
                        try {
                            response = data;


                            if ((response.success) && (response.next)) {
                                var current_count = parseInt(response.current_count) + 1;
                                jQuery("#cat-parent-process").text(current_count);
                                self.assignparentcategories(current_count,copytableresponse);

                            } else if ((response.success) && (!response.next)) {
                                jQuery("#cat-parent-process-status").text('Completed');
                                
								if(jQuery("#assing-parent-category").hasClass('active')){
									    jQuery("#assing-parent-category").removeClass("active");
								}
								if(copytableresponse.wordpress_blog_tag_count > 0)
								{
									jQuery("#create-tag").show();
									var current_count = 1;
									jQuery("#tag-process").text(current_count);
									jQuery("#tag-process-status").text('Importing');
									jQuery("#importing-tags").addClass("active");

								self.createtags(current_count,copytableresponse);
								}else if(copytableresponse.wordpress_blog_post_count > 0)
								{
									jQuery("#create-post").show();
									var current_count = 1;
									jQuery("#post-process").text(current_count);
									jQuery("#post-process-status").text('Importing');
									jQuery("#importing-posts").addClass("active");
									self.createposts(current_count,copytableresponse);
								}else if(copytableresponse.wordpress_blog_post_comment_count > 0)
								{
									var current_count = 1;
									
									jQuery("#create-comment").show();
									jQuery("#comment-process-status").text('Importing');
									jQuery("#importing-posts").removeClass("active");
									jQuery("#importing-comments").addClass("active");
									jQuery("#comment-process").text(0);
									self.createcomment(current_count,copytableresponse);
								}
							}

                        } catch (e) {

                            jQuery("#error_message").text('Error: Assing Category Into Parent Categories');
                            jQuery("#error_message").show();
                        }
                    },
                    error: function(request, status, error) {
                        jQuery("#error_message").text('Error: Assing Category Into Parent Categories');
                        jQuery("#error_message").show();
                    }
                });




            },
            createtags: function(current_count,copytableresponse) {
                var self = this;
                jQuery.ajax({
                    url: this.options.wordpress_tag,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        current_count: current_count
                    },
                    success: function(data) {
                        try {
                            response = data;
                            if ((response.success) && (response.next)) {

                                var current_count = parseInt(response.current_count) + 1;
                                jQuery("#tag-process").text(current_count);
                                self.createtags(current_count,copytableresponse);
                            } else if ((response.success) && (!response.next)) {
                                jQuery("#tag-process-status").text('Completed');
                                
								if(jQuery("#importing-tags").hasClass('active')){
									    jQuery("#importing-tags").removeClass("active");
									}
								
								if(copytableresponse.wordpress_blog_post_count > 0)
								{
									jQuery("#create-post").show();
									var current_count = 1;
									jQuery("#post-process").text(current_count);
									jQuery("#post-process-status").text('Importing');
									
									jQuery("#importing-posts").addClass("active");
									self.createposts(current_count,copytableresponse);
								}else if(copytableresponse.wordpress_blog_post_comment_count > 0)
								{
									var current_count = 1;
									jQuery("#create-comment").show();
									jQuery("#comment-process-status").text('Importing');
									jQuery("#importing-comments").addClass("active");
									jQuery("#comment-process").text(0);
									self.createcomment(current_count,copytableresponse);
								}
                                
                            }


                        } catch (e) {
                            jQuery("#error_message").text('Error: Error Importing Tags');
                            jQuery("#error_message").show();
                        }
                    },
                    error: function(request, status, error) {

                        jQuery("#error_message").text('Error: Error Importing Tags');
                        jQuery("#error_message").show();
                        
                    }
                });
            },
            createposts: function(current_count,copytableresponse) {
                var self = this;
                var db_name = jQuery("#import_dbname").val();
                var db_username = jQuery("#import_username").val();
                var db_password = jQuery("#import_password").val();
                var db_host = jQuery("#import_host").val();
                var db_prefix = jQuery("#import_prefix").val();
                var selectedStores = jQuery('#import_store_id').val();

                jQuery.ajax({
                    url: this.options.wordpress_post,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        current_count: current_count
                    },
                    data: {
                        db_name: db_name,
                        db_user: db_username,
                        db_password: db_password,
                        db_host: db_host,
                        db_prefix: db_prefix,
                        selectedStores: selectedStores,
                        current_count: current_count
                    },


                    success: function(data) {
                        try {
                            response = data;
                            if ((response.success) && (response.next)) {

                                var current_count = parseInt(response.current_count) + 1;
                                jQuery("#post-process").text(current_count);
                                self.createposts(current_count,copytableresponse);
                            } else if ((response.success) && (!response.next)) {
                                jQuery("#post-process-status").text('Completed');
                               
								if(jQuery("#importing-posts").hasClass('active')){
									    jQuery("#importing-posts").removeClass("active");
								}
								if(copytableresponse.wordpress_blog_post_comment_count > 0)
								{
									var current_count = 1;
									jQuery("#create-comment").show();
									jQuery("#comment-process-status").text('Importing');
									jQuery("#importing-comments").addClass("active");
									jQuery("#comment-process").text(0);
									self.createcomment(current_count,copytableresponse);
								}
                            }


                        } catch (e) {
                            jQuery("#error_message").text('Error: Error Importing Posts');
                            jQuery("#error_message").show();
                        }
                    },
                    error: function(request, status, error) {

                        jQuery("#error_message").text('Error: Error Importing Posts');
                        jQuery("#error_message").show();
                        
                    }
                });
            },
            createcomment: function(current_count,copytableresponse) {
                var self = this;
                var db_name = jQuery("#import_dbname").val();
                var db_username = jQuery("#import_username").val();
                var db_password = jQuery("#import_password").val();
                var db_host = jQuery("#import_host").val();
                var db_prefix = jQuery("#import_prefix").val();
                var selectedStores = jQuery('#import_store_id').val();

                jQuery.ajax({
                    url: this.options.wordpress_comment,
                    dataType: 'json',
                    type: 'post',
                    data: {
                        current_count: current_count
                    },
                    data: {
                        db_name: db_name,
                        db_user: db_username,
                        db_password: db_password,
                        db_host: db_host,
                        db_prefix: db_prefix,
                        selectedStores: selectedStores,
                        current_count: current_count
                    },


                    success: function(data) {
                        try {
                            response = data;
                            if ((response.success) && (response.next)) {
                                var post_comment_count = parseInt(response.post_comment_count);
								var current_post_comment_count = parseInt(jQuery("#comment-process").text());
                                var current_count = parseInt(response.current_count) + 1;
								current_post_comment_count = post_comment_count + current_post_comment_count;
								jQuery("#comment-process").text(current_post_comment_count);
                                self.createcomment(current_count,copytableresponse);
                            } else if ((response.success) && (!response.next)) {
                                jQuery("#comment-process-status").text('Completed');
                                jQuery("#loading-icon").hide();
                            }


                        } catch (e) {
                            jQuery("#error_message").text('Error: Error Importing Comments');
                            jQuery("#error_message").show();

                        }
                    },
                    error: function(request, status, error) {

                        jQuery("#error_message").text('Error: Error Importing Comments');
                        jQuery("#error_message").show();
                        
                    }
                });
            },
            _init: function() {
                this._load();

                
            },
            _load: function() {
                
            },


        });
        return jQuery.Blog.importblog;
    });
