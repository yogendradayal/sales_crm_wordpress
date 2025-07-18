<?php

/**
 *
 * By Largest Income Form
 *
 * Generate Report by largest income
 * Highest amount by client (client who brought the largest income)
 *
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ){ exit; }

?>
<p><?php _e( 'Customer report with largest income.' , 'dxcrm' ); ?></p>
<form action="" method="post" id="crm-customer-form" class="form-dx-crm-report">
	
	<?php 
		/* Determine what type of report we are going to generate */
	?>
	<input type="hidden" name="dx_crm_report" value="customer">
	<input type="hidden" name="dx_crm_report_type" value="largest-income">
	
	<table border="0" class="aligncenter" width="100%" id="dx-crm-report-table">		
		<tr>
		
			<td><?php _e( 'Total Paid' , 'dxcrm' ); ?>:</td>
			<td>
				<select name="total_paid" class="chosen-select">
					<option value="DESC"><?php _e( 'DESC' , 'dxcrm' ); ?></option>
					<option value="ASC"><?php _e( 'ASC' , 'dxcrm' ); ?></option>
				</select>
			</td>
			
		</tr>
	</table>

	<?php wp_nonce_field( 'report-customer-nonce', 'customer-nonce-report' );	?>

	<input type="submit" class="button button-primary" name="submit" value="<?php _e( 'Generate Report' , 'dxcrm' ); ?>" />
	
</form>