<?php
/**
 * User input sanitization / validation
 *
 * @package  viraquiz
 */

namespace Inc\Helper;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_Sanitize' ) )
{

	class VIRAQUIZ_Sanitize 
	{


		/**
		 * Sanitize Facebook Application data
		 *
		 * @param  array  $options       Unsanitized user input data
		 * @return array  $valid_output  Sanitized data
	     */
		public static function fbAppConfig( array $options  ) 
		{

			$valid_output = [];

			foreach ( $options as $key => $value ) {
				
				switch ( $key ) {

					case 'app_id':
						$valid_output[$key] = self::bigIntegerValidation( $value );
						break;

					case 'app_secret':
						$valid_output[$key] = self::textField( $value );
						break;	

					default:
						$valid_output[$key] = $value;
						break;

				}

			}

			return $valid_output;

		}


		/**
		 * Sanitize Translation texts
		 *
		 * @param  array  $options  Unsanitized user input data
		 * @return array            Sanitized data
	     */
		public static function translation( array $options )
		{

			return array_map( array( __CLASS__, 'textField' ), $options );

		}


		/**
		 * Sanitize General Settings data
		 *
		 * @param  array  $options       Unsanitized user input data
		 * @return array  $valid_output  Sanitized data
	     */
		public static function generalSettings( array $options  ) 
		{

			$valid_output = [];

			foreach ( $options as $key => $value ) {
				
				switch ( $key ) {

					case 'more_quizzes_text':
						$valid_output[$key] = self::wpEditorText( $value );
						break;

					case 'shared_page_redirect':
						$valid_output[$key] = self::textField( $value );
						break;	

					case 'delete_quizzes_on_uninstall':
					case 'delete_options_on_uninstall':
						$valid_output[$key] = self::integerValidation( $value );
						break;	

					default:
						$valid_output[$key] = $value;
						break;

				}

			}

			return $valid_output;

		}


		/**
		 * Sanitize Quiz Result Layers data
		 *
		 * @param  array  $layers        Unsanitized user input data
		 * @return array  $valid_layers  Sanitized data
	     */
		public static function quizResultLayers( array $layers  ) 
		{

			$valid_layers = [];

			foreach ( $layers as $layer_key => $layer ) {

				$valid_layer = [];
				
				foreach ( $layer[0] as $layer_data_key => $layer_data_value ) {
					
					switch ( $layer_data_key ) {

						case 'top':
						case 'left':
						case 'width':
						case 'height':
						case 'img_src':
						case 'fontsize':
						case 'font_family':
							$valid_layer[0][$layer_data_key] = self::integerValidation( $layer_data_value );
							break;

						case 'text':
						case 'type':
							$valid_layer[0][$layer_data_key] = self::textField( $layer_data_value );
							break;	

						case 'color':
							$valid_layer[0][$layer_data_key] = self::hexColor( $layer_data_value );
							break;	
						
						default:
							$valid_layer[0][$layer_data_key] = $layer_data_value;
							break;

					}

				}

				$valid_layers[$layer_key] = $valid_layer;

			}

			return $valid_layers;

		}







		/*
		 * Sanitization / validation helper functions
		 */

		public static function hexColor( $color ) 
		{

			return sanitize_hex_color( $color );

		}


		public static function textField( $text ) 
		{

			return sanitize_text_field( $text );

		}

		public static function textarea( $text )
		{

			return sanitize_textarea_field( $text );

		}

		public static function wpEditorText( $text ) {

			return wp_kses_post( $text );

		}


		public static function integerValidation( $input ) 
		{

			return (int)$input;

		}

		public static function bigIntegerValidation( $input )
		{

			return filter_var( $input, FILTER_SANITIZE_NUMBER_INT );

		}

		public static function floatValidation( $input ) 
		{

			return (float)$input;
			
		}


	}


}