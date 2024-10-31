<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function revglue_cashback_subscription_validate() 
{
	global $wpdb;
    // die;
	$project_table = $wpdb->prefix.'rg_projects';
	$sanitized_sub_id	= sanitize_text_field( $_POST['sub_id'] );
	$sanitized_email	= sanitize_email( $_POST['sub_email'] );
	$password  			= $_POST['sub_pass'];
	$resp_from_server   = json_decode( wp_remote_retrieve_body( wp_remote_get( RGCASHBACK__API_URL . "api/validate_subscription_key/$sanitized_email/$password/$sanitized_sub_id", array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
	$result             = $resp_from_server['response']['result'];
    // pre($result);
    // die;
	$string = '';
	if( $resp_from_server['response']['success'] == true )
	{
//die('success');
		$sql = "SELECT *FROM $project_table WHERE project LIKE '".$result['project']."' and status = 'active'";
	    $execute_query = $wpdb->get_results( $sql );
		$rows = $wpdb->num_rows;
        // die;
		if(  empty( $rows ) || $rows ==0 )
		{	
             $project = $result['project'];
			$wpdb->insert( 
				$project_table, 
				array( 
					'subcription_id' 		=> $sanitized_sub_id, 
					'user_name' 			=> $result['user_name'], 
					'email' 			    => $result['email'], 
					'project' 			    => $result['project'] == "Cashback" ? str_replace ("Cashback", "Cashback UK", $result['project']) : $result['project'], 
					'expiry_date' 			=> $result['expiry_date'], 
					'partner_iframe_id' 	=> $result['iframe_id'], 
					'password' 			    => $password, 
					'status' 			    => $result['status']
				) 
            );
            $template_type = revglue_check_subscriptions();
			if($template_type=="Free"){
			$string .= "<div class='panel-white mgBot'>"; 
				$string .= "<p><b>Your RevEmbed cashback data subscription is ".$result['status'].".  </b><img  class='tick-icon' src=".RGCASHBACK__PLUGIN_URL. 'admin/images/ticks_icon.png'." /> </p>";
				$string .= "<p><b>Name = </b>".$result['user_name']."</p>";
				$string .= "<p><b>Project = </b>".$result['project']."</p>";
				$string .= "<p><b>Email = </b>".$result['email']."</p>";
			$string .= "</div>";
			}else{
			$string .= "<div class='panel-white mgBot'>"; 
				$string .= "<p><b>Your cashback data subscription is ".$result['status'].".  </b><img  class='tick-icon' src=".RGCASHBACK__PLUGIN_URL. 'admin/images/ticks_icon.png'." /> </p>";
				$string .= "<p><b>Name = </b>".$result['user_name']."</p>";
				$string .= "<p><b>Project = </b>".$result['project']."</p>";
				$string .= "<p><b>Email = </b>".$result['email']."</p>";
				$string .= "<p><b>Expiry Date = </b>".date('d-M-Y' ,  strtotime($result['expiry_date']))."</p>";
			$string .= "</div>"; 
			}
		} else 
		{
			$string .= "<div style='color: green;'>You already have subscription of this project, thankyou! </div>";	
		}
	} else 
	{
		$string .= "<p>&raquo; Your subscription unique ID <b class='grmsg'> ". $sanitized_sub_id ." </b> is Invalid.</p>";
	}
	echo $string;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_subscription_validate', 'revglue_cashback_subscription_validate' );
function revglue_cashback_pay_cashout() 
{
	// -- validate nonce request
	check_ajax_referer('secure_nonce', 'security');

	global $wpdb;
	$usercashout_table = $wpdb->prefix.'rg_user_cashout';
	$usercashback_table = $wpdb->prefix.'rg_user_cashback';
	$cashout_id = $_POST['cashoutid'];
	$msg ='';
	$sql = "UPDATE $usercashout_table SET `status`='paid' WHERE `rg_usercashout_id`=$cashout_id AND `status` = 'requested'";
	$query = $wpdb->query($sql);
	$sqlcb = "UPDATE $usercashback_table SET `status`='paid' WHERE `rg_usercashback_id`=$cashout_id AND `status` = 'requested'";
		$querycb = $wpdb->query($sqlcb); 
// die($wpdb->last_query); 
	if($query  ) {
	$msg ="cashoutpaid";
	}else {
		$msg ="cashoutnotpaid";
	}
	 echo $msg ;
	 wp_die();
}
add_action( 'wp_ajax_revglue_cashback_pay_cashout', 'revglue_cashback_pay_cashout' );
function revglue_cashback_data_import()
{
	
	// -- validate nonce request
	check_ajax_referer('secure_nonce', 'security');

	global $wpdb;
	$project_table = $wpdb->prefix.'rg_projects';
	$stores_table = $wpdb->prefix.'rg_stores';
	$cashback_table = $wpdb->prefix.'rg_cashback';
	$categories_table = $wpdb->prefix.'rg_categories';
	$date =  date('Y-m-d H:i:s');
	$string = '';
	$import_type = sanitize_text_field( $_POST['import_type'] );
	$sql = "SELECT *FROM $project_table WHERE project LIKE 'Cashback UK'";
	$project_detail = $wpdb->get_results($sql);
	$rows = $wpdb->num_rows;
	if( !empty ( $rows ) )
	{
		$subscriptionid = $project_detail[0]->subcription_id;
		$useremail = $project_detail[0]->email;
		$userpassword = $project_detail[0]->password;
		$projectid = $project_detail[0]->partner_iframe_id;
		// die($import_type);
		// die(" import_type -> ".$import_type);
		if( $import_type == 'rg_stores_import'  )
		{
			revglue_update_subscription_expiry_date($subscriptionid, $userpassword, $useremail, $projectid);
			$template_type = revglue_check_subscriptions();
			if($template_type=="Free"){
				$apiURL ="https://www.revglue.com/partner/cashback_stores/$projectid/json/wp/$subscriptionid";
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $apiURL , array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
			}else{
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGCASHBACK__API_URL . "api/cashback_stores/json/".$project_detail[0]->subcription_id, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
			}
			$result = $resp_from_server['response']['stores'];
	  		if($resp_from_server['response']['success'] == 1 )
			{
				foreach($result as $row)
				{
					//pre($result);
					// pre($result);
					// die();
				$sqlinstore = "SELECT rg_store_id FROM $stores_table WHERE rg_store_id = '".$row['rg_store_id']."'";
				$rg_store_exists = $wpdb->get_var( $sqlinstore );
					if( empty( $rg_store_exists ) ) {
						$wpdb->insert( 
							$stores_table, 
							array( 
								'rg_store_id' 				=> $row['rg_store_id'], 
								'mid' 						=> $row['affiliate_network_mid'], 
								'title' 					=> $row['store_title'], 
								'url_key' 					=> $row['url_key'], 
								'description' 				=> $row['store_description'], 
								'image_url' 				=> $row['image_url'], 
								'affiliate_network' 		=> $row['affiliate_network'], 
								'affiliate_network_link'	=> $row['affiliate_network_link'], 
								'store_base_currency' 		=> $row['store_base_currency'], 
								'store_base_country' 		=> $row['store_base_country'], 
								'category_ids' 				=> $row['cashback_category_ids'] 
							) 
						);
					} else  {
						$wpdb->update( 
							$stores_table, 
							array( 
								'mid' 						=> $row['affiliate_network_mid'], 
								'title' 					=> $row['store_title'], 
								'url_key' 					=> $row['url_key'], 
								'description' 				=> $row['store_description'], 
								'image_url' 				=> $row['image_url'], 
								'affiliate_network' 		=> $row['affiliate_network'], 
								'affiliate_network_link'	=> $row['affiliate_network_link'], 
								'store_base_currency' 		=> $row['store_base_currency'], 
								'store_base_country' 		=> $row['store_base_country'], 
								'category_ids' 				=> $row['cashback_category_ids'] 
							),
							array( 'rg_store_id' => $rg_store_exists )
						);
					}
				}
				$sql = "SELECT rg_store_id FROM $stores_table WHERE  homepage_store_tag ='no' LIMIT 30";
						$storeIDs = $wpdb->get_results( $sql );
						if ( count($storeIDs) > 0 ){
							foreach ($storeIDs as $sID) {
								$wpdb->update( 
										$stores_table, 
										array( 
											'homepage_store_tag' 	=> 'yes'  
										),
										array( 'rg_store_id' => $sID->rg_store_id )
									);
								}
						}
			} else 
			{
				$string .= '<p style="color:red">'.$resp_from_server['response']['message'].'</p>';
			}
		} else 
		 if( $import_type == 'rg_categories_import' )
		{
			revglue_update_subscription_expiry_date($subscriptionid, $userpassword, $useremail, $projectid);
			$template_type = revglue_check_subscriptions();
			if($template_type=="Free"){
				$apiURL ="https://www.revglue.com/partner/cashback_categories/$projectid/json/wp/$subscriptionid";
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $apiURL , array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
			}else{
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGCASHBACK__API_URL . "api/cashback_categories/json/".$project_detail[0]->subcription_id, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
			}
			$resultCategories = $resp_from_server['response']['categories'];
			// pre($resultCategories);
			// die;
			if($resp_from_server['response']['success'] == true )
			{
				foreach($resultCategories as $row)
				{	
						$title 		= $row['cashback_cateogry_title'];
						$url_key 	= preg_replace('/[^\w\d_ -]/si', '', $title); 	// remove any special character
						$url_key 	= preg_replace('/\s\s+/', ' ', $url_key);		// replacing multiple spaces to signle
						$url_key 	= strtolower(str_replace(" ","-",$url_key));
						// die($url_key);
					// print_r($resultCategories);
					// die();
					$sqlincat = "SELECT rg_category_id FROM $categories_table WHERE rg_category_id = '".$row['cashback_category_id']."'";
					$rg_category_exists = $wpdb->get_var( $sqlincat );
					if( empty( $rg_category_exists ) )
					{					
						$wpdb->insert( 
							$categories_table, 
							array( 
								'rg_category_id' 		=> $row['cashback_category_id'], 
								'title' 				=> $row['cashback_cateogry_title'], 
								'url_key' 				=> $url_key, 
								'parent' 				=> $row['parent_category_id'], 
								'image_url_pop_cat' 	=> $url_key, 
								'date' 					=> $date
							) 
						);
					} else 
					{
						$wpdb->update( 
							$categories_table, 
							array( 
								'title' 				=> $row['cashback_cateogry_title'], 
								'url_key' 				=> $url_key, 
								'image_url_pop_cat' 	=> $url_key, 
								'parent' 				=> $row['parent_category_id'],
								'date' 					=> $date
							),
							array( 'rg_category_id' => $rg_category_exists )
						);
					}
					/*echo $wpdb->last_query;
					die();*/
				}
				// $wpdb->query( "DELETE FROM $categories_table WHERE `date` != '$date' " );
			    $sqlParentCat = "SELECT * FROM $categories_table ";
				$CateIDs = $wpdb->get_results( $sqlParentCat ); 
				foreach ($CateIDs as $key => $cID) {
								$catnames = array("Food and Drink",  "Arts and Crafts","Automotive","Books and Magazines");
								$rg_this_theme_name= get_option("rg_this_theme_name"); 
								$update_array = array();
								if($cID->parent == '0'){
									$update_array['header_category_tag'] = 'yes';
									$catid = $cID->rg_category_id;
								}else{
									$catid = $cID->parent;
								}
								//die("asdfas");
								$update_array['icon_url'] = $catid;
								$update_array['image_url'] = $catid;
								if( $rg_this_theme_name=="cashbox"){
									if ( in_array( $cID->title, $catnames) ){
									$update_array['popular_category_tag'] = 'yes';
									}
								}else if ( $rg_this_theme_name=="revglue_free" || $rg_this_theme_name=="pinkash") {
									if ( $key < 12 ){
									$update_array['popular_category_tag'] = 'yes';
									}
								}else{
									$update_array['popular_category_tag'] = 'no';
								}
								$wpdb->update( 
										$categories_table, 
										$update_array,
										array( 'rg_category_id' => $cID->rg_category_id )
									); 
					}
					// echo $wpdb->last_query;
					// die();
			} else 
			{
				$string .= '<p style="color:red">'.$resp_from_server['response']['message'].'</p>';
			}
		} else 
		 if( $import_type == 'rg_cashback_import'  )
		{
			revglue_update_subscription_expiry_date($subscriptionid, $userpassword, $useremail, $projectid);
			$template_type = revglue_check_subscriptions();
			if($template_type=="Free"){
				$subscriptionid = $project_detail[0]->subcription_id;
				$projectid = $project_detail[0]->partner_iframe_id;
				$apiURL ="https://www.revglue.com/partner/stores_cashback/$projectid/json/wp/$subscriptionid";
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $apiURL, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
				$resultCashbacks = $resp_from_server['response']['stores'];
			}else{
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGCASHBACK__API_URL . "api/cashbacks/json/".$project_detail[0]->subcription_id, array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
				$resultCashbacks = $resp_from_server['response']['cashbacks'];
			}
			if($resp_from_server['response']['success'] == true )
			{
				foreach($resultCashbacks as $row)
				{	
					// pre($resultCashbacks);
					// die();
					$sqlincat = "SELECT rg_cashback_id FROM $cashback_table WHERE rg_cashback_id = '".$row['cashback_id']."'";
					$rg_cashback_exists = $wpdb->get_var( $sqlincat );
					if( empty( $rg_cashback_exists ) )
					{					
						$wpdb->insert( 
							$cashback_table, 
							array( 
								'rg_cashback_id' 	=> $row['cashback_id'], 
								'rg_store_id' 		=> $row['rg_store_id'], 
								'cashback_type' 	=> $row['cashback_type'], 
								'commission' 		=> $row['network_commission'], 
								'description' 		=> $row['cashback_description'], 
								'date' 				=> $date
							) 
						);
					} else 
					{
						$wpdb->update( 
							$cashback_table, 
							array( 
								'rg_store_id' 		=> $row['rg_store_id'], 
								'cashback_type' 	=> $row['cashback_type'], 
								'commission' 		=> $row['network_commission'], 
								'description' 		=> $row['cashback_description'],
								'date' 				=> $date
							),
							array( 'rg_cashback_id' => $rg_cashback_exists )
						);
					}
				}
				$wpdb->query("DELETE FROM $cashback_table Where `date` != '$date' ");
			} else 
			{
				$string .= '<p style="color:red">'.$resp_from_server['response']['message'].'</p>';
			}
		}
	} else 
	{
		$string .= "<p style='color:red'>Please subscribe for your RevGlue project first, then you have the facility to import the data";
	}
	$response_array = array();
	$response_array['error_msgs'] = $string;
	$sql = "SELECT MAX(date) FROM $stores_table";
	$last_updated_store = $wpdb->get_var($sql);
	$response_array['last_updated_store'] = ( $last_updated_store ? date( 'l, d-M-Y h:i:s A', strtotime( $last_updated_store ) ) : '-' );
	$sql_1 = "SELECT MAX(date) FROM $categories_table";
	$last_updated_category = $wpdb->get_var($sql_1);
	$response_array['last_updated_category'] = ( $last_updated_category ? date( 'l, d-M-Y h:i:s A', strtotime( $last_updated_category ) ) : '-' );
	$sql_4 = "SELECT MAX(date) FROM $cashback_table";
	$last_updated_cashback = $wpdb->get_var($sql_4);
	$response_array['last_updated_cashback'] = ( $last_updated_cashback ? date( 'l, d-M-Y h:i:s A', strtotime( $last_updated_cashback ) ) : '-' );
	$sql_2 = "SELECT count(*) as categories FROM $categories_table";
	$count_category = $wpdb->get_results($sql_2);
	$response_array['count_category'] = $count_category[0]->categories;
	$sql_3 = "SELECT count(*) as stores FROM $stores_table";
	$count_store = $wpdb->get_results($sql_3);
	$response_array['count_store'] = $count_store[0]->stores;
	$sql_5 = "SELECT count(*) as cashback FROM $cashback_table";
	$count_cashback = $wpdb->get_results($sql_5);
	$response_array['count_cashback'] = $count_cashback[0]->cashback; 
	echo json_encode($response_array);
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_data_import', 'revglue_cashback_data_import' );
function revglue_banner_data_import()
{
	
	// -- validate nonce request
	check_ajax_referer('secure_nonce', 'security');

	global $wpdb;
	$project_table = $wpdb->prefix.'rg_projects';
	$banner_table = $wpdb->prefix.'rg_banner';
	$string = '';
	$date = date('Y-m-d H:i:s');
	$import_type = sanitize_text_field( $_POST['import_type'] );
	$sql = "SELECT *FROM $project_table WHERE project LIKE 'Banners UK'";
	$project_detail = $wpdb->get_results($sql);
	$rows = $wpdb->num_rows;
	if( !empty ( $rows ) )
	{
		if( $import_type == 'rg_banners_import' || !isset($import_type) )
		{
			$i = 0;
			$page = 1;
			do {
				$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( RGCASHBACK__API_URL . "api/banners/json/".$project_detail[0]->subcription_id."/".$page."/1000", array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
				$total = ceil( $resp_from_server['response']['banners_total'] / 1000 ) ;
				$result = $resp_from_server['response']['banners'];
				if($resp_from_server['response']['success'] == true )
				{
					foreach($result as $row)
					{
						$sqlinstore = "SELECT rg_store_banner_id FROM $banner_table WHERE rg_store_banner_id = '".$row['rg_banner_id']."' AND `banner_type` = 'imported'";
						//die($sqlinstore);
						$rg_banner_exists = $wpdb->get_var( $sqlinstore );
						if( empty( $rg_banner_exists ) )
						{
							$wpdb->insert( 
								$banner_table, 
								array( 
									'rg_store_banner_id' 	=> $row['rg_banner_id'], 
									'rg_store_id' 			=> $row['rg_store_id'], 
									'rg_store_name' 	    => $row['banner_alt_text'], 
									'title' 				=> $row['banner_alt_text'], 
									'image_url' 			=> $row['banner_image_url'], 
									'placement' 			=> 'unassigned', 
									'rg_size' 				=> $row['width_pixels'].'x'.$row['height_pixels'], 
									'url' 					=> $row['deep_link'], 
									'banner_type' 			=> 'imported'
								) 
							);
						} else 
						{
							$wpdb->update( 
								$banner_table, 
								array( 
									'rg_store_id' 			=> $row['rg_store_id'], 
									'rg_store_name' 	    => $row['banner_alt_text'], 
									'title' 				=> $row['banner_alt_text'], 
									'image_url' 			=> $row['banner_image_url'], 
									'placement' 			=> 'unassigned', 
									'rg_size' 				=> $row['width_pixels'].'x'.$row['height_pixels'], 
									'url' 					=> $row['deep_link']
								),
								array( 'rg_store_banner_id' => $rg_banner_exists )
							);
						}
						/*echo $wpdb->last_query;
						die();	*/				
					}
				} else 
				{
					$string .= '<p style="color:red">'.$resp_from_server['response']['message'].'</p>';
				}
				$i++;
				$page++;
			} while ( $i < $total );
			$wpdb-query("DELETE FROM $banner_table WHERE `date` != '$date'");
		}
	} else 
	{
		$string .= "<p style='color:red'>Please subscribe for your RevGlue project first, then you have the facility to import the data";
	}
	$response_array = array();
	$response_array['error_msgs'] = $string;
	$sql1 = "SELECT count(*) as banner FROM $banner_table where banner_type= 'imported'";
	$count_banner = $wpdb->get_results($sql1);
	$response_array['count_banner'] = $count_banner[0]->banner;
	if ( !isset($import_type) ){
		return json_encode($response_array);
	}else {
		echo json_encode($response_array);
	}
	wp_die();
}
add_action( 'wp_ajax_revglue_banner_data_import', 'revglue_banner_data_import' );
function revglue_cashback_data_delete()
{
	
	// -- validate nonce request
	check_ajax_referer('secure_nonce', 'security');
	
	global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$categories_table = $wpdb->prefix.'rg_categories';
	$banner_table = $wpdb->prefix.'rg_banner';
	$cashback_table = $wpdb->prefix.'rg_cashback';
	$data_type = sanitize_text_field( $_POST['data_type'] );
	$response_array = array();
	if( $data_type == 'rg_stores_delete' )
	{
		$response_array['data_type'] = 'rg_stores';
		$wpdb->query( "DELETE FROM $stores_table" );	
		$sql = "SELECT MAX(date) FROM $stores_table";
		$last_updated_store = $wpdb->get_var($sql);
		$response_array['last_updated_store'] = ( $last_updated_store ? date( 'l, d-M-Y h:i:s A', strtotime( $last_updated_store ) ) : '-' );
		$sql2 = "SELECT count(*) AS cashback FROM $stores_table";
		$count_store = $wpdb->get_results($sql2);
		$response_array['count_store'] = $count_store[0]->cashback;
	} else if( $data_type == 'rg_categories_delete' )
	{
		$response_array['data_type'] = 'rg_categories';
		$wpdb->query( "DELETE FROM $categories_table" );	
		$sql = "SELECT MAX(date) FROM $categories_table";
		$last_updated_category = $wpdb->get_var($sql);
		$response_array['last_updated_category'] = ( $last_updated_category ? date( 'l, d-M-Y h:i:s A', strtotime( $last_updated_category ) ) : '-' );
		$sql2 = "SELECT count(*) AS categories FROM $categories_table";
		$count_category = $wpdb->get_results($sql2);
		$response_array['count_category'] = $count_category[0]->categories;
	} else if( $data_type == 'rg_banners_delete' )
	{
		$response_array['data_type'] = 'rg_banners';
		$wpdb->query( "DELETE FROM $banner_table where banner_type='imported'" );	
		$sql1 = "SELECT count(*) AS banner FROM $banner_table where banner_type= 'imported'";
		$count_banner = $wpdb->get_results($sql1);
		$response_array['count_banner'] = $count_banner[0]->banner;
	} else if( $data_type == 'rg_cashback_delete' )
	{
		$response_array['data_type'] = 'rg_cashback_delete';
		$wpdb->query( " DELETE FROM $cashback_table " );	
		$sql1 = "SELECT count(*) AS countofCashback FROM $cashback_table ";
		$count_cashback = $wpdb->get_results($sql1);
		$response_array['count_cashback'] = $count_cashback[0]->countofCashback;
		$sql = "SELECT MAX(date) FROM $cashback_table";
		$last_updated_cashback = $wpdb->get_var($sql);
		$response_array['last_updated_cashback'] = ( $last_updated_cashback ? date( 'l, d-M-Y h:i:s A', strtotime( $last_updated_cashback ) ) : '-' );
	}
	echo json_encode($response_array);
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_data_delete', 'revglue_cashback_data_delete' );
function revglue_cashback_update_home_store()
{
	global $wpdb; 
	$stores_table = $wpdb->prefix.'rg_stores';
	$store_id		= absint( $_POST['store_id'] );
	$cat_state 	= sanitize_text_field( $_POST['state'] );
	$wpdb->update( 
		$stores_table, 
		array( 'homepage_store_tag' => $cat_state ), 
		array( 'rg_store_id' => $store_id )
	);
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_update_home_store', 'revglue_cashback_update_home_store' );

function revglue_cashback_update_popular_store()
{
	global $wpdb; 
	$stores_table = $wpdb->prefix.'rg_stores';
	$store_id		= absint( $_POST['store_id'] );
	$cat_state 	= sanitize_text_field( $_POST['state'] );
	$wpdb->update( 
		$stores_table, 
		array( 'popular_store_tag' => $cat_state ), 
		array( 'rg_store_id' => $store_id )
	);
	echo $wpdb->last_query;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_update_popular_store', 'revglue_cashback_update_popular_store' );

/* jawad write for dispay hide stores */
function revglue_cashback_display_store()
{
	global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$store_id		= absint( $_POST['store_id'] );
	$cat_state 	= sanitize_text_field( $_POST['state'] );
	$wpdb->update(
		$stores_table,
		array( 'display_store' => $cat_state ),
		array( 'rg_store_id' => $store_id )
	);
	echo $wpdb->last_query;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_display_store', 'revglue_cashback_display_store' );
function revglue_cashback_update_header_category()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cat_id		= absint( $_POST['cat_id'] );
	$cat_state 	= sanitize_text_field( $_POST['state'] );
	$wpdb->update( 
		$categories_table, 
		array( 'header_category_tag' => $cat_state ), 
		array( 'rg_category_id' => $cat_id )
	);
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_update_header_category', 'revglue_cashback_update_header_category' );
function revglue_cashback_update_popular_category()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cat_id		= absint( $_POST['cat_id'] );
	$cat_state 	= sanitize_text_field( $_POST['state'] );
	$wpdb->update( 
		$categories_table, 
		array( 'popular_category_tag' => $cat_state ), 
		array( 'rg_category_id' => $cat_id )
	);
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_update_popular_category', 'revglue_cashback_update_popular_category' );
function revglue_cashback_update_display_category()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cat_id		= absint( $_POST['cat_id'] );
	$cat_state 	= sanitize_text_field( $_POST['state'] );
	$wpdb->update( 
		$categories_table, 
		array( 'display' => $cat_state ), 
		array( 'rg_category_id' => $cat_id )
	);
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_update_display_category', 'revglue_cashback_update_display_category' );
function revglue_cashback_update_category_icon()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cat_id		= absint( $_POST['cat_id'] );
	$icon_url 	= esc_url_raw( $_POST['icon_url'] );
	$wpdb->update( 
		$categories_table, 
		array( 'icon_url' => $icon_url ), 
		array( 'rg_category_id' => $cat_id )
	);
	echo $cat_id;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_update_category_icon', 'revglue_cashback_update_category_icon' );
function revglue_cashback_delete_category_icon()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cat_id		= absint( $_POST['cat_id'] );
	$wpdb->update( 
		$categories_table, 
		array( 'icon_url' => '' ), 
		array( 'rg_category_id' => $cat_id )
	);
	echo $cat_id;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_delete_category_icon', 'revglue_cashback_delete_category_icon' );
function revglue_cashback_update_category_image()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cat_id		= absint( $_POST['cat_id'] );
	$image_url 	= esc_url_raw( $_POST['image_url'] );
	$wpdb->update( 
		$categories_table, 
		array( 'image_url' => $image_url ), 
		array( 'rg_category_id' => $cat_id )
	);
	echo $cat_id;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_update_category_image', 'revglue_cashback_update_category_image' );
function revglue_cashback_delete_category_image()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cat_id		= absint( $_POST['cat_id'] );
	$wpdb->update( 
		$categories_table, 
		array( 'image_url' => '' ), 
		array( 'rg_category_id' => $cat_id )
	);
	echo $cat_id;
	wp_die();
}
add_action( 'wp_ajax_revglue_cashback_delete_category_image', 'revglue_cashback_delete_category_image' );
function revglue_cashback_load_stores()
{
	global $wpdb; 
	$categories_table = $wpdb->prefix.'rg_categories';
	$cashback_table = $wpdb->prefix.'rg_cashback';
	$sTable = $wpdb->prefix.'rg_stores';
	$aColumns = array(
		'rg_store_id', 
		'affiliate_network', 
		'mid', 
		'image_url', 
		'title', 
		'store_base_country', 
		'affiliate_network_link', 
		'category_ids', 
		'homepage_store_tag',
		'popular_store_tag',
		'display_store' );
	$sIndexColumn = "rg_store_id"; 
	$sLimit = "LIMIT 1, 50";
	if ( isset( $_REQUEST['start'] ) && sanitize_text_field($_REQUEST['length']) != '-1' )
	
	{
		$sLimit = "LIMIT ".intval(sanitize_text_field($_REQUEST['start'])).", ".intval(sanitize_text_field($_REQUEST['length']));
	}

	
	$sOrder = "";
	// make order functionality
	$where = "";
	$globalSearch = array();
	$columnSearch = array();
	$dtColumns = $aColumns;
	if ( isset($_REQUEST['search']) && sanitize_text_field($_REQUEST['search']['value']) != '' ) {
		$str = sanitize_text_field($_REQUEST['search']['value']) ;
		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}


		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]);
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]);
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}





		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data'] ];
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Individual column filtering
	if ( isset( $_REQUEST['columns'] ) ) {

		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			$str = sanitize_text_field($requestColumn['search']['value']) ;
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}


		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data'] ];
			$str = $requestColumn['search']['value'];
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Combine the filters into a single string
	$where = '';
	if ( count( $globalSearch ) ) {
		$where = '('.implode(' OR ', $globalSearch).')';
	}
	if ( count( $columnSearch ) ) {
		$where = $where === '' ?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	}
	if ( $where !== '' ) {
		$where = 'WHERE '.$where;
	}
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."` FROM $sTable $where $sOrder $sLimit";
	$rResult = $wpdb->get_results($sQuery, ARRAY_A);
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $wpdb->get_results($sQuery, ARRAY_N); 
	$iFilteredTotal = $rResultFilterTotal [0];
	$sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM $sTable";
	$rResultTotal = $wpdb->get_results($sQuery, ARRAY_N);
	$iTotal = $rResultTotal [0];
	$output = array(
		"draw"            => isset ( $_REQUEST['draw'] ) ? intval( sanitize_text_field($_REQUEST['draw'])  ) : 0,
		"recordsTotal"    => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data"            => array()
	);
	foreach($rResult as $aRow)
	{
		// pre($aRow);
		// echo count($aColumns);
		// die();
		$row = array();
		for ( $i=0 ; $i<13 ; $i++ )
		{
			if( $i == 0 )
			{
				$row[] = esc_html( $aRow[ $aColumns[0] ] ) ; // Store id
			} else if( $i == 1 )
			{
				$row[] = esc_html( $aRow[ $aColumns[1] ] ) ; // Network
			} else if( $i == 2 )
			{
				$row[] = esc_html( $aRow[ $aColumns[2] ] ) ; // MID  
			} else if( $i == 3 )
			{
				$row[] = '<div class="revglue-banner-thumb"><img class="revglue-unveil" src="' . RGCASHBACK__PLUGIN_URL . '/admin/images/loading.gif" data-src="' . esc_url( $aRow[ $aColumns[$i] ] ) . '" /></div>'; 
			} else if( $i == 4 )
			{
				$row[] = esc_html( $aRow[ $aColumns[4] ] ) ; // Store Name 
		} else if( $i == 5 ){
				$row[] = esc_html( $aRow[ $aColumns[5] ] ) ; //Country
		}else if( $i == 6 ){
			$row[] = '<a class="rg_store_link_pop_up" id="'. esc_html( $aRow[ $aColumns[0] ] )  .'" 
			title="'. str_replace(array("subid-value","{}"),"" , $aRow[ $aColumns[$i] ] ).'"  href="'. str_replace("subid-value","" , esc_url( $aRow[ $aColumns[$i] ]) ).'" target="_blank"><img src="'. RGCASHBACK__PLUGIN_URL .'/admin/images/linkicon.png" style="width:50px;"/>
			</a>';
		}else if( $i == 7 ){
			if( $aRow[ $aColumns[8] ] == 'yes' )
				{
					$checked = 'checked="checked"';
				} else
				{
					$checked = '';
				}
				$row[] = '<input '.$checked.' type="checkbox" id="'.$aRow[ $aColumns[0] ].'" class="rg_store_popular_tag" >';
				// $row[] = $aRow[ $aColumns[8] ] ;
		}else if( $i == 8 ){
			if( $aRow[ $aColumns[$i] ] == 'yes' )
				{
					$checked = 'checked="checked"';
				} else
				{
					$checked = '';
				}
				$row[] = '<input '.$checked.' type="checkbox" id="'.$aRow[ $aColumns[0] ].'" class="rg_store_popular_tag" />';
		}else if( $i == 10 ){
			// pre($aColumns);
			// pre($aRow);
			// die();
			if( $aRow[ $aColumns[$i] ] == 'yes' )
				{
					$checked = 'checked="checked"';
				} else
				{
					$checked = '';
				}
				$row[9] = '<input '.$checked.' type="checkbox" id="'.$aRow[ $aColumns[0] ].'" class="rg_store_display_tag" />';
		} else if( $i == 9 ){
			$storeid = $aRow[ $aColumns[0] ];
			$cashbacks =	revglue_get_cashback_by_storeid($storeid);
			$row[10] = $cashbacks;
		}else if ($i == 11 ) {
			$storeid		= $aRow[ $aColumns[0] ] ;
			$storeurl		= revglue_money_back_check_store_banner_url($storeid); 
			$hideshowrow	= revglue_money_back_hideshowrow(); 
			$row[] 		= '<div '.$hideshowrow.' class="storebanner rg_pop_stores_image_'.$storeid.'">'.$storeurl.'</div>';
		}else if ($i == 12 ) {
			$row[] = '<a '.$hideshowrow.' href="#." data-storeid01="'.$aRow[ $aColumns[0] ].'" id="setstoreimage01">Set Banner</a><br><a '.$hideshowrow.' href="#." id="delstoreimage01" data-storeid011="'.$aRow[ $aColumns[0] ].'">Remove Banner</a>';
		}
	}
		$output['data'][] = $row;
	}
	echo json_encode( $output );
	die();
}
add_action('wp_ajax_revglue_cashback_load_stores','revglue_cashback_load_stores');
function revglue_cashback_load_banners()
{
	global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$sTable = $wpdb->prefix.'rg_banner';
	$upload = wp_upload_dir();
	$base_url = $upload['baseurl'];
	$uploadurl = $base_url.'/revglue/cashback/banners/';
	$placements = array(
		'home-top'				=> 'Home:: Top Header',
		'home-slider'			=> 'Home:: Main Banners',
		'home-mid'				=> 'Home:: After Categories',
		'home-bottom'			=> 'Home:: Before Footer',
		'cat-top'				=> 'Category:: Top Header',
		'cat-side-top'			=> 'Category:: Top Sidebar',
		'cat-side-bottom'		=> 'Category:: Bottom Sidebar 1',
		'cat-side-bottom-two'	=> 'Category:: Bottom Sidebar 2',
		'cat-bottom'			=> 'Category:: Before Footer',
		'store-top'				=> 'Store:: Top Header',
		'store-side-top'		=> 'Store:: Top Sidebar',
		'store-side-bottom'		=> 'Store:: Bottom Sidebar 1',
		'store-side-bottom-two'	=> 'Store:: Bottom Sidebar 2',
		'store-main-bottom'		=> 'Store:: After Review',
		'store-bottom'			=> 'Store:: Before Footer',
		'unassigned' 			=> 'Unassigned Banners'
	);
	$aColumns = array( 'banner_type', 'placement', 'status', 'title', 'url', 'image_url', 'rg_store_id', 'rg_id', 'rg_size' , 'rg_store_name' ); 
	$sIndexColumn = "rg_store_id"; 
	$sLimit = "LIMIT 1, 50"; 
	if ( isset( $_REQUEST['start'] ) && sanitize_text_field($_REQUEST['length']) != '-1' )
	
	{
		$sLimit = "LIMIT ".intval(sanitize_text_field($_REQUEST['start'])).", ".intval(sanitize_text_field($_REQUEST['length']));
	}
	$sOrder = "";
	// make order functionality
	$where = "";
	$globalSearch = array();
	$columnSearch = array();
	$dtColumns = $aColumns;
	if ( isset($_REQUEST['search']) && sanitize_text_field($_REQUEST['search']['value'])  != '' ) {
		$str = sanitize_text_field($_REQUEST['search']['value']) ;

		$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}


		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			$column = $dtColumns[ $requestColumn['data'] ];
			if ( $requestColumn['searchable'] == 'true' ) {
				$globalSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Individual column filtering
	if ( isset( $_REQUEST['columns'] ) ) {

			$request_columns = [];
		foreach ($_REQUEST['columns'] as $key => $val ) {
			if(is_array($val)){$request_columns[$key] = $val;}
			else{$request_columns[$key] = sanitize_text_field($val);}
		}

		for ( $i=0, $ien=count($request_columns) ; $i<$ien ; $i++ ) {
			$requestColumn = sanitize_text_field($request_columns[$i]) ;
			$column = sanitize_text_field($dtColumns[ $requestColumn['data'] ]) ;
			$str = sanitize_text_field($requestColumn['search']['value']) ;
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}


		/*for ( $i=0, $ien=count($_REQUEST['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $_REQUEST['columns'][$i];
			//$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column = $dtColumns[ $requestColumn['data'] ];
			$str = $requestColumn['search']['value'];
			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$columnSearch[] = "`".$column."` LIKE '%".$str."%'";
			}
		}*/
	}
	// Combine the filters into a single string
	$where = '';
	if ( count( $globalSearch ) ) {
		$where = '('.implode(' OR ', $globalSearch).')';
	}
	if ( count( $columnSearch ) ) {
		$where = $where === '' ?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	}
	if ( $where !== '' ) {
		$where = 'WHERE '.$where;
	}
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."` FROM   $sTable $where $sOrder $sLimit";
	$rResult = $wpdb->get_results($sQuery, ARRAY_A);
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $wpdb->get_results($sQuery, ARRAY_N); 
	$iFilteredTotal = $rResultFilterTotal [0];
	$sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM   $sTable";
	$rResultTotal = $wpdb->get_results($sQuery, ARRAY_N); 
	$iTotal = $rResultTotal [0];
	$output = array(
		"draw"            => isset ( $_REQUEST['draw'] ) ? intval( sanitize_text_field($_REQUEST['draw'])  ) : 0,
		"recordsTotal"    => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data"            => array()
	);
	foreach($rResult as $aRow)
	{
		/*pre($aRow);
		die();*/
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if( $i == 0 )
			{
				if( $aRow[ $aColumns[5] ] == '' )
				{
					$uploadedbanner = $uploadurl . $aRow[ $aColumns[3] ];
					$row[] = '<div class="revglue-banner-thumb"><img class="revglue-unveil" src="'. RGCASHBACK__PLUGIN_URL .'/admin/images/loading.gif" data-src="'. esc_url( $uploadedbanner ) .'"/></div>';
				} else
				{
					$row[] = '<div class="revglue-banner-thumb"><img class="revglue-unveil" src="'. RGCASHBACK__PLUGIN_URL .'/admin/images/loading.gif" data-src="'. esc_url( $aRow[ $aColumns[5] ] ) .'" /></div>';
				}
			} else if( $i == 1 )
			{ 
				$row[] = $aRow[ $aColumns[7] ]; // rg_id
			} else if( $i == 2 )
			{
				$row[] = $aRow[ $aColumns[9] ];// Store_name 
			} else if( $i == 3 )
			{
				$row[] = $aRow[ $aColumns[0] ];// banner_type
			} else if( $i == 4 )
			{
				$row[] = $aRow[ $aColumns[1] ]; // placement  
			} else if( $i == 5 )
			{
				$row[] = $aRow[ $aColumns[8] ]; // rg_size  
			}
			else if( $i == 6 )
			{
				if( ! empty( $aRow[ $aColumns[4]] ) )
				{
					$url_to_show = esc_url( $aRow[ $aColumns[4]] ); 
				} else if( ! empty( $aRow[ $aColumns[6]] ) )
				{
					$sql_1 = "SELECT affiliate_network_link FROM $stores_table where rg_store_id = ".$aRow[ $aColumns[6]];
					$deep_link = $wpdb->get_row($sql_1, ARRAY_A);
					$url_to_show = ( !empty( $deep_link['affiliate_network_link'] ) ? esc_url( $deep_link['affiliate_network_link'] ) : 'No Link'  );
				} else
				{
					$url_to_show = 'No Link';
				}
				$row[] = '<a class="rg_store_link_pop_up" id="'. $aRow[ $aColumns[7]] .'" 
					title="'. str_replace( array("subid-value","{}"), "" , $url_to_show ).'" 
					href="'. str_replace( array("subid-value","{}"), "" , $url_to_show ).'" target="_blank"><img src="'. RGCASHBACK__PLUGIN_URL .'/admin/images/linkicon.png" style="width:50px;"/>
				</a>';
			} 
			else if( $i == 7 )
			{
				$row[] = $aRow[ $aColumns[2] ]; // status 
			}else if( $i == 8 )
			{
				$row[] = '<a href="'. admin_url( 'admin.php?page=revglue-banners&action=edit&banner_id='.$aRow[ $aColumns[7]] ) .'">Edit</a>';
			}  
			 else if ( $aColumns[$i] != ' ' )
			{    
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['data'][] = $row;
	}
	echo json_encode( $output );
	die(); 
}
add_action( 'wp_ajax_revglue_cashback_load_banners', 'revglue_cashback_load_banners' );
function revglue_update_subscription_expiry_date($purchasekey, $userpassword, $useremail, $projectid){
	global $wpdb; 
	$projects_table = $wpdb->prefix.'rg_projects';
	$apiurl = RGCASHBACK__API_URL . "api/validate_subscription_key/$useremail/$userpassword/$purchasekey";
	$resp_from_server = json_decode( wp_remote_retrieve_body( wp_remote_get( $apiurl , array( 'timeout' => 120, 'sslverify'   => false ) ) ), true); 
	$expiry_date = $resp_from_server['response']['result']['expiry_date'];
	if ( empty($projectid)){
		$sql ="UPDATE $projects_table SET `expiry_date` = '$expiry_date' WHERE `subcription_id` ='$purchasekey'";
		$wpdb->query($sql);
	} 
}
function revglue_check_subscriptions(){
	global $wpdb;
	$project_table = $wpdb->prefix.'rg_projects';
	$sql ="SELECT `expiry_date` FROM $project_table WHERE `expiry_date`='Free' ";
	$project = $wpdb->get_var($sql);
	return $project;
}
/**************************************** *
 *
 *	Custom feature only for Money Back theme 
 *	Written by Imran Javed twitter handle @MrImranJaved
 *
 ******************************************************************************** */
add_action( 'wp_ajax_revglue_money_back_set_store_banner', 'revglue_money_back_set_store_banner' );
function revglue_money_back_set_store_banner(){
	global $wpdb;
	// pre($_POST);
	$banner_image_url = $_POST['banner_image_url'];
	$store_id = $_POST['store_id'];
	$stores_table = $wpdb->prefix.'rg_stores';
	$sql ="UPDATE $stores_table SET banner_image_url='$banner_image_url' WHERE rg_store_id = $store_id";
	$wpdb->query($sql);
	echo $store_id.'^'.$banner_image_url;
	die(); 
}
add_action( 'wp_ajax_revglue_money_back_remove_store_banner', 'revglue_money_back_remove_store_banner' );
function revglue_money_back_remove_store_banner(){
	global $wpdb;
	$storeid = $_POST['store_id'];
	$stores_table = $wpdb->prefix.'rg_stores';
	$sql ="UPDATE $stores_table SET `banner_image_url` = '' WHERE `rg_store_id` = $storeid";
	$wpdb->query($sql);
	echo $storeid;
	die(); 
}
function revglue_money_back_check_store_banner_url($storeid){
	global $wpdb;
	$banner_image_url ='';
	$stores_table = $wpdb->prefix.'rg_stores';
	$sql ="SELECT banner_image_url FROM $stores_table WHERE `rg_store_id` = $storeid";
	$image_url = $wpdb->get_var($sql);
	// die($image_url);
	if (!empty($image_url)){
		// echo " image_url -> ".$image_url;
		 $banner_image_url='<img src="'.$image_url.'" style="max-width:90%" />';
	}else {
		$banner_image_url='Banner is Not Set';
	}
	return $banner_image_url;
}
function revglue_money_back_hideshowrow(){
	$themename = wp_get_theme();
	$hideshow ='style="display:none;"';
	if ($themename=='Money Back'){
		$hideshow='';
	}
	return $hideshow;
}
?>