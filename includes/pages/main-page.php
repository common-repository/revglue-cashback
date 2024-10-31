<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
function rg_cashback_main_page()
{

	$template_type = revglue_check_subscriptions();
	$re='';
	if ($template_type=="Free"){
		$re ='RevEmbed';
	}else{
		
		$re ='RevGlue';
	}
	?><div class="rg-admin-container">
		<h1 class="rg-admin-heading ">Welcome to <?php echo $re; ?> Cashback WordPress CMS Plugin</h1>
		<div style="clear:both;"></div>
		<hr/>
		<div class="panel-white mgBot">
			<h3>Introduction</h3>
			<p>RevGlue provides WordPress plugins for affiliates that are free to download and earn 100% commissions. RevGlue provides the following WordPress plugins.</p>
			<ul class="pagelist">
				<li>RevGlue Store  - setup your shopping directory</li>
				<li>RevGlue Coupons – setup your vouchers / coupons website.</li>
				<li>RevGlue Cashback – setup your cashback website in minutes.</li>
				<li>RevGlue Product Feeds – setup your niche product website.</li>
				<li>RevGlue Daily Deals – setup your daily deals aggregation engine in minutes.</li>
				<li>RevGlue Mobile Comparison – setup mobile comparison website in minutes.</li>
				<li>Banners API – add banners on your projects integrated in all plugins above.</li>
				<li>Broadband & TV - Setup broadband, tv and phone comparison website in minutes.</li>
			</ul>
		</div>
		<div class="panel-white mgBot">
<?php
if($template_type="Free"){
?>

<h3><?php echo $re;?> Cashback Data and WordPress CMS Plugin</h3>
<p>There are two ways you can obtain Cashback data in this plugin.</p>
<p> <b> 1 </b> - Subscribe to RevGlue affiliate Cashback data for £60 and add your own affiliate network IDs to earn 100% commission on your affiliate network accounts.      
Try is free for the first 30 days. Create RevGlue.com user account and subscribe with affiliate Cashback data set today. </p>
<p> <b>2 </b> - You can use RevEmbed Cashback data set that is free to use and you are not required to create affiliate network accounts. RevEmbed data set for Cashback offers 80% commission to you on all the sales referred from your Cashback website. This is based on revenue share basis with RevGlue that saves your time and money and provides you ability to create your Cashback website in minutes. Browse RevEmbed module. Once you register for any both data source from the options given above. 
You will be provided with the project unique id that you are required to add in Import Cashback section and fetch the Cashback data. </p>
<?php }else{?>    
<h3><?php echo $re; ?> Cashback WordPress CMS Plugin</h3>
<p>The aim of RevGlue Cashback plugin is to allow you to setup a cashback website in UK. You will earn 100% commissions generated via the plugin and the CMS is totally free for all affiliates. You may make further copies or download latest versions from RevGlue website. You will require RevGlue account and then subscribe to RevGlue Cashback data set to setup UK cashback website. </p>
<?php } ?>    
		</div>
		<div class="panel-white mgBot">
			<h3><?php echo $re; ?> Cashback Menu Explained</h3>
			<p><b>Import Cashback Data</b>– Add your RevGlue Data account credentials to validate your account and obtain RevGlue Cashback Data. </p>
			<p><b>Cashback</b>– Shows all Cashback data obtained via RevGlue Data API. </p>
			<p><b>Categories</b>- Cashback categories obtained from RevGlue Cashback Data API under upload Cashback menu.</p>
			<p><b>Import Stores</b> - Add your RevGlue Data account credentials to validate your account and obtain RevGlue Cashback Stores Data. </p>
			<p><b>Stores</b> - Shows all stores data obtained via RevGlue Data API. The Data api only fetches the stores you have selected on your RevGlue account so make sure you have selected all the daily deals stores. </p>
			<p><b>User Cashback</b> -These are the cashback that are obtained via RevGlue Data API. </p>
			<p><b>User Cashout</b> -These are the user cashouts that are request by the users for cashouts, it is also linked with the cashout threshhold can be on cashback settings. </p>
			<p><b>Cashback Settings</b> -On cashback settings page, website admin can setup the threshhold for user cashout, cashback dividend percentage. </p>
			<p><b>Import Banners</b>– Add your RevGlue Data account credentials to validate your account and obtain RevGlue Banners Data. Use CRON file path to setup on your server to auto update the data dynamically.</p>
			<p><b>Banners</b>- Allows you to add your own banner on website placements that are pre-defined for you. You may add multiple banners on one placements and they will auto change on each refresh. You may also subscribe with RevGlue Banners API and obtain latest banners for each Daily Deals from RevGlue Banners. The banners you may add are known as LOCAL banners and others obtained via RevGlue Banner API are shown as RevGlue Banners.</p>
			<p><b>User Tickets</b> -Under this menu website ownwer will be able to answer the tickets that are submitted by the users for support.  </p>
			<p><b>Store Reviews</b> -These are user reviews on this local WordPress and does not relate to any api data. You can validate each review before setting it live. </p>
			<p><b>Exit Clicks</b> - This report shows all exit clicks from your WordPress project.</p>
			<p><b>Newsletter Subscribers</b>- Here is the list of all newsletter subscribers for you that have opted in for newsletter on your WordPress stores cms. </p>
		</div>
		<div class="panel-white mgBot">
			<h3>Further Development</h3>
			<p>If you wish to add new modules or require additional design or development changes then contact us via email, support@revglue.com.</a></p>
			<p>
				We are happy to analyse the required work and provide you a quote and schedule. 
			</p> 
		</div>
		<div class="panel-white mgBot">
			<h3>Useful Links</h3>
			<p><b>RevGlue</b>- <a href="https://www.revglue.com/" target="_blank">https://www.revglue.com/</a></p>
			<p><b>RevGlue Cashback Data</b>- <a href="https://www.revglue.com/data/cashback" target="_blank">https://www.revglue.com/data/cashback</a></p>
			<p><b>RevGlue WordPress CMS</b>- <a href="https://www.revglue.com/free-wordpress-plugins" target="_blank">https://www.revglue.com/free-wordpress-plugins</a></p>
			<p><b>RevGlue New Cashback Templates</b>- <a href="https://www.revglue.com/affiliate-website-templates" target="_blank">https://www.revglue.com/affiliate-website-templates</a></p>
		</div>
	</div><?php		
}
?>