<?php

/**
 *
 * By Highest Cost Form
 *
 * Generate Report by highest cost
 * Highest project revenue (projects that were negotiated for/got paid the highest amount).
 *
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ){ exit; }

?>
<p><?php _e( 'Project report with highest price.' , 'dxcrm' ); ?></p>
<form action="" method="post" id="crm-customer-form" class="form-dx-crm-report">
	
	<?php 
		/* Determine what type of report we are going to generate */
	?>
	<input type="hidden" name="dx_crm_report" value="project">
	<input type="hidden" name="dx_crm_report_type" value="highest-cost">
	
	<table border="0" class="aligncenter" width="100%" id="dx-crm-report-table">		
		<tr>		
			<td><?php _e( 'Project Price' , 'dxcrm' ); ?>:</td>
			<td>
				<select name="agreed_cost_sort" class="chosen-select">
					<option value="DESC"><?php _e( 'DESC' , 'dxcrm' ); ?></option>
					<option value="ASC"><?php _e( 'ASC' , 'dxcrm' ); ?></option>
				</select>
			</td>			
		</tr>
	</table>

	<?php wp_nonce_field( 'report-project-nonce', 'project-nonce-report' );	?>
	
	<input type="submit" class="button button-primary" name="submit" value="<?php _e( 'Generate Report' , 'dxcrm' ); ?>" />
	
</form>