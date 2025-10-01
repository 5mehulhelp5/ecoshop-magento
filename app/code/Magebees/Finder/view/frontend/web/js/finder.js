define([
    "jquery",
    "jquery/ui",
	"cwschosen"
], function (jQuery) {
    "use strict";
    //creating jquery widget
    jQuery.widget('magebees.cwsfinder',{
        selects: [],
        _create: function () {
            this.selects = jQuery('#finderDropdowns_'+this.options.finderId).find('select');
			this.selects.on('change', this, this._onChange);
            var finderId = this.options.finderId;
			var enableDrpdwnSearch = this.options.enableDrpdwnSearch;
            
            //reset all dropdowns on reset button
             jQuery('#reset_'+finderId).click(function () {
				var count = jQuery(this).attr('drop-down-counts');
				var reset_ele = jQuery('#reset_'+finderId);
				var dropdownids = reset_ele.attr('drop-down-ids');
				var ddidsarr = dropdownids.split(",");
				
                for (var j=1; j<=count; j++) {
					if (j==1) {
						document.getElementById("finder_"+finderId+"_"+(ddidsarr[j-1])).selectedIndex=0;
					} else {
					    document.getElementById("finder_"+finderId+"_"+(ddidsarr[j-1])).selectedIndex=0;
                        jQuery('#finder_'+finderId+'_'+(ddidsarr[j-1])).children('option:not(:first)').remove();
					}
					if(enableDrpdwnSearch){
						jQuery("#finder_"+finderId+"_"+(ddidsarr[j-1])).trigger("chosen:updated");
					}
				}
				jQuery('#find_'+finderId).attr("disabled", true);
            });
        },
        
        _onChange: function (event) {
            var select = this;
            var parentId = select.value;
            var self = event.data;
            var optionurl = self.options.optionurl
            var finderId = self.options.finderId;
            var dropdownId = jQuery(this).attr('drop-down-id');
            var dropdownCounts = jQuery(this).attr('drop-down-counts');
            var autosearch = self.options.autosearch;
			var enableDrpdwnSearch = self.options.enableDrpdwnSearch;
            var nextDropdownId= parseInt(dropdownId)+1;
						
            jQuery('#find_'+finderId).attr("disabled", false);
            if (jQuery('#finder_'+finderId+"_"+nextDropdownId).length > 0) {
                if (0 != parentId && nextDropdownId) {
                    jQuery.ajax({
                            url : optionurl,
                            type: 'post',
                            data: { parent_id:parentId,dropdown_id:nextDropdownId,finder_id:finderId,cat_id:0} ,
                            dataType: 'json',
                            showLoader:true,
                            success: function (data) {
                                if (data.length) {
                                    var optionStr ="";
                                    var cnt=1;
                                    while (jQuery('#finder_'+finderId+"_"+nextDropdownId).length > 0) {
                                        if (cnt > 1) {
                                            //clears dropdown
                                            jQuery('#finder_'+finderId+'_'+nextDropdownId).children('option:not(:first)').remove();
                                        } else {
                                            for (var i = 0; i < data.length; i++) {
                                                optionStr = optionStr + '<option value="' + data[i]['value'] + '">' + data[i]['label'] + '</option>';
                                            }
                                            jQuery('#finder_'+finderId+'_'+nextDropdownId).empty();
                                            jQuery('#finder_'+finderId+'_'+nextDropdownId).append(optionStr);
										}
										if(enableDrpdwnSearch){
											jQuery('#finder_'+finderId+'_'+nextDropdownId).trigger("chosen:updated");
										}
                                        nextDropdownId += 1;
                                        cnt++;
                                    }
                                }
                            }
                        });
                } else { //calls if select on "Please Select"
                    var cnt=1;
                    while (jQuery('#finder_'+finderId+"_"+nextDropdownId).length > 0) {
                        jQuery('#finder_'+finderId+'_'+nextDropdownId).children('option:not(:first)').remove();
						if(enableDrpdwnSearch){
							jQuery('#finder_'+finderId+'_'+nextDropdownId).trigger("chosen:updated");
						}
                        nextDropdownId += 1;
                        cnt++;
                    }
                    if (cnt == dropdownCounts) {
                        jQuery('#find_'+finderId).attr("disabled", true);
                    }
                }
            } else {
                //auto submit after last dropdown is selected
                if (autosearch) {
                    jQuery('#finderform_'+finderId).submit();
                }
            }
        },//End _onChange
    });
    return jQuery.magebees.cwsfinder;
});