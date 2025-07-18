<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * add customer title popup
 *
 * This is the code for the pop up for add customer, which shows up when an user clicks
 * on the plus image button on the company edit page and project edit page.
 *
 * @package CRM System
 * @since 1.0.0
 **/
?>

<div class="dx-crm-cust-popup-content">

	<div class="dx-crm-cust-header">
		<div class="dx-crm-cust-header-title"><?php _e( 'Add New Customer', 'dxcrm' );?></div>
		<div class="dx-crm-cust-popup-close"><a href="javascript:void(0);" class="dx-crm-cust-close-button"><img src="<?php echo DX_CRM_IMG_URL;?>/tb-close.png" title="Close"></a></div>
	</div>
	
	<div class="dx-crm-cust-popup">
		<div class="dx-crm-cust-add-customer-title">
			<div>
				<label for="dx_crm_customer_title"><?php _e( 'Enter Customer Title', 'dxcrm' );?></label>
				<input type="text" class="dx_crm_customer_title_text" name="dx_crm_customer_title" id="dx_crm_customer_title" size="31">
			</div>
			<div class="dx-crm-cust-title-error"><?php _e('Please Enter title for customer', 'dxcrm') ?></div>
			<br /><div>
				<label for="dx_crm_customer_email"><?php _e( 'Enter Customer Email', 'dxcrm' );?></label>
				<input type="text" class="dx_crm_customer_email_text" name="dx_crm_customer_email" id="dx_crm_customer_email" size="31">
			</div>			
			<div class="dx-crm-cust-email-error"><?php _e('Please Enter email for customer', 'dxcrm') ?></div>
			<div class="dx-crm-cust-title-success"><?php _e('Customer Added Successfully.', 'dxcrm') ?></div>
			<div>
				<input type="button" class="dx_crm_add_customer_button button" id="dx_crm_add_customer_button" value="Add customer" />
				<input type="button" class="dx_crm_add_more_customer_button button" id="dx_crm_add_more_customer_button" value="Save and Add More" />			
			</div>			
		</div>		
	</div><!--.edd-points-popup-->
	
</div><!--.edd-points-popup-content-->
<div class="dx-crm-cust-popup-overlay"></div>