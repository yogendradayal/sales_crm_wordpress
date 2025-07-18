<?php

/**
 *
 * General Form
 *
 * Generate Report by general field
 *
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ){ exit; }

?>
<form action="" method="post" id="crm-customer-form" class="form-dx-crm-report">
	
	<?php 
		/* Determine what type of report we are going to generate */
	?>
	<input type="hidden" name="dx_crm_report" value="customer">
	<input type="hidden" name="dx_crm_report_type" value="general">
	
	<table border="0" class="aligncenter" width="100%" id="dx-crm-report-table">		
		<tr>
		
			<td><?php _e( 'Contact Date' , 'dxcrm' ); ?>:</td>
			<td><input type="text" id="contact_date" name="contact_date" class="add-datepicker"></td>
			
			<td><?php _e( 'Contact Type' , 'dxcrm' ); ?>:</td>
			<td><?php echo $dx_crm_model->crm_contact_type_dropdown('contact_type', false); ?></td>
			
		</tr>
		<tr>
		
			<td><?php _e( 'Company' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_company_dropdown(); ?></td>
			
			<td><?php _e( '1st Project Type' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_project_type_dropdown('project_type', false); ?></td>
			
		</tr>
		<tr>
		
			<td><?php _e( 'Project' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_project_dropdown(); ?></td>
		</tr>
	</table>

	<?php wp_nonce_field( 'report-customer-nonce', 'customer-nonce-report' );	?>

	<input type="submit" class="button button-primary" name="submit" value="<?php _e( 'Generate Report' , 'dxcrm' ); ?>" />
	
</form>