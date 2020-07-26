<?php 
/**
 * Fonts Control Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_FontsController' ) )
{

	class VIRAQUIZ_FontsController
	{

		/**
	     * Set all fonts which will be used by plugin
	     *
	     * @return array All plugin fonts
		 */
		public static function getAllFonts()
		{

			return array(

					array(
						'name'   => 'Roboto',
						'family' => 'sans-serif',
						),
					
					array(
						'name'   => 'Oswald',
						'family' => 'sans-serif',
						),	

					array(
						'name'   => 'Courgette',
						'family' => 'cursive',
						),

					array(
						'name'   => 'PT Serif',
						'family' => 'serif',
						),	

					array(
						'name'   => 'Indie Flower',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Inconsolata',
						'family' => 'monospace',
						),

					array(
						'name'   => 'Anton',
						'family' => 'sans-serif',
						),

					array(
						'name'   => 'Dosis',
						'family' => 'sans-serif',
						),	

					array(
						'name'   => 'Ranga',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Libre Baskerville',
						'family' => 'serif',
						),		

					array(
						'name'   => 'Lobster',
						'family' => 'cursive',
						),

					array(
						'name'   => 'Yanone Kaffeesatz',
						'family' => 'sans-serif',
						),

					array(
						'name'   => 'Bree Serif',
						'family' => 'serif',
						),	

					array(
						'name'   => 'Libre Franklin',
						'family' => 'sans-serif',
						),		

					array(
						'name'   => 'Pacifico',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Shadows Into Light',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Ubuntu Condensed',
						'family' => 'sans-serif',
						),	

					array(
						'name'   => 'Gloria Hallelujah',
						'family' => 'cursive',
						),		

					array(
						'name'   => 'Dancing Script',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Berkshire Swash',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Shrikhand',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Hind Siliguri',
						'family' => 'sans-serif',
						),		

					array(
						'name'   => 'Permanent Marker',
						'family' => 'cursive',
						),		

					array(
						'name'   => 'Great Vibes',
						'family' => 'cursive',
						),	

					array(
						'name'   => 'Ovo',
						'family' => 'serif',
						),		

					array(
						'name'   => 'Barlow Condensed',
						'family' => 'sans-serif',
						),	

					array(
						'name'   => 'Kaushan Script',
						'family' => 'cursive',
						),			

					array(
						'name'   => 'Cabin Condensed',
						'family' => 'sans-serif',
						)

				);

		}
		

	}


}