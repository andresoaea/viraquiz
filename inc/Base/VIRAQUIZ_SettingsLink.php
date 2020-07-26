<?php
/**
 * Generate Settings Links on Plugins Page
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_SettingsLink' ) )
{

	class VIRAQUIZ_SettingsLink
	{

		/**
		 * Plugin name
		 *
		 * @var string 
		 */
		protected $plugin;


		/**
		 *  Class constructor
		 *  Set $plugin variable
		 */
		public function __construct()
		{

			$this->plugin = VIRAQUIZ_PLUGIN;

		}


		
		/**
		 *  Add filters
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register() 
		{

			add_filter( "plugin_action_links_$this->plugin", array( $this, 'settingsLink' ) );

		}


		/**
		 *  Add new settings links to existing settings array
		 *
	     * @param  array  $links Default settings array
	     * @return array  $links Modified settings array
		 */
		public function settingsLink( $links ) 
		{

			$dashboard_link = '<a href="admin.php?page=viraquiz_plugin">Dashboard</a>';
			$app_config_link = '<a href="admin.php?page=viraquiz_app_config">App Config</a>';

			array_push( $links, $dashboard_link );
			array_push( $links, $app_config_link );

			return $links;

		}

	}

}