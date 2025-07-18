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
				__( 'Company Info', 'dxcrm' ),
				__( 'Responsible Person', 'dxcrm' ),
				__( 'Type', 'dxcrm' ),
				__( 'Industry', 'dxcrm' ),
				__( 'Employees', 'dxcrm' ),
				__( 'Annual Income', 'dxcrm' ),
				__( 'Currency', 'dxcrm' ),
				__( 'Website', 'dxcrm' ),
				__( 'Customers', 'dxcrm' )
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
			
			// Convert Employees into readable string
			switch( $post->comp_employees ){
				case 'EMPLOYEES_1':
					$employees = 'less than 50';
				break;
				
				case 'EMPLOYEES_2':
					$employees = '50 - 250';
				break;

				case 'EMPLOYEES_3':
					$employees = '250 - 500';
				break;
				
				case 'EMPLOYEES_4':
					$employees = 'over 500';
				break;		
				
				default:
					$employees = '';
				break;
			}
			
			$exports .= '"'.$post->comp_name.'",'; 				// Name
			$exports .= '"'.$post->comp_description.'",';		// Company Info
			$exports .= '"'.$post->comp_responsible_person.'",';// Responsible Person
			$exports .= '"'.$post->comp_type.'",';				// Type
			$exports .= '"'.$post->comp_industry.'",';			// Industry
			$exports .= '"'.$employees.'",';					// Employees
			$exports .= '"'.$post->comp_annual_income.'",';		// Annual Income
			$exports .= '"'.$post->comp_currency.'",';			// Currency
			$exports .= '"'.$post->comp_url.'",';				// Website
			$exports .= '"'.$post->comp_customers.'",';			// Customers

			$exports .="\n";	
			
		}
		
	}
	
} else {
	
	/**
	 *
	 * NEED TO FIX BELOW
	 *
	*/

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
			
			// Responsible Person
			$c_rspnsbl_prsn = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_assigned_by', true );
			
			// Company Type
			$c_cmpny_typ = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_type', true );
			
			// Industry
			$c_ndstry = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_industry', true );
			
			// Employees
			$c_mplys = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_employees', true );
			
			// Convert Employees into readable string
			switch( $c_mplys ){
				case 'EMPLOYEES_1':
					$employees = 'less than 50';
				break;
				
				case 'EMPLOYEES_2':
					$employees = '50 - 250';
				break;

				case 'EMPLOYEES_3':
					$employees = '250 - 500';
				break;
				
				case 'EMPLOYEES_4':
					$employees = 'over 500';
				break;		
				
				default:
					$employees = '';
				break;
			}
			
			// Annual Income
			$c_annl_ncm = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_annual_income', true );
			
			// Currency
			$c_crrncy = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_currency', true );
			
			// URL
			$c_url = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_url', true );
			
			/**
			 *
			 * Get Customer.
			 *
			 * Customer is a custom post not meta
			 *
			*/
			$c_cstmr_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_pro_customer' );
			
			if( !empty ( $c_cstmr_ids ) ){
				
				$c_cstmrs = array();
				
				foreach( $c_cstmr_ids as $value ){
					$c_cstmrs[] = get_the_title( $value );
				}
				
				if( !empty ( $c_cstmrs ) ){
					$c_cstmr = implode( ", ", $c_cstmrs );
				} else {
					$c_cstmr = '';
				}
				
			} else {
				$c_cstmr = '';
			}

			//this line should be on start of loop		
			$exports .= '"'.$post_name.'",'; 		// Name
			$exports .= '"'.$post_content.'",';		// Company Info
			$exports .= '"'.$c_rspnsbl_prsn.'",';	// Responsible Person
			$exports .= '"'.$c_cmpny_typ.'",';		// Type
			$exports .= '"'.$c_ndstry.'",';			// Industry
			$exports .= '"'.$employees.'",';		// Employees
			$exports .= '"'.$c_annl_ncm.'",';		// Annual Income
			$exports .= '"'.$c_crrncy.'",';			// Currency
			$exports .= '"'.$c_url.'",';			// Website
			$exports .= '"'.$c_cstmr.'",';			// Customers

			$exports .="\n";	

		}
		
	}

}
?>