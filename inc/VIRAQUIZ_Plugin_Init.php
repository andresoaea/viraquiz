<?php
/**
 * Initialize all the core classes of the plugin
 *
 * @package  viraquiz
 */

namespace Inc;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_Plugin_Init' ) )
{
	
	final class VIRAQUIZ_Plugin_Init
	{
		/**
		 * Store all the classes inside an array
		 * @return array Full list of classes
		 */
		public static function get_services() {

			$plugin_classes = array( 

				Base\VIRAQUIZ_Ajax::class,
				Base\VIRAQUIZ_WP_Init::class,
				Base\VIRAQUIZ_Enqueue::class,
			    Base\VIRAQUIZ_Metaboxes::class,
			    Base\VIRAQUIZ_RegisterCPT::class,
			    Base\VIRAQUIZ_RewriteRules::class,
			    Base\VIRAQUIZ_SettingsLink::class,
			    Controllers\VIRAQUIZ_Facebook::class,
			    Controllers\VIRAQUIZ_DownloadCSV::class,
			    Controllers\VIRAQUIZ_TemplateController::class,

			    Pages\Backend\VIRAQUIZ_Admin::class,
			    Pages\Backend\VIRAQUIZ_FbAppConfig::class,
			    Pages\Backend\VIRAQUIZ_Translation::class,
			    Pages\Backend\VIRAQUIZ_GeneralSettings::class,

			 );


			return $plugin_classes;

		}

		/**
		 * Loop through the classes, initialize them, 
		 * and call the register() method if it exists
		 * @return
		 */
		public static function register_services() {

			foreach ( self::get_services() as $class ) {

				$service = self::instantiate( $class );

				if ( method_exists( $service, 'register' ) ) {
					$service->register();
				}

			}

		}

		/**
		 * Initialize the class
		 * @param  class $class    Class from the services array
		 * @return class instance  New instance of the class
		 */
		private static function instantiate( $class ) {

				$service = new $class();
				return $service;
			
		}

	}

}