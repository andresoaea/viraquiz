<?php 
/**
 * Single Quiz on Homepage Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

?>

<div class="vrq-single-quiz">

	<div class="vrq-quiz-overlay"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><span><?php echo ( ! empty( $language['take_quiz'] ) ? esc_html( $language['take_quiz'] ) : 'Take Quiz' ); ?></span></a></div>

	<?php if( has_post_thumbnail() ) : ?>

		<img src="<?php echo esc_url( wp_get_attachment_image_src( get_post_thumbnail_id(), 'viraquiz_medium' )[0] ); ?>">

	<?php else : ?>

		<img src="<?php echo VIRAQUIZ_PLUGIN_URL . 'assets/images/default-quiz-thumbnail.jpg'; ?>">

	<?php endif; ?>	

	<h1><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h1>

</div><!-- .vrq-single-quiz -->