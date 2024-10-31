jQuery( document ).ready(function($) {



	$.notify.defaults({globalPosition: 'bottom right'})
	jQuery( document).on( "click", "#delstoreimage01",   function(e) 
	{
		e.preventDefault();
		var delStoreId = jQuery(this).data('storeid011');
		var del_store_banner_string = {
			'action'	: 'revglue_money_back_remove_store_banner',
			'store_id'	: delStoreId
		};
		jQuery.post(
			ajaxurl,
			del_store_banner_string,
			function( response )
			{
		
			jQuery( ".rg_pop_stores_image_"+response ).html('Banner is Removed');
			 
		}
		);
		return false;
		
	});
	jQuery( document).on( "click", "#requesttopaycashout",   function(e) 
	{

		var cashoutid = jQuery(this).data("cashoutid");
		var cashoutuserid = jQuery(this).data("cashoutuserid");
		var pay_cashout_data_string = {
			'action'	: 'revglue_cashback_pay_cashout',
			'cashoutid'	: cashoutid,
			'userid'	: cashoutuserid,
			'security'	: MyAjax.security
		};
		console.log(pay_cashout_data_string);
		//  return false;
		jQuery.post(
			ajaxurl,
			pay_cashout_data_string,
			function( response )
			{
		console.log(response);
			if(response =="cashoutpaid"){
				jQuery(".requesttopaycashout_"+cashoutid).text("Paid");
				jQuery(".cashoutstatus0999_"+cashoutid).text("Paid");
				$.notify("Cashout request has been paid.", "success",  {position:"bottom left"} );
			}else if (response =="cashoutnotpaid"){
				$.notify("Cashout request has not been paid.", "warning",  {position:"bottom left"} );

			}
		}
		);
		return false;

	}); 
	jQuery("#rg_stores_delete_popup").hide()
	jQuery("#rg_stores_import_popup").hide()
	var thisthemname = jQuery("#categories_admin_screen").data("thisthemname");
	jQuery("img.revglue-unveil").unveil();
	// Initialize Stores Datatable
    jQuery('#stores_admin_screen').DataTable({
		"processing": true,
        "serverSide": true,
        "ajax": ajaxurl+'?action=revglue_cashback_load_stores',
		"pageLength": 50,
		"drawCallback": function( settings ) {
            jQuery("#stores_admin_screen img:visible").unveil();
			jQuery('.rg_store_homepage_tag').iphoneStyle();
			jQuery('.rg_store_popular_tag').iphoneStyle();
			jQuery('.rg_store_display_tag').iphoneStyle();
        }
	});
	// Initialize Categories Datatable
    jQuery('#categories_admin_screen').DataTable({
		"bPaginate": false
	});
	// Initialize Banners Datatable
    jQuery('#banners_admin_screen').DataTable({
		"processing": true,
        "serverSide": true,
        "ajax": ajaxurl+'?action=revglue_cashback_load_banners',
		"pageLength": 50,
		"drawCallback": function( settings ) {
            jQuery("#banners_admin_screen img:visible").unveil();
          //console.log(settings);
        }
	});
	// Initialize Categories Datatable
    jQuery('#cashback_admin_screen').DataTable({
		"pageLength": 50,
		"order": [[ 0, 'desc' ]]
	});
	jQuery( "#rg_store_sub_activate" ).on( "click", function() {
		var sub_id 		= jQuery( "#rg_store_sub_id" ).val();
		var sub_email 	= jQuery( "#rg_store_sub_email" ).val();
		var sub_pass 	= jQuery( "#rg_store_sub_password" ).val();
		if( sub_id == "" )
		{
			jQuery('#subscription_error').text("Please First enter your unique Subscription ID");	
			return false;
		}
		if( sub_email == "" )
		{
			jQuery('#subscription_error').text( "Please First enter your Email" );	
			return false;
		}
		if( sub_pass == "" )
		{
			jQuery('#subscription_error').text("Please First enter your Password");	
			return false;
		}
		var subscription_data = {
			'action'	: 'revglue_cashback_subscription_validate',
			'sub_id'	: sub_id,
			'sub_email'	: sub_email,
			'sub_pass'	: sub_pass
		};
		jQuery('#subscription_error').html("");
		jQuery('#subscription_response').html("");
		jQuery("#sub_loader").show();
		jQuery.post(
			ajaxurl,
			subscription_data,
			function( response )
			{		
				jQuery("#rg_store_sub_id").val("");
				jQuery('#sub_loader').hide();
				jQuery('#subscription_response').html(response);
			}
		);
		return false;
	});
jQuery( document ).on( "click", ".fetchstorecashback", function(e) {
	e.preventDefault();
	jQuery(".modal-content").empty();
	var storeid	= jQuery(this).data("storeid");
	var fetch_cashback_data_string = {
			'action'	: 'revglue_cashback_render_popup_for_stores',
			'storeid'	: storeid
		};
	jQuery.post(
			MyAjax.ajaxurl,
			fetch_cashback_data_string,
			function( response )
			{
				console.log(response);
				jQuery(".modal-content").html(response);
			}
		);
		return false;
});
	jQuery( "#rg_store_import" ).on( "click", function(e) {
		e.preventDefault();
		type = jQuery( this ).attr( 'href' );
		console.log(type);
		var import_data = {
			'action': 'revglue_cashback_data_import',
			'import_type': type,
			'security'	: MyAjax.security
		};
		jQuery(".dataloader").hide();
		if( type == 'rg_cashback_import' )
				{
					jQuery("#subscription_error").html("");
					jQuery('#cashback_import_loader').show();
					jQuery('.cashback-import-links').hide();
					jQuery('#rg_stores_import_popup').hide();
					
					
					
				} else if ( type == 'rg_categories_import' )
				{
					jQuery("#subscription_error").html("");
					jQuery('#category_import_loader').show();
					jQuery('.category-import-links').hide();
					jQuery('#rg_stores_import_popup').hide();
					
				} 
				else if(type == 'rg_stores_import'){
					jQuery("#subscription_error").html("");
					jQuery('#store_import_loader').show();
					jQuery('.store-import-links').hide();
					jQuery('#rg_stores_import_popup').hide();

				}
		
		
		
		console.log(import_data);
		jQuery.post(
			ajaxurl, 
			import_data, 
			function(response) 
			{
				console.log(response);
				jQuery('#store_import_loader').hide();
				jQuery('#category_import_loader').hide();
				jQuery('#cashback_import_loader').hide();
				
				jQuery(".store-import-links").show();
				jQuery(".category-import-links").show();
				jQuery(".cashback-import-links").show();
				if( type == 'rg_cashback_import' )
				{
					jQuery(".cahsback-import-links").notify("Cashback Import Successfully","success");

				} else if( type == 'rg_categories_import' )
				{
					jQuery(".category-import-links").notify("Categories Import Successfully","success");
				}
				else if (type == 'rg_stores_import' ) {
					jQuery(".store-import-links").notify("Stores Import Successfully","success");


				}
				
				jQuery('#rg_stores_import_popup').hide();
				var response_object = JSON.parse(response);
				jQuery(".sub_page_table").prepend(response_object.error_msgs);
				jQuery('#rg_store_count').text(response_object.count_store);	
				jQuery('#rg_category_count').text(response_object.count_category);	
				jQuery('#rg_cashback_count').text(response_object.count_cashback);
				if (response_object.count_cashback > 0) {
					jQuery(".hidestore").removeClass();
					jQuery(".showstore").addClass();
					jQuery(".showstore").show();
				}
				jQuery('#rg_store_date').text(response_object.last_updated_store);	
				jQuery('#rg_category_date').text(response_object.last_updated_category);
				jQuery('#rg_cashback_date').text(response_object.last_updated_cashback);
			}
		);
		return false;
	});
	jQuery( "#rg_banner_import" ).on( "click", function(e) {
		e.preventDefault();
		type = jQuery( this ).attr( 'href' );
		var import_data = {
			'action': 'revglue_banner_data_import',
			'import_type': type,
			'security'	: MyAjax.security
		};
		jQuery("#subscription_error").html("");
		jQuery(".sub_page_table").hide();
		jQuery('#store_loader').show();
		jQuery.post(
			ajaxurl, 
			import_data, 
			function(response) 
			{
				jQuery('#store_loader').hide();
				jQuery(".sub_page_table").show();
				jQuery('#rg_cashback_import_popup').hide();
				var response_object = JSON.parse(response);
				jQuery(".sub_page_table").prepend(response_object.error_msgs);
				jQuery('#rg_banner_count').text(response_object.count_banner);
			}
		);
		return false;
	});
	jQuery( document).on("click" ,"#rg_store_delete" , function(e) {
		e.preventDefault();
		type = jQuery( this ).attr( 'href' );
		console.log(type);
		var delete_data = {
			'action': 'revglue_cashback_data_delete',
			'data_type': type,
			'security'	: MyAjax.security
		};
		console.log(delete_data);
		jQuery(".dataloader").hide();
		if( type == 'rg_stores_delete' )
				{
					// alert("stores");
					jQuery("#subscription_error").html("");
					jQuery(".store-import-links").hide();
					jQuery("#rg_stores_delete_popup").hide();
					jQuery('#store_import_loader').show();
					
					
				} else if( type == 'rg_categories_delete' )
				{
					// alert("categpries");
					jQuery("#subscription_error").html("");
					jQuery(".category-import-links").hide();
					jQuery("#rg_stores_delete_popup").hide();
					jQuery('#category_import_loader').show();

					
				} 
				 else if( type == 'rg_cashback_delete' )
				{
					// alert("categpries");
					jQuery("#subscription_error").html("");
					jQuery(".cashback-import-links").hide();
					jQuery("#rg_stores_delete_popup").hide();
					jQuery('#cashback_import_loader').show();
					
				} 
		jQuery.post(
			ajaxurl, 
			delete_data, 
			function(response) 
			{
				jQuery('#cashback_import_loader').hide();
				jQuery('#store_import_loader').hide();
				jQuery('#category_import_loader').hide();
				jQuery(".store-import-links").show();
				jQuery(".category-import-links").show();
				jQuery(".cashback-import-links").show();

				console.log("Server response"+ response);
				// exit();
				jQuery('#rg_stores_delete_popup').hide();
				var response_object = JSON.parse(response);
				// alert(response_object.data_type);
				if( response_object.data_type == 'rg_stores' )
				{
					jQuery(".store-import-links").notify("Stores Deleted Successfully","error");

					jQuery('#rg_store_count').text(response_object.count_store);	
					jQuery('#rg_store_date').text(response_object.last_updated_store);
				} else if( response_object.data_type == 'rg_categories' )
				{
					jQuery(".category-import-links").notify("Categories Deleted Successfully","error");

					jQuery('#rg_category_count').text(response_object.count_category);		
					jQuery('#rg_category_date').text(response_object.last_updated_category);
				} else if( response_object.data_type == 'rg_banners' )
				{
					jQuery('#rg_banner_count').text(response_object.count_banner);
				}else if( response_object.data_type == 'rg_cashback_delete' )
				{
					jQuery(".cashback-import-links").notify("Cashback Deleted Successfully","error");

					console.log("Server response"+ response_object.data_type);
					jQuery('#rg_cashback_date').text(response_object.last_updated_cashback);
					jQuery('#rg_cashback_count').text(response_object.count_cashback);
					if (response_object.count_cashback == 0) {
					jQuery(".showstore").hide(); 
				}
				}
			}
		);
		return false;
	});
	jQuery('.rg-admin-container').on('mouseenter', '.rg_store_link_pop_up', function( event ) {
		var id = this.id;
		jQuery('#imp_popup'+id).show();
	}).on('mouseleave', '.rg_store_link_pop_up', function( event ) {
		var id = this.id;
		jQuery('#imp_popup'+id).hide();
	});
	jQuery('.rg_store_homepage_tag').iphoneStyle();
	jQuery( "#stores_admin_screen" ).on( "change",  ".rg_store_homepage_tag", function(e) {
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var store_tag_data = {
			'action': 'revglue_cashback_update_home_store',
			'store_id': this.id,
			'state' : tag_checked
		};
		jQuery.post(
			ajaxurl, 
			store_tag_data, 
			function(response) 
			{
			}
		);
	});
	jQuery('.rg_store_popular_tag').iphoneStyle();
	jQuery( "#stores_admin_screen" ).on( "change",  ".rg_store_popular_tag", function(e) {
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var store_tag_data = {
			'action': 'revglue_cashback_update_popular_store',
			'store_id': this.id,
			'state' : tag_checked
		};
		jQuery.post(
			ajaxurl, 
			store_tag_data, 
			function(response) 
			{
			}
		);
	});
	jQuery('.rg_store_cat_tag_head').iphoneStyle();
	jQuery( ".rg_store_cat_tag_head" ).on( "change", function(e) {
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var cat_tag_data = {
			'action': 'revglue_cashback_update_header_category',
			'cat_id': this.id,
			'state' : tag_checked
		};
		jQuery.post(
			ajaxurl, 
			cat_tag_data, 
			function(response) 
			{
			}
		);
	});


	jQuery( "#stores_admin_screen" ).on( "change",  ".rg_store_display_tag", function(e) {
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var store_tag_data = {
			'action': 'revglue_cashback_display_store',
			'store_id': this.id,
			'state' : tag_checked
		};
		jQuery.post(
			ajaxurl, 
			store_tag_data, 
			function(response) 
			{
			}
		);
	});
















	jQuery('.rg_store_cat_tag').iphoneStyle();
	jQuery( ".rg_store_cat_tag" ).on( "change", function(e) {
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var cat_tag_data = {
			'action': 'revglue_cashback_update_popular_category',
			'cat_id': this.id,
			'state' : tag_checked
		};
		jQuery.post(
			ajaxurl, 
			cat_tag_data, 
			function(response) 
			{
			}
		);
	});
	jQuery('.rg_store_cat_display').iphoneStyle();
	jQuery( ".rg_store_cat_display" ).on( "change", function(e) {
		if( jQuery( this ).prop( 'checked' ) )
		{
		   var tag_checked = 'yes';
		} else
		{
		   var tag_checked = 'no';
		}	
		var cat_tag_data = {
			'action': 'revglue_cashback_update_display_category',
			'cat_id': this.id,
			'state' : tag_checked
		};
		jQuery.post(
			ajaxurl, 
			cat_tag_data, 
			function(response) 
			{
			}
		);
	});
	jQuery( ".rg_stores_open_import_popup" ).on( "click", function(e) {
		e.preventDefault();
		var type = jQuery( this ).attr( "href" );
		jQuery('#rg_stores_delete_popup').hide();	
		jQuery('#rg_stores_import_popup').show();
		jQuery('.rg_stores_start_import').attr( "href", type );
	});
	jQuery( ".rg_stores_open_delete_popup" ).on( "click", function(e) {
		e.preventDefault();
		var type = jQuery( this ).attr( "href" );
		jQuery('#rg_stores_import_popup').hide();
		jQuery('#rg_stores_delete_popup').show();	
		jQuery('.rg_stores_start_delete').attr( "href", type );
	});
	jQuery('#rg_banner_image_type').on( "change", function(e) {
		var type = jQuery( this ).val();
		if( type == 'url' )
		{
			jQuery('#rg_banner_image_file').val('');
			jQuery('#rg_cashback_banner_image_upload').hide();
			jQuery('#rg_cashback_banner_image_url').show();
		} else
		{
			jQuery('#rg_banner_image_url').val('');
			jQuery('#rg_cashback_banner_image_url').hide();
			jQuery('#rg_cashback_banner_image_upload').show();
		}
	});
	// Set all variables to be used in scope
	var frame;
	// ADD ICON LINK
	jQuery( "#categories_admin_screen" ).on( "click", ".rg_add_category_icon", function( event ) {
		var the_cat_id = this.id
		event.preventDefault();
		/* // If the media frame already exists, reopen it.
		if ( frame ) 
		{
			frame.open();
			return;
		} */
		// Create a new media frame
		frame = wp.media({
			title: 'Select or Upload Media Of Your Chosen Persuasion',
			button: 
			{
				text: 'Use this media'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		// When an image is selected in the media frame...
		frame.on( 'select', function() 
		{
			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();
			var cat_img_data = {
				'action': 'revglue_cashback_update_category_icon',
				'cat_id': the_cat_id,
				'icon_url' : attachment.url
			};
			jQuery.post(
				ajaxurl, 
				cat_img_data, 
				function(response) 
				{
					jQuery( ".rg_store_icon_thumb_"+response ).html( 
					"<a id='"+response+"' class='rg_category_delete_icons' href='javascript;'>"+
					"<i class='fa fa-times' aria-hidden='true'></i></a>"+
					"<img alt='image' src='"+attachment.url+"'>" );
					jQuery( ".rg_add_category_icon_"+response ).text('Edit Icon');
				}
			);
		});
		// Finally, open the modal on click
		frame.open();
	});
	// DELETE ICON LINK
	jQuery( "#categories_admin_screen" ).on( "click",  ".rg_category_delete_icons", function( event ) {
		var the_cat_id = this.id
		event.preventDefault();
		jQuery.confirm({
			title: 'Category Icon',
			content: 'Are you sure you want to remove this icon ?',
			icon: 'fa fa-question-circle',
			animation: 'scale',
			closeAnimation: 'scale',
			opacity: 0.5,
			buttons: {
				'confirm': {
					text: 'Remove',
					btnClass: 'btn-blue',
					action: function () {
						var cat_img_data = {
							'action': 'revglue_cashback_delete_category_icon',
							'cat_id': the_cat_id,
						};
						jQuery.post(
							ajaxurl, 
							cat_img_data, 
							function(response) 
							{
								console.log(response);
								jQuery( ".rg_store_icon_thumb_"+response ).html( '' );
								jQuery( ".rg_add_category_icon_"+response ).text('Add Icon');
							}
						);
					}
				},
				cancel: function () {
				},
			}
		});	
	});
	// ADD IMAGE LINK
	jQuery( "#categories_admin_screen" ).on( "click",  ".rg_add_category_image", function( event ) {
		var the_cat_id = this.id
		event.preventDefault();
		/* // If the media frame already exists, reopen it.
		if ( frame ) 
		{
			frame.open();
			return;
		} */
		// Create a new media frame
		frame = wp.media({
			title: 'Select or Upload Media Of Your Chosen Persuasion',
			button: 
			{
				text: 'Use this media'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		// When an image is selected in the media frame...
		frame.on( 'select', function() 
		{
			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();
			var cat_img_data = {
				'action': 'revglue_cashback_update_category_image',
				'cat_id': the_cat_id,
				'image_url' : attachment.url
			};
			jQuery.post(
				ajaxurl, 
				cat_img_data, 
				function(response) 
				{
					jQuery( ".rg_store_image_thumb_"+response ).html( 
					"<a id='"+response+"' class='rg_category_delete_icons' href='javascript;'>"+
					"<i class='fa fa-times' aria-hidden='true'></i></a>"+
					"<img alt='image' src='"+attachment.url+"'>" );
					jQuery( ".rg_add_category_image_"+response ).text('Edit Image');
				}
			);
		});
		// Finally, open the modal on click
		frame.open();
	});
	// DELETE IMAGE LINK
	jQuery( "#categories_admin_screen" ).on( "click",  ".rg_category_delete_images", function( event ) {
		var the_cat_id = this.id
		event.preventDefault();
		jQuery.confirm({
			title: 'Category Image',
			content: 'Are you sure you want to remove this image ?',
			icon: 'fa fa-question-circle',
			animation: 'scale',
			closeAnimation: 'scale',
			opacity: 0.5,
			buttons: {
				'confirm': {
					text: 'Remove',
					btnClass: 'btn-blue',
					action: function () {
						var cat_img_data = {
							'action': 'revglue_cashback_delete_category_image',
							'cat_id': the_cat_id,
						};
						jQuery.post(
							ajaxurl, 
							cat_img_data, 
							function(response) 
							{
								console.log(response);
								jQuery( ".rg_store_image_thumb_"+response ).html( '' );
								jQuery( ".rg_add_category_image_"+response ).text('Add Image');
							}
						);
					}
				},
				cancel: function () {
				},
			}
		});	
	});
	/***********************************************************
	*
	*	Add or Edit Popular Category image only for cashbox theme.
	*
	*******************************************************/
	// Set all variables to be used in scope
	var frame;
	// ADD ICON LINK
	jQuery( "#categories_admin_screen" ).on( "click", ".rg_add_pop_category_image", function( event ) {
		var the_cat_id = this.id
		event.preventDefault(); 
		frame = wp.media({
			title: 'Select or Upload Media Of Your Chosen Persuasion',
			button: 
			{
				text: 'Use this media'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		// When an image is selected in the media frame...
		frame.on( 'select', function() 
		{
			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();
			var cat_img_data = {
				'action': 'revglue_cashback_update_popular_category_image',
				'cat_id': the_cat_id,
				'image_url_pop_cat' : attachment.url
			};
			jQuery.post(
				ajaxurl, 
				cat_img_data, 
				function(response) 
				{
					jQuery( ".rg_pop_category_image_"+response ).html( 
					"<a id='"+response+"' class='rg_pop_category_delete_image' href='javascript;'>"+
					"<i class='fa fa-times' aria-hidden='true'></i></a>"+
					"<img alt='image' src='"+attachment.url+"'>" );
					jQuery( ".rg_add_pop_category_image_"+response ).text('Edit Popular Category Image');
				}
			);
		});
		// Finally, open the modal on click
		frame.open();
	});
	// DELETE POPULAR CATEGORY IMAGE LINK
	jQuery( "#categories_admin_screen" ).on( "click",  ".rg_pop_category_delete_image", function( event ) {
		var the_cat_id = this.id
		event.preventDefault();
		jQuery.confirm({
			title: 'Popular Category Image',
			content: 'Are you sure you want to remove this image ?',
			icon: 'fa fa-question-circle',
			animation: 'scale',
			closeAnimation: 'scale',
			opacity: 0.5,
			buttons: {
				'confirm': {
					text: 'Remove',
					btnClass: 'btn-blue',
					action: function () {
						var cat_img_data = {
							'action': 'revglue_cashback_delete_popular_category_image',
							'cat_id': the_cat_id,
						};
						jQuery.post(
							ajaxurl, 
							cat_img_data, 
							function(response) 
							{
								console.log(response);
								jQuery( ".rg_pop_category_image_"+response ).html( '' );
								jQuery( ".rg_add_pop_category_image_"+response ).text('Add Popular Category Image');
							}
						);
					}
				},
				cancel: function () {
				},
			}
		});	
	});

	/***********************************************************
	*
	*	Add or delete store banner on money back theme
	*******************************************************/
	// Set all variables to be used in scope
	var frame;
	
	jQuery( "#stores_admin_screen" ).on( "click", "#setstoreimage01", function( event ) {
		var setStoreId = jQuery(this).data('storeid01');
		event.preventDefault(); 
		frame = wp.media({
			title: 'Select or Upload Media Of Your Chosen Persuasion',
			button: 
			{
				text: 'Use this media'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});
		// When an image is selected in the media frame...
		frame.on( 'select', function() 
		{
			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();
			var store_img_data = {
				'action': 'revglue_money_back_set_store_banner',
				'store_id': setStoreId,
				'banner_image_url' : attachment.url
			};
			// console.log(store_img_data);
			jQuery.post(
				ajaxurl, 
				store_img_data, 
				function(response) 
				{

					var stringsplit = response.split('^');
					var storeid = stringsplit[0];
					var imageurl = stringsplit[1];
					jQuery( ".rg_pop_stores_image_"+storeid ).html('<img src="'+imageurl+'" style="max-width:80%"  />');


					
				}
			);
		});
		// Finally, open the modal on click
		frame.open();
	});
});