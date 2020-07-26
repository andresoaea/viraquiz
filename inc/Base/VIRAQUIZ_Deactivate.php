<?php
/**
 * Plugin Deactivation Class
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

use Inc\Pages\Frontend\VIRAQUIZ_Homepage;

if ( !class_exists( 'VIRAQUIZ_Deactivate' ) )
{
	class VIRAQUIZ_Deactivate
	{

		/**
		 *  Master plugin deactivation function
		 */
		public static function deactivate() 
		{

			// Set default site homepage, which was before adding plugin
			VIRAQUIZ_Homepage::unsetHomepage();

			//Flush rewrite rules
			flush_rewrite_rules();
			
		}	
		
	}

}