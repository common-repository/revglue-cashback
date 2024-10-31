<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
global $wpdb;
global $rg_db_version;
$rg_db_version = '1.0.0';
add_option("rg_db_version", $rg_db_version);
require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix.'rg_projects'; 
$sql= "CREATE TABLE IF NOT EXISTS `$table_name`  
(
	`rg_project_id` int(11) NOT NULL AUTO_INCREMENT,
	`subcription_id` varchar(255) NOT NULL,
	`user_name` varchar(100) NOT NULL,
	`email` varchar(100) NOT NULL,
	`project` varchar(100) NOT NULL,
	`expiry_date` varchar(100) NOT NULL,
	`partner_iframe_id` varchar(100) NOT NULL,
	`password` varchar(100) NOT NULL,
	`subscription_type` enum('Free','Paid') NOT NULL DEFAULT 'Paid',
	`status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
	PRIMARY KEY (`rg_project_id`)
) $charset_collate;";
dbDelta($sql);
$table_name = $wpdb->prefix.'rg_stores'; 
$sql = "CREATE TABLE IF NOT EXISTS `$table_name` 
(
	`rg_store_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mid` int(11) NOT NULL,
	`title` varchar(255) DEFAULT NULL,
	`url_key` varchar(255) NOT NULL,
	`description` text,
	`image_url` varchar(255) DEFAULT NULL,
	`banner_image_url` varchar(555) DEFAULT NULL,
	`affiliate_network` varchar(255) DEFAULT NULL,
	`affiliate_network_link` varchar(255) DEFAULT NULL,
	`store_base_currency` varchar(255) DEFAULT NULL,
	`store_base_country` varchar(255) DEFAULT NULL,
	`category_ids` varchar(128) DEFAULT NULL,
	`display_store` enum('yes','no') NOT NULL DEFAULT 'yes',
	`homepage_store_tag` enum('yes','no') NOT NULL DEFAULT 'no',
	`popular_store_tag` enum('yes','no') NOT NULL DEFAULT 'no',
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`status` enum('active','in-active') NOT NULL DEFAULT 'active',
	PRIMARY KEY (`rg_store_id`)
) $charset_collate;";
dbDelta($sql);
$table_name = $wpdb->prefix.'rg_categories'; 
$sql = "CREATE TABLE IF NOT EXISTS `$table_name` 
(
	`rg_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) DEFAULT NULL,
	`url_key` varchar(255) NOT NULL,
	`parent` int(11) DEFAULT NULL,
	`image_url` varchar(255) DEFAULT NULL,
	`image_url_pop_cat` varchar(255) DEFAULT NULL,
	`icon_url` varchar(255) DEFAULT NULL,
	`display` enum('yes','no') NOT NULL DEFAULT 'yes',
	`header_category_tag` enum('yes','no') NOT NULL DEFAULT 'no',
	`popular_category_tag` enum('yes','no') NOT NULL DEFAULT 'no',
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`status` enum('active','in-active') NOT NULL DEFAULT 'active',
	PRIMARY KEY (`rg_category_id`)
) $charset_collate;";
dbDelta($sql);
$table_name = $wpdb->prefix.'rg_banner'; 
$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 
(
	`rg_id` int(11) NOT NULL AUTO_INCREMENT,
	`rg_store_banner_id` int(11),
	`rg_store_id` int(11),
	`rg_store_name` varchar(255) NULL,
	`title` varchar(255) NOT NULL,
	`image_url` varchar(255) NOT NULL,
	`url` varchar(255) NOT NULL,
	`placement` varchar(100) NOT NULL,
	`rg_size` varchar(50) NULL,
	`banner_type` enum('local','imported') NOT NULL DEFAULT 'local',
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`status` enum('active','inactive') NOT NULL DEFAULT 'active',
	PRIMARY KEY (`rg_id`)
) $charset_collate;";
dbDelta($sql);
$table_name = $wpdb->prefix.'rg_cashback'; 
$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 
(
	`rg_cashback_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`rg_store_id` int(11),
	`cashback_type` enum('percentage','fixed') NOT NULL,
	`commission` float(10,2) NOT NULL, 
	`description` text,
	`status` enum('active','inactive') NOT NULL DEFAULT 'active',
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`rg_cashback_id`)
) $charset_collate;";
dbDelta($sql);
$table_name = $wpdb->prefix.'rg_user_cashback'; 
$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 
(
	`rg_usercashback_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`rg_exitclick_id` int(11) DEFAULT NULL,
	`rg_store_id` int(11) NOT NULL,
	`rg_user_id` int(11) NOT NULL,
	`commission` float(10,2) NOT NULL, 
	`cashback` float(10,2) NOT NULL,
	`status` enum('confirmed','pending','requested','paid') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'pending',
	`order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (`rg_usercashback_id`)
) $charset_collate;";
dbDelta($sql);
$table_name = $wpdb->prefix.'rg_user_cashout'; 
$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 
(
	`rg_usercashout_id` int(11) NOT NULL AUTO_INCREMENT,
	`rg_user_id` int(11),
	`amount` varchar(100) NOT NULL,
	`status` enum('requested', 'paid') NOT NULL DEFAULT 'requested',
	`payment_date` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	`date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`date_updated` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`rg_usercashout_id`)
) $charset_collate;";
dbDelta($sql);
$table_name = $wpdb->prefix.'rg_payment_method'; 
$sql= "CREATE TABLE IF NOT EXISTS `$table_name` 
(
	`rg_pm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`pm_paypal_email_address` varchar(250),
	`pm_bank_name` varchar(250),
	`pm_bank_account_name` varchar(250),
	`pm_bank_account_number` varchar(250),
	`pm_bank_account_softcode` varchar(250),
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`rg_pm_id`)
) $charset_collate;";
dbDelta($sql);
?>