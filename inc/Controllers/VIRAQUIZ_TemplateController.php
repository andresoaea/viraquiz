<?php 
/**
 * Templates Controller Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

use Inc\Controllers\VIRAQUIZ_Facebook;
use Inc\Controllers\VIRAQUIZ_OpenGraph;
use Inc\Controllers\VIRAQUIZ_ImageGenerator;
use Inc\Controllers\VIRAQUIZ_ResultGenerator;

if ( !class_exists( 'VIRAQUIZ_TemplateController' ) )
{

	class VIRAQUIZ_TemplateController
	{

		/**
		 *  Add filters
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register()
		{

			add_filter( 'template_include', array( $this, 'templateInclude' ), 1, 1 );
			add_filter( 'single_template', array( $this, 'singleTemplate' ) );

		}

		/**
		 *  Control template by query_vars variables
		 *
		 * @param  string  $template Actual template 
		 * @return string  $template Template which will be displayed
		 */
		public function templateInclude( $template )
		{

		    global $wp_query; 

		    // Template for generating result image 
		    if ( !empty( $wp_query->query_vars['viraquiz'] ) ) { 
		        
		       $result_image = new VIRAQUIZ_ImageGenerator;
		       $result_image->generateQuizResult();

		       return;

		    }


		    // Template for generating result schema image on admin area
		    if ( !empty( $wp_query->query_vars['viraquiz_result_schema'] ) ) {

		    	$result_schema = new VIRAQUIZ_ImageGenerator;
		    	$result_schema->generateResultSchema();

		    	return;

		    }

		    // Connect to Facebook template
		    if ( !empty( $wp_query->query_vars['viraquiz_facebook_login'] ) ) { 
		        
		      $quiz_id = ( isset( $wp_query->query_vars['viraquiz_quiz_id'] ) ? (int)$wp_query->query_vars['viraquiz_quiz_id'] : null );
		      VIRAQUIZ_Facebook::connect( $quiz_id );
		      return;

		    }

		    

		    // Result generator template
		    if ( !empty( $wp_query->query_vars['viraquiz_result_process'] ) ) { 
		        
		      $generate_result = new VIRAQUIZ_ResultGenerator;
		      $generate_result->generate();
		      return;

		    }


		    // Template for a shared page
		    if ( !empty( $wp_query->query_vars['viraquiz_shared_page'] ) ) { 
		      
		      global $fbuizzer_redirect_to_start_page;
		      $fbuizzer_redirect_to_start_page = ( get_option( 'viraquiz_general_settings' )['shared_page_redirect'] == 'redirect_to_start_page' ? true : false );  
		       

		      // Instantiate VIRAQUIZ_OpenGraph class to generate open graph metatags
		      new VIRAQUIZ_OpenGraph;

		      // Change archive title
		      add_filter( 'get_the_archive_title', function() { 

		      	global $post;
		        return esc_html( $post->post_title );

		       }, 10, 2 );

		       // Set page template 
		       $template = VIRAQUIZ_PLUGIN_PATH . '/templates/frontend/viraquiz-single.php';

		    }

		    // Template for Homepage
		    if ( is_front_page() && is_page_template( 'viraquiz-homepage.php' ) ) {

		       $template = VIRAQUIZ_PLUGIN_PATH . '/templates/frontend/viraquiz-homepage.php';

		    }

		    return $template;

		}




		/**
		 *  Control template for single quiz page
		 *
		 * @param  string  $template Actual template 
		 * @return string  $template Template which will be displayed
		 */
		public function singleTemplate( $template )
		{

			global $post;

			$post_type = $post->post_type;

			if( $post_type == 'viraquiz-app' ) {

				session_start();

				$template = VIRAQUIZ_PLUGIN_PATH . '/templates/frontend/viraquiz-single.php';

			}


			return $template;

		}


	}


}