<?php
/*
Plugin Name: BBWP Custom Fields
Plugin URI: https://bytebunch.com
Description: Allows you to add additional Meta Boxes with custom fields into Post types, Taxonomies, User Profile, Comments and more.
Author: Team ByteBunch
Version: 0.0.1
Author URI: https://bytebunch.com
Text Domain: bbwpcustomfields
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//define('BBWPOP','bbwpop');
define('BBWP_CF_URL', plugin_dir_url(__FILE__));
define('BBWP_CF_ABS', plugin_dir_path( __FILE__ ));
define('BBWP_CF_PLUGIN_FILE', plugin_basename(__FILE__));

include_once BBWP_CF_ABS.'inc/functions.php';

if(is_admin_panel()){

	if(!class_exists('BBWPSanitization'))
		include_once BBWP_CF_ABS.'inc/classes/BBWPSanitization.php';

	if(!class_exists('BBWPListTable'))
		include_once BBWP_CF_ABS.'inc/classes/BBWPListTable.php';

	if(!class_exists('BBWPFieldTypes'))
		include_once BBWP_CF_ABS.'inc/classes/BBWPFieldTypes.php';


	if(!class_exists('BBWP_CustomFields')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CustomFields.php';
		$BBWP_CustomFields = new BBWP_CustomFields();
	}

	if(!class_exists('BBWP_CF_PageSettings')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_PageSettings.php';
		$BBWP_CF_PageSettings = new BBWP_CF_PageSettings();
	}

	if(!class_exists('BBWP_CF_CreateMetaBoxes')){
		include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_CreateMetaBoxes.php';
		$BBWP_CF_CreateMetaBoxes = new BBWP_CF_CreateMetaBoxes();
	}

}

if(!class_exists('BBWP_CF_CustomPostType')){
	include_once BBWP_CF_ABS.'inc/classes/BBWP_CF_CustomPostType.php';
	$BBWP_CF_CustomPostType = new BBWP_CF_CustomPostType();
}
