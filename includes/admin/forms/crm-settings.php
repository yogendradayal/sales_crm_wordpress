<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * CRM Country Setting Page
 * 
 * 
 * @package CRM System
 * @since 1.0.0
 */

//check settings updated or not
if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
	
	echo '<div class="updated" id="message">
		<p><strong>'. __("Changes Saved Successfully.",'dxcrm') .'</strong></p>
	</div>';
}
	
?>

<div class="wrap">
    
    <h2 class="crm-settings-title">
    	<?php _e( 'CRM Settings', 'dxcrm' ); ?>
    </h2>
    	 
	<form method="POST" action="options.php" enctype="multipart/form-data">
	    <?php
	    	settings_fields( 'dx_crm_plugin_options' );
	    	$dx_crm_options = get_option( 'dx_crm_options' );
	    	
			//Action for DM settings
		    do_action('dx_crm_dm_admin_settings');

		    //Action for add country settings
		    do_action('dx_crm_get_country_settings');
		    
		    //Action for social profiles settings
		    do_action('dx_crm_get_social_profiles_settings');
		    
		    //Google contact settings form			
			do_action('dx_crm_get_google_settings');

			//Woo Commerce User Import			
			do_action('dx_crm_woo_import_user_settings');

			//Extension Generic action 
			do_action('dx_crm_extension_generic_action');
	    ?>
	</form>
</div>