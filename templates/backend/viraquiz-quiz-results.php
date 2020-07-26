<?php
/**
 * Admin CPT Quiz Results Metabox Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

settings_errors(); 

?>

<div class="quiz-results">

	<ul class="quiz-results-list clearfix">

		<?php 

			if( ! empty( $results ) ) {

				$html = '';

				foreach ( $results as $result_key => $result ) {
					
					$gender = esc_attr( $result['gender'] );
					$html .= '<li class="quiz-results-item">';
					$html .= '<div class="result-item-header" data-result="' . $result_key . '">Result ' . ( $result_key + 1 )  .  '</div>';
					$html .= '<div class="result-item-body">';
					$html .= '<img src="' . esc_html( get_site_url() ) . '/viraquiz-result-schema/' . (int)$post->ID . '/' . $result_key . '">';
					$html .= '<div class="result-genders">';

					if( $gender == 'male' || $gender == 'both' ) {
						$html .= '<img src="' .  VIRAQUIZ_PLUGIN_URL . 'assets/images/male.png">';
					}

					if( $gender == 'female' || $gender == 'both' ) {
						$html .= '<img src="' .  VIRAQUIZ_PLUGIN_URL . 'assets/images/female.png">';
					}

					$html .= '<button type="button" class="delete-quiz-result"><span class="dashicons dashicons-trash"></span> Delete</button>';
					$html .= '</div>';			
					$html .= '</div>';				
					$html .= '</li>';

				}

				echo $html;

			} else {

				echo '<li><p class="vrq-info vrq-warning description"><span class="dashicons dashicons-info"></span> <strong>You have no results added to this quiz.</strong><br />Start adding results using result builder below.</p></li>';
					
			}


		 ?>

	</ul><!-- .quiz-results-list -->

	<div class="quiz-creator clearfix">

		<div class="quiz-controls">
			<ul>
					
				<li class="add-user-profile"><span class="dashicons dashicons-admin-users"></span> Add user profile picture</li>	
				<li class="add-text"><span class="dashicons dashicons-edit"></span> Add text</li>
				<li class="add-image"><span class="dashicons dashicons-format-image"></span> Add image</li>
				<li class="layers-btn"><span class="dashicons dashicons-images-alt"></span> Layers</li>
			
			</ul>	

		</div>

		<div class="quiz-editor-wrapper">
			<div class="quiz-editor" style="background-image: url( <?php echo VIRAQUIZ_PLUGIN_URL . 'assets/images/editor-background.png' ?> )">
				
			</div>
		</div><!-- .quiz-editor -->

		<div class="quiz-actions">

			<input type="hidden" id="post-id" value="<?php echo $post->ID; ?>" />
			
			<div class="result-genders clearfix">

				<p>Result Genter</p>

				<div>
					<img class="gender-male gender-active" src="<?php echo VIRAQUIZ_PLUGIN_URL . 'assets/images/male.png'; ?>">
					<img class="gender-female gender-active" src="<?php echo VIRAQUIZ_PLUGIN_URL . 'assets/images/female.png'; ?>">
				</div>

			</div><!-- .result-genders -->
			<br>

				
			<button type="button" class="save-result-button"><span class="dashicons dashicons-plus-alt"></span>
				<span class="save-result">Save result</span>
			</button>

		</div><!-- .quiz-actions -->

	</div><!-- .quiz-creator -->

</div><!-- .quiz-results -->