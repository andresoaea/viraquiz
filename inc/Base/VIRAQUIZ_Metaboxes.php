<?php 
/**
 * Add Metaboxes to Custom Post Type Class
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_Metaboxes' ) )
{
	class VIRAQUIZ_Metaboxes
	{

		
		/**
		 *  Add actions
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register()
		{

			add_action( 'add_meta_boxes_viraquiz-app', array( $this, 'addMetaBoxes' ) );

			add_action( 'save_post', array( $this, 'viraquiz_save_quiz_description' ) );
			add_action( 'save_post', array( $this, 'viraquiz_save_quiz_open_graph' ) );

		}


		/**
		 *  Add metaboxes 
		 */
		public function addMetaBoxes()
		{

			add_meta_box( 'quiz_description', 'Quiz Description', array( $this, 'descriptionBox' ), 'viraquiz-app', 'normal', 'high' );
			add_meta_box( 'quiz_open_graph', 'Open Graph Settings', array( $this, 'openGraphBox' ), 'viraquiz-app', 'normal' );
			add_meta_box( 'quiz_results', 'Quiz Results', array( $this, 'resultsBox' ), 'viraquiz-app', 'normal', 'low' );

		}


		/**
		 *  Add Quiz Description Metabox 
		 *
		 * @param object $post Current Post
		 */
		public function descriptionBox( $post )
		{


			wp_nonce_field( 'viraquiz_save_quiz_description', 'viraquiz_save_quiz_description_nonce' );
			$value = get_post_meta( $post->ID, '_viraquiz_quiz_description', true );
			wp_editor( wp_kses_post( $value ), 'quiz-description', array( 'media_buttons' => false, 'textarea_rows' => 2, 'textarea_name' => '_viraquiz_quiz_description' ) );
			

		}

		/**
		 *  Add Quiz Open Graph Metabox 
		 *
		 * @param object $post Current Post
		 */
		public function openGraphBox( $post )
		{

			wp_nonce_field( 'viraquiz_save_quiz_open_graph', 'viraquiz_save_quiz_open_graph_nonce' );

			$value = get_post_meta( $post->ID, '_viraquiz_quiz_open_graph', true );

			$output = '<label for="open-graph-title">Open Graph Quiz Title:</label>';
			$output .= '<input type="text" id="open-graph-title" name="_viraquiz_quiz_open_graph[title]" value="' . ( ! empty( $value['title'] ) ? esc_html( $value['title'] ) : '' ) . '" />';
			$output .= '<p class="description">Title which will be displayed on Facebook.</p>';

			$output .= '<label for="open-graph-description">Open Graph Quiz Description:</label>';
			$output .= '<input type="text" id="open-graph-description" name="_viraquiz_quiz_open_graph[description]" value="' . ( ! empty( $value['description'] ) ? esc_html( $value['description'] ) : '' ) . '" />';
			$output .= '<p class="description">Description which will be displayed on Facebook.</p>';

			$output .= '<label for="open-graph-image">Open Graph Quiz Image:</label>';
			$output .= '<select name="_viraquiz_quiz_open_graph[image]" id="open-graph-image">';

			$options = array( 'user-result' => 'User result randomly generated', 'quiz-image' => 'Default quiz image' );

			foreach ( $options as $option_key => $option ) {
				$selected = ( !empty( $value['image'] ) && ( $value['image'] == $option_key ) ? 'selected' : '' );
				$output .= '<option value="' . $option_key . '" ' . $selected . '>' . $option . '</option>';
			}

			$output .= '</select><div class="vrq-clearfix"></div>';


			$output .= '<label for="shared-page-description">Text to display on shared page as description:</label>';
			$output .= '<input type="text" id="shared-page-description" name="_viraquiz_quiz_open_graph[shared_page_text]" value="' . ( ! empty( $value['shared_page_text'] ) ? esc_html( $value['shared_page_text'] ) : '' ) . '" />';
			$output .= '<p class="description">Text to display on shared page by users, right after user result image.</p>';

			$output .= '<p class="vrq-info vrq-info-op description"><span class="dashicons dashicons-info"></span> <strong>Info! </strong> You can use the following shortcodes in above inputs <br /><strong>[firstname]</strong> - retrieves user first name <br /><strong>[lastname]</strong> - retrieves user last name <br /><strong>[fullname]</strong> - retrieves  user full name</p>';
		
			echo $output;

		}


		/**
		 *  Add Quiz Results Metabox 
		 *
		 * @param object $post Current Post
		 */
		public function resultsBox( $post ) 
		{

			global $post;

			//If the post was not published, show a warning message and return
			if( $post->post_status == 'auto-draft' ) {

				$output = '<p class="vrq-info vrq-high-warning description"><span class="dashicons dashicons-info"></span> <strong>In order to add results to this quiz, you must publish the quiz.</strong><br />';
				$output .= 'It\'s recommended to set the quiz visibility to "Protected" in the "Publish" box until the quiz develop will be finished, so the quiz will be visible only for admins. <br />If you have set the quiz title, description and image, please click on Publish button.</p>';
				$output .= '<br /><input type="submit" name="publish" class="button button-primary button-large" value="Publish">';
				
				echo $output;

				return;

			}
			
			//Require quiz results template 
			$results = get_post_meta( $post->ID, '_viraquiz_results', true );
			
			require_once VIRAQUIZ_PLUGIN_PATH . '/templates/backend/viraquiz-quiz-results.php';

		}




		/**
		 *  Save Quiz Description Metabox 
		 *
		 * @param int $post_id Current Post ID
		 */
		public function viraquiz_save_quiz_description( $post_id )
		{

			// Ckeck if this save is valid	
			global $post; 

		    if ( ! empty( $post ) && $post->post_type != 'viraquiz-app' ) {
		        return;
		    }
				
		    if( ! isset( $_POST['viraquiz_save_quiz_description_nonce'] ) ) {
				return;
			}
			
			if( ! wp_verify_nonce( $_POST['viraquiz_save_quiz_description_nonce'], 'viraquiz_save_quiz_description') ) {
				return;
			}
			
			if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return;
			}
			
			if( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			
			if( ! isset( $_POST['_viraquiz_quiz_description'] ) ) {
				return;
			}


			// Save / Update Post Meta 
			$input = wp_kses_post( $_POST['_viraquiz_quiz_description'] );

			update_post_meta( $post_id, '_viraquiz_quiz_description', $input );


		}



		/**
		 *  Save Quiz Open Graph Metabox 
		 *
		 * @param int $post_id Current Post ID
		 */
		public function viraquiz_save_quiz_open_graph( $post_id )
		{

			// Ckeck if this save is valid
			global $post; 

		    if ( ! empty( $post ) && $post->post_type != 'viraquiz-app' ) {
		        return;
		    }
				
		    if( ! isset( $_POST['viraquiz_save_quiz_open_graph_nonce'] ) ) {
				return;
			}
			
			if( ! wp_verify_nonce( $_POST['viraquiz_save_quiz_open_graph_nonce'], 'viraquiz_save_quiz_open_graph') ) {
				return;
			}
			
			if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
				return;
			}
			
			if( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			
			if( ! isset( $_POST['_viraquiz_quiz_open_graph'] ) ) {
				return;
			}


			// Save / Update Post Meta 
			$input = array_map( 'sanitize_text_field', $_POST['_viraquiz_quiz_open_graph'] );

			update_post_meta( $post_id, '_viraquiz_quiz_open_graph', $input );


		}


	}


}