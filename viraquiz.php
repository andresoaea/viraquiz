<?php  
/*
Plugin Name: Viraquiz - Viral Facebook quizzes / apps
Plugin URI: https://github.com/andresoaea/viraquiz
Description: Turn your wordpress site into a Viral Facebook quizzes / applications  website
Version: 1.0.0
Author: Constantin Andresoaea
*/

// If this file is called directly, abort!
if ( !defined('ABSPATH') ) exit;

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// Define plugin CONSTANTS
define( 'VIRAQUIZ_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'VIRAQUIZ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VIRAQUIZ_PLUGIN', plugin_basename( __FILE__ ) );
define( 'VIRAQUIZ_PLUGIN_VERSION', '1.0.0' );


/**
 * The code that runs during plugin activation
 */
function viraquiz_activate_plugin() {
	 Inc\Base\VIRAQUIZ_Activate::activate();
}
register_activation_hook( __FILE__, 'viraquiz_activate_plugin' );


/**
 * The code that runs during plugin deactivation
 */
function viraquiz_deactivate_plugin() {
	Inc\Base\VIRAQUIZ_Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'viraquiz_deactivate_plugin' );



/**
 * Initialize all the core classes of the plugin
 */
Inc\VIRAQUIZ_Plugin_Init::register_services();