<?php 
/**
 * Single Quiz Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

get_header();

$language = get_option( 'viraquiz_translation' );
$main_quiz_id = 0;


if( !empty( get_query_var( 'viraquiz_shared_page' ) ) && $fbuizzer_redirect_to_start_page ) {
	return;
}

// Print Widgets Area Below Header
if( ! empty( wp_get_sidebars_widgets()['viraquiz_below_header_ads_space'] ) ) {
	echo '<div class="vrq-ads-space">';
	dynamic_sidebar( 'viraquiz_below_header_ads_space' );
	echo '</div>';
}

 ?>
	
	<div class="viraquiz-container vrq-single-container vrq-clearfix">

		<div class="viraquiz-main-column viraquiz-reset">

			<div class="quiz-content">

				<?php 

				/* Show shared page */
				if( !empty( get_query_var( 'viraquiz_shared_page' ) ) ) : ?>

					<div class="vrq-shared-page">

						<img src="<?php echo esc_url( get_site_url() . '/viraquiz-image/' . (int)get_the_ID() . '/' . get_query_var( 'viraquiz_fbid' ) . '-' . get_query_var( 'viraquiz_gender' ) . '-' . get_query_var( 'viraquiz_result' ) . '-' . get_query_var( 'viraquiz_first_name' ) . '-' . get_query_var( 'viraquiz_last_name' ) . '?shared_page=true' ); ?>">

						<?php 

							$text = get_post_meta( (int)get_the_ID(), '_viraquiz_quiz_open_graph', true )['shared_page_text'];

							if( !empty( $text ) ) {

								$text = Inc\Controllers\VIRAQUIZ_OpenGraph::replaceTextName( $text );

								echo '<h3>' . esc_html( $text ) . '</h3>';

							}
							
						 ?>

						 <div class="vrq-clearfix"></div>

						<a href="<?php echo esc_url( get_the_permalink() ); ?>"><button type="button"><span class="dashicons dashicons-arrow-right-alt2"></span> <?php echo ( ! empty( $language['find_your_result'] ) ? esc_html( $language['find_your_result'] ) : 'Find out your result' ); ?></button></a>

					</div>

				<?php else : ?>

					<?php if ( have_posts() ) : ?>
				 
						<?php while ( have_posts() ) : the_post(); ?>

					    	<?php

					    	$main_quiz_id = get_the_ID();
					    	
					    	/* Show quiz result */
					    	if( isset( $_SESSION['viraquiz_facebook_user_data'] ) ) :
					    		
						    	require_once VIRAQUIZ_PLUGIN_PATH . 'templates/frontend/single-templates/viraquiz-show-result.php';

						    	unset( $_SESSION['viraquiz_facebook_user_data'] );
						    	unset( $_SESSION['viraquiz_quiz_id'] );
				
					    	else :

					    	/* Start quiz */
					    	?>

					        <h1 class="quiz-title"><?php echo esc_html( get_the_title() ); ?></h1>

					        <?php echo wp_kses_post( get_post_meta( get_the_ID(), '_viraquiz_quiz_description', true ) ); ?>

					        <?php 

					        if ( has_post_thumbnail() ) {
							   
							    echo '<img class="quiz-image" src=' .  esc_url( wp_get_attachment_image_src( get_post_thumbnail_id(), 'viraquiz_medium_large' )[0] ) . '>';
							
							} 

					         ?>

					        <a href="<?php echo esc_url( get_site_url() ) . '/viraquiz-facebook-login/' .  (int)get_the_ID(); ?>"><button type="button" id="viraquiz-fb-login" class="continue-with-facebook"><span class="dashicons dashicons-facebook-alt"></span> <span class="continue-fb-text"><?php echo ( ! empty( $language['continue_with_facebook'] ) ? esc_html( $language['continue_with_facebook'] ) : 'Continue with Facebook' ); ?></span></button></a>

					    <?php endif; ?>


					    <?php endwhile; ?>
				 
					<?php else : ?>
					    <p><?php esc_html_e( 'Sorry, this quiz is not available.' ); ?></p>
					<?php endif; ?>

				<?php endif; ?>

				<?php 

				    // Print Ads Space Below Quiz Content
					if( ! empty( wp_get_sidebars_widgets()['viraquiz_below_quiz_content_ads_space'] ) ) {
						echo '<div class="vrq-ads-space vrq-below-quiz-content-ads-space">';
						dynamic_sidebar( 'viraquiz_below_quiz_content_ads_space' );
						echo '</div>';
					}

				 ?>

			</div><!-- .quiz-content -->

			<?php require_once VIRAQUIZ_PLUGIN_PATH . 'templates/frontend/single-templates/viraquiz-more-quizzes.php' ?>


		</div><!-- .viraquiz-main-column -->

		<div class="viraquiz-sidebar">

			<?php get_sidebar(); ?>

		</div><!-- .viraquiz-sidebar -->

	</div><!-- .viraquiz-container -->	

	<div class="viraquiz-preloader">

		<div class="vrq-pixel-loader"></div>
		<h3 class="vrq-preloader-msg"><?php echo ( ! empty( $language['connect_preloader'] ) ? esc_html( $language['connect_preloader'] ) : 'Connecting to Facebook and preparing your result...' ); ?></h3>

	</div><!-- .viraquiz-preloader -->
		 
<?php 

	// Print Ads Space Above Footer
	if( ! empty( wp_get_sidebars_widgets()['viraquiz_above_footer_ads_space'] ) ) {
		echo '<div class="vrq-ads-space">';
		dynamic_sidebar( 'viraquiz_above_footer_ads_space' );
		echo '</div>';
	}
	
	get_footer(); 

?>