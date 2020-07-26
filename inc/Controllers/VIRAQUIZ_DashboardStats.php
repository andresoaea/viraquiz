<?php 
/**
 * Viraquiz Plugin Dashboard Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_DashboardStats' ) )
{

	class VIRAQUIZ_DashboardStats
	{

		/**
		 *  Class constructor
		 *  Call updateDailyStats method
		 */
		public function __construct()
		{

			$this->updateGeneralStats();

		}


		/**
		 *  Update daily statistics on Plugin Dashboard
		 */
		public function updateDailyStats()
		{

			$dates = [];
			$months = array( "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec" );

			for( $i = 0; $i < 15; $i++ ) {

				$day_decrease = $i;
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


			$today = date_parse( date( 'Y-m-d' ) );
			$last_date = get_option( 'viraquiz_last_date' );
	 		$daily_stats = get_option( 'viraquiz_daily_stats');
			$dates_compare = ( $today == $last_date );

			if( $dates_compare ) {
				
				$stats_new = $stats;
				
				if( ! empty( $daily_stats ) ) {
					$stats_new = $daily_stats;
				}

				$generated_results = (int)$daily_stats[0]['generated_results'] + 1;
				$stats_new[0]['generated_results'] = $generated_results;

				update_option( 'viraquiz_daily_stats', $stats_new );

			} else {

				$last_stats = $stats;

				if( ! empty( $daily_stats ) ) {
					$last_stats = $daily_stats;
				} 

				$stats_new = $stats[0];
				$stats_new['generated_results'] = 1;

				array_pop( $last_stats );
				array_unshift( $last_stats, $stats_new );

				update_option( 'viraquiz_daily_stats', $last_stats );
				update_option( 'viraquiz_last_date', $today );
			}


		}


		/**
		 *  Update recent activity on Plugin Dashboard
		 *
		 * @param  int      $post_id          Current Post ID
		 * @param  array    $user_data        User details 
		 * @param  string   $action           What user do on site ( shared quiz / did quiz )
	     * @return bool     $update_activity  True if viraquiz_recent_activity was updated, false otherwise
		 */
		public static function updateRecentActivity( $post_id, $user_data, $action ) 
		{

			$new_activity = $user_data;
			$new_activity['post_id'] = $post_id;
			$new_activity['action'] = $action;

			$full_date_time = date_parse( date('Y-m-d H:i') );
			$date_time = array( 'month' => $full_date_time['month'], 'day' => $full_date_time['day'], 'hour' => $full_date_time['hour'], 'minute' => $full_date_time['minute'] );

			$new_activity['date_time'] = $date_time;
			$recent_activity = get_option( 'viraquiz_recent_activity' );

			if( count( $recent_activity ) < 20 ) {
				array_unshift( $recent_activity, $new_activity );
			} else {
				array_unshift( $recent_activity, $new_activity );
				array_pop( $recent_activity );
			}
			
			$update_activity = update_option( 'viraquiz_recent_activity', $recent_activity );

			return $update_activity;


		}


		/**
		 *  Update general statistics on Plugin Dashboard
		 */
		public function updateGeneralStats()
		{

			$general_stats = get_option( 'viraquiz_general_stats' );
			$general_stats['total_generated_results'] = (int)$general_stats['total_generated_results'] + 1;

			update_option( 'viraquiz_general_stats', $general_stats );

		}

	}

}