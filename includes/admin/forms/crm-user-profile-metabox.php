<?php
	$crm_customer_css = 'crm-display-none';

	if( $user_role == DX_CRM_CUSTOMER_ROLE || (isset($current_user_role[0]) && $current_user_role[0] == DX_CRM_CUSTOMER_ROLE) ) { // Check staff member role
		$crm_customer_css = '';
	}
	
?>
<style type="text/css">
	.crm-display-none {
		display: none;
	}
</style>
<div class="crm-user-meta-details-wrp">
	<table class="form-table">
		<tbody>
			<input type="hidden" name="current_user_role" id="current_user_role" value="<?php echo $current_user_role[0]; ?>">
			<tr class="dxcrm-customer-row tr <?php echo $crm_customer_css; ?>">
				<th><label for="_crm_user_group_id"><?php _e( 'Select Customer' , 'dxcrm' );?></label></th>
				<td>
					<select name="_crm_user_group_id" id="_crm_user_group_id" class="crm-customer-select">
					<?php echo $customers_list; ?>
					</select>
				</td>
			</tr>
	</table>
</div> <!-- end .dxcrm-cmp-details-wrp -->