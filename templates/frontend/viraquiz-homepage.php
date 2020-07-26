<?php
/**
 *  Home Page Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

get_header(); ?>


	<?php 

		// Print Widgets Area Below Header
		if( ! empty( wp_get_sidebars_widgets()['viraquiz_below_header_ads_space'] ) ) {
			echo '<div class="vrq-ads-space">';
			dynamic_sidebar( 'viraquiz_below_header_ads_space' );
			echo '</div>';
		}


		//Prepare new WP_Query to get Homepage Quizzes
		$language = get_option( 'viraquiz_translation' );
		$paged = ( get_query_var('page') ? get_query_var('page') : 1 );

		$args = array(

				'post_type' => 'viraquiz-app',
				'status'    => 'publish',
				'posts_per_page' => 6,
				'paged'			 => $paged

			);

		$the_query = new \WP_Query( $args ); 

	?>

	<div class="viraquiz-container viraquiz-reset vrq-homepage-container vrq-clearfix">

		<?php if ( $the_query->have_posts() ) : ?>

		    <div class="vrq-page-limit" data-page="/<?php echo Inc\Base\VIRAQUIZ_Ajax::checkPaged(1); ?>">
		 
			    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

			    	<?php include VIRAQUIZ_PLUGIN_PATH . 'templates/frontend/single-templates/viraquiz-single-homepage-quiz.php'; ?>
			   
			    <?php endwhile; ?>
		   
		 	</div><!-- .page-limit -->

		    <?php if( (int)$the_query->post_count < (int)wp_count_posts( 'viraquiz-app' )->publish ) : ?>

			    <button type="button" class="vrq-load-more-quizzes-btn" data-page="<?php echo Inc\Base\VIRAQUIZ_Ajax::checkPaged(1); ?>">
						<span class="dashicons dashicons-update"></span>
						<span class="vrq-load-more-text"><?php echo ( ! empty( $language['load_more_quizzes'] ) ? esc_html( $language['load_more_quizzes'] ) : 'Load More Quizzes' ); ?></span>
				</button>

			<?php endif; ?>
		 
		    <?php wp_reset_postdata(); ?>
		 
		<?php else : ?>

		    <h1><?php echo ( ! empty( $language['no_quizzes'] ) ? esc_html( $language['no_quizzes'] ) : 'Sorry, no quizzes available.' ); ?></h1>

		<?php endif; ?>


	</div><!-- .viraquiz-container -->


<?php 

	// Print Ads Space Above Footer
	if( ! empty( wp_get_sidebars_widgets()['viraquiz_above_footer_ads_space'] ) ) {
		echo '<div class="vrq-ads-space">';
		dynamic_sidebar( 'viraquiz_above_footer_ads_space' );
		echo '</div>';
	}

	get_footer(); 

?>