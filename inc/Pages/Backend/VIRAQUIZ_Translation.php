<?php 
/**
 * Generate Translation Admin Page Class
 *
 * @package  viraquiz
 */

namespace Inc\Pages\Backend;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_Translation' ) )
{
	
	class VIRAQUIZ_Translation 
	{

		/**
		 * Translation strings
		 *
		 * @var array
		 */
		protected $language;


		/**
		 * Class constructor 
		 *
		 * Set $language variable 
		 */
		public function __construct() 
		{

			$this->language = get_option( 'viraquiz_translation' );

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
			register_setting( 'vrq-translation-settings', 'viraquiz_translation', 'Inc\Helper\VIRAQUIZ_Sanitize::translation' );

			//Sections 
			add_settings_section( 'vrq-translation-settings', 'Translation', array( $this, 'translationSectionCallback' ), 'viraquiz_translation' );

			//Fields 
			add_settings_field( 'vrq-take-quiz', 'Take Quiz', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'take_quiz' ) );
			add_settings_field( 'vrq-continue-with-facebook', 'Continue with Facebook', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'continue_with_facebook' ) );
			add_settings_field( 'vrq-share-on-facebook', 'Share on Facebook', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'share_on_facebook' ) );
			add_settings_field( 'vrq-try-again', 'Try again', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'try_again' ) );
			add_settings_field( 'vrq-find-your-result', 'Find out your result', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'find_your_result' ) );
			add_settings_field( 'vrq-connect-preloader', 'Connecting to Facebook and preparing your result...', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'connect_preloader' ) );
			add_settings_field( 'vrq-please-wait', 'Please wait a moment', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'please_wait' ) );
			add_settings_field( 'vrq-result-ready', 'Your result is ready...', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'result_ready' ) );
			add_settings_field( 'vrq-no-quizzes', 'Sorry, no quizzes available.', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'no_quizzes' ) );
			add_settings_field( 'vrq-load-more-quizzes', 'Load more quizzes', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'load_more_quizzes' ) );
			add_settings_field( 'vrq-loading-more', 'Loading more...', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'loading_more' ) );
			add_settings_field( 'vrq-no-more-quizzes', 'No more quizzes to load', array( $this, 'generateTextInput' ), 'viraquiz_translation', 'vrq-translation-settings', array( 'option_name' => 'no_more_quizzes' ) );


		}
		

		/**
		 *  Translation Section Callback
		 */
		public function translationSectionCallback()
		{

			echo 'Translate plugin strings in your language <p class="description">By default, the language used by the plugin is English. <br />If a text input below is empty, the default language will be used for respective text.</p>';

		}

		
		/**
		 *  Generate Text Input
	     *
		 *  @param array $args Option arguments ( option name )
		 */
		public function generateTextInput( $args  ) 
		{

			$setting = $args['option_name'];

			$value = ( ! empty( $this->language[$setting] ) ? esc_html( $this->language[$setting] ) : '' );

			echo '<input type="text" name="viraquiz_translation[' . $setting . ']" value="' . $value . '" />';	

		}


	}


}