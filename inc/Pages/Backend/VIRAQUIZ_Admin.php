<?php 
/**
 * Add Admin Pages / Subpages Class
 *
 * @package  viraquiz
 */

namespace Inc\Pages\Backend;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_Admin' ) )
{
	class VIRAQUIZ_Admin
	{

		/**
		 *  Add actions
		 *
		 *  Method runs when Class is Instantiated
		 */
		public function register()
		{

			add_action( 'admin_menu', array( $this, 'addAdminPages' ) );
			add_filter( 'admin_footer_text', array( $this, 'changeAdminFooterText' ) );

		}

		/**
		 *  Add admin pages / subpages
		 */
		public function addAdminPages()
		{

			//Generate Main Admin Page
			add_menu_page( 'Viraquiz', 'Viraquiz', 'manage_options', 'viraquiz_plugin', array( $this, 'adminDashboard' ), VIRAQUIZ_PLUGIN_URL . 'assets/images/viraquiz-icon.png', 110 );
			
			//Generate Admin Sub Pages
			add_submenu_page( 'viraquiz_plugin', 'VIRAQUIZ Dashboard', 'Dashboard', 'manage_options', 'viraquiz_plugin', array( $this, 'adminDashboard' ) );
			add_submenu_page( 'viraquiz_plugin', 'VIRAQUIZ FB App Config', 'FB App Config', 'manage_options', 'viraquiz_app_config', array( $this, 'appConfig' ) );
			add_submenu_page( 'viraquiz_plugin', 'VIRAQUIZ Translation', 'Translation', 'manage_options', 'viraquiz_translation', array( $this, 'translation' ) );
			add_submenu_page( 'viraquiz_plugin', 'VIRAQUIZ General Settings', 'General Settings', 'manage_options', 'viraquiz_general_settings', array( $this, 'generalSettings' ) );

		}


		/**
		 *  Admin Dashboard Page Callback
		 */
		public function adminDashboard()
		{

			$vrq_daily_stats = get_option( 'viraquiz_daily_stats' );
			$vrq_general_stats = get_option( 'viraquiz_general_stats' );

			self::getHeaderSection();

			require_once VIRAQUIZ_PLUGIN_PATH . 'templates/backend/viraquiz-dashboard.php';

		}

		/**
		 *  Admin Facebook APP Configuration Page Callback
		 */
		public function appConfig()
		{

			self::getHeaderSection();

			require_once VIRAQUIZ_PLUGIN_PATH . 'templates/backend/viraquiz-fb-app-config.php';
				
		}


		/**
		 *  Admin Translation Page Callback
		 */
		public function translation()
		{

			self::getHeaderSection();

			require_once VIRAQUIZ_PLUGIN_PATH . 'templates/backend/viraquiz-translation.php';

		}


		/**
		 *  Admin General Settings Page Callback
		 */
		public function generalSettings()
		{

			self::getHeaderSection();

			require_once VIRAQUIZ_PLUGIN_PATH . 'templates/backend/viraquiz-general-settings.php';
				
		}

		/**
		 * Change Footer Text on Plugin Admin Pages
		 *
		 * @param   string  $text  Default Wordpress Footer "Thank you" message 
		 * @return  string  $text  Modified Footer message if the parent page is VIRAQUIZ Plugin main page
		 */
		public function changeAdminFooterText( $text )
		{


			if( get_current_screen()->parent_file == 'viraquiz_plugin' ) {
				
				$text = '<span id="footer-thankyou">Wordpress Plugin developed by <a href="http://elite-code.net" target="_blank">Elite-WP</a></span>';

			}

			return $text;

		}


		/**
		 *  Generate Admin Header Section 
		 */
		public static function getHeaderSection() 
		{

			$output  = '<div class="viraquiz-admin-header">';
			$output .= '<img class="viraquiz-logo" src="' . VIRAQUIZ_PLUGIN_URL  . 'assets/images/viraquiz-logo.png">';
			$output .= '<h1 class="plugin-name">Viraquiz</h1>';
			$output .= '<h3 class="plugin-description">Viral Facebook Quizzes/Apps <span> - Plugin for Wordpress</span></h3>';
			$output .= '<p>V ' . VIRAQUIZ_PLUGIN_VERSION .'</p>';

			$page_title = '';

			$adnin_menu_links = array(
				
					'viraquiz_plugin'  	 	       => '<span class="dashicons dashicons-dashboard"></span> Dashboard',
					'viraquiz_app_config' 		   => '<span class="dashicons dashicons-admin-network"></span> FB App Config',
					'viraquiz_translation'  	   => '<span class="dashicons dashicons-admin-site"></span> Translation',
					'viraquiz_general_settings'    => '<span class="dashicons dashicons-admin-tools"></span> General Settings'

				);

			$output .= '<ul class="vrq-admin-menu">';

			foreach( $adnin_menu_links as $admin_menu_link_key => $admin_menu_link ) {

				$actived = ( $admin_menu_link_key == $_GET['page'] ? 'vrq-menu-active' : '' );

				if( $admin_menu_link_key == $_GET['page'] ) {
					$page_title = $admin_menu_link;

				}

				$output .= '<li><a href="' . esc_url( admin_url( 'admin.php?page=' . $admin_menu_link_key ) ) . '" class="' . $actived . '">' . $admin_menu_link  . '</a></li>';

			}

			$output .= '</ul>';
			$output .= '<div class="clearfix"></div></div>';

			echo $output;

		}


	}


}	