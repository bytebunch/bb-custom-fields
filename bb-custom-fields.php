<?php
/*
Plugin Name: BB Custom Fields
Plugin URI: https://bytebunch.com
Description: Allows you to add additional Meta Boxes with custom fields into Post types, Taxonomies, User Profile, Comments and more.
Author: Team ByteBunch
Version: 0.0.1
Author URI: https://bytebunch.com
Text Domain: bbcustomfields
Domain Path: /languages/
*/

//define('BBWPOP','bbwpop');
define('BBCUSTOMFIELDS_URL', plugin_dir_url(__FILE__));
define('BBCUSTOMFIELDS_ABS', plugin_dir_path( __FILE__ ));
define('BBCUSTOMFIELDS_PLUGIN_FILE', plugin_basename(__FILE__));

include_once BBCUSTOMFIELDS_ABS.'inc/functions.php';

if(is_admin_panel()){

	if(!class_exists('BBWPSanitization'))
		include_once BBCUSTOMFIELDS_ABS.'inc/classes/BBWPSanitization.php';

	if(!class_exists('BBWPListTable'))
		include_once BBCUSTOMFIELDS_ABS.'inc/classes/BBWPListTable.php';

	if(!class_exists('BBWPFieldTypes'))
		include_once BBCUSTOMFIELDS_ABS.'inc/classes/BBWPFieldTypes.php';


	if(!class_exists('BBCustomFields')){
		include_once BBCUSTOMFIELDS_ABS.'inc/classes/BBCustomFields.php';
		$BBCustomFields = new BBCustomFields();
	}

	if(!class_exists('PageSettingsBBCF')){
		include_once BBCUSTOMFIELDS_ABS.'inc/classes/PageSettingsBBCF.php';
		$PageSettingsBBCF = new PageSettingsBBCF();
	}

	if(!class_exists('CreateMetaBoxesBBCF')){
		include_once BBCUSTOMFIELDS_ABS.'inc/classes/CreateMetaBoxesBBCF.php';
		$CreateMetaBoxesBBCF = new CreateMetaBoxesBBCF();
	}

}
