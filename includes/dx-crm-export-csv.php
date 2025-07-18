<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Export to CSV for CRM System
 * 
 * Handles to Export to CSV on run time when 
 * user will execute the url which is sent to
 * 
 * @package CRM System
 * @since 1.0.0
 */ 
function dx_crm_export_to_csv(){
	$prefix = DX_CRM_META_PREFIX;
	
	global $dx_crm_report;
	
	if( isset( $_GET['dx-crm-exp-csv'] ) && !empty( $_GET['dx-crm-exp-csv'] ) && $_GET['dx-crm-exp-csv'] == '1'
		&& isset($_GET['crm_post_type']) && !empty($_GET['crm_post_type'] ) ) {
		
		$crm_post_type = $_GET['crm_post_type']; 
		$crm_tab_type = ( !empty ( $_GET['tab'] ) ) ? $_GET['tab'] : '';
		
		/**
		 *
		 * Project Report
		 *
		 * Generated in DX CRM->Report under Project tab
		 *
		*/
		if( $crm_post_type == DX_CRM_POST_TYPE_ROADMAP ){
			
			/**
			 *
			 * We build our query here
			 * On Report, post type is being determine by tab
			 *
			*/
			$report_post_type = '';
			
			if( $crm_tab_type == "project" ){
				
				$report_post_type = DX_CRM_POST_TYPE_PROJECTS; // Project
				
				// Check if there's report
				$cposts = $dx_crm_report->dx_get_report( $_POST );
				
				if( empty( $cposts ) ){ 					
					// Throw error
					add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_err_ntc' ); 
					
					return; // Stop process
					
				} else {
					// Display success message
					add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_sccss_ntc' ); 
					
					// include template
					include_once( DX_CRM_REPORT_TPL . '/dx-crm-project-report-template.php' );
					
				}				
				
			}
			
			if( $crm_tab_type == "customer" ){
				
				$report_post_type = DX_CRM_POST_TYPE_CUSTOMERS; // Customer
				
				// Check if there's report
				$cposts = $dx_crm_report->dx_get_report( $_POST );
				
				if( empty( $cposts ) ){ 					
					// Throw error
					add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_err_ntc' ); 
					
					return; // Stop process
					
				} else {
					
					// Display success message
					add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_sccss_ntc' ); 
					
					// include template
					include_once( DX_CRM_REPORT_TPL . '/dx-crm-customer-report-template.php' );
					
				}				
				
			}
			
			if( $crm_tab_type == "company" ){
				
				$report_post_type = DX_CRM_POST_TYPE_COMPANY; // Company
				
				// Check if there's report
				$cposts = $dx_crm_report->dx_get_report( $_POST );
				
				if( empty( $cposts ) ){ 					
					// Throw error
					add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_err_ntc' ); 
					
					return; // Stop process
					
				} else {
					// Display success message
					add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_sccss_ntc' ); 
					
					// include template
					include_once( DX_CRM_REPORT_TPL . '/dx-crm-company-report-template.php' );
					
				}
			}
			
			// IF CAMPAIGN
			// CAMPAIGN IS AN ADD-ON PLUGIN
			if( $crm_tab_type == "campaign" ){
				
				// CHECK IF PLUGIN ACTIVATED
				include_once(ABSPATH.'wp-admin/includes/plugin.php');
				if ( !function_exists('is_plugin_active') || is_plugin_active( 'crm-campaigns/crm-campaigns.php' ) ){
					$report_post_type = DX_CRM_POST_TYPE_CAMPAIGN; // Company
					
					// Check if there's report
					$cposts = $dx_crm_report->dx_get_report( $_POST );
					
					if( empty( $cposts ) ){ 					
						// Throw error
						add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_err_ntc' ); 
						
						return; // Stop process
						
					} else {
						// Display success message
						add_action( 'wp_settings_admin_notices', 'dx_crm_rprt_sccss_ntc' ); 
						
						// include template
						include_once( DX_CRM_REPORT_TPL . '/dx-crm-campaign-report-template.php' );
						
					}
				}
			}
						
			if( empty( $report_post_type ) ){
				return; // Return if undentified report post type
			}
			
			$file_name = 'dx-crm-' . $crm_tab_type . '-report-';
		}
		
		if( $crm_post_type == DX_CRM_POST_TYPE_CUSTOMERS ){

			$args = array(
				'post_type'   => $crm_post_type,
				'posts_per_page' => -1,
			);				
			$posts_data = get_posts( $args );
			$exports = '';
			$columns = array(	
				__( 'Customer Name', 'dxcrm' ),
				__( 'First Project Type', 'dxcrm' ),
				__( 'Initial Investment', 'dxcrm' ),
				__( 'Referral', 'dxcrm' ),
				__( 'Contact Date', 'dxcrm' ),
				__( 'Contact Type', 'dxcrm' ),
				__( 'Company Role', 'dxcrm' ),
				__( 'Email', 'dxcrm' ),
				__( 'Phone Number', 'dxcrm' ),
				__( 'Company', 'dxcrm' ),
				__( 'Project', 'dxcrm' ),
		     );
					
	        // Put the name of all fields
			$exports .= dx_crm_add_to_export( $columns );
			$exports .="\n";
			
			if( !empty( $posts_data ) ) { 
	
				foreach ( $posts_data as $post_data  ) { 
					$post_id = $post_data->ID;
					$project_type_id 		= get_post_meta( $post_id, $prefix . 'cust_first_pro_type', true );
					$project_type 			= get_term_by( 'id', $project_type_id, 'crm_pro_type', ARRAY_A );
					$project_type			= $project_type['name'];
					$joined_companies_ids = get_post_meta( $post_id, $prefix . 'joined_company', false );
					$joined_company = dx_crm_get_names( $joined_companies_ids );
					$joined_projects_ids = get_post_meta( $post_id, $prefix . 'joined_project', false );
					$joined_project = dx_crm_get_names( $joined_projects_ids );

					$customer = array(
						'project_name' => $post_data->post_title,
						'project_type' => $project_type,
						'cust_initial_investment' => get_post_meta( $post_id, $prefix . 'cust_initial_investment', true ),
						'cust_referral' => get_post_meta( $post_id, $prefix . 'cust_referral', true ),
						'cust_contact_date' => get_post_meta( $post_id, $prefix . 'cust_contact_date', true ),
						'contact_type' => get_post_meta( $post_id, $prefix . 'contact_type', true ),
						'company_role' => get_post_meta( $post_id, $prefix . 'company_role', true ),
						'cust_email' => get_post_meta( $post_id, $prefix . 'cust_email', true ),
						'contact_number' => get_post_meta( $post_id, $prefix . 'contact_number', true ),
						'joined_company' => $joined_company,
						'joined_project' => $joined_project,

					);
					$exports .= dx_crm_add_to_export( $customer );
				}
				
			}
			$file_name = 'dx-crm-customers-';
		}
		
		// Export CSV of Projects
		elseif( $crm_post_type == DX_CRM_POST_TYPE_PROJECTS ){
			
			global $current_user;

			// Creating meta query for get_posts
			// this is getting posts(projects) in which current user is involved
			$meta_query = ( in_array( DX_CRM_CUSTOMER_ROLE, $current_user->roles) ) ? array(
				'key' => $prefix . 'joined_pro_customer',
				'value' => get_user_meta( $current_user->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', true ),
				'compare' => 'IN',
			) : array();

			// Arguments for get_posts
			$args = array(
				'post_type'   => $crm_post_type,
				'posts_per_page' => -1,
				'meta_query' => array( $meta_query ),
			);
							
			$posts_data = get_posts( $args );
			$exports = '';
			$columns = array(	
								__( 'Project Name', 'dxcrm' ),
								__( 'Company', 'dxcrm' ),
								__( 'Start Date', 'dxcrm' ),
								__( 'Planned End Date', 'dxcrm' ),
								__( 'Ongoing', 'dxcrm' ),
								__( 'Real End Date for first milestone	', 'dxcrm' ),
								__( 'Real End Date for last conversation', 'dxcrm' ),
								__( 'Agreed Cost', 'dxcrm' ),
								__( 'Currency', 'dxcrm' ),
								__( 'Project status', 'dxcrm' ),
								__( 'Total Paid', 'dxcrm' ),
								__( 'Responsible person', 'dxcrm' ),
								__( 'Customers', 'dxcrm' ),
						     );
					
	        // Put the name of all fields
			$exports .= dx_crm_add_to_export( $columns );
			
			if( !empty( $posts_data ) ) { 
				foreach ( $posts_data as $post_data  ) {
					$post_id = $post_data->ID;
					$custom = get_post_custom( $post_id );
					$project_company = get_post_meta( $post_id, $prefix . 'company_project', true );
					$project_company = get_the_title( $project_company );
					$project_joined_pro_customer = get_post_meta( $post_id, $prefix . 'joined_pro_customer', false );
					$project_joined_pro_customer = dx_crm_get_names( $project_joined_pro_customer );

					$project = array(
						'project_name' => $post_data->post_title,
						'project_company' => $project_company,
						'project_start_date' => get_post_meta( $post_id, $prefix  . 'pro_start_date', true ),
						'project_end_date' => get_post_meta( $post_id, $prefix . 'pro_end_date', true ),
						'project_ongoing' => get_post_meta( $post_id, $prefix . 'pro_ongoing', true ),
						'project_real_end_date_first_mile' => get_post_meta( $post_id, $prefix . 'pro_real_end_date_first_mile', true ),
						'project_real_end_date_last_conversation' => get_post_meta( $post_id, $prefix . 'pro_real_end_date_last_conversation', true ),
						'project_agreed_cost' => get_post_meta( $post_id, $prefix . 'pro_agreed_cost', true ),
						'project_currency' => get_post_meta( $post_id, $prefix . 'project_currency', true ),
						'project_status' => dxcrm_project_status_conversion( get_post_meta( $post_id, $prefix . 'project_status', true ) ),
						'project_total' => get_post_meta( $post_id, $prefix . 'project_total', true ),
						'project_assigned_by' => get_post_meta( $post_id, $prefix . 'project_assigned_by', true ),
						'project_joined_pro_customer' => $project_joined_pro_customer,
					);
					$exports .= dx_crm_add_to_export( $project );	
				}
				
			}
			$file_name = 'dx-crm-projects-';
		}
		
		elseif( $crm_post_type == DX_CRM_POST_TYPE_COMPANY_EXPENSES ){
			
			$args = array(
							'post_type'   => $crm_post_type,
							'posts_per_page' => -1,
							);
							
			$posts_data = get_posts( $args );
			
			
			$exports = '';
			
			$columns = array(	
								__( 'Company Expenses Name', 'dxcrm' ),
								__( 'Expenses Date', 'dxcrm' ),
								__( 'Expenses Name', 'dxcrm' ),
								__( 'Expenses Category', 'dxcrm' ),
								__( 'Expenses Description', 'dxcrm' ),
								__( 'Expenses Cost', 'dxcrm' ),
						     );
					
	        // Put the name of all fields
			$exports .= dx_crm_add_to_export( $columns );
			
			if( !empty( $posts_data ) ) { 
	
				foreach ( $posts_data as $post_data  ) { 
					$post_id 				= $post_data->ID;
					$post_name 				= $post_data->post_title;
					$expenses_date			= get_post_meta( $post_id, $prefix . 'comp_date', true );					
					$expenses_name			= get_post_meta( $post_id, $prefix . 'comp_name', true );
					$expenses_category		= get_post_meta( $post_id, $prefix . 'comp_category', true );
					$expenses_description	= get_post_meta( $post_id, $prefix . 'comp_description', true );
					$expenses_cost			= get_post_meta( $post_id, $prefix . 'comp_cost', true );
															
					//$expenses_date 	= !empty( $expenses_date ) ? date( 'd-m-Y', $expenses_date ) : '';
					$expenses_date 	= !empty( $expenses_date ) ? date( 'd-m-Y', strtotime(str_replace('-','/', $expenses_date))) : '';
					
					//this line should be on start of loop
					$exports .= '"'.$post_name.'",';					
					$exports .= '"'.$expenses_date.'",';
					$exports .= '"'.$expenses_name.'",';
					$exports .= '"'.$expenses_category.'",';
					$exports .= '"'.$expenses_description.'",';
					$exports .= '"'.$expenses_cost.'",';
					
					$exports .="\n";
					
				}
				
			}
			$file_name = 'dx-crm-company-expenses-';

		} elseif( $crm_post_type == DX_CRM_POST_TYPE_COMPANY ){

			global $current_user;

			// Creating meta query for get_posts
			// this is getting posts(companies) in which current user is involved
			$meta_query = ( in_array( DX_CRM_CUSTOMER_ROLE, $current_user->roles) ) ? array(
				'key' => $prefix . 'joined_customer',
				'value' => get_user_meta( $current_user->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', true ),
				'compare' => 'IN',
			) : array();

			$args = array(
				'post_type'   => $crm_post_type,
				'posts_per_page' => -1,
				'meta_query' => array( $meta_query ),
			);
							
			$posts_data = get_posts( $args );
			$exports = '';
			$columns = array(	
				__( 'Company Name', 'dxcrm' ),
				__( 'Company Responsible Person', 'dxcrm' ),
				__( 'Company Type', 'dxcrm' ),
				__( 'Company Industry', 'dxcrm' ),
				__( 'Company Employees', 'dxcrm' ),
				__( 'Company Annual Income', 'dxcrm' ),
				__( 'Company Currency', 'dxcrm' ),
				__( 'Company Company URL', 'dxcrm' ),
				__( 'Company Customers', 'dxcrm' ),
		     );

	        // Put the name of all fields
			$exports .= dx_crm_add_to_export( $columns );
			
			if( !empty( $posts_data ) ) { 

				$company_employees_values = array(
					'EMPLOYEES_1'	=> __('less than 50', 'dxcrm'),
					'EMPLOYEES_2'	=> __('50 - 250'	, 'dxcrm'),
					'EMPLOYEES_3'	=> __('250 - 500'	, 'dxcrm'),
					'EMPLOYEES_4'	=> __('over 500'	, 'dxcrm')
				);
	
				foreach ( $posts_data as $post_data  ) {
					$post_id = $post_data->ID;
					$company_employees = get_post_meta($post_id, $prefix. 'company_employees', true);
					if( ! empty( $company_employees ) && array_key_exists( $company_employees, $company_employees_values ) ) {
						$company_employees = $company_employees_values[$company_employees];
					}
					$company_customers = get_post_meta($post_id, $prefix. 'joined_customer', false);
					$company_customers = dx_crm_get_names( $company_customers );

					$company = array(
						'company_name' => $post_data->post_title,
						'company_responsible_person' => get_post_meta($post_id, $prefix. 'company_assigned_by', true),
						'company_type' => get_post_meta($post_id, $prefix. 'company_type', true),
						'company_industry' => get_post_meta($post_id, $prefix. 'company_industry', true),
						'company_employees' => $company_employees,
						'company_annual_income' => get_post_meta($post_id, $prefix. 'company_annual_income', true),
						'company_currency' => get_post_meta($post_id, $prefix. 'company_currency', true),
						'company_url' => get_post_meta($post_id, $prefix. 'company_url', true),
						'company_customers' => $company_customers,

					);
					$exports .= dx_crm_add_to_export( $company );
				}
				
			}
			$file_name = 'dx-crm-company-';
		}
		
		$crm_file_name = $file_name.date('d-m-Y');
		
		// Output to browser with appropriate mime type, you choose ;)
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=".$crm_file_name.".csv");
		echo $exports;
		exit;
		
	}
}
add_action( 'admin_init','dx_crm_export_to_csv' );

function dxcrm_project_status_conversion( $status ){
	switch( $status ){
		case '0':
			return __( 'Draft' , 'dxcrm' );
		break;
		case '1':
			return __( 'Sent to client' , 'dxcrm' );
		break;
		case '2':
			return __( 'Being reviewed by client' , 'dxcrm' );
		break;
		case '3':
			return __( 'Approved' , 'dxcrm' );
		break;
		case '4':
			return __( 'No response' , 'dxcrm' );
		break;
		case '5':
			return __( 'Declined' , 'dxcrm' );
		break;
		case '6':
			return __( 'In Development' , 'dxcrm' );
		break;
		case '7':
			return __( 'Awaiting Review' , 'dxcrm' );
		break;
		case '8':
			return __( 'Successfully Completed' , 'dxcrm' );
		break;
	}
}

/**
 *
 * Add to the export output
 *
*/
function dx_crm_add_to_export( $values ) {
	$exports='';
	foreach ($values as $value) {
		$exports .= '"'.$value.'",';
	}
	$exports .="\n";
	return $exports;
}

/**
 *
 * Return customer names
 *
*/
function dx_crm_get_names( $ids ) {
	$values = array();
	if( ! empty( $ids) ) {
		foreach ( $ids as $id ) {
			$name = get_the_title( $id );
			array_push($values, $name);
		}		
	}
	return implode(', ', $values);
}

/**
 *
 * Display error message if no query found
 *
*/
function dx_crm_rprt_err_ntc(){
	 echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'No report matched your criteria. Please try again!', 'dxcrm' ) . '</p></div>';
}

/**
 *
 * Display info if query found
 *
*/
function dx_crm_rprt_sccss_ntc(){
	echo '<div class="updated notice is-dismissable"><p>' . __( 'Report successfully generated!', 'dxcrm' ) . '</p></div>';
}
?>