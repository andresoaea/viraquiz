<?php 
/**
 * Register Custom Post Type Class
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_RegisterCPT' ) )
{
	
	class VIRAQUIZ_RegisterCPT 
	{


		/**
		 *  Add actions and filters
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register()
		{


			add_action( 'init', array( $this, 'registerCPT' ) );

			add_filter( 'manage_viraquiz-app_posts_columns', array( $this, 'quizesColumns' ) );
			add_action( 'manage_viraquiz-app_posts_custom_column',  array( $this, 'quizesCustomColumns' ), 10, 2 );

			add_filter( 'manage_viraquiz-user_posts_columns', array( $this, 'usersColumns' ) );
			add_action( 'manage_viraquiz-user_posts_custom_column',  array( $this, 'usersCustomColumns' ), 10, 2 );


		}


		/**
		 *  Register 'viraquiz-app' Custom Post Type 
		 *  All quizzes are saved with this CPT
		 *
		 *  Register 'viraquiz-user' Custom Post Type 
		 *  All quizzes users are saved with this CPT
		 */
		public static function registerCPT()
		{

			if( is_admin() && !current_user_can( 'administrator' ) ) {
				return;
			}

			/* Register FB Quizes post type */
			$labels = array(
			'name' 					=> 'FB Quizzes',
			'singular_name' 		=> 'FB Quiz',
			'menu_name'				=> 'FB Quizzes',
			'name_admin_bar'		=> 'FB Quiz',
		    'new_item' 				=> 'New FB Quiz',
			'add_new_item'			=> 'Add new FB Quiz',
			'edit_item'     		=> 'Edit Quiz',
			'featured_image' 		=> 'Quiz image',
			'set_featured_image' 	=> 'Set Quiz image'
			);
			
			$args = array(
				'labels'				=> $labels,
				'public'            	=> true,
	            'publicly_queryable'	=> true,
	            'query_var'        	    => true,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'capability_type'		=> 'post',
				'rewrite'          	    => array( 'slug' => 'quiz' ),
				'hierarchical'			=> false,
				'menu_position'			=> 8,
				'menu_icon'				=> 'dashicons-id',
				'supports'				=> array( 'title', 'thumbnail' )
			);
			
			register_post_type( 'viraquiz-app', $args );



			/* Register Quiz Users  post type */
			$labels = array(

				'name' 					=> 'Quiz Users',
				'singular_name' 		=> 'Quiz User',
				'menu_name'				=> 'Quiz Users',
				'name_admin_bar'		=> 'Quiz Users',
				'edit_item'     		=> 'Edit User'

			);
			
			$args = array(

				'labels'				=> $labels,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'capability_type'		=> 'post',
				'capabilities' 			=> array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'		    => true,
				'hierarchical'			=> false,
				'menu_position'			=> 9,
				'menu_icon'				=> 'dashicons-businessman',
				'supports'				=> array( 'title' )

			);
			
			register_post_type( 'viraquiz-user', $args );


		}


		/**
		 *  Customize 'viraquiz-app' CPT columns 
		 */
		public function quizesColumns()
		{

			$columns = array();
			$columns['cb'] = '<input type="checkbox" />';
			$columns['title'] = 'Quiz Title';
			$columns['results'] = 'Quiz results';
			$columns['shares'] = 'Quiz FB Shares';
			$columns['date'] = 'Date';

			return $columns;

		}

		/**
		 *  Print custom data in quizzes columns
		 *
		 * @param string $column  Column Key 
		 * @param int 	 $post_id Current Post ID 
		 */
		public function quizesCustomColumns( $column, $post_id )
		{

			switch( $column ){
					
				case 'results' :

					$results = get_post_meta( $post_id, '_viraquiz_results', true );
					
					if( empty( $results ) ) {
						$results = 'Please add results to this quiz!';
					} else {
						$results = count( $results );
					}


					echo $results;

					break;

				case 'shares':

					$shares = get_post_meta( $post_id, 'viraquiz_quiz_shares', true );

					if( empty( $shares ) ) {
				 		$shares = 0;
				 	} 

					echo (int)$shares;

					break;	

			}

		}


		/**
		 *  Customize 'viraquiz-user' CPT columns 
		 */
		public function usersColumns()
		{

			$columns = array();
			$columns['cb'] = '<input type="checkbox" />';
			$columns['picture'] = 'User Picture';
			$columns['fullname'] = 'Full Name';
			$columns['email'] = 'Email';
			$columns['fbid'] = 'Facebook ID';
			$columns['date'] = 'Registered';

			return $columns;

		}


		/**
		 *  Print custom data in users columns
		 *
		 * @param string $column  Column Key 
		 * @param int 	 $post_id Current Post ID 
		 */
		public function usersCustomColumns( $column, $post_id )
		{

			$user_data = get_post_meta( $post_id, 'viraquiz_user_data', true );
			$user_fb_id = filter_var( $user_data['fbid'], FILTER_SANITIZE_NUMBER_INT );

			switch( $column ) {
			
				case 'picture' :
					echo '<a href="https://facebook.com/' . $user_fb_id . '" target="_blank"><img src="http://graph.facebook.com/' . $user_fb_id . '/picture?width=50&height=50"></a>';
					break;
					
				case 'fullname' :
					echo '<a href="https://facebook.com/' . $user_fb_id . '" target="_blank">' . esc_html( $user_data['fullname'] ) . '</a>';
					break;

				case 'email' :
					echo sanitize_email( $user_data['email'] );
					break;


				case 'fbid' :
					echo $user_fb_id;
					break;


			}

		}


	}


}