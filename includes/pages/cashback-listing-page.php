<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_cashback_listing_page()
{
    global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$usercashback_table = $wpdb->prefix.'rg_user_cashback';
	$exitclick_table = $wpdb->prefix.'rg_exitclicks';

	if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
	{
		// pre($_FILES['cashbacks']);
		// die();
		if( $_FILES['cashbacks']['error'] == 0 )
		{
			$tmpName = $_FILES['cashbacks']['tmp_name'];
			$csvAsArray = array_map('str_getcsv', file($tmpName));
			if( !empty($csvAsArray) )
			{
				  unset($csvAsArray[0]);
				
				  $checkcondition = 0;
				  $insertedRecords =0;
				foreach( $csvAsArray as $key => $single_cashback )
				{ 

				if (  !empty($single_cashback[0]) && $single_cashback[0] >= 1   ){
							
							$exitClickidFromCSV = $single_cashback[0];
							$statusCVS =  $single_cashback[2]; 
							// either cashback status confirmed or pending
							$sql  = " SELECT * FROM $exitclick_table ";
							$sql .= " WHERE rg_exitclick_id = $exitClickidFromCSV ";
							$exitclickdataFromTble = $wpdb->get_row($sql,ARRAY_A ); 
							
							$storeidFromTble  	=	$exitclickdataFromTble['rg_store_id'];
							$useridFromTble     =	$exitclickdataFromTble['rg_user_id'];
							$exitidFromTble     =	$exitclickdataFromTble['rg_exitclick_id'];
							
							$exitidFromTble     = isset($exitidFromTble) ? $exitidFromTble :"0";
							 
							$sqlc  = " SELECT count(*) as recordcount, rg_exitclick_id,status FROM $usercashback_table WHERE ";
							$sqlc  .= " rg_exitclick_id=$exitClickidFromCSV ";  
							// echo $sqlc;
							$usercashbackdata = $wpdb->get_row($sqlc, ARRAY_A);
							$recordCount = $usercashbackdata['recordcount']; 
							$exitclickid = $usercashbackdata['rg_exitclick_id']; 
							$status = $usercashbackdata['status']; 
							$recordCount = isset($recordCount) ? $recordCount :"0";
							$exitclickid = isset($exitclickid) ? $exitclickid :"0";
							$status      = isset($status) ? $status :"";
							
							if( $recordCount==0 && $exitidFromTble == $exitClickidFromCSV ){
								$insert_arr = array(   
										'rg_exitclick_id' 		=> $exitClickidFromCSV, //from cvs file
										'rg_store_id' 		    => $storeidFromTble, // get from exitclick table
										'rg_user_id' 		    => $useridFromTble, // get from exitclick table
										'commission' 		    => $single_cashback[1], //from cvs file
										'cashback' 			    => $single_cashback[1]*0.5,  //from cvs file
										'status' 			    => $single_cashback[2],   //from cvs file
									) ; 
								$wpdb->insert( 
									$usercashback_table, 
									$insert_arr
								);
								//echo $wpdb->last_query;
								$insertedRecords++;
								/*echo $wpdb->last_query;
								die();*/
						 	   }  else if ( $status == "pending" ) {  
										$sql="UPDATE $usercashback_table SET status='$statusCVS' WHERE rg_exitclick_id =$exitClickidFromCSV ";
										$wpdb->query($sql); 
						 	   } else {
						 		$checkcondition = 1;
							} 
						// }
				}
			}
				 if($checkcondition > 0){
				?>
					 <div class="container">
						<div class="row">
							<div class="col-md-8 topmargin20">
									<div class="alert alert-warning">
									<strong>Important!</strong> Duplicate records found in CVS file. <?php echo $insertedRecords > 0 ? $insertedRecords. " New record added." : "";?>
									</div>
							</div>
						</div>
					</div>  
				<?php
				}	
				}
			}
		}
	$sql  = "SELECT $usercashback_table.*, $stores_table.title as store FROM $usercashback_table ";
	$sql .= "LEFT JOIN $exitclick_table ON $usercashback_table.rg_exitclick_id = $exitclick_table.rg_exitclick_id ";
	$sql .= "LEFT JOIN $stores_table ON $exitclick_table.rg_store_id = $stores_table.rg_store_id ";
	$usercashbacks = $wpdb->get_results($sql);
	$sqlEC  = "SELECT COUNT(*) as countofExitClick FROM $exitclick_table ";
	$countofExitClick = $wpdb->get_results($sqlEC);
	$countofEC = $countofExitClick[0]->countofExitClick;
	if ($countofEC > 0){?>
	<div class="container" style="width: 90%">
	<div class="cashbackfileuploadBox col-sm-8">
		<div class="alert alert-info">
  <strong>Info!</strong> You can upload cashback through .csv file here to update all cashbacks below list. You can obtain the cashback information from your affiliate networks and compile the file according to example. <a href="https://revglue.com/resources/wp-theme-resources/cashback/assets/excel/cashback.csv" class="downloadcsv">Here is the example file</a>.
</div>
	<form method="post" action="" enctype="multipart/form-data">
		<input type="file" name="cashbacks">
		<input type="submit" value="Submit" class="button-primary">
	</form>
</div>
</div>
<?php } else { ?>
<div class="container" style="width: 90%">
<div class="row">
<div class="col-md-8 topmargin20">
<div class="alert alert-info">
  <strong>Important!</strong> Please make sure you have got exit clicks on your listings then you will be able to import cashback via .CSV file.
</div>
</div>
</div>
</div>
<?php } ?>	
	<div class="rg-admin-container">
		<h1 class="rg-admin-heading ">User Cashback</h1>
		<a href="<?php echo site_url( '/wp-admin/admin-post.php?action=create_csv_for_cashback' ) ?>"><button  class="button-primary float-right" style="margin-right:5px;" type="submit">Download All Cashbacks in CSV File</button></a>
		<div style="clear:both;"></div>
		<br/>
		<hr/>
		<table id="cashback_admin_screen" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>ID</th>
					<th>User ID / User Name</th>
					<th>Date / Time</th>
					<th>Store</th>
					<th>Commission</th>
					<th>Cashback</th>
					<th>Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>ID</th>
					<th>User ID / User Name</th>
					<th>Date / Time </th>
					<th>Store</th>
					<th>Commission</th>
					<th>Cashback</th>
					<th>Status</th>
				</tr>
			</tfoot>
			<tbody>
				<?php  
					$userid = get_current_user_id();      
				foreach( $usercashbacks as $single_cashback ) 
				{
					$DteX  =  $single_cashback->date_created ;
					$Dtex = explode(" ", $DteX );
					$user_name = ( !empty(get_user_meta( $userid, 'first_name', 'true' )) ? get_user_meta( $userid, 'first_name', 'true' ) : get_user_meta( $userid, 'nickname', 'true' ) ); 
					?><tr>
						<td><?php esc_html_e( $single_cashback->rg_usercashback_id ); ?></td>
						<td><?php esc_html_e( $userid." / ".$user_name ); ?></td>
						<td><?php esc_html_e( date("d-M-Y", strtotime($Dtex[0])) . ' / '. $Dtex[1]  ); ?></td>
						<td><?php esc_html_e( $single_cashback->store ); ?></td>
						<td>£<?php esc_html_e( $single_cashback->commission ); ?></td>
						<td>£<?php esc_html_e( $single_cashback->cashback ); ?></td>
						<td><?php esc_html_e( $single_cashback->status ); ?></td>
					</tr><?php
				}
				?>
			</tbody>
		</table>
		<br/>
		<a href="<?php echo site_url( '/wp-admin/admin-post.php?action=create_csv_for_cashback' ) ?>"><button  class="button-primary float-right" style="margin-right:5px;" type="submit">Download All Cashbacks in CSV File</button></a>
	</div>
	<?php
}
?>