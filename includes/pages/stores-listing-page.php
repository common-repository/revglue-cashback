<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_stores_listing_page()
{
	global $wpdb;
	$stores_table = $wpdb->prefix.'rg_stores';
	$cashback_table = $wpdb->prefix.'rg_cashback';
	$categories_table = $wpdb->prefix.'rg_categories';
	$sql = "SELECT *FROM $stores_table";
	$stores = $wpdb->get_results($sql);
	$hideshowrow	= revglue_money_back_hideshowrow(); 

	?><div class="rg-admin-container">
		<h1 class="rg-admin-heading ">Stores</h1>
		<div style="clear:both;"></div>
		<hr/>
		<div class="text-right">You can filter stores by RG ID, Network, MID, Name, or Country by typing in the Search box below. <br/><br/></div>
		<table id="stores_admin_screen" class="display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>RG ID</th>
					<th>Network</th>
					<th>MID</th>
					<th>Logo</th>
					<th>Name</th>
					<th>Country</th>
					<th>Affiliate network link</th>
					<th>Home Page Stores Carousel</th>
					<th>Popular Stores</th>
					<th>Hide/Show</th>
					<th>Cashback Info</th>
					<th <?php echo $hideshowrow; ?>>Banner Image</th>
					<th <?php echo $hideshowrow; ?>>Upload Banner Image</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>RG ID</th>
					<th>Network</th>
					<th>MID</th>
					<th>Logo</th>
					<th>Name</th>
					<th>Country</th>
					<th>Affiliate network link</th>
					<th>Home Page Stores Carousel</th>
					<th>Popular Stores</th>
					<th>Hide/Show</th>
					<th>Cashback Info</th>
					<th <?php echo $hideshowrow; ?>>Banner Image</th>
					<th <?php echo $hideshowrow; ?>>Upload Banner Image</th>
					
				</tr>
			</tfoot>
		</table>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content"></div>
  </div>
</div>
	</div><?php
}
?>