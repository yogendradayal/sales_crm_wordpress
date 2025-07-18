<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function dx_crm_add_meta_boxes() {
	
	global $current_screen;
	
	if ( is_admin() ) {
		
		/**
		 * Display CRM System Meta Box
		 * 
		 * Handles to display meta box for CRM System Customers
		 * 
		 * @package CRM System
		 * @since 1.0.0
		 */
		//include the main class file for metabox using class
		require_once ( DX_CRM_INC_DIR . '/meta-boxes/class-dx-crm-meta-box.php' );		
				
		if( current_user_can( 'administrator' ) ) {
			
			if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_CUSTOMERS) {
				
				include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-customers.php');
			}			
			if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_TIMESHEETS) {
				
				include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-timesheet.php');
			}
			if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_MILESTONES) {
				
				include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-milestone.php');	
			}
			if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_STAFF) {
				
				include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-potential.php');
			}
			if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_COMPANY_EXPENSES) {
				
				//include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-company-expenses.php');
			}
			if( isset( $current_screen->post_type ) && $current_screen->post_type == DX_CRM_POST_TYPE_ROADMAP ) {
				
				include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-roadmap.php');
			}
			
			apply_filters( 'dx_crm_admin_meta_after', 'dx_crm_admin_meta_after' );
		}
		
		if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_COMPANY) {
		
			include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-company.php');
		}
		if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_PROJECTS) {
		
			include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-projects.php');
		}
		if(isset($current_screen->post_type) && $current_screen->post_type == DX_CRM_POST_TYPE_DOC_MNGR) {
		
			include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-document-management.php');
		}
		
		//include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-customers-invoice.php');
		//include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-customers-advance.php');
		//include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-quote.php');
		//include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-pro-customer.php');
	}
}

// add action to add custom meta box in custom post
add_action( 'load-edit.php', 'dx_crm_add_meta_boxes' );
add_action( 'load-post.php', 'dx_crm_add_meta_boxes' );
add_action( 'load-post-new.php', 'dx_crm_add_meta_boxes' );
?>