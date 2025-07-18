<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * add project type title popup
 *
 * This is the code for the pop up for add project type, which shows up when an user clicks
 * on the plus image button on the customer edit page.
 *
 * @package CRM System
 * @since 1.0.0
 **/
?>

<div class="dx-crm-pro-type-popup-content">

	<div class="dx-crm-pro-type-header">
		<div class="dx-crm-pro-type-header-title"><?php _e( 'Add New Project Type', 'dxcrm' );?></div>
		<div class="dx-crm-pro-type-popup-close"><a href="javascript:void(0);" class="dx-crm-pro-type-close-button"><img src="<?php echo DX_CRM_IMG_URL;?>/tb-close.png" title="Close"></a></div>
	</div>
	
	<div class="dx-crm-pro-type-popup">
		<div class="dx-crm-pro-type-add-project-title">
			<div>
				<label for="dx_crm_project_type_title"><?php _e( 'Enter Project Type Title', 'dxcrm' );?></label>
				<input type="text" class="dx_crm_project_type_title_text" name="dx_crm_project_type_title" id="dx_crm_project_type_title" size="33">
			</div>
			<div class="dx-crm-pro-type-title-error"><?php _e('Please Enter title for Project Type', 'dxcrm') ?></div>
			<div class="dx-crm-pro-type-title-success"><?php _e('Project Type Added Successfully.', 'dxcrm') ?></div>
			<div>
				<input type="button" class="dx_crm_add_project_type_button button" id="dx_crm_add_project_type_button" value="Add Project Type" />	
				<input type="button" class="dx_crm_add_more_project_type_button button" id="dx_crm_add_more_project_type_button" value="Save and Add More" />			
			</div>
			
		</div>		
	</div><!--.edd-points-popup-->
	
</div><!--.edd-points-popup-content-->
<div class="dx-crm-pro-type-popup-overlay"></div>