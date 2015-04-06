/**
 * This will overwrite the wordpress core scripts...so..let's be careful...
 *
**/
jQuery(function() {
	
	var postID = gup('post');    
	var rCat = gup('term_id');
	var pType = gup('post_type');
	var layoutType = setTimeout(function(){
		if(jQuery('#aviaLayoutBuilder').children('div').hasClass('sw_paginated')) {
			jQuery('.avia_scope.avia_meta_box.avia_meta_box_visual_editor').addClass('paginated').removeClass('waterfall');
		} else if(jQuery('#aviaLayoutBuilder').children('div').hasClass('sw_waterfall')) {
			jQuery('.avia_scope.avia_meta_box.avia_meta_box_visual_editor').addClass('waterfall').removeClass('paginated');
		}

		/**
		 * if article admin page for fashion, jetset or michaels edits post
		 * hide the default post content field to prevent user from editing in it
		**/
		if(jQuery('#fashion-postbox-container-1').length>0
		   ||jQuery('#jetset-postbox-container-1').length>0
		   ||jQuery('#mks-postbox-container-1').length>0)
		{
			jQuery('#postdivrich').hide();
			var $aviabuilder = jQuery('#avia_builder');
			$aviabuilder.parent().append($aviabuilder);
			jQuery('#postexcerpt').hide();
			jQuery('#freeFormContent').hide();
		}
    /**
     * if article admin page for fashion spotlight
     * hide all custom field to prevent user from editing in it
    **/
    if(jQuery('#article-type-id').length>0){
      console.log("hey");
        jQuery('#article-type-id').on("change",function(){
            console.log(jQuery(this).val());
            showHideFieldsByArticleType(jQuery(this).val());
        });
        showHideFieldsByArticleType(jQuery('#article-type-id').val());
    }

	},500);

	function showHideFieldsByArticleType(val){
		console.log(rCat);
        if(val==="collaborator"){
            jQuery('#fashion-postbox-container-1').hide();
            jQuery('#fashion-postbox-container-2').hide();
            jQuery('#fashion-postbox-container-3').hide();
            jQuery('#fashion-postbox-container-4').hide();
        } else {
            jQuery('#fashion-postbox-container-1').show();
            jQuery('#fashion-postbox-container-2').show();
            jQuery('#fashion-postbox-container-3').show();
            jQuery('#fashion-postbox-container-4').show();
        }
        
	}
	jQuery('#menu-posts-jet ul li a').click(function() {
		jQuery(this).parent().addClass('current');
	});

	if(postID == 5 ) {
		console.log(jQuery('#menu-posts-mks-edit').find("a").attr("href"));
		jQuery('#menu-posts-mks-edit, #menu-posts-mks-edit a.menu-top').addClass("wp-has-current-submenu wp-menu-open");
	}
	install_data();
	if(postID == '57') {//if(postID == '721')
		jQuery('#toplevel_page_post-post-57-action-edit').addClass('wp-has-current-submenu wp-menu-open').removeClass('wp-not-current-submenu');
		jQuery('#toplevel_page_post-post-57-action-edit a:first').addClass('wp-has-current-submenu wp-menu-open').removeClass('wp-not-current-submenu');
	}
	if(rCat) {
		if(rCat == '32') {
			jQuery('#postbox-container-2 div .postbox').hide();
			jQuery('#postbox-container-2 #avia_builder').show();
		}
		jQuery('#in-' + pType +'-category-' + rCat).attr('checked', true);
		jQuery('#postbox-container-2 #avia_builder').show();
	}
	if(pType) {
		jQuery('#menu-posts-' + pType).addClass('wp-has-current-submenu wp-menu-open').removeClass('wp-not-current-submenu');
		jQuery('#menu-posts-' + pType + ' a:first').addClass('wp-has-current-submenu wp-menu-open').removeClass('wp-not-current-submenu');
	}

	//console.log(jQuery('.categorychecklist .selectit').text());
	/*
	if(jQuery('label:contains("Travel Diaries") input').is(':checked')) jQuery('#tagsdiv-post_tag').show();
	jQuery('label:contains("Travel Diaries") input').click(function () {
		jQuery('#tagsdiv-post_tag').hide();
		jQuery("#tagsdiv-post_tag").toggle(this.checked);
	});
	*/
	if(jQuery('label:contains("Celebrities") input').is(':checked')) {
		jQuery('#tagsdiv-celebrity-tag').show();
		jQuery('#jetset-postbox-container-0').show();
	}
	jQuery('label:contains("Celebrities") input').click(function () {
		jQuery('#jetset-postbox-container-0').hide();
		jQuery("#jetset-postbox-container-0").toggle(this.checked);
		jQuery('#tagsdiv-celebrity-tag').hide();
		jQuery("#tagsdiv-celebrity-tag").toggle(this.checked);
	});
	jQuery('.add_city_field').on('click', function(){
		jQuery('.extraField').show();
		jQuery(this).hide();
	});
	// ATG option
	// will be handled from avia-modal.js
});
function getOffset(el) {
    var x = 0;
    var y = 0;
    while(el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
        x += el.offsetLeft - el.scrollLeft;
        y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return {top: y, left: x};
}
function gup(param){
	param = param.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\?&]"+param+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( window.location.href );
	if(results == null) return "";
	else return results[1];
}

function install_data() {
	jQuery("input[name^='install_data']").bind("click",function(){
		var data_type = jQuery("input:radio:checked").val();

		jQuery.ajax({
			type: "post",
			url: "/MVC/wp-admin/admin-ajax.php",
			dataType: 'json',
			data: {action: "install_data", data_type:data_type},
			beforeSend: function() {
				jQuery(".install_data_result").html('');
				jQuery(".install_data_loading").css({display:"block"});
				jQuery("input[name^='install_data']").attr('disabled', 'disabled');
				jQuery(".install_data_result").html("Importing dummy content...<br /> Please wait, it can take up to a few minutes.");

			}, //fadeIn loading just when link is clicked
			success: function(response){ //so, if data is retrieved, store it in html
				jQuery("input[name^='install_data']").removeAttr('disabled');
				var dummy_result = jQuery(".install_data_result");
				if(typeof response != 'undefined')
				{
					if(response.hasOwnProperty('status'))
					{
						switch(response.status)
						{
							case 'success':
									jQuery("input[name^='install_data']").remove();
									dummy_result.html('Completed');
								break;
							case 'error':
									dummy_result.html('<font color="red">'+response.data+'</font>');
									if(!response.hasOwnProperty('need_plugin'))
									{
										jQuery("input[name^='install_data']").remove();
									}
								break;
							default:
								break;
						}
					}
				}
	//				jQuery(".install_data_loading").css({display:"none"});
			},
			complete:function(){
				jQuery(".install_data_loading").css({display:"none"});
			}
		}); //close jQuery.ajax
		return false;
	});
}
