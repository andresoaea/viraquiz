<?php 
/**
 * Download Users List as CSV Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_DownloadCSV' ) )
{

	class VIRAQUIZ_DownloadCSV
	{

		/**
		 *  Add actions
		 */
		public function register()
		{

			add_action( 'restrict_manage_posts', array( $this, 'printDownloadButton' ) );
			add_action( 'init', array( $this, 'download' ) );

		}


		/**
		 *  Print "Export Users List as CSV" button on Admin area
		 */
		public function printDownloadButton()
		{

			$screen = get_current_screen();

	    	if ( isset($screen->parent_file ) && ( 'edit.php?post_type=viraquiz-user' == $screen->parent_file ) ) {

				$output = '<input type="submit" name="export_fb_users" id="export_fb_users" class="button button-primary" value="Export Users List as CSV">';
				$output .= '<script type="text/javascript">';
				$output .= 'jQuery(function($) {';
				$output .= '$("#export_fb_users").insertAfter("#post-query-submit");';
				$output .= '});';
				$output .= '</script>';

				echo $output;

			}

		}


		/**
		 *  Download users list as CSV
		 */
		public function download()
		{

			// Get users list
			if( isset( $_GET['export_fb_users'] ) ) {

				$arg = array(
						'post_type'      => 'viraquiz-user',
						'post_status' 	 => 'publish',
						'posts_per_page' => -1,
					);

				global $post;

				$arr_post = get_posts( $arg );

				// Generate dowload file
				if ( $arr_post ) {

					header( 'Content-type: text/csv' );
					header( 'Content-Disposition: attachment; filename="viraquiz-users-list-' . date( 'd-m-Y' ) . '.csv"' );
					header( 'Pragma: no-cache' );
					header( 'Expires: 0' );

					$file = fopen( 'php://output', 'w' );

					fputcsv( $file, array( 'Full User Name', 'Email', 'User Facebook ID' ) );

					foreach ( $arr_post as $post ) {

						setup_postdata( $post );

						$user_data = get_post_meta( get_the_ID(), 'viraquiz_user_data', true );

						fputcsv( $file, array( $user_data['fullname'], $user_data['email'], $user_data['fbid'] ) );

					}

					exit();
				}

			}

		}

	}

}