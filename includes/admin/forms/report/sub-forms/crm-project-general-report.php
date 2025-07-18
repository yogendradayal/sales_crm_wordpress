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
<form action="" method="post" id="crm-project-form" class="form-dx-crm-report">
	
	<input type="hidden" name="dx_crm_report" value="project">
	<input type="hidden" name="dx_crm_report_type" value="general">
	
	<table border="0" class="aligncenter" width="100%" id="dx-crm-report-table">
		<tr>
		
			<td><?php _e( 'Start Date' , 'dxcrm' );?>:</td>
			<td><input type="text" id="start_date" name="start_date" class="add-datepicker"></td>
			
			<td><?php _e( 'Planned End Date' , 'dxcrm' );?>:</td>
			<td><input type="text" id="end_date" name="end_date" class="add-datepicker"></td>
			
		</tr>
		<tr>
		
			<td><?php _e( 'Real End Date for 1st Milestone' , 'dxcrm' );?>:</td>
			<td><input type="text" id="end_date_milestone" name="end_date_milestone" class="add-datepicker"></td>
			
			<td><?php _e( 'Real End Date for Last Conversation' , 'dxcrm' );?>:</td>
			<td><input type="text" id="end_date_conversation" name="end_date_conversation" class="add-datepicker"></td>
			
		</tr>
		<tr>
			<?php
				/**
				 * Remove customer if current user is SALES_CRM_CUSTOMER
				*/
				if( current_user_can( 'administrator' ) ){
			?>
			<td><?php _e( 'Customers' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_customer_dropdown('customers'); ?></td>			
			<?php
				}
			?>
			
			<td><?php _e( 'Project Status' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_project_status_dropdown('project_status', false); ?></td>
			
		</tr>
	</table>

	<?php wp_nonce_field( 'report-project-nonce', 'project-nonce-report' );	?>

	<input type="submit" class="button button-primary" name="submit" value="<?php _e( 'Generate Report' , 'dxcrm' );?>" />
</form>
