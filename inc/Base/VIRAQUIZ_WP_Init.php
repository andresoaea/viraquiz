<?php 
/**
 * Wp Init Class
 *
 * @package  viraquiz
 */

namespace Inc\Base;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_WP_Init' ) )
{

	class VIRAQUIZ_WP_Init
	{

		/**
		 * Attached images to a Custom Post Type
		 *
		 * @var array
		 */
		public static $attached_images_ids;


		/**
		 *  Add actions 
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register()
		{

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'getCptAttachedImages' ) );
			add_action( 'widgets_init', array( $this, 'registerAdsSpacesSidebars' ) );

			add_action( 'admin_head', array( $this, 'imagesProtect' ) );
			add_action( 'add_meta_boxes', array( $this, 'removeYoastMetaboxes' ), 11 );

			if( ! is_admin() ) {
				add_action( 'pre_get_posts', array( $this, 'preGetPostQueryVars' ) ); 
			}


		}


		/**
		 *  Functions to run on WP Init 
		 */
		public function init()
		{

			$this->addImageSizes();

		}


		/**
		 *  Add size for a single quiz image ( featured image )
		 */
		public function addImageSizes()
		{

		  add_image_size( 'viraquiz_medium', 400, 210, true );
		  add_image_size( 'viraquiz_medium_large', 680, 357, true );

		}


		/**
		 *  Protect images used by plugin 
		 *
		 *  Add js to wp_head to replace "Delete permanently" button on Wp Media Library with info message 
		 *  "This image cannot be deleted because it's used by VIRAQUIZuizzer Plugin."
		 */
		public function imagesProtect()
		{

			$output = '<script type="text/javascript">';
			$output .= '(function($){"use strict";$(function(){$("body").on("click",".attachment-preview",function(){var attached_ids=' . json_encode( self::$attached_images_ids ) . ';setTimeout(function(){var $media=$("body").find(".media-frame-content").find(".attachment-details");var image_id=$media.data("id");var $delete_btn=$media.find(".delete-attachment");if(-1!=$.inArray(parseInt(image_id),attached_ids)){$delete_btn.hide();$delete_btn.after("<span class=\"description\">This image cannot be deleted because it\'s used by VIRAQUIZuizzer Plugin. </span>")}},200)})})})(jQuery)';	
			$output .= '</script>';

			echo $output;

		}


		/**
		 *  Register Sidebars for Advertisments Spaces
		 */
		public function registerAdsSpacesSidebars()
		{

			register_sidebar(
				array(
					'name'          => 'VIRAQUIZuizzer - Below Header',
					'id'            => 'viraquiz_below_header_ads_space',
					'description'   => 'VIRAQUIZuizzer - Below Header Ads Space',
					'before_widget' => '<div><section id="%1$s" class="%2$s">',
					'after_widget'  => '</section></div>',
					'before_title'  => '<h2 class="fbq-ads-title">',
					'after_title'   => '</h2>',
				) );

			register_sidebar(
				array(
					'name'          => 'VIRAQUIZuizzer - Above Footer',
					'id'            => 'viraquiz_above_footer_ads_space',
					'description'   => 'VIRAQUIZuizzer - Above Footer Ads Space',
					'before_widget' => '<div><section id="%1$s" class="%2$s">',
					'after_widget'  => '</section></div>',
					'before_title'  => '<h2 class="fbq-ads-title">',
					'after_title'   => '</h2>',
				) );	

			register_sidebar(
				array(
					'name'          => 'VIRAQUIZuizzer - Below Quiz Content',
					'id'            => 'viraquiz_below_quiz_content_ads_space',
					'description'   => 'VIRAQUIZuizzer - Below Quiz Content Ads Space',
					'before_widget' => '<div><section id="%1$s" class="%2$s">',
					'after_widget'  => '</section></div>',
					'before_title'  => '<h2 class="fbq-ads-title">',
					'after_title'   => '</h2>',
				) );

		}


		/**
		 *  Remove Yoast SEO Plugin Metaboxes on Admin Edit Post
		 */
		public function removeYoastMetaboxes()
		{

			remove_meta_box( 'wpseo_meta', 'viraquiz-app', 'normal' );

		}


		/**
		 *  Set Post Type to 'viraquiz-app'
		 *
	     *  @param object $query Current Wp_Query Instance
		 */
		public function preGetPostQueryVars( $query ) 
		{

			if( is_single() || $query->is_search ) {

			     $query->query_vars['post_type'] = 'viraquiz-app';

			  }

		}


		/**
		 *  Get all images IDs attached to "viraquiz-app" CPT
		 */	
		public static function getCptAttachedImages()
		{

		    //Get all post IDs for "viraquiz-app" CPT
			$attached_images_ids = array();
			$fbqiuzzer_cpt_ids = array();

			$args = array( 'post_type' => 'viraquiz-app', 'posts_per_page' => -1 );
			$loop = new \WP_Query( $args );

			while ( $loop->have_posts() ) : $loop->the_post();
			$fbqiuzzer_cpt_ids[] = get_the_ID();
			endwhile;

			wp_reset_postdata();

			// Find attached images to 'viraquiz-cpt' post
			foreach ( $fbqiuzzer_cpt_ids as $cpt_id ) {

				$attachments = get_attached_media( 'image', $cpt_id );

				foreach ( $attachments as $attachment ) {
					$attached_images_ids[] =  $attachment->ID;
				}

			}

			// Set class variable $attached_images_ids with all attached images IDs
			self::$attached_images_ids = $attached_images_ids;

		}


	}


}