<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 
function rg_cashback_admin_enqueue()
{
	global $hook_suffix;
	// List of Plugin Pages
	$rg_cashback_hook_suffixes = array(
		'toplevel_page_revglue-dashboard',
		'revglue-cashback_page_revglue-import-cashback',
		'revglue-cashback_page_revglue-import-banners',
		'revglue-cashback_page_revglue-stores',
		'revglue-cashback_page_revglue-categories',
		'revglue-cashback_page_revglue-banners',
		'revglue-cashback_page_revglue-cashback',
		'revglue-cashback_page_revglue-cashout',
		'revglue-cashback_page_revglue-cashback-settings'
	);
	// Only enqueue if current page is one of plugin pages
	if ( in_array( $hook_suffix, $rg_cashback_hook_suffixes ) ) 
	{
		// Enqueue Admin Styles
		wp_register_style( 'rg-cashback-confirm', RGCASHBACK__PLUGIN_URL . 'admin/css/jquery-confirm.css' );
		wp_enqueue_style( 'rg-cashback-confirm' );
		wp_register_style( 'rg-cashback-bootstrapstyle', RGCASHBACK__PLUGIN_URL . 'admin/css/bootstrap.min.css' );
		wp_enqueue_style( 'rg-cashback-bootstrapstyle' );
		wp_register_style( 'rg-cashback-confirm-bundled', RGCASHBACK__PLUGIN_URL . 'admin/css/bundled.css' );
		wp_enqueue_style( 'rg-cashback-confirm-bundled' );
		wp_register_style( 'rg-cashback-main', RGCASHBACK__PLUGIN_URL . 'admin/css/admin_style.css' );
		wp_enqueue_style( 'rg-cashback-main' );
		wp_register_style( 'rg-cashback-checkbox', RGCASHBACK__PLUGIN_URL . 'admin/css/iphone_style.css' );
		wp_enqueue_style( 'rg-cashback-checkbox' );
		wp_register_style( 'rg-cashback-datatables', RGCASHBACK__PLUGIN_URL . 'admin/css/jquery.dataTables.css' );
		wp_enqueue_style( 'rg-cashback-datatables' );
		wp_register_style( 'rg-cashback-fontawesome', RGCASHBACK__PLUGIN_URL . 'admin/css/font-awesome.css' );
		wp_enqueue_style( 'rg-cashback-fontawesome' );
		// Enqueue Admin Scripts
		wp_register_script( 'rg-cashback-datatables', RGCASHBACK__PLUGIN_URL . 'admin/js/jquery.dataTables.js', array ( 'jquery' ) );
		wp_enqueue_script( 'rg-cashback-datatables' );
		wp_register_script( 'rg-cashback-unveil', RGCASHBACK__PLUGIN_URL . 'admin/js/jquery.unveil.js', array ( 'jquery' ) );
		wp_enqueue_script( 'rg-cashback-unveil' );
		
		wp_register_script( 'rg-cashback-bootstrapjs', RGCASHBACK__PLUGIN_URL . 'admin/js/bootstrap.min.js', array ( 'jquery' ) );

		wp_enqueue_script( 'rg-cashback-bootstrapjs' );


		wp_register_script( 'rg-cashback-notify',  RGCASHBACK__PLUGIN_URL . 'admin/js/notify.min.js', array ( 'jquery' ) );
		wp_enqueue_script( 'rg-cashback-notify' );



		wp_register_script( 'rg-cashback-checkbox', RGCASHBACK__PLUGIN_URL . 'admin/js/iphone-style-checkboxes.js', array ( 'jquery' ) );
		wp_enqueue_script( 'rg-cashback-checkbox' );
		wp_register_script( 'rg-cashback-confirm', RGCASHBACK__PLUGIN_URL . 'admin/js/jquery-confirm.js', array ( 'jquery' ) );
		wp_enqueue_script( 'rg-cashback-confirm' );
		wp_register_script( 'rg-cashback-main', RGCASHBACK__PLUGIN_URL . 'admin/js/main.js', array ( 'jquery', 'jquery-form' ) );
		wp_enqueue_script( 'rg-cashback-main' );
		wp_localize_script('rg-cashback-main','MyAjax',array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce('secure_nonce')
		) );

		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'rg_cashback_admin_enqueue' );
function rg_cashback_admin_actions() 
{
	add_menu_page('RevGlue Cashback', 'RevGlue Cashback', 'manage_options', 'revglue-dashboard', 'rg_cashback_main_page', RGCASHBACK__PLUGIN_URL .'admin/images/menuicon.png' );
	
	add_submenu_page('revglue-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'revglue-dashboard', 'rg_cashback_main_page');
	
	add_submenu_page('revglue-dashboard', 'Import Cashback Data', 'Import Cashback Data', 'manage_options', 'revglue-import-cashback', 'rg_cashback_store_import_page');
	
	add_submenu_page('revglue-dashboard', 'Stores', 'Stores', 'manage_options', 'revglue-stores', 'rg_stores_listing_page');
	
	add_submenu_page('revglue-dashboard', 'Categories', 'Categories', 'manage_options', 'revglue-categories', 'rg_cashback_category_listing_page');
	
	$template_types = revglue_check_subscriptions();
	if($template_types != "Free"){

	add_submenu_page('revglue-dashboard', 'User Cashback', 'User Cashback', 'manage_options', 'revglue-cashback', 'rg_cashback_listing_page');
	
	add_submenu_page('revglue-dashboard', 'User Cashout', 'User Cashout', 'manage_options', 'revglue-cashout', 'rg_cashout_listing_page');
	}
	add_submenu_page('revglue-dashboard', 'Cashback Settings', 'Cashback Settings', 'manage_options', 'revglue-cashback-settings', 'rg_cashback_settings_page');
}
add_action( 'admin_menu', 'rg_cashback_admin_actions' );
function rg_cashback_create_directory_structures( $dir_structure_array )
{
	$upload = wp_upload_dir();
	$base_dir = $upload['basedir'];
	foreach( $dir_structure_array as $single_dir )
	{
		$create_dir = $base_dir.'/'.$single_dir;
		if ( ! is_dir( $create_dir ) ) 
		{
			mkdir( $create_dir, 0755 );
		}
		$base_dir = $create_dir;
	}
}
function rg_cashback_remove_directory_structures()
{
	$upload = wp_upload_dir();
	$base_dir = $upload['basedir'].'\revglue';
	rg_cashback_folder_cleanup($base_dir);
}
function rg_cashback_folder_cleanup( $dirpath )
{
	if( substr( $dirpath, strlen($dirpath) - 1, 1 ) != '/' )
	{
        $dirpath .= '/';
    }
	$files = glob($dirpath . '*', GLOB_MARK);
	foreach( $files as $file )
	{
		if( is_dir( $file ) )
		{
			deleteDir($file);
		}
		else
		{
			unlink($file);
        }
    }
	rmdir($dirpath);
}
function rg_cashback_auto_import_data()
{
    $auto_var = basename( $_SERVER["REQUEST_URI"] );
	if ( $auto_var ==  'auto_import_data') 
	{
		include( RGCASHBACK__PLUGIN_DIR . 'includes/auto-import-data.php');
	}
}
add_action( 'template_redirect', 'rg_cashback_auto_import_data' );
function rg_cashback_populate_recursive_categories( $category_object, $parent_title, &$counter )
{
	/*pre($category_object);
	die();*/
	global $wpdb;
	$categories_table = $wpdb->prefix.'rg_categories';
	$sql = "SELECT *FROM $categories_table WHERE `parent` = $category_object->rg_category_id ORDER BY `title` ASC";
	$subcategories = $wpdb->get_results($sql);
	if ( !empty($parent_title) )
	{
		$title = $parent_title.'->'.$category_object->title;
		$strong_title = $parent_title.'-><strong>'.$category_object->title.'</strong>';
	} else 
	{
		$title = $category_object->title;
		$strong_title = '<strong>'.$title.'</strong>';
	}
	?><tr class="ui-state-default">
		<td>
			<?php esc_html_e( $counter ); ?>
		</td>
		<td style="text-align:left;">
			<?php _e( $strong_title ); ?>
		</td>
		<td style="text-align:left;">
			<?php _e( $category_object->status ); ?>
		</td>
		<?php 
				$rg_this_theme_name= get_option("rg_this_theme_name");
				 $rg_this_theme_name= isset($rg_this_theme_name) && $rg_this_theme_name =="cashbox" ? $rg_this_theme_name :"" ;
				if ( !empty($rg_this_theme_name)) {
		?>
		<td style="text-align:left;">
			<div class="revglue-banner-thumb rg_pop_category_image_<?php echo  $category_object->rg_category_id ?>">
				<?php 
				 $image_url_pop_cat = $category_object->image_url_pop_cat; ?>
				<a id="<?php esc_attr_e( $category_object->rg_category_id ); ?>" data-type="image_url" class="rg_category_delete_icons" href="javascript;"><i class="fa fa-times" aria-hidden="true"></i></a>
					<img alt="image" src="<?php  esc_html_e( $category_object->image_url_pop_cat ) ; ?>">
			</div>
		</td>
		<?php } ?>
		<td style="text-align:left;">
			<div class="revglue-banner-thumb rg_store_icon_thumb_<?php echo  $category_object->rg_category_id ?>">
				<?php 
				 $iconurl = $category_object->icon_url;
				 if (is_numeric(substr($iconurl, 0, 1))) {
					?><a id="<?php esc_attr_e( $category_object->rg_category_id ); ?>" data-type="image_url" class="rg_category_delete_icons" href="javascript;"><i class="fa fa-times" aria-hidden="true"></i></a>
					<img alt="image" src="<?php echo REVGLUE__STORE_ICONS.'/'.$iconurl.'.png' ; ?>"><?php
				} else { ?>
				<a id="<?php esc_attr_e( $category_object->rg_category_id ); ?>" data-type="image_url" class="rg_category_delete_icons" href="javascript;"><i class="fa fa-times" aria-hidden="true"></i></a>
					<img alt="image" src="<?php  esc_html_e( $category_object->icon_url ) ; ?>">
				<?php }
				?>
			</div>
		</td>
		<td style="text-align:left;">
			<div class="revglue-banner-thumb rg_store_image_thumb_<?php echo  $category_object->rg_category_id ?>">
				<?php 
				$imageurl = $category_object->image_url;
				 if (is_numeric(substr($imageurl, 0, 1))) { 
					?><a id="<?php esc_attr_e( $category_object->rg_category_id ); ?>" data-type="icon_url" class="rg_category_delete_images" href="javascript;"><i class="fa fa-times" aria-hidden="true"></i></a>
					<img alt="image" src="<?php echo  REVGLUE__CATEGORY_BANNERS.'/'.$imageurl.'.jpg' ; ?>"><?php
				} else { ?>
					<a id="<?php esc_attr_e( $category_object->rg_category_id ); ?>" data-type="icon_url" class="rg_category_delete_images" href="javascript;"><i class="fa fa-times" aria-hidden="true"></i></a>
					<img alt="image" src="<?php esc_html_e( $category_object->image_url) ; ?>">
			 <?php	}
				?>
			</div>
		</td>
		<td>
			<?php 
			if( $category_object->header_category_tag == 'yes')
			{
				$checked = 'checked="checked"';
			} else
			{
				$checked = '';
			}
			if ($category_object->parent == "0"){
			?>
			<input <?php echo $checked; ?> type="checkbox" id="<?php echo  $category_object->rg_category_id ?>" class="rg_store_cat_tag_head" />
			<?php }?>
		</td>
		<td>
			<?php 
			if( $category_object->popular_category_tag == 'yes' )
			{
				$checked = 'checked="checked"';
			} else
			{
				$checked = '';
			}
			?>
			<input <?php echo $checked; ?> type="checkbox" id="<?php echo  $category_object->rg_category_id ?>" class="rg_store_cat_tag" />
		</td>
		<td>
			<?php 
			if( $category_object->display == 'yes' )
			{
				$checked = 'checked="checked"';
			} else
			{
				$checked = '';
			}
			?>
			<input <?php echo $checked; ?> type="checkbox" id="<?php echo  $category_object->rg_category_id ?>" class="rg_store_cat_display" />
		</td>
		<td>
			<a id="<?php echo $category_object->rg_category_id; ?>" class="rg_add_category_icon rg_add_category_icon_<?php echo $category_object->rg_category_id; ?>" href="javascript;">
				<?php if(!empty($category_object->icon_url))
				{
					echo 'Edit Icon <br>';
				} else 
				{
					echo 'Add Icon <br>';
				}
				?>
			</a>
			<a id="<?php echo $category_object->rg_category_id; ?>" class="rg_add_category_image rg_add_category_image_<?php echo $category_object->rg_category_id; ?>" href="javascript;">
				<?php if(!empty($category_object->image_url))
				{
					echo 'Edit Image <br>';
				} else 
				{
					echo 'Add Image <br>';
				}
				?>
			</a>
		<?php 
				$rg_this_theme_name= get_option("rg_this_theme_name");
				if ($rg_this_theme_name=="cashbox" ) {
		?>
			<a id="<?php echo $category_object->rg_category_id; ?>" class="rg_add_pop_category_image rg_add_pop_category_image_<?php echo $category_object->rg_category_id; ?>" href="javascript;">
				<?php if(!empty($category_object->image_url_pop_cat))
				{
					echo 'Edit Popular Category Image <br>';
				} else 
				{
					echo 'Add Popular Category Image <br>';
				}
				?>
			</a>
		<?php } ?>
		</td>
	</tr><?php
	if( !empty( $subcategories ) )
	{
		foreach( $subcategories as $single_cateogory )
		{
			++$counter;
			rg_cashback_populate_recursive_categories( $single_cateogory, $title, $counter );
		}
	}
}
	function prefix_admin_create_csv_for_cashback()
{
    global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$usercashback_table = $wpdb->prefix.'rg_user_cashback';
	$exitclick_table = $wpdb->prefix.'rg_exitclicks';
	$sql  = "SELECT $usercashback_table.*, $stores_table.title as store FROM $usercashback_table ";
	$sql .= "LEFT JOIN $exitclick_table ON $usercashback_table.rg_exitclick_id = $exitclick_table.rg_exitclick_id ";
	$sql .= "LEFT JOIN $stores_table ON $exitclick_table.rg_store_id = $stores_table.rg_store_id ";
	$cashbacks = $wpdb->get_results($sql, ARRAY_A);
	header("Content-Type: text/csv;charset=utf-8");
	header("Content-Disposition: attachment; filename=cashback.csv");
	$output = fopen("php://output", "w");
	fputcsv($output, array_keys($cashbacks[0]));
	foreach ($cashbacks as $row)
	{
		fputcsv($output, $row); // here you can change delimiter/enclosure
	}
	fclose($output);
}
add_action( 'admin_post_create_csv_for_cashback', 'prefix_admin_create_csv_for_cashback' ); 
function  rg_create_csv_for_cashouts()
{
	global $wpdb;
	$usercashout_table = $wpdb->prefix.'rg_user_cashout';
	$sql  = "SELECT * FROM $usercashout_table";
	//echo $sql;
	  $usercashouts = $wpdb->get_results($sql, ARRAY_A); 
	/* print_r($usercashouts);
	 die();*/
	header("Content-Type: text/csv;charset=utf-8");
	header("Content-Disposition: attachment; filename=cashouts.csv");
	$output = fopen("php://output", "w");
	fputcsv($output, array_keys($usercashouts[0]));
	foreach ($usercashouts as $row)
	{
		fputcsv($output, $row); // here you can change delimiter/enclosure
	}
	fclose($output);
}
add_action( 'admin_post_create_csv_for_cashouts', 'rg_create_csv_for_cashouts' );  
function revglue_cashback_default_required_options() {
	update_option( 'rg_cashback_dividend_percentage', '50' );
	update_option( 'rg_cashback_cashout_limit', '100' );
	update_option( 'rg_cashback_support_email', 'wordpress@revglue.com' );
	update_option( 'rg_cashback_support_website_title', 'RevGlue' );
	update_option( 'rg_cashback_support_website_link', 'www.revglue.com' );
}
 function revglue_cashback_user_has_not_subscription_id() {
		global $wpdb;
		$rg_projects_table = $wpdb->prefix.'rg_projects'; 
		$sql = "SELECT  email FROM $rg_projects_table WHERE email !='' limit 1";
		$email = $wpdb->get_var($sql);
		$admin_page = get_current_screen();
		if ($email =='' && $admin_page->base == "dashboard" ) {
		echo '<div class="notice notice-success customstyle  subscriptiondone ">  ';
		echo  '<p>Please read the instructions on  <a href="'.get_home_url().'/wp-admin/admin.php?page=revglue-dashboard" target="_blank">RevGlue Dashbaord</a> for importing your RevGlue projects data. </p>';
		echo  '</div>';  
		} 
}
add_action( 'admin_notices', 'revglue_cashback_user_has_not_subscription_id' );
/*function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}*/
//add_filter('pre_site_transient_update_core','remove_core_updates'); //hide updates for WordPress itself
//add_filter('pre_site_transient_update_plugins','remove_core_updates'); //hide updates for all plugins
//add_filter('pre_site_transient_update_themes','remove_core_updates'); //hide updates for all themes	
/**************************************************************************************************
*
* Remove Wordpress dashboard default widgets
*
***************************************************************************************************/
function revglue_cashback_remove_default_widgets(){
	remove_action('welcome_panel', 'wp_welcome_panel');
	remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
	remove_meta_box( 'dashboard_quick_press',   'dashboard', 'side' );      //Quick Press widget
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );      //Recent Drafts
	remove_meta_box( 'dashboard_primary',       'dashboard', 'side' );      //WordPress.com Blog
	remove_meta_box( 'dashboard_incoming_links','dashboard', 'normal' );    //Incoming Links
	remove_meta_box( 'dashboard_plugins',       'dashboard', 'normal' );    //Plugins
	remove_meta_box('dashboard_activity', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'revglue_cashback_remove_default_widgets');
register_activation_hook(__FILE__, 'rg_cron_activation');
function revglue_get_cashback_by_storeid( $id )
{
    global $wpdb; 
	$cashback_table = $wpdb->prefix.'rg_cashback'; 
	$sql = "SELECT *FROM $cashback_table WHERE rg_store_id = $id";
	$cashback_data = $wpdb->get_results($sql);
	// pre($cashback_data);
	 $max = count($cashback_data);
	$string = '';
	$div_percent = get_option( 'rg_cashback_dividend_percentage') / 100;
	$div_percent= isset($div_percent) && $div_percent!='' ?$div_percent : 0.5 ;

if(!empty($cashback_data)){
	 if( $max > 1 )
	{
		$all_cashbacks = array();
		for( $i = 0; $i < $max; $i++ )
		{
			if( $cashback_data[$i]->cashback_type == 'percentage' )
			{
				$all_cashbacks[$i] = $cashback_data[$i]->commission;
			} else if ( $cashback_data[$i]->cashback_type == 'fixed' )
			{
				$all_cashbacks[$i] = $cashback_data[$i]->commission; 
			}
		}
		$position = array_search( max($all_cashbacks), $all_cashbacks );
		if( $cashback_data[$position]->cashback_type == 'percentage' )
		{
			$string = 'Upto '.$cashback_data[$position]->commission .'% Cashback' .'<a href="javascript:" data-toggle="modal" data-storeid="'. $id .'" class="fetchstorecashback" data-target="#myModal"><i class="fa fa-search" style="margin:0 0 0 10px; font-size:20px"></i></a>'; 
		} else if ( $cashback_data[$position]->cashback_type == 'fixed' )
		{
				$comm = $cashback_data[$position]->commission;
				$string = 'Upto £'. $comm .' Cashback' .'<a href="javascript:" data-toggle="modal" class="fetchstorecashback" data-storeid="'. $id .'" data-target="#myModal"><i class="fa fa-search" style="margin:0 0 0 10px; font-size:20px"></i></a>'; 
		}
	} else
	{
		if( $cashback_data[0]->cashback_type == 'percentage' )
		{
			$string = $cashback_data[0]->commission .'% Cashback';
		} else if ( $cashback_data[0]->cashback_type == 'fixed' )
		{
			$comm = $cashback_data[0]->commission;
			$string = '£'. $comm .' Cashback';
		}
	} 
	return $string;
}	
} 
function revglue_cashback_get_storename_by_id($id){
	global $wpdb; 
	$stores_table = $wpdb->prefix.'rg_stores'; 
	$sql = "SELECT title FROM $stores_table WHERE `rg_store_id` = $id";
	$storename =  $wpdb->get_var($sql);
	return $storename;
}
function revglue_cashback_render_popup_for_stores(){
	 global $wpdb; 
	$storeid = $_POST['storeid'];
	$cashback_table = $wpdb->prefix.'rg_cashback'; 
	$sql5 = "SELECT ($cashback_table.commission+0 ) AS comm, $cashback_table.cashback_type, $cashback_table.description  FROM $cashback_table WHERE rg_store_id=$storeid ORDER BY comm DESC";
	 $cashback_data = $wpdb->get_results($sql5);
	 $div_percent = get_option( 'rg_cashback_dividend_percentage', '50' ) / 100 ;
	// pre($cashback_data);
	// die();
	$storename = revglue_cashback_get_storename_by_id($storeid);
	$string ="";
	$string .='	<div class="modal-header">';
	$string .='<button type="button" class="close" data-dismiss="modal">&times;</button>';
	$string .='<h4 class="modal-title"> Cashback for  '.$storename.'</h4>';
	$string .='</div>';
	$string .='<div class="modal-body">';
	$string .='<div>';
	// $string .='<h3>Cashback for '.$storename.'y</h3>';
	$string .='<div class="shop_list full_list">';
	$string .='<ul class="list-group">';
	 foreach ($cashback_data as $single_cashback) {
	 	$description=  strtolower($single_cashback->description ); 
				if( $single_cashback->cashback_type == 'percentage' ){
						$percentageAmt = $single_cashback->comm  ;	
						$cashbackVal = $percentageAmt."% &nbsp;&nbsp;";
						if (preg_match("/^on/i", "$description")) {
							$cashbackDesc =	ucwords($description);
						} else {
							$cashbackDesc =	"On ".ucwords($description);
						}
						$string .='<li class="list-group-item">';
						$string .='<div class="shop_box">';
						$string .='<div class="row">';
						$string .='<div class="col-md-12 col-sm-12 col-xs-12 prod-text-sec">';
						$string .='<p>'.$cashbackVal. ' '. $cashbackDesc. '</p>';
						$string .='</div>'; 
						$string .='</div>';
						$string .='</div>';
						$string .='</li>';
				} else if ( $single_cashback->cashback_type == 'fixed' )
				{
						$fixcomm =  $single_cashback->comm ;
						$cashbackVal = "£". $fixcomm."&nbsp;&nbsp;"; 
						$cashbackDesc = "";
						if (preg_match("/^on/i", "$description")) {
							$cashbackDesc = ucwords($description);
						} else {
							$cashbackDesc =	"On ".ucwords($description);
						}
						$string .='<li class="list-group-item">';
						$string .='<div class="shop_box">';
						$string .='<div class="row">';
						$string .='<div class="col-md-12 col-sm-12 col-xs-12 prod-text-sec">';
						$string .='<p>'.$cashbackVal. ' '. $cashbackDesc. '</p>';
						$string .='</div>'; 
						$string .='</div>';
						$string .='</div>';
						$string .='</li>';
				}
		}
$string .= '</ul> ';
$string .='</div>';
$string .='</div>';
$string .='</div>';
$string .='<div class="modal-footer">';
$string .='<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>';
echo $string;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_render_popup_for_stores', 'revglue_cashback_render_popup_for_stores' );
?>