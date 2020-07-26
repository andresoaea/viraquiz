<?php
/**
 * Plugin Activation Class
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

use Inc\Base\VIRAQUIZ_RegisterCPT;
use Inc\Base\VIRAQUIZ_RewriteRules;
use Inc\Pages\Frontend\VIRAQUIZ_Homepage;


if ( !class_exists( 'VIRAQUIZ_Activate' ) )
{

	class VIRAQUIZ_Activate
	{

		/**
		 *  Master plugin activation function
		 */
		public static function activate()
		{
			//Set new homepage
			new VIRAQUIZ_Homepage;

			//Register custom post type
			VIRAQUIZ_RegisterCPT::registerCPT();

			//Add rewrite rules
			VIRAQUIZ_RewriteRules::pluginRules();

			//Save predefined plugin options
			self::addPluginOptions();

			//Flush rewrite rules
			flush_rewrite_rules();

		}


		/**
		 * Save predefined plugin options into database
		 */
		public static function addPluginOptions()
		{

			//Add Dashboard General Stats Option
			add_option( 'viraquiz_general_stats', array( 
				'new_users' 			    => 0,
				'total_users' 				=> 0,
				'total_generated_results' 	=> 0,
				'total_shares' 				=> 0
			 ) );

			//Add Dashboard genders option
			$genders = array( 'male' => 0, 'female' => 0 );
			add_option( 'viraquiz_stats_genders', $genders );

			//Add Dashboard recent activity option
			add_option( 'viraquiz_recent_activity', array() );

			//Add Dashboard daily stats option 
			$dates = [];
			$months = array( "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec" );

			for( $i = 0; $i < 15; $i++ ) {

				$day_decrease = $i + 1;
				$day = date('d', strtotime('today - ' . $day_decrease . ' days'));
				$day = sprintf("%d", $day);
				$month = date('m', strtotime('today - ' . $day_decrease . ' days'));

				$dates[] = $day . ' ' . $months[($month - 1)];

			}


			$stats = [];

			foreach ( $dates as $date_key => $date ) {
				
				$stats[$date_key]['date'] = $date;
				$stats[$date_key]['generated_results'] = 0;
				$stats[$date_key]['shares'] = 0;

			}

			add_option( 'viraquiz_daily_stats', $stats );
			add_option( 'viraquiz_last_date', date_parse( date( 'Y-m-d' ) ) );


		}


	}

}