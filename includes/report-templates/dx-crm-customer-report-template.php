<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $dx_crm_report;

/**
 *
 * Report template for Customer
 * Generated in DX CRM->Report->Customer
 *
*/

$exports = '';

$columns = array(	
				__( 'Name', 'dxcrm' ),
				__( 'Customer Info', 'dxcrm' ),
				__( 'Skills', 'dxcrm' ),
				__( 'First Projecy Type', 'dxcrm' ),
				__( 'Initial Investment', 'dxcrm' ),
				__( 'Referral', 'dxcrm' ),
				__( 'Contact Date', 'dxcrm' ),
				__( 'Contact Type', 'dxcrm' ),
				__( 'Company Role', 'dxcrm' ),
				__( 'Email', 'dxcrm' ),
				__( 'Phone Number', 'dxcrm' ),
				__( 'Company', 'dxcrm' ),
				__( 'Project', 'dxcrm' ),	
				__( 'Campaigns', 'dxcrm' ),	
				__( 'Bank Info', 'dxcrm' ),		
				__( 'VAT Number', 'dxcrm' ),	
				__( 'Country', 'dxcrm' ),
				__( 'Total Project', 'dxcrm' ),	
				__( 'Totap Paid', 'dxcrm' ),	
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
		
	//	print_r( $posts );
		
		
		foreach ( $posts as $post  ) { 
			
			$exports .= '"'.$post->cust_name.'",'; 					// Customer Name
			$exports .= '"'.$post->cust_desc.'",';					// Customer Info
			$exports .= '"'.$post->cust_skills.'",';				// Skills
			$exports .= '"'.$post->cust_project_type.'",';			// First Projecy Type
			$exports .= '"'.$post->cust_initial_investment.'",';	// Initital Investment
			$exports .= '"'.$post->cust_referral.'",';				// Referral
			$exports .= '"'.$post->cust_contact_date.'",';			// Contact Date
			$exports .= '"'.$post->cust_contact_type.'",';			// Contact Type
			$exports .= '"'.$post->cust_company_role.'",';			// Company Role
			$exports .= '"'.$post->cust_email.'",';					// Email
			$exports .= '"'.$post->cust_phone_number.'",';			// Phone Number
			$exports .= '"'.$post->cust_companies.'",';				// Company
			$exports .= '"'.$post->cust_projects.'",';				// Project
			$exports .= '"'.$post->cust_campaigns.'",';				// Campaigns
			$exports .= '"'.$post->cust_bank_info.'",';				// Bank Info
			$exports .= '"'.$post->cust_vat_number.'",';			// VAT Number
			$exports .= '"'.$post->cust_country.'",';				// Country
			$exports .= '"'.$post->total_project.'",';				// Total Project
			$exports .= '"'.$post->total_paid.'",';					// Total Paid

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
		
		global $dx_crm_model;
			
		$cstmr_dtls_cstm = $dx_crm_model->crm_get_customer_details_custom();
		
		foreach ( $posts as $post  ) { 
					
			$skills_arr = array();		
			$project_type = array();
			
			$post_id 		= $post->ID;
			$post_name 		= $post->post_title;	
			$post_content 	= $post->post_content;		
			$skills_raw	= wp_get_post_terms( $post_id, 'crm_emp_skill' );
			
			/**
			 *
			 * Prepare raw info from taxonomy table
			 *
			*/		
			if ( !empty( $skills_raw ) ){
				
				foreach( $skills_raw as $value ){
					$skills_arr[] = $value->name;
				}
				
				if( !empty ( $skills_arr ) ){
					$c_skills = implode( ", ", $skills_arr );
				} else {
					$c_skills = '';
				}
				
			} else {
				$c_skills = '';
			}	
			
			// First Project type
			$c_fpt = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_first_pro_type', true );		
			
			// Initial Investment
			$c_ciivstmnt = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_initial_investment', true );	
			
			// Referral
			$c_referral = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_referral', true );	
			
			// Contract Date
			$c_cdate_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_contact_date', true );		
			$c_cdate = ( !empty ( $c_cdate_raw ) ) ? date( DX_DATE_META_FORMAT, strtotime( $c_cdate_raw ) ) : '' ;	
			
			// Contact Type
			$c_cntcttype = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'contact_type', true );	
			
			// Company Role
			$c_cmpnyrole = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_role', true );	
			
			// Email
			$c_email = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_email', true );	
			
			// Phone Number
			$c_pnum = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'contact_number', true );
			
			/**
			 *
			 * Get company.
			 *
			 * Company is a custom post not meta
			 *
			*/
			$c_company_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_company' );
			
			if( !empty ( $c_company_ids ) ){
				
				$c_cmpnys = array();
				
				foreach( $c_company_ids as $value ){
					$c_cmpnys[] = get_the_title( $value );
				}
				
				if( !empty ( $c_cmpnys ) ){
					$c_company = implode( ", ", $c_cmpnys );
				} else {
					$c_company = '';
				}
				
			} else {
				$c_company = '';
			}
		
			/**
			 *
			 * Get Project.
			 * Convert Project type into readable string
			 *
			*/		
			$projects_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_project' );
			if( !empty ( $projects_raw ) ){
				
				$projects = array();
				
				foreach( $projects_raw as $value ) {
					$projects[] = get_the_title( $value );
				}
				
				if( !empty( $projects ) ){
					$c_project = implode( ", ", $projects );
				} else {
					$c_project = '';
				}
				
			} else {
				$c_project = '';
			}
			
			/**
			 *
			 * Get Campaigns.
			 *
			 * Campaigns is a custom post not meta
			 *
			*/
			$c_cmpgns_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_campaigns' );
			
			if( !empty ( $c_cmpgns_ids ) ){
				
				$c_cmpgns = array();
				
				foreach( $c_cmpgns_ids as $value ){
					$c_cmpgns[] = get_the_title( $value );
				}
				
				if( !empty ( $c_cmpgns ) ){
					$c_cmpgn = implode( ", ", $c_cmpgns );
				} else {
					$c_cmpgn = '';
				}
				
			} else {
				$c_cmpgn = '';
			}	
			
			// Bank Info
			$c_bankinfo = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_bank_info', true );
			
			// VAT Number
			$c_vatnum = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_vat_number', true );
			
			// Country
			$c_country = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_country', true );
			
			/**
			 *
			 * Get total project and paid
			 *
			*/
			if( !empty ( $cstmr_dtls_cstm ) ){
				
				foreach( $cstmr_dtls_cstm as $c_cstm ){
					
					if( $c_cstm['ID'] == $post_id ){
						
						$c_tpaid = $c_cstm['total_paid'];
						$c_tproj = $c_cstm['total_project'];					
						
					}
					
				}
			
			} else {
				$c_tpaid = '';
				$c_tproj = '';
			}

			//this line should be on start of loop		
			$exports .= '"'.$post_name.'",'; 	// Customer Name
			$exports .= '"'.$post_content.'",';	// Customer Info
			$exports .= '"'.$c_skills.'",';		// Skills
			$exports .= '"'.$c_fpt.'",';		// First Projecy Type
			$exports .= '"'.$c_ciivstmnt.'",';	// Initital Investment
			$exports .= '"'.$c_referral.'",';	// Referral
			$exports .= '"'.$c_cdate.'",';		// Contact Date
			$exports .= '"'.$c_cntcttype.'",';	// Contact Type
			$exports .= '"'.$c_cmpnyrole.'",';	// Company Role
			$exports .= '"'.$c_email.'",';		// Email
			$exports .= '"'.$c_pnum.'",';		// Phone Number
			$exports .= '"'.$c_company.'",';	// Company
			$exports .= '"'.$c_project.'",';	// Project
			$exports .= '"'.$c_cmpgn.'",';		// Campaigns
			$exports .= '"'.$c_bankinfo.'",';	// Bank Info
			$exports .= '"'.$c_vatnum.'",';		// VAT Number
			$exports .= '"'.$c_country.'",';	// Country
			$exports .= '"'.$c_tproj.'",';		// Total Project
			$exports .= '"'.$c_tpaid.'",';		// Total Paid

			$exports .="\n";	

		}
		
	}

}
?>