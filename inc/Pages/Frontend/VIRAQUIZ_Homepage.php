<?php 
/**
 * Manipulate Home Page Class
 *
 * @package  viraquiz
 */

namespace Inc\Pages\Frontend;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_Homepage' ) )
{
    
    class VIRAQUIZ_Homepage 
    {

        /**
         * Class constructor
         * 
         * Call needed methods
         */
    	public function __construct()
    	{

    		$this->addHomePage();
    		$this->setHomepage();

    	}


        /**
         * Create Homepage
         */
    	public function addHomePage()
    	{

            $post_id = -1;

            // Setup custom vars
            $author_id = 1;
            $slug = 'viraquiz-homepage';
            $title = 'VIRAQUIZ Homepage';

            // Check if page exists, if not create it
            if ( null == get_page_by_title( $title ) ) {

                $uploader_page = array(
                        'comment_status'        => 'closed',
                        'ping_status'           => 'closed',
                        'post_author'           => $author_id,
                        'post_name'             => $slug,
                        'post_title'            => $title,
                        'post_status'           => 'publish',
                        'post_type'             => 'page'
                );

                $post_id = wp_insert_post( $uploader_page, false );


                if ( ! $post_id ) {

                        wp_die( 'Error creating template page' );

                } else {

                        update_post_meta( $post_id, '_wp_page_template', 'viraquiz-homepage.php' );

                }

            } 


    	}


        /**
         * Set Homepage
         */
    	public function setHomepage()
    	{

    		$homepage = get_page_by_title( 'VIRAQUIZ Homepage' );

    		if ( $homepage )
    		{
    		    update_option( 'page_on_front', $homepage->ID );
    		    update_option( 'show_on_front', 'page' );
    		}

    	}


        /**
         * Unset Homepage
         */
        public static function unsetHomepage()
        {

             update_option( 'page_on_front', 0 );
             update_option( 'show_on_front', 'posts' );

        }


    }


}