<?php 
/**
 * Generate General Settings Admin Page Class
 *
 * @package  viraquiz
 */

namespace Inc\Pages\Backend;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_GeneralSettings' ) )
{
	
	class VIRAQUIZ_GeneralSettings 
	{

		/**
		 * General Settings
		 *
		 * @var array
		 */
		protected $general_settings;


		/**
		 * Class constructor 
		 *
		 * Set $general_settings variable 
		 */
		public function __construct() 
		{

			$this->general_settings = get_option( 'viraquiz_general_settings' );

		}


		/**
		 * Add actions
		 */
		public function register()
		{

			add_action( 'admin_init', array( $this, 'settingsInit' ) );

		}


		/**
		 *  Registser options, sections and fileds
		 */
		public function settingsInit()
		{

			//Settings
			register_setting( 'vrq-general-settings', 'viraquiz_general_settings', 'Inc\Helper\VIRAQUIZ_Sanitize::generalSettings' );

			//Sections 
			add_settings_section( 'vrq-general-settings', 'General Settings', array( $this, 'generalSettingsSectionCallback' ), 'viraquiz_general_settings' );

			//Fields 
			add_settings_field( 'vrq-more-quizzes-text', '"More quizzes" text', array( $this, 'moreQuizzesText' ), 'viraquiz_general_settings', 'vrq-general-settings' );
			add_settings_field( 'vrq-shared-page-redirect', 'Redirect user to Start Quiz Page when accessing a shared link', array( $this, 'sharedPageRedirect' ), 'viraquiz_general_settings', 'vrq-general-settings' );
			add_settings_field( 'vrq-delete-all-quizzes', 'Delete all quizzes on plugin uninstall', array( $this, 'generateToggleButton' ), 'viraquiz_general_settings', 'vrq-general-settings', array( 'option_name' => 'delete_quizzes_on_uninstall' ) );
			add_settings_field( 'vrq-delete-plugin-options', 'Delete all plugin options on uninstall', array( $this, 'generateToggleButton' ), 'viraquiz_general_settings', 'vrq-general-settings', array( 'option_name' => 'delete_options_on_uninstall' ) );


		}


		/**
		 *  General Settings Section Callback
		 */
		public function generalSettingsSectionCallback()
		{

			echo 'Customize VIRAQUIZuizzer plugin';

		}


		/**
		 *  Generate "More Quizzes text" Textarea using builtin WP Editor
		 */
		public function moreQuizzesText()
		{

			$value = ( ! empty( $this->general_settings['more_quizzes_text'] ) ? $this->general_settings['more_quizzes_text'] : '' );
			
			wp_editor( wp_kses_post( $value ), 'more-quizzes-text', array( 'media_buttons' => false, 'textarea_rows' => 2, 'textarea_name' => 'viraquiz_general_settings[more_quizzes_text]' ) );
				
			echo '<p class="description">Text to print before showing more quizzes on single quiz page</p>';	

		}


		/**
		 *  Generate "Redirect user to Start Quiz Page when accessing a shared link" Select
		 */
		public function sharedPageRedirect()
		{


			$value = ( ! empty( $this->general_settings['shared_page_redirect'] ) ? $this->general_settings['shared_page_redirect'] : '' );
			$output = '<select name="viraquiz_general_settings[shared_page_redirect]">';
			$options = array( 'see_previous_user_result' => 'See the result of previous user', 'redirect_to_start_page' => 'Redirect to Start Quiz page' );

			foreach ( $options as $option_key => $option_value ) {
			
				$selected = ( $option_key == $value ? 'selected' : '' );
				$output .='<option value="' . $option_key . '" ' . $selected . '>' . $option_value . '</option>';

			}

			$output .= '<select>';
			$output .='<p class="description">By default, when an user access a quiz link on Facebook, he will see the result of previous user ( who shared the link). If you want to redirect user directly to strt quiz page, select "Redirect to Start Quiz page"</p>';

			echo $output;
			
		}	


		/**
		 *  Generate Toggle Button 
	     *
		 *  @param array $args Option arguments ( option name )
		 */
		public function generateToggleButton( $args )
		{

			$name = $args['option_name'];
			$value = ( ! empty( $this->general_settings[$name] ) ? (int)$this->general_settings[$name] : '0' );
			$checked = ( (int)$value === 1 ? 'checked' : '' );

			echo '<input class="tgl tgl-ios" id="vrq_' . $name . '" type="checkbox" name="viraquiz_general_settings[' . $name . ']" value="1" ' . $checked . '/><label class="tgl-btn" for="vrq_' . $name . '"></label>';

		}


	}


}