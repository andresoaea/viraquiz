<?php
/**
 * Trigger this file on Plugin uninstall
 *
 * @package viraquiz
 */


if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}


// Get plugin settings
$plugin_settings = get_option( 'viraquiz_general_settings' );


//Delete all quizzes if option active
if( ! empty( $plugin_settings['delete_quizzes_on_uninstall'] ) ) {

	    $quizzes = get_posts( array( 'post_type' => 'viraquiz-app', 'numberposts' => -1) );

		foreach( $quizzes as $quiz ) {
		     
		   wp_delete_post( $quiz->ID, true );
		    
		}

}


// Delete all plugin saved options
if( ! empty( $plugin_settings['delete_options_on_uninstall'] ) ) {

		$options = array(

				'viraquiz_last_date',
				'viraquiz_daily_stats',
				'viraquiz_translation',
				'viraquiz_fb_app_config',
				'viraquiz_general_stats',
				'viraquiz_stats_genders',
				'viraquiz_recent_activity',
				'viraquiz_general_settings'

			);

		foreach ( $options as $option ) {
			
			delete_option( $option );

		}

}