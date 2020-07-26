<?php 
/**
 * Show Quiz Result Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

$user_data = $_SESSION['viraquiz_facebook_user_data'];
$language = get_option( 'viraquiz_translation' );

?>

<div class="viraquiz-result-preloader">

	<div class="vrq-pixel-loader"></div>

	<h3><?php echo ( ! empty( $language['please_wait'] ) ? esc_html( $language['please_wait'] ) : 'Please wait a moment' ); ?></h3>
	<h4><?php echo ( ! empty( $language['result_ready'] ) ? esc_html( $language['result_ready'] ) : 'Your result is ready...' ); ?></h4>

</div>

<img class="viraquiz-result-image" src="<?php echo esc_url( get_site_url() . '/viraquiz-image/' . (int)get_the_ID() . '/' . $user_data['fbid'] . '-' . $user_data['gender'] . '-' . $user_data['result'] . '-' . urlencode( $user_data['firstname'] ) . '-' . urlencode( $user_data['lastname'] ) ); ?>">

<div class="viraquiz-result-actions">

	<a href="https://www.facebook.com/sharer/sharer.php?&sdk=joey&app_id=<?php echo filter_var( $user_data['appid'], FILTER_SANITIZE_NUMBER_INT ); ?>&redirect_uri=<?php echo esc_url( get_site_url() ); ?>&u=<?php echo esc_url( get_the_permalink() . $user_data['fbid'] . '-' . $user_data['gender'] . '-' . $user_data['result'] . '-' . urlencode( $user_data['firstname'] ) . '-' . urlencode( $user_data['lastname'] ) ); ?>&display=popup&ref=plugin"
       onclick="window.open(this.href, 'mywin', 'left=250,top=100,width=640,height=400,toolbar=1,resizable=0'); return false;">
		<button type="button" id="vrq-share-on-facebook" class="continue-with-facebook vrq-share-fb-btn"><span class="dashicons dashicons-facebook"></span> <span class="vrq-share-text"><?php echo ( ! empty( $language['share_on_facebook'] ) ? esc_html( $language['share_on_facebook'] ) : 'Share on Facebook' ); ?></span> </button>
	</a>	

	<a class="viraquiz-try-again" href="<?php echo esc_url( get_the_permalink( (int)get_the_ID() ) ); ?>">
		<button type="button"><span class="dashicons dashicons-image-rotate"></span> <span class="vrq-try-again-text"><?php echo ( ! empty( $language['try_again'] ) ? esc_html( $language['try_again'] ) : 'Try again' ); ?></span></button>
	</a>
	
</div>