<?php 
/**
 * Facebook Login Class
 *
 * @package  viraquiz
 */

namespace Inc\Controllers;

if ( !defined('ABSPATH') ) exit;

if ( !class_exists( 'VIRAQUIZ_Facebook' ) )
{
	
	class VIRAQUIZ_Facebook
	{

		/**
		 * Facebook Application ID
		 *
		 * @var string
		 */
		protected static $fb_app_id;


		/**
		 * Facebook Application Secret
		 *
		 * @var string
		 */
		protected static $fb_app_secret;


		/**
		 * Site URL 
		 *
		 * @var string
		 */
		protected static $site_url;


		/**
		 * Update Class Variables
		 *
		 * Method runs when Class is Instantiated
		 */	
		public function register()
		{

			$fb_app_config = get_option( 'viraquiz_fb_app_config' );

			self::$fb_app_id = ( ! empty( $fb_app_config['app_id'] ) ? $fb_app_config['app_id'] : '' );
			self::$fb_app_secret = ( ! empty( $fb_app_config['app_secret'] ) ? $fb_app_config['app_secret'] : '' );
			self::$site_url =  esc_url( get_site_url() );

		}


		/**
		 * Connect to Facebook
		 *
		 * @param int $quiz_id Current Quiz ID
		 */
	    public static function connect( $quiz_id = null ) {

		    session_start();

		    if( ! isset( $_SESSION['viraquiz_quiz_id'] ) ) {
		    	 $_SESSION['viraquiz_quiz_id'] = $quiz_id;
		    }
		   	
		    $fb = new \Facebook\Facebook([
			  'app_id' => self::$fb_app_id,
			  'app_secret' => self::$fb_app_secret,
			  'default_graph_version' => 'v2.11',
			]);


		    $helper = $fb->getRedirectLoginHelper();

		     if(isset($_GET['state'])) {
		     	$helper->getPersistentDataHandler()->set('state', $_GET['state']);
			 }

			try {
			  $accessToken = $helper->getAccessToken();
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
			  // When Graph returns an error
			  echo 'Graph returned an error: ' . $e->getMessage();
			  exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			  // When validation fails or other local issues
			  echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  exit;
			}

			if ( isset($accessToken) ) {

			    // Logged in
				$fb->setDefaultAccessToken($accessToken);
			    try {
			        $response = $fb->get('/me?fields=email,name,first_name,last_name,gender');
			        $userNode = $response->getGraphUser();
			    } catch (Facebook\Exceptions\FacebookResponseException $e) {
			        // When Graph returns an error
			        echo 'Graph returned an error: ' . $e->getMessage();
			        exit;
			    } catch (Facebook\Exceptions\FacebookSDKException $e) {
			        // When validation fails or other local issues
			        echo 'Facebook SDK returned an error: ' . $e->getMessage();
			        exit;
			    }
			    
			
			    $user_data = array(
		
			    	'appid'      => self::$fb_app_id,
			        'fbid'		 => $userNode->getId(),
			        'gender' 	 => $userNode->getGender(),
			        'email' 	 => $userNode->getProperty('email'),
			        'firstname'  => $userNode->getFirstName(),
			        'lastname'	 => $userNode->getLastName(),
			        'fullname' 	 => $userNode->getName(),
			       
			    );
	 
			 //Save user data in session
			 $_SESSION['viraquiz_facebook_user_data'] = $user_data;

			 //Redirect to quiz result generator
			 header( 'Location: ' . self::$site_url . '/viraquiz-result-process/' );


			} else {


				$permissions = ['email']; // Optional permissions
				$login_url = $helper->getLoginUrl( self::$site_url . '/viraquiz-facebook-login/', $permissions );

				header( 'Location: ' . $login_url );

			}

		}

	}

}