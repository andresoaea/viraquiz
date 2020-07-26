<?php 
/**
 * More Quizzes Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

?>

<div class="more-quizzes">

	<?php 

		$language = get_option( 'viraquiz_translation' );
		$paged = ( get_query_var('page') ? get_query_var('page') : 1 );
		$general_settings = get_option( 'viraquiz_general_settings' );

		if( ! empty( $general_settings['more_quizzes_text'] ) ) {

			echo '<div class="more-quizzes-text">' . wp_kses_post( $general_settings['more_quizzes_text'] ) . '</div>';

		}

		$args = array(

				'post_type' => 'viraquiz-app',
				'status'    => 'publish',
				'posts_per_page' => 6,
				'paged'			 => $paged,
				'post__not_in' => array( $main_quiz_id ) 

			);

		$the_query = new \WP_Query( $args ); 

	?>

	<div class="vrq-clearfix">
		 
		<?php if ( $the_query->have_posts() ) : ?>
		 
		    <div class="vrq-page-limit" data-page="/<?php echo Inc\Base\VIRAQUIZ_Ajax::checkPaged(1); ?>">

		    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

		    	<?php include VIRAQUIZ_PLUGIN_PATH . 'templates/frontend/single-templates/viraquiz-single-homepage-quiz.php'; ?>
		   
		    <?php endwhile; ?>

		 	</div><!-- .page-limit -->


		 	<?php if( (int)$the_query->post_count < ( (int)wp_count_posts( 'viraquiz-app' )->publish ) - 1 ) : ?>

			    <button type="button" class="vrq-load-more-quizzes-btn" data-page="<?php echo Inc\Base\VIRAQUIZ_Ajax::checkPaged(1); ?>" exclude-quiz="<?php echo $main_quiz_id; ?>">
						<span class="dashicons dashicons-update"></span>
						<span class="vrq-load-more-text"><?php echo ( ! empty( $language['load_more_quizzes'] ) ? esc_html( $language['load_more_quizzes'] ) : 'Load More Quizzes' ); ?></span>
				</button>

			<?php endif; ?>
		 
		    <?php wp_reset_postdata(); ?>
		 
		<?php endif; ?>

	</div><!-- .viraquiz-container -->

</div><!-- .more-quizzes -->