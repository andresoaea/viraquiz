<?php
/**
 * Admin Facebook Application Config Page Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

settings_errors(); 

?>

<div class="wrap">

	<div class="vrq-panel vrq-fb-app-config">

		<div class="vrq-panel-header"><span class="dashicons dashicons-admin-network"></span> Facebook Application Config</div>

		<div class="vrq-panel-body">
		
			<form id="submitForm" method="post" action="options.php">

				<?php settings_fields( 'vrq-fb-app-config-settings' ); ?>
				<?php do_settings_sections( 'viraquiz_app_config' ); ?>
				<?php submit_button( 'Save Changes', 'primary', 'btnSubmit' ); ?>

			</form>

		</div>

	</div>

</div>