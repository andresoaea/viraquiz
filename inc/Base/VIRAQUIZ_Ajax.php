<?php 
/**
 * Handle AJAX Requests Class
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

use Inc\Helper\VIRAQUIZ_Sanitize;
use Inc\Controllers\VIRAQUIZ_DashboardStats;

if ( !class_exists( 'VIRAQUIZ_Ajax' ) )
{

	class VIRAQUIZ_Ajax
	{

		/**
		 *  Add actions 
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register()
		{

			add_action( 'wp_ajax_viraquiz_save_quiz_results', array( $this, 'viraquiz_save_quiz_results'  ) );
			add_action( 'wp_ajax_nopriv_viraquiz_count_shares', array( $this, 'viraquiz_count_shares'  ) );
			add_action( 'wp_ajax_viraquiz_count_shares', array( $this, 'viraquiz_count_shares'  ) );

			add_action('wp_ajax_nopriv_viraquiz_load_more', array( $this, 'viraquiz_load_more' ) );
			add_action('wp_ajax_viraquiz_load_more', array( $this, 'viraquiz_load_more' ) );

		}


		
		/**
		 *  AJAX SAVE QUIZ RESULTS 
		 */
		public function viraquiz_save_quiz_results()
		{

			/* Verify ajax nonce */
			$nonce = $_POST['nonce'];

			if ( ! wp_verify_nonce( $nonce, 'viraquiz-save-result' ) ) {
				die();
			}


			/* Get current quiz results */
			$results = get_post_meta( (int)$_POST['post_id'], '_viraquiz_results', true );


			if( isset( $_POST['method'] ) && $_POST['method'] == 'delete' ) {

				$delete_id = (int)$_POST['delete_id'];	
				unset( $results[$delete_id] );
				$results = array_values( $results );

			} else {

				if( ! $results ){
					$results = array();
				}

				$current_result = array();
				$current_result['gender'] = sanitize_text_field( $_POST['gender'] );
				$current_result['layers'] = VIRAQUIZ_Sanitize::quizResultLayers( $_POST['layers'] );

				$results[] = $current_result;


			}


			/* Update results */		
			if( $_POST['method'] == 'delete' ) {
				$update_results = update_post_meta( (int)$_POST['post_id'], '_viraquiz_results', $results );
			} else {

				if( !empty( $_POST['layers'] ) ) {
					$update_results = update_post_meta( (int)$_POST['post_id'], '_viraquiz_results', $results );
				} else {
					$update_results = false;
				}

			}
			


			/* Create html response */
			$html = '';

			foreach ( $results as $result_key => $result ) {		

				$gender = esc_attr( $result['gender'] );

					$html .= '<li class="quiz-results-item">';
					$html .= '<div class="result-item-header" data-result="' . $result_key . '">Result ' . ( $result_key + 1 )  .  '</div>';
					$html .= '<div class="result-item-body">';
					$html .= '<img src="' . esc_html( get_site_url() ) . '/viraquiz-result-schema/' . (int)$_POST['post_id'] . '/' . $result_key . '?t=' . time() . '">';
					$html .= '<div class="result-genders">';

					if( $gender == 'male' || $gender == 'both' ) {
						$html .= '<img src="' .  VIRAQUIZ_PLUGIN_URL . 'assets/images/male.png">';
					}

					if( $gender == 'female' || $gender == 'both' ) {
						$html .= '<img src="' .  VIRAQUIZ_PLUGIN_URL . 'assets/images/female.png">';
					}

					$html .= '<button type="button" class="delete-quiz-result"><span class="dashicons dashicons-trash"></span> Delete</button>';
					$html .= '</div>';	
					$html .= '</div>';				
					$html .= '</li>';

			}


			/* Send response back to JS */
			$response = array();

			if( $update_results ) {
				$response['status'] = 'success';
				$response['html'] = $html;
			} else {
				$response['status'] = 'error';
			}

			wp_send_json( $response );
			wp_die();

		}


		/**
		 *  AJAX COUNT FACEBOOK SHARES
		 */
		function viraquiz_count_shares()
		{

			/* Verify ajax nonce */
			$nonce = $_POST['nonce'];

			if ( ! wp_verify_nonce( $nonce, 'viraquiz-nonce' ) ) {
				die();
			}


		 	/* Update recent activity */
		 	$update_activity = VIRAQUIZ_DashboardStats::updateRecentActivity( $_POST['post_id'], $_POST['user_data'], 'shared' );


		 	/* Increase post shares meta */
		 	$post_shares = get_post_meta( (int)$_POST['post_id'], 'viraquiz_quiz_shares', true );

		 	if( empty( $post_shares ) ) {
		 		$post_shares = 0;
		 	} 

		 	$post_shares = (int)$post_shares + 1;

		 	update_post_meta( (int)$_POST['post_id'], 'viraquiz_quiz_shares', $post_shares );


		 	/* Increase total number of shares */
		 	$general_stats = get_option( 'viraquiz_general_stats' );
			$general_stats['total_shares'] = (int)$general_stats['total_shares'] + 1;

			update_option( 'viraquiz_general_stats', $general_stats );


			/* Increase daily shares number */
			$daily_stats = get_option( 'viraquiz_daily_stats' );
			$shares_count = $daily_stats[0]['shares'];
			$new_shares_count = (int)$shares_count + 1;
			$daily_stats[0]['shares'] = $new_shares_count;
			$count_share = update_option( 'viraquiz_daily_stats', $daily_stats );


			/* Send response back to JS */
			$response = array();

			if( $count_share ){
				$response['status'] = 'success';
			} else {
				$response['status'] = 'error';
			}

			wp_send_json( $response );
			wp_die();



		}


		/**
		 *  AJAX PAGINATION
		 */
		public function viraquiz_load_more()
		{

			/* Verify ajax nonce */
			$nonce = $_POST['nonce'];

			if ( ! wp_verify_nonce( $nonce, 'viraquiz-pagination-nonce' ) ) {
				die();
			}


			/* Prepare Wp_Query arguments */
			$paged = (int)$_POST['page'] + 1;
		    $args = array(
		        'post_type' 		=> 'viraquiz-app',
		        'post_status' 		=> 'publish',
		        'posts_per_page' 	=> 6,
		        'paged' 			=> $paged,
		    );


		    if( ! empty( $_POST['exclude'] ) ) {
		    	$args['post__not_in'] = array( (int)$_POST['exclude'] );
		    }

		    $page_trail = '/';
			$response_quizzes = '';
			$language = get_option( 'viraquiz_translation' );

			/* Instantiate new Wp_Query */
		    $query = new \WP_Query( $args );

		    if ($query->have_posts()):

		        $response_quizzes .= '<div class="fbq-page-limit fbq-ajax-loaded-quizzes" data-page="' . $page_trail . 'page/' . $paged . '/">';

			    while ($query->have_posts()): $query->the_post();

				    ob_start();
				    include VIRAQUIZ_PLUGIN_PATH . 'templates/frontend/single-templates/viraquiz-single-homepage-quiz.php';
				   	$response_quizzes .=  ob_get_contents();
					ob_end_clean();

			    endwhile;
			    $response_quizzes .= '</div>'; 

			else:
			    $response_quizzes = 0;
		    endif;

		    wp_reset_postdata();


		    /* Send response back to JS */
			$response = array();

			if( $query ) {
				$response['status'] = 'success';
				$response['quizzes'] = $response_quizzes;
			} else {
				$response['status'] = 'error';
			}

			wp_send_json( $response );
			wp_die();

			
		}


		/**
		 *  Get current page number
		 *
	     * @param  int         $num      Predefined page number
	     * @return string/int  $output   Current page
		 */
		public static function checkPaged( $num = null )
		{
		    $output = '';

		    if ( is_paged() ) {

		        $output = 'page/' . get_query_var('page');

		    }

		    if ( $num == 1 ) {
	 
		        $paged = ( get_query_var('page') == 0 ? 1 : get_query_var('page') );
		        return $paged;

		    } else {

		        return $output;

		    }

		}

	}


}