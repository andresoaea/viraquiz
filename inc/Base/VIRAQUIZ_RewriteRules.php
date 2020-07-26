<?php 
/**
 * Add Plugin Rewrite Rules Class
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;


if ( !class_exists( 'VIRAQUIZ_RewriteRules' ) )
{
	
	class VIRAQUIZ_RewriteRules
	{

		
		/**
		 *  Add actions and filters
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register()
		{

			add_action( 'init', array( $this, 'pluginRules' ) );
			add_filter( 'query_vars', array( $this, 'pluginQueryVars' ) );
			

		}


		/**
		 *  Add rewrite rules
		 */
		public static function pluginRules() 
		{

			  $url_args = 'viraquiz_quiz_id=$matches[1]&viraquiz_fbid=$matches[2]&viraquiz_gender=$matches[3]&viraquiz_result=$matches[4]&viraquiz_first_name=$matches[5]&viraquiz_last_name=$matches[6]';

			  add_rewrite_rule( '^viraquiz-image/([0-9]+)/([0-9]+)-([A-Za-z0-9]+)-([0-9]+)-([A-Za-z0-9+]+)-([A-Za-z0-9+]+)?$', 'index.php?viraquiz=true&' . $url_args, 'top' );
			  add_rewrite_rule( '^viraquiz-result-schema/([0-9]+)/([0-9]+)?$', 'index.php?viraquiz_result_schema=true&viraquiz_quiz_id=$matches[1]&viraquiz_result=$matches[2]', 'top' );
			  add_rewrite_rule( '^viraquiz-result-process/?$', 'index.php?viraquiz_result_process=true', 'top' );	
			  add_rewrite_rule( '^viraquiz-facebook-login/?$', 'index.php?viraquiz_facebook_login=true', 'top' );
			  add_rewrite_rule( '^viraquiz-facebook-login/([0-9]+)?$', 'index.php?viraquiz_facebook_login=true&viraquiz_quiz_id=$matches[1]', 'top' );	
			  add_rewrite_rule( '^quiz/([^/]*)/([0-9]+)-([A-Za-z0-9]+)-([0-9]+)-([A-Za-z0-9+]+)-([A-Za-z0-9+]+)?$', 'index.php?post_type=viraquiz-app&viraquiz_shared_page=true&viraquiz_fbid=$matches[2]&viraquiz_gender=$matches[3]&viraquiz_result=$matches[4]&viraquiz_first_name=$matches[5]&viraquiz_last_name=$matches[6]', 'top' );

		}


		/**
		 *  Add query variables to $query_vars array
		 */
		public function pluginQueryVars( $query_vars )
		{

			$query_vars[] = 'viraquiz';
		    $query_vars[] = 'viraquiz_fbid';
		    $query_vars[] = 'viraquiz_gender';
		    $query_vars[] = 'viraquiz_result';
		    $query_vars[] = 'viraquiz_first_name';
		    $query_vars[] = 'viraquiz_last_name';
		    $query_vars[] = 'viraquiz_facebook_login';
		    $query_vars[] = 'viraquiz_result_process';
		    $query_vars[] = 'viraquiz_quiz_id';
		    $query_vars[] = 'viraquiz_shared_page';
		    $query_vars[] = 'viraquiz_result_schema';

		    return $query_vars;

		}
		

	}


}