<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $dx_crm_report;

/**
 *
 * Report template for Project
 * Generated in DX CRM->Report->Project
 *
*/

$exports = '';

$columns = array(	
				__( 'Project Name', 'dxcrm' ),
				__( 'Project Type', 'dxcrm' ),
				__( 'Company', 'dxcrm' ),
				__( 'Start Date', 'dxcrm' ),
				__( 'Planned End Date', 'dxcrm' ),
				__( 'Ongoing', 'dxcrm' ),
				__( 'Real End Date for Last Milestone', 'dxcrm' ),
				__( 'Real End Date for Last Conversation', 'dxcrm' ),
				__( 'Agreed Cost', 'dxcrm' ),
				__( 'Currency', 'dxcrm' ),
				__( 'Status', 'dxcrm' ),
				__( 'Total Paid', 'dxcrm' ),	
				__( 'Responsible Person', 'dxcrm' ),	
				__( 'Customers', 'dxcrm' ),									
			);							 

// Put the name of all fields
foreach ($columns as $column) {

$exports .= '"'.$column.'",';

}
$exports .="\n";


if( !empty( $_POST['dx_crm_report'] ) ){
	
	/*
	 *
	 * Generate report from DX CRM Report Table
	 *
	*/
	$posts = $dx_crm_report->dx_get_report( $_REQUEST );
	
	if( !empty( $posts ) ) { 
	
		foreach ( $posts as $post  ) { 
			
			$exports .= '"'.$post->project_name.'",'; 	// Project Name
			$exports .= '"'.$post->project_type.'",';			// Project Type
			$exports .= '"'.$post->project_company.'",';	// Company
			$exports .= '"'.$post->project_start_date.'",';		// Start Date
			$exports .= '"'.$post->project_planned_end_date.'",';		// Planned End Date
			$exports .= '"'.$post->project_ongoing.'",';	// Ongoing
			$exports .= '"'.$post->project_end_date_first_milestone.'",';		// Real End Date for Last Milestone
			$exports .= '"'.$post->project_end_date_last_conversation.'",';		// Real End Date for Last Conversation
			$exports .= '"'.$post->project_agreed_cost.'",';		// Agreed Cost
			$exports .= '"'.$post->project_currency.'",';		// Currency
			$exports .= '"'.$post->project_status.'",';		// Status
			$exports .= '"'.$post->project_total_paid.'",';		// Total Paid
			$exports .= '"'.$post->project_responsible_person.'",';		// Responsible Person
			$exports .= '"'.$post->project_customers.'",';		// Customers

			$exports .="\n";	
			
		}
		
	}
	
} else {
	
	$args = array(
				'post_type'   => $report_post_type,
				'posts_per_page' => -1,
				);

	$posts = get_posts( $args );
	
	if( !empty( $posts ) ) { 
	
		foreach ( $posts as $post  ) { 
		
			$project_type = array();
			
			$post_id 		= $post->ID;
			$post_name 		= $post->post_title;		
			$project_types 	= wp_get_post_terms( $post_id, 'crm_pro_type' );
			
			/**
			 *
			 * Prepare raw info from taxonomy table
			 *
			*/
			if ( !empty( $project_types ) ){
				foreach( $project_types as $ptype ){
					$project_type[] = $ptype->name;
				}
			}
			
			/**
			 *
			 * Convert Project type into readable string
			 *
			*/
			if( !empty ( $project_type ) ){
				$type = implode( ", ", $project_type );
			} else {
				$type = '';
			}
					
			/**
			 *
			 * Get company.
			 *
			 * Company is a custom post not meta
			 *
			*/
			$p_company_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_project' );
			
			if( !empty ( $p_company_ids ) ){
				
				$p_cmpnys = array();
				
				foreach( $p_company_ids as $value ){
					$p_cmpnys[] = get_the_title( $value );
				}
				
				if( !empty ( $p_cmpnys ) ){
					$p_company = implode( ", ", $p_cmpnys );
				} else {
					$p_company = '';
				}
				
			} else {
				$p_company = '';
			}
			
			// Start Date
			$p_sdate_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_start_date', true );		
			$p_sdate = ( !empty ( $p_sdate_raw ) ) ? date( DX_DATE_META_FORMAT, strtotime( $p_sdate_raw ) ) : '' ;
			
			// Planned End Date
			$p_edate_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_end_date', true );		
			$p_edate = ( !empty ( $p_edate_raw ) ) ? date( DX_DATE_META_FORMAT, strtotime( $p_edate_raw ) ) : '' ;
			
			// Ongoing
			$p_ongoing_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_ongoing', true );		
			$p_ongoing = ( !empty ( $p_ongoing_raw ) ) ? 'Yes' : 'No';
			
			// Real End Date for Last Milestone
			$p_redlm_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_real_end_date_first_mile', true );
			$p_redlm = ( !empty ( $p_redlm_raw ) ) ? date( DX_DATE_META_FORMAT, strtotime( $p_redlm_raw ) ) : '' ;
			
			// Real End Date for Last Conversation
			$p_redlc_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_real_end_date_last_conversation', true );
			$p_redlc = ( !empty ( $p_redlc_raw ) ) ? date( DX_DATE_META_FORMAT, strtotime( $p_redlc_raw ) ) : '' ;
			
			// Currency
			$p_curr = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'project_currency', true );
			
			// Cost
			$p_cost = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_agreed_cost', true );
			
			// Status
			$p_status = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'project_status', true );
			
			// Total Paid
			$p_total = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'project_total', true );
			
			// Responsible Person
			$p_rprsn = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'project_assigned_by', true );
			
			/**
			 *
			 * Get Customer.
			 *
			 * Customer is a custom post not meta
			 *
			*/
			$p_cstmr_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_pro_customer' );
			
			if( !empty ( $p_cstmr_ids ) ){
				
				$p_cstmrs = array();
				
				foreach( $p_cstmr_ids as $value ){
					$p_cstmrs[] = get_the_title( $value );
				}
				
				if( !empty ( $p_cstmrs ) ){
					$p_cstmr = implode( ", ", $p_cstmrs );
				} else {
					$p_cstmr = '';
				}
				
			} else {
				$p_cstmr = '';
			}		
			
			//this line should be on start of loop
			
			$exports .= '"'.$post_name.'",'; 	// Project Name
			$exports .= '"'.$type.'",';			// Project Type
			$exports .= '"'.$p_company.'",';	// Company
			$exports .= '"'.$p_sdate.'",';		// Start Date
			$exports .= '"'.$p_edate.'",';		// Planned End Date
			$exports .= '"'.$p_ongoing.'",';	// Ongoing
			$exports .= '"'.$p_redlm.'",';		// Real End Date for Last Milestone
			$exports .= '"'.$p_redlc.'",';		// Real End Date for Last Conversation
			$exports .= '"'.$p_cost.'",';		// Agreed Cost
			$exports .= '"'.$p_curr.'",';		// Currency
			$exports .= '"'.$p_status.'",';		// Status
			$exports .= '"'.$p_total.'",';		// Total Paid
			$exports .= '"'.$p_rprsn.'",';		// Responsible Person
			$exports .= '"'.$p_cstmr.'",';		// Customers

			$exports .="\n";	

		}
	
	}
	
}
?>