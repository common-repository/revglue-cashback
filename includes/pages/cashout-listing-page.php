<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 
function rg_cashout_listing_page()
{
    global $wpdb;
	$usercashout_table = $wpdb->prefix.'rg_user_cashout';
	$sql  = "SELECT *FROM $usercashout_table";
	$usercashouts = $wpdb->get_results($sql);
	?>
	<div class="rg-admin-container">
		<h1 class="rg-admin-heading ">User Cashout</h1>
		<div style="clear:both;"></div>
		<a href="<?php echo site_url( '/wp-admin/admin-post.php?action=create_csv_for_cashouts' ) ?>"><button  class="button-primary float-right" style="margin-right:5px;" type="submit">Download All Cashouts in CSV File</button></a>
		<div class="clear"></div>
		<hr/>
		<div class="text-right">You search the users by user name, email address.</div>
		<table id="cashback_admin_screen" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>ID</th>
					<th>User ID / User Name</th>
					<th>Date / Time</th>
					<th>Amount</th>
					<th>Paypal Email</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>ID</th>
					<th>User ID / User Name</th>
					<th>Date / Time</th>
					<th>Amount</th>
					<th>Paypal Email</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</tfoot>
			<tbody>
				<?php        
				foreach( $usercashouts as $single_cashback ) 
				{
					$DteX  =  $single_cashback->date_created ;
					$Dtex = explode(" ", $DteX );
					$user_id = $single_cashback->rg_user_id;
					$user_name = ( !empty(get_user_meta( $user_id, 'first_name', 'true' )) ? get_user_meta( $user_id, 'first_name', 'true' ) : get_user_meta( $user_id, 'nickname', 'true' ) ); 
					$paypal_email = ( !empty(get_user_meta( $user_id, 'rg_paypal_email', 'true' )) ? get_user_meta( $user_id, 'rg_paypal_email', 'true' ) : '' ); 
					?><tr>
						<td><?php esc_html_e( $single_cashback->rg_usercashout_id ); ?></td>
						<td><?php esc_html_e( $user_id." / ".$user_name ); ?></td>
						<td><?php esc_html_e( date('d-M-Y', strtotime($Dtex[0])) . ' / '. $Dtex[1]  ); ?></td>
						<td>£<?php esc_html_e( $single_cashback->amount ); ?></td>
						<td><?php esc_html_e( $paypal_email ); ?></td>
						<td class="cashoutstatus0999_<?php echo $single_cashback->rg_usercashout_id ; ?>"><?php esc_html_e( $single_cashback->status ); ?></td>
						<td>
							<?php if( $single_cashback->status == 'requested' )
							{
							echo '<a href="javascript:;" id="requesttopaycashout" class="requesttopaycashout_'.$single_cashback->rg_usercashout_id .'" data-cashoutid="'.absint($single_cashback->rg_usercashout_id).'" data-cashoutuserid="'.absint($single_cashback->rg_user_id).'">Pay Now</a>';
							} else if($single_cashback->status == 'paid' ) {
							echo '<p>Paid</p>';
							?>
						</td>
					</tr><?php
					}
				}
				?>
			</tbody>
		</table>
		<a href="<?php echo site_url( '/wp-admin/admin-post.php?action=create_csv_for_cashouts' ) ?>"><button  class="button-primary float-right" style="margin-right:5px;" type="submit">Download All Cashouts in CSV File</button></a>
	</div>
	<?php
}
?>