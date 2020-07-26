<?php 
/**
 * Generate Facebook Application Configuration Admin Page Class
 *
 * @package  viraquiz
 */

namespace Inc\Pages\Backend;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_FbAppConfig' ) )
{
	
	class VIRAQUIZ_FbAppConfig 
	{

		/**
		 * Facebook application configuration
		 *
		 * @var array
		 */
		protected $app_config;


		/**
		 * Class constructor 
		 *
		 * Set $app_config variable 
		 */
		public function __construct() 
		{

			$this->app_config = get_option( 'viraquiz_fb_app_config' );

		}


		/**
		 * Add actions
		 */
		public function register()
		{

			add_action( 'admin_init', array( $this, 'settingsInit' ) );

		}


		/**
		 *  Registser options, sections and fileds
		 */
		public function settingsInit()
		{

			//Settings
			register_setting( 'vrq-fb-app-config-settings', 'viraquiz_fb_app_config', 'Inc\Helper\VIRAQUIZ_Sanitize::fbAppConfig' );

			//Sections 
			add_settings_section( 'vrq-fb-app-config-settings', 'Facebook APP', array( $this, 'appConfigSectionCallback' ), 'viraquiz_app_config' );

			//Fields 
			add_settings_field( 'vrq-app-id', 'Facebook APP ID', array( $this, 'appId' ), 'viraquiz_app_config', 'vrq-fb-app-config-settings' );
			add_settings_field( 'vrq-app-secret', 'Facebook APP Secret', array( $this, 'appSecret' ), 'viraquiz_app_config', 'vrq-fb-app-config-settings' );


		}

		/**
		 *  App Config Section Callback
		 */
		public function appConfigSectionCallback()
		{

			echo 'Please configure the Facebook Application';

		}


		/**
		 *  Generate App Id Input 
		 */
		public function appId()
		{

			$value = ( ! empty( $this->app_config['app_id'] ) ? $this->app_config['app_id'] : '' );

			echo '<input type="text" name="viraquiz_fb_app_config[app_id]" value="' . $value . '" />';
			
		}	


		/**
		 *  Generate App Secret Input 
		 */
		public function appSecret()
		{

			$value = ( ! empty( $this->app_config['app_secret'] ) ? $this->app_config['app_secret'] : '' );

			echo '<input type="text" name="viraquiz_fb_app_config[app_secret]" value="' . $value . '" />';
			
		}


	}


}