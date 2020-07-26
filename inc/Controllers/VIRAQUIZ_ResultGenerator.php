<?php 
/**
 * Generate Quiz Result Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

use Inc\Controllers\VIRAQUIZ_RegisterFacebookUser;
use Inc\Controllers\VIRAQUIZ_DashboardStats;

if ( !class_exists( 'VIRAQUIZ_ResultGenerator' ) )
{
	
	class VIRAQUIZ_ResultGenerator
	{

		/**
		 *  Class constructor 
		 *
		 *  Start session 
		 */
		public function __construct()
		{

			session_start();

		}


		/**
		 *  Generate quiz result
		 */
		public function generate()
		{

			// Get user data stored in session
			$user_data = $_SESSION['viraquiz_facebook_user_data'];
			$quiz_id = $_SESSION['viraquiz_quiz_id'];

			$full_quiz_url = esc_url( get_the_permalink( $quiz_id ) );
			$quiz_results = get_post_meta( (int)$quiz_id, '_viraquiz_results', true );

			if( empty( $quiz_results ) ) {
				 
				// Redirect back if quiz don't have results
				session_destroy();
				header( 'Location: ' . $full_quiz_url );

			}

			// Generate random result according user gender
			$random_result = rand( 0, ( count( $quiz_results ) - 1 ) );
			$result_gender = $quiz_results[$random_result]['gender'];

			if( $result_gender != $user_data['gender'] && $result_gender != 'both' ) {
				$this->generate();
				return;
			}
			
			// Add result to user data array stored in session
			$_SESSION['viraquiz_facebook_user_data']['result'] = $random_result;


			// Register user in datatabase if not already registered
			$register_user = new VIRAQUIZ_RegisterFacebookUser;
			$register_user->registerUser( $user_data );


			// Update recent activity in Admin Dashboard
			$activity_user_data = array( 
						'fbid'	   => $user_data['fbid'],
						'fullname' => $user_data['fullname']
					 );

			$update_stats = new VIRAQUIZ_DashboardStats;
			$update_stats->updateDailyStats();
			$update_stats->updateRecentActivity( $quiz_id, $activity_user_data, 'did_quiz' );

			// Redirect to Show result page
			header( 'Location: ' . $full_quiz_url );

		}


	}


}