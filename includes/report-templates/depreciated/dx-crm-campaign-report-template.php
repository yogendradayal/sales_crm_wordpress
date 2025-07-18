<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $dx_crm_report;

/**
 *
 * Report template for Campaign Add-on plugin
 * Generated in DX CRM->Report->Campaign
 *
*/

$exports = '';

$columns = array(	
				__( 'Name', 'dxcrm' ),
				__( 'Campaign Description', 'dxcrm' ),
				__( 'Customers', 'dxcrm' ),
				__( 'Contact Type', 'dxcrm' )
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
			
			// Convert contact type into readable string
			switch( $post->camp_contact_type ){
				case '1':
					$ctype = 'Email';
				break;
				case '2':
					$ctype = 'Phone';
				break;
				case '3':
					$ctype = 'Social Network';
				break;
				default:
					$ctype = '';
				break;
			}
			
			$exports .= '"'.$post->camp_name.'",'; 			// Name
			$exports .= '"'.$post->camp_description.'",';	// Campaign Description
			$exports .= '"'.$post->camp_customers.'",';		// Customers
			$exports .= '"'.$ctype.'",';					// Contact Type

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
				
		foreach ( $posts as $post  ) { 
			
			$post_id 		= $post->ID;
			$post_name 		= $post->post_title;	
			$post_content 	= $post->post_content;		
			
			/**
			 *
			 * Get Customer.
			 *
			 * Customer is a custom post not meta
			 *
			*/
			$c_cstmr_ids = get_post_meta( $post_id, '_dx_crm_joined_pro_customer' );
			
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
			
			// Initial Investment
			$c_cntct_type = get_post_meta( $post_id, '_dx_crm_cmp_contact_type', true );	
			
			// Convert contact type into readable string
			switch( $c_cntct_type ){
				case '1':
					$ctype = 'Email';
				break;
				case '2':
					$ctype = 'Phone';
				break;
				case '3':
					$ctype = 'Social Network';
				break;
				default:
					$ctype = '';
				break;
			}
			
			//this line should be on start of loop		
			$exports .= '"'.$post_name.'",'; 			// Name
			$exports .= '"'.$post_content.'",';	// Campaign Description
			$exports .= '"'.$c_cstmr.'",';		// Customers
			$exports .= '"'.$ctype.'",';	// Contact Type

			$exports .="\n";	

		}
		
	}

}
?>