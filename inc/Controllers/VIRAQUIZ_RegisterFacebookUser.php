<?php 
/**
 * Register Facebook Users Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_RegisterFacebookUser' ) )
{

	class VIRAQUIZ_RegisterFacebookUser
	{

		/**
		 * Register New Facebook Users 	
		 * @param array $user_data All user details ( Facebook ID, Email, Firstname, Lastname, Fullname )	
		 */
		public function registerUser( $user_data )
		{

			$new_user_registered = false;
			$user_fb_id = filter_var( $user_data['fbid'], FILTER_SANITIZE_NUMBER_INT );
			$user_data = array_map( 'wp_strip_all_tags', $user_data );

			unset( $user_data['result'] );

			if ( ! function_exists( 'post_exists' ) ) {
			    require_once( ABSPATH . 'wp-admin/includes/post.php' );
			}

			// Check if user is already registered
			if( post_exists( $user_fb_id ) == 0 && !empty( $user_fb_id ) ) {

				// Create post object
				$user_post = array(
				  'post_title'    => $user_fb_id,
				  'post_status'   => 'publish',
				  'post_author'   => 1,
				  'post_type'     => 'viraquiz-user',
				  'meta_input'    => array( 
				  		'viraquiz_user_data' => $user_data
				   )

				);
				 
				// Insert the post into the database
				$new_user_registered = wp_insert_post( $user_post );

				$this->saveUserGender( $user_data['gender'] );
				
			}

			// Update general stats
			$this->updateGeneralStats( $new_user_registered );

		}


		/**
		 *  Update Dashboard General Stats
		 *
		 *  @param bool $new_user_registered True if a new user is registered, false if user is already registered
		 */
		public function updateGeneralStats( $new_user_registered )
		{

			$general_stats = get_option( 'viraquiz_general_stats' );

			if( $new_user_registered ) {
				$general_stats['total_users'] = (int)$general_stats['total_users'] + 1;
			}

			if( date_parse( date( 'Y-m-d' ) ) == get_option( 'viraquiz_last_date' ) ) {

				if( $new_user_registered ) {
					$general_stats['new_users'] = (int)$general_stats['new_users'] + 1;
				}
				

			} else {
				
				if( $new_user_registered ) {
					$general_stats['new_users'] = 1;
				} else {
					$general_stats['new_users'] = 0;
				}
				
			}

			update_option( 'viraquiz_general_stats', $general_stats );

		}


		/**
		 * Save user gender to Dashboard stats 
		 *
		 * @param string $user_gender User gender ( male / female )
		 */
		public function saveUserGender( $user_gender )
		{

			if( $user_gender == 'male' || $user_gender == 'female' ) {

				$gender_stats = get_option( 'viraquiz_stats_genders' );
				$curr_gender_count = $gender_stats[$user_gender];
				$gender_stats[$user_gender] = (int)$curr_gender_count + 1;

				update_option( 'viraquiz_stats_genders', $gender_stats );

			}


		}


	}


}	