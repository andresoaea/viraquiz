<?php 
/**
 *
 * @package viraquiz
 *
 * Enqueue scripts
 *
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

use Inc\Controllers\VIRAQUIZ_FontsController;

if ( !class_exists( 'VIRAQUIZ_Enqueue' ) )
{
	class VIRAQUIZ_Enqueue 
	{

		
		/**
		 *  Add actions
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register() {
			
		    add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdmin' ) );

		    add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueueAdminCPT' ), 11 );
			add_action( 'admin_print_scripts-post.php', array( $this, 'enqueueAdminCPT' ), 11 );



			add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontend' ) );


		}
	 

		/**
		 * Enqueue frontend scripts
		 */
		public function enqueueFrontend() {

				//Css
				wp_enqueue_style( 'dashicons' );
				wp_enqueue_style( 'viraquiz', VIRAQUIZ_PLUGIN_URL . 'assets/css/viraquiz.css', array(), VIRAQUIZ_PLUGIN_VERSION, 'all' );


				//Js 
				wp_enqueue_script( 'viraquiz', VIRAQUIZ_PLUGIN_URL . 'assets/js/viraquiz.js', array( 'jquery' ), VIRAQUIZ_PLUGIN_VERSION, true );

				$fb_app_config = get_option( 'viraquiz_fb_app_config' );

				if( is_single() ) {

					wp_localize_script( 'viraquiz', 'VIRAQUIZ_fb_app', array( 

							'status' => ( ! empty( $fb_app_config['app_id'] ) && ! empty( $fb_app_config['app_secret'] ) ? 'is-set' : 'is-not-set' )

					   ) ); 

				}

				$language = get_option( 'viraquiz_translation' );

			    wp_localize_script( 'viraquiz', 'VIRAQUIZ_ajax_pagination', array( 

			    	'url' 		=> esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'    	=> wp_create_nonce( 'viraquiz-pagination-nonce' ),
					'strings'   => array(
							'loading_more' 	  => ( ! empty( $language['loading_more'] ) ? esc_html( $language['loading_more'] ) : 'Loading more...' ),
							'no_more_quizzes' => ( ! empty( $language['no_more_quizzes'] ) ? esc_html( $language['no_more_quizzes'] ) : 'No more quizzes to load' )
						)
	 

			     ) );


				if( ! empty( $_SESSION['viraquiz_facebook_user_data'] ) ) {

					$user_data = array( 
						'fbid'	   => $_SESSION['viraquiz_facebook_user_data']['fbid'],
						'fullname' => $_SESSION['viraquiz_facebook_user_data']['fullname']
					 );

				
					wp_localize_script( 'viraquiz', 'VIRAQUIZ_Vars', array( 

						'ajax'	 	=> array( 
							'url' 		=> esc_url( admin_url( 'admin-ajax.php' ) ),
							'nonce'    	=> wp_create_nonce( 'viraquiz-nonce' )
						 ),
						'post_id'  	=> (int)get_the_ID(),
						'user_data' => $user_data,
						


				    ) );
			    }
			

		}


		/**
		 * Enqueue admin custom post type scripts
		 */
		public function enqueueAdminCPT( $hook ) {

			global $post_type;

		    if( 'viraquiz-app' == $post_type ){

		    	wp_enqueue_style( 'wp-color-picker' ); 
		    	wp_enqueue_style( 'roboto', 'https://fonts.googleapis.com/css?family=Roboto' );

		        wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css', array(), '1.12.1', 'all' );
		        wp_enqueue_script( 'jquery-ui-draggable' );
		        wp_enqueue_script( 'jquery-ui-resizable' );
		        wp_enqueue_script( 'jquery-ui-slider' );

		    	wp_enqueue_style( 'viraquiz-admin',  VIRAQUIZ_PLUGIN_URL . 'assets/css/viraquiz-admin-cpt.css', array(), VIRAQUIZ_PLUGIN_VERSION, 'all' );
		        wp_enqueue_script( 'viraquiz-admin', VIRAQUIZ_PLUGIN_URL . 'assets/js/viraquiz-admin-cpt.js', array( 'jquery', 'wp-color-picker' ), VIRAQUIZ_PLUGIN_VERSION, true );
		        wp_localize_script( 'viraquiz-admin', 'VIRAQUIZ_vars', array(

		        		'fonts'   				 => VIRAQUIZ_FontsController::getAllFonts(),
		        		'assets_url'			 => VIRAQUIZ_PLUGIN_URL . '/assets',
		        		'site_url'				 => esc_html( get_site_url() ),
		        		'ajax_save_result_nonce' => wp_create_nonce( 'viraquiz-save-result' ),

		        	) );
		    }
			
		} 


		/**
		 * Enqueue admin scripts
		 */
		public function enqueueAdmin( $hook ) {

			$pagesArray = array( 

				'toplevel_page_viraquiz_plugin',
				'viraquiz_page_viraquiz_app_config',
				'viraquiz_page_viraquiz_translation',
				'viraquiz_page_viraquiz_general_settings'

			 );

			if( in_array( $hook, $pagesArray ) ){

				wp_enqueue_style( 'viraquiz-admin', VIRAQUIZ_PLUGIN_URL . 'assets/css/viraquiz-admin.css', array(), VIRAQUIZ_PLUGIN_VERSION, 'all' );

			 }


			if( 'toplevel_page_viraquiz_plugin' == $hook ) {

				wp_enqueue_script( 'viraquiz-chart', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js' );
				wp_enqueue_script( 'viraquiz-dashboard', VIRAQUIZ_PLUGIN_URL . 'assets/js/viraquiz-dashboard-stats.js', array( 'jquery', 'viraquiz-chart' ), VIRAQUIZ_PLUGIN_VERSION, true );
				wp_localize_script( 'viraquiz-dashboard', 'VIRAQUIZ_dashboard', array(

		        		'daily_stats' => get_option( 'viraquiz_daily_stats'),
		        		'genders'     => get_option( 'viraquiz_stats_genders' )

		        	) );

			}

			
		} 


	}

}