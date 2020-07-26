<?php
/**
 * Admin Translation Page Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

settings_errors(); 

?>

<div class="wrap">

	<div class="vrq-panel vrq-translation-panel">

		<div class="vrq-panel-header"><span class="dashicons dashicons-admin-site"></span> Translation</div>

		<div class="vrq-panel-body">
		
			<form id="submitForm" method="post" action="options.php">

				<?php settings_fields( 'vrq-translation-settings' ); ?>
				<?php do_settings_sections( 'viraquiz_translation' ); ?>
				<?php submit_button( 'Save Changes', 'primary', 'btnSubmit' ); ?>

			</form>

		</div>

	</div>

</div>