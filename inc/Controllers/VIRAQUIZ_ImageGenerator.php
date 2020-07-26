<?php
/**
 * Image Generator Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

use Inc\Controllers\VIRAQUIZ_FontsController;
use Intervention\Image\ImageManagerStatic as Image;

if ( !class_exists( 'VIRAQUIZ_ImageGenerator' ) )
{

	class VIRAQUIZ_ImageGenerator
	{


		/**
		 *  Generate quiz result with user data 
		 *
		 *  Print image to browser
		 */
		function generateQuizResult()
		{

			$results = get_post_meta( (int)get_query_var( 'viraquiz_quiz_id' ), '_viraquiz_results', true );
			$layers = $results[get_query_var( 'viraquiz_result' )]['layers'];

			$img = $this->drawImage( $layers, 'live' );

			if( !empty( $_GET['shared_page'] ) ) {
				$img->resize( 400, 210 );
			}

			echo $img->response('jpg');

		}


		/**
		 *  Generate quiz result schema image on Admin area
		 *
		 *  Print image to browser
		 */
		public function generateResultSchema() 
		{

			$quiz_id = (int)get_query_var( 'viraquiz_quiz_id' );
			$result_number = (int)get_query_var( 'viraquiz_result' );

			$layers = get_post_meta( $quiz_id, '_viraquiz_results', true )[$result_number]['layers'];

			$img = $this->drawImage( $layers );
			$img->resize( 400, 210 );

			echo $img->response('jpg');


		}


		/**
		 *  Draw text / image layers to main image 
		 *
		 * @param  array   $layers  All image layers 
		 * @param  string  $type    Type of image ( live / result_schema )
	     * @return object  $img     Final image
		 */
		public function drawImage( $layers, $type = null ) 
		{

			// Create image canvas
			$img = Image::canvas( 880, 462, '#ffffff' );

			//Get required fonts list
			$fonts = VIRAQUIZ_FontsController::getAllFonts();

			//Get a single layer and draw it to image
			foreach ( $layers as $layer ) {
				
				$layer_data = $layer[0];

				if( $layer_data['type'] == 'text' ) {

					//Draw texts 
					$text = $layer_data['text'];
					$original_text = $text;

					if( $type == 'live' ) {

						$firstname = urldecode( get_query_var( 'viraquiz_first_name' ) );
						$lastname =  urldecode( get_query_var( 'viraquiz_last_name' ) );
						$fullname = $firstname . ' ' . $lastname; 

						$text = str_replace( '[firstname]', $firstname, $text );
						$text = str_replace( '[lastname]', $lastname, $text );
						$text = str_replace( '[fullname]', $fullname, $text );

					}

					
					$strings = explode( '[line_break]', $text );
					$original_strings = explode( '[line_break]', $original_text );

					$font_family = $layer_data['font_family'];
					$font_name = $fonts[$font_family]['name'];
					$font = preg_replace( "/[\s]/", "", $font_name );
					$font_directory = preg_replace( "/[\s]/", "_", $font_name );

					$font_path = VIRAQUIZ_PLUGIN_PATH . 'assets/fonts/' . $font_directory . '/' . $font . '-Regular.ttf';
					$original_string_length = imagettfbbox( $layer_data['fontsize'], 0, $font_path, $original_strings[0] );
					$top = $layer_data['top'];

					$i = 0;
					$first_length = 0;
					$last_string_height = 0;
					$first_uppercase = false;

					$space = round( $layer_data['fontsize'] * 14 / 100 );
					$space = $space + 1;

					foreach( $strings as $string ) {

						$string_length = imagettfbbox( $layer_data['fontsize'], 0, $font_path, $string );
						$string_sizes = self::calculateTextBox( $layer_data['fontsize'], 0, $font_path, "A" );
						$string_height = round( ( $string_sizes['height'] * 76 / 100 ) );

						$measure_string_lowercase = preg_replace("/[bBdDfFgGhHiIjJlLpPqQtTyY]/", "a", $string);
						$string_sizes_lowercase = self::calculateTextBox( $layer_data['fontsize'], 0, $font_path, $measure_string_lowercase );
						$string_height_lowercase = round( ( $string_sizes_lowercase['height'] * 76 / 100 ) );

						$string_length = $original_string_length[2];

						if( $i == 0 ) {
							$first_length = $original_string_length[2];
						}
						
						$last_string_height = $last_string_height + $string_height;

						if( $i > 0 ) {
							$top_pos = $top + $last_string_height + $space - $string_height;// - 30;
							$last_string_height = $last_string_height + $space;

						} else {

							$top_pos = $top;

							if( ! preg_match( '/[A-Z]/', $string ) && ! preg_match( '/[bdfhlt]/', $string ) ){
								 $top_pos = $top_pos + ( $string_height - $string_height_lowercase );
								 
							} else {
								$first_uppercase = true;
							}
							
						}


						$font_diff = 0;
						$length_diff = $string_length * 69 / 183;
						$left_deviation = $length_diff - $font_diff; //$layer_data['left'] * 140 / 360;

						if( $i > 0 ) {
							$left_deviation = ( $first_length * 69 / 183 ) - $font_diff;
						}

						$top_pos = $top_pos + 2;

						$img->text( $string, ( $layer_data['left'] + $left_deviation ), $top_pos, function( $font ) use ( $layer_data, $font_path ) {

						$font->file( $font_path );
						$font->size( $layer_data['fontsize'] );
						$font->color( $layer_data['color'] );
						$font->align('center');
						$font->valign('top');

						} );

						$i++;

					}


				} else {

					//Draw images
					if( $layer_data['type'] == 'profile' ) {

						if( $type == 'live' ) {
							$img_src = 'http://graph.facebook.com/' . filter_var( get_query_var( 'viraquiz_fbid' ), FILTER_SANITIZE_NUMBER_INT ) . '/picture?width=900';
						} else {
							$img_src = VIRAQUIZ_PLUGIN_URL . '/assets/images/profile-picture.png';
						}

						
					} else {
						$img_src = esc_url( wp_get_attachment_image_src( $layer_data['img_src'], 'large' )[0] );
					}


					$new_layer = Image::make( $img_src );
					$new_layer->resize( $layer_data['width'], $layer_data['height'] );

					$img->insert( $new_layer, 'top-left', $layer_data['left'], $layer_data['top'] );

				}

				
				
			}

			return $img;

		}	


		/**
		 *  Hepler function - Calculate text box sizes
		 *
		 *  @param  int     $font_size   Font size of text
		 *  @param  int     $font_angle  Angle of text
		 *  @param  string  $font_file   Font file path 
		 *  @param  string  $text        Text
		 *
		 *  @return array                Box sizes
		 */ 
		public static function calculateTextBox( $font_size, $font_angle, $font_file, $text ) { 

		    $rect = imagettfbbox( $font_size, $font_angle, $font_file, $text ); 
		    $minX = min( array( $rect[0], $rect[2], $rect[4], $rect[6] ) ); 
		    $maxX = max( array( $rect[0], $rect[2], $rect[4], $rect[6] ) ); 
		    $minY = min( array( $rect[1], $rect[3], $rect[5], $rect[7] ) ); 
		    $maxY = max( array( $rect[1], $rect[3], $rect[5], $rect[7] ) ); 
		    
		    return array( 
		     "left"   => abs( $minX ) - 1, 
		     "top"    => abs( $minY ) - 1, 
		     "width"  => $maxX - $minX, 
		     "height" => $maxY - $minY, 
		     "box"    => $rect 
		    ); 

		} 

	}

}