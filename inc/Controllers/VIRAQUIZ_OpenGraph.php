<?php 
/**
 * Generate Open Graph Metatags Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_OpenGraph' ) )
{

	class VIRAQUIZ_OpenGraph
	{

		/**
		 *  Class constructor
		 *
		 *  Add actions
		 */
		public function __construct()
		{

			add_action( 'wp_head', array( $this, 'updatePostData' ), 10 );
			add_action( 'wp_head', array( $this, 'setMetaTags' ), 0 );	

		}


		/**
		 *  Add Open Graph Metatags to wp_head 
		 */
		public function setMetaTags()
		{

			global $wp_query;

			$curr_quiz = $this->getCurrentQuiz();

	        // Prepare Open Graph Metatags variables
			$open_graph_data = get_post_meta( (int)$curr_quiz->ID, '_viraquiz_quiz_open_graph', true );	

			$user_fbid = $wp_query->query_vars['viraquiz_fbid'];
			$user_gender = $wp_query->query_vars['viraquiz_gender'];
			$user_result = $wp_query->query_vars['viraquiz_result'];
			$user_first_name = $wp_query->query_vars['viraquiz_first_name'];
			$user_last_name = $wp_query->query_vars['viraquiz_last_name'];


			$meta_title = ( !empty( $open_graph_data['title'] ) ? self::replaceTextName( $open_graph_data['title'] ) : $curr_quiz->post_title );
			$meta_description = ( !empty( $open_graph_data['description'] ) ? self::replaceTextName( $open_graph_data['description'] ) : '' );

			if( $open_graph_data['image'] == 'quiz-image' ) {

				$meta_image = wp_get_attachment_image_src( get_post_thumbnail_id( (int)$curr_quiz->ID ), 'viraquiz_medium_large' )[0];

				if( empty( $meta_image ) ) {
					$meta_image = VIRAQUIZ_PLUGIN_URL . 'assets/images/default-quiz-thumbnail-big.jpg';
				}

			} else {
				$meta_image = get_site_url() . '/viraquiz-image/' . (int)$curr_quiz->ID . '/' . $user_fbid . '-' . $user_gender . '-' . $user_result . '-' .  $user_first_name . '-' . $user_last_name;	
			}

			$fb_app_config = get_option( 'viraquiz_fb_app_config' );
			$fb_app_id = ( ! empty( $fb_app_config['app_id'] ) ? $fb_app_config['app_id'] : '' );


	        // Set page title
			apply_filters( 'wp_title', esc_html( $meta_title ) );


	        // Print Open Graph Metatags
			echo '<meta name="robots" content="noindex,nofollow"/>
			<meta property="fb:app_id" content="' . filter_var( $fb_app_id, FILTER_SANITIZE_NUMBER_INT ) . '" />
			<meta property="og:type" content="website" />
			<meta property="og:site_name" content="' . esc_html( get_bloginfo( 'name' ) ) . '" />
			<meta property="og:url" content="' . esc_url( $this->getQuizUrl() ) . '/" />
			<meta property="og:title" content="' . esc_html( $meta_title ) . '" />
			<meta property="og:description" content="' . esc_html( $meta_description ) . '" />      			
			<meta property="og:image" content="' .  esc_url( $meta_image ) . '/" />
			<meta property="og:image:type" content="image/jpeg" />
			<meta property="og:image:width" content="880" />
			<meta property="og:image:height" content="462" />' . "\n";

			$this->removeExistentTags();      


			/* Redirect to start quiz page if this option is active */
			global $fbuizzer_redirect_to_start_page;

			if( $fbuizzer_redirect_to_start_page ) {
				echo '<meta http-equiv="refresh" content="0; url=' . esc_url( get_the_permalink( (int)$curr_quiz->ID ) ) . '" />';     
			}

		}


		/**
		 *  Remove existent Open Graph Metatags
		 */
		public function removeExistentTags()
		{

			//Remove JetPack meta tags 
		    add_filter( 'jetpack_enable_open_graph', '__return_false' );


		    //Remove Yoast SEO meta tags
		    if ( defined('WPSEO_VERSION') ) { 

		      remove_action( 'wpseo_head', array( $GLOBALS['wpseo_og'], 'opengraph' ), 30 ); 
		      add_action( 'wpseo_opengraph', '__return_false' );
		      add_filter( 'wpseo_canonical', '__return_false' );
		      add_filter( 'wpseo_robots', '__return_false' );
		      add_filter( 'wpseo_twitter_image', '__return_false' );
		      add_filter( 'wpseo_twitter_card_type', '__return_false' );
		      add_filter( 'wpseo_twitter_image_size', '__return_false' );

		    }

		}


		/**
		 *  Get current quiz 
		 *
		 *  @return object $curr_quiz 
		 */
		public function getCurrentQuiz()
		{

			$quiz_url_arr = array_reverse( explode( '/', $this->getQuizUrl() ) );
			$quiz_slug = $quiz_url_arr[1];

			$curr_quiz = get_page_by_path( $quiz_slug, OBJECT, 'viraquiz-app' );

			return $curr_quiz;

		}


		/**
		 *  Get current quiz URL
		 *
		 *  @return string $quiz_url 
		 */
		public function getQuizUrl()
		{

			global $wp;
	        $quiz_url = home_url( $wp->request );

	        return $quiz_url;

		}


		/**
		 *  Update global post object with current post
		 */
		public function updatePostData() 
		{

			global $post;

			$post = $this->getCurrentQuiz();

		}


		/**
		 *  Replace "shortcodes" with the user data
		 *
		 * [firstname] is replaced with the real user first name
		 * [lastname] is replaced with the real user last name
		 * [fullname] is replaced with the real user full name
	     *
		 * @param  string  $text  Original text
		 * @return string  $text  Final text containing user name
		 */
		public static function replaceTextName( $text )
		{

			$user_first_name = urldecode( get_query_var( 'viraquiz_first_name' ) );
			$user_last_name =  urldecode( get_query_var( 'viraquiz_last_name' ) );
			$user_full_name = $user_first_name . ' ' . $user_last_name; 

		    $text = str_replace( '[firstname]', $user_first_name, $text );
			$text = str_replace( '[lastname]', $user_last_name, $text );
			$text = str_replace( '[fullname]', $user_full_name, $text );


			return $text;

		}

	}

}