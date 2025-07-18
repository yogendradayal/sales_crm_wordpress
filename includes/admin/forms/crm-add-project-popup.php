<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * add project title popup
 *
 * This is the code for the pop up for add project, which shows up when an user clicks
 * on the plus image button on the customer edit page.
 *
 * @package CRM System
 * @since 1.0.0
 **/
?>

<div class="dx-crm-pro-popup-content">

	<div class="dx-crm-pro-header">
		<div class="dx-crm-pro-header-title"><?php _e( 'Add New Project', 'dxcrm' );?></div>
		<div class="dx-crm-pro-popup-close"><a href="javascript:void(0);" class="dx-crm-pro-close-button"><img src="<?php echo DX_CRM_IMG_URL;?>/tb-close.png" title="Close"></a></div>
	</div>
	
	<div class="dx-crm-pro-popup">
		<div class="dx-crm-pro-add-project-title">
			<div>
				<label for="dx_crm_project_title"><?php _e( 'Enter Project Title', 'dxcrm' );?></label>
				<input type="text" class="dx_crm_project_title_text" name="dx_crm_project_title" id="dx_crm_project_title" size="33">
			</div>
			<div class="dx-crm-pro-title-error"><?php _e('Please Enter title for Project', 'dxcrm') ?></div>
			<div class="dx-crm-pro-title-success"><?php _e('Project Added Successfully.', 'dxcrm') ?></div>
			<div>
				<input type="button" class="dx_crm_add_project_button button" id="dx_crm_add_project_button" value="Add Project" />	
				<input type="button" class="dx_crm_add_more_project_button button" id="dx_crm_add_more_project_button" value="Save and Add More" />			
			</div>
			
		</div>		
	</div><!--.edd-points-popup-->
	
</div><!--.edd-points-popup-content-->
<div class="dx-crm-pro-popup-overlay"></div>