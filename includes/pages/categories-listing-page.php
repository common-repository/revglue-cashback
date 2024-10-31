<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_cashback_category_listing_page()
{
    global $wpdb;
	$categories_table = $wpdb->prefix.'rg_categories';
	$sql = "SELECT *FROM $categories_table WHERE `parent` = 0 ORDER BY `title` ASC";
	$categories = $wpdb->get_results($sql);
	?>
	<div class="rg-admin-container">
		<h1 class="rg-admin-heading ">Categories</h1>
		<div style="clear:both;"></div>
		<hr/>
		<p class="text-right">The categories you have selected at RevGlue are showing below. You may change them on RevGlue and run import categories link again from upload cashback menu.</p>
		<table id="categories_admin_screen" class="display" cellspacing="0" width="100%">
		<input data-thisthemname="<?php echo $rg_this_theme_name= get_option("rg_this_theme_name");  ?>" type="hidden" />
			<thead>
				<tr>
					<th>S.No.</th>
					<th>Category Name</th>
					<th>Status</th>
					<?php 
					$rg_this_theme_name= get_option("rg_this_theme_name"); 
					if($rg_this_theme_name=="cashbox"){
					?>
					<th>Popular Category Image</th>
					<?php } ?>
					<th>Category Icon</th>
					<th>Category Image</th>
					<th>Header Categories</th>
					<th>Popular Categories</th>
					<th>Hide/Show</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>S.No.</th>
					<th>Category Name</th>
					<th>Status</th>
					<?php 
					$rg_this_theme_name= get_option("rg_this_theme_name"); 
					if($rg_this_theme_name=="cashbox"){
					?>
					<th>Popular Category Image</th>
					<?php } ?>
					<th>Category Icon</th>
					<th>Category Image</th>
					<th>Header Categories</th>
					<th>Popular Categories</th>
					<th>Hide/Show</th>
					<th>Actions</th>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$counter = 1;
			foreach ( $categories as $single_category )
			{
				$parent_title = '';
				rg_cashback_populate_recursive_categories( $single_category, $parent_title, $counter );
				++$counter;
			} 
			?>
			</tbody>
		</table>
	</div>
	<?php
}
?>