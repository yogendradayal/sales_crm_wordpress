<?php
/**
 * Public Class
 *
 * Handles all admin functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Public Class
 *
 * Handles all public functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */
class Dx_Crm_Public {
	
	public $model, $admin;
	
	function __construct() {
		
		global $dx_crm_model, $dx_crm_admin;		
		$this->model = $dx_crm_model;		
		$this->admin = $dx_crm_admin;
	}
	
	/**
	 * Save customer data entered on Customer custom page template in front-end
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_save_customer( $data ) {
		/**
		 * Put the nta prefix on variable from constant.
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		$prefix = DX_CRM_META_PREFIX;	
		
		/**
		 * Proceed only if add_customer is not empty
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		if( isset( $data[$prefix.'add_cust'] ) && !empty( $data[$prefix.'add_cust'] ) ) {
			$post_title 			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_name'] ) ? $data[$prefix.'cust_name'] : '' ) );
			$post_content 			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_desc'] ) ? $data[$prefix.'cust_desc'] : '' ) );
			$cust_skills			= isset( $data[$prefix.'cust_skills'] ) ? $data[$prefix.'cust_skills'] : array();
			$cust_assign_customer	= isset( $data[$prefix.'cust_assign_customer'] ) ? $data[$prefix.'cust_assign_customer'] : array();
			$cust_first_pro_type 	= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_first_pro_type'] ) ? $data[$prefix.'cust_first_pro_type'] : '' ) );
			$cust_initial_investment= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_initial_investment'] ) ? $data[$prefix.'cust_initial_investment'] : '' ) );
			$cust_referral 			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_referral'] ) ? $data[$prefix.'cust_referral'] : '' ) );
			$cust_contact_date 		= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_contact_date'] ) ? $data[$prefix.'cust_contact_date'] : '' ) );
			//$contact_type 			= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'contact_type'] );
			//$company_role 			= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'company_role'] );
			$cust_bank_info 		= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_bank_info'] ) ? $data[$prefix.'cust_bank_info'] : '' ) );
			$cust_vat_number 		= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_vat_number'] ) ? $data[$prefix.'cust_vat_number'] : '' ) );
			$cust_country 			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'cust_country'] ) ? $data[$prefix.'cust_country'] : '' ) );
			$add_cust 				= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'add_cust'] ) ? $data[$prefix.'add_cust'] : '' ) );
			
			/**
			 * Prepare customer argument for wp_insert_post later
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			$post_arr = array(
				'post_title'    => $post_title,
				'post_content'  => $post_content,
				'post_status'   => 'publish',
				'post_author'   => 1,							  
				'post_type'     => DX_CRM_POST_TYPE_CUSTOMERS,
			);			
			
			/**
			 * Check if user assign to someone else or not.
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			$valid_user = $this->dxcrm_check_user_public( $cust_assign_customer );	

			if( ! empty( $valid_user ) ) {
				$user_data = get_user_by( 'id', $valid_user );
				return new WP_Error( 'duplicate_user', __( 'Sorry ' . $user_data->display_name . ' user already assign to some.', 'dxcrm' ) );
			}
			
			/**
			 * Do the insertion.
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			$post_id = wp_insert_post( $post_arr );
			
			/**
			 * Update meta data based on return ID from wp_insert_post
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			if( ! empty( $post_id ) ) {
				update_post_meta( $post_id, $prefix.'cust_first_pro_type', $cust_first_pro_type );
				update_post_meta( $post_id, $prefix.'cust_initial_investment', $cust_initial_investment );
				update_post_meta( $post_id, $prefix.'cust_referral', $cust_referral );
				update_post_meta( $post_id, $prefix.'cust_contact_date', $cust_contact_date );
				//update_post_meta( $post_id, $prefix.'contact_type', $contact_type );
				//update_post_meta( $post_id, $prefix.'company_role', $company_role );
				update_post_meta( $post_id, $prefix.'cust_bank_info', $cust_bank_info );
				update_post_meta( $post_id, $prefix.'cust_vat_number', $cust_vat_number );
				update_post_meta( $post_id, $prefix.'cust_country', $cust_country );
				update_post_meta( $post_id, $prefix.'add_cust', $add_cust );				
				
				wp_set_post_terms( $post_id, $cust_skills, DX_CRM_SKILL_TAXONOMY );
				update_post_meta( $post_id, $prefix.'cust_assign_customer', $cust_assign_customer );
				
				/**
				 * Return success message
				 *
				 * @package CRM System
				 * @since 1.0.0
				 */
				return __( 'New customer has been added!', 'dxcrm' );
			} else {
				
				/**
				 * Fire error using WP_Error class if wp_insert_post
				 * failed to add new customer entry
				 *
				 * @package CRM System
				 * @since 1.0.0
				 */
				return new WP_Error( 'failed_saving', __( 'Failed to add new customer! Please try again.', 'dxcrm' ) );
			}
		}	
	}
	
	/**
	 * Save project data entered on Project custom page template in front-end
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_save_project( $data ){
		/**
		 * Put the nta prefix on variable from constant.
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		$prefix = DX_CRM_META_PREFIX;
		
		/*
		*
		* This code used for validate data and 
		* insert projectpage data at front side
		*
		*/
		if( isset ( $data[ $prefix . 'add_proj' ] ) && ! empty ( $data[ $prefix . 'add_proj' ] ) ) {

			$post_title 				= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_name'] );
			$post_content 				= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_desc'] );			
			$proj_pro_type				= isset( $data[$prefix.'proj_pro_type'] ) ? $data[$prefix.'proj_pro_type'] : array();
			$proj_assign_customer 		= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_assign_customer'] );
			$proj_start_date			= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_start_date'] );
			$proj_planned_end_date		= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_planned_end_date'] );			
			$proj_milestone_end_date	= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_milestone_end_date'] );
			$proj_conversation_end_date = $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_conversation_end_date'] );
			$proj_agreed_cost 			= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'proj_agreed_cost'] );			
			$add_proj 					= $this->model->dx_crm_escape_slashes_deep( $data[$prefix.'add_proj'] );
			
			/**
			 * Prepare project argument for wp_insert_post later
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			$post_arr 		= array(
				'post_title'    => $post_title,
				'post_content'  => $post_content,
				'post_status'   => 'publish',
				'post_author'   => 1,							  
				'post_type'     => DX_CRM_POST_TYPE_PROJECTS,
			);			
			
			/**
			 * Do the insertion.
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			$post_id = wp_insert_post( $post_arr );
			
			/**
			 * Update meta data based on return ID from wp_insert_post
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			if( ! empty( $post_id ) ) {
				update_post_meta( $post_id, $prefix.'pro_assign_customer', $proj_assign_customer );
				update_post_meta( $post_id, $prefix.'pro_start_date', $proj_start_date );
				update_post_meta( $post_id, $prefix.'pro_end_date', $proj_planned_end_date );
				update_post_meta( $post_id, $prefix.'pro_real_end_date_first_mile', $proj_milestone_end_date );
				update_post_meta( $post_id, $prefix.'pro_real_end_date_last_conversation', $proj_conversation_end_date );
				update_post_meta( $post_id, $prefix.'pro_agreed_cost', $proj_agreed_cost );														
				
				wp_set_post_terms( $post_id, $proj_pro_type, DX_CRM_PRO_TAXONOMY ); 
				
				/**
				 * Return success message
				 *
				 * @package CRM System
				 * @since 1.0.0
				 */
				return __( 'New project has been added!', 'dxcrm' );
			} else {
				
				/**
				 * Fire error using WP_Error class if wp_insert_post
				 * failed to add new project entry
				 *
				 * @package CRM System
				 * @since 1.0.0
				 */
				return new WP_Error( 'failed_saving', __( 'Failed to add new project! Please try again.', 'dxcrm' ) );
			}
			
		}

	}

	/**
	 * Save company data entered on Company custom page template in front-end
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_save_company( $data ) {
		
		/**
		 * Prepare prefix set on constant variable
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		$prefix = DX_CRM_META_PREFIX;

		/**
		 * Proceed only if add_company is provided
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( isset( $data[ $prefix . 'add_company'] ) && ! empty ( $data[ $prefix . 'add_company'] ) ){
			
			/**
			 * Proceed only if add_company is provided
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			$post_title 			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_name'] ) ? $data[$prefix.'company_name'] : '' ) );
			$post_content 			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_desc'] ) ? $data[$prefix.'company_desc'] : '' ) );			
			$resp_person			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_responsible_person'] ) ? $data[$prefix.'company_responsible_person'] : '' ) );
			$company_type			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_type'] ) ? $data[$prefix.'company_type'] : '' ) );
			$company_industry		= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_industry'] ) ? $data[$prefix.'company_industry'] : '' ) );
			$company_employees		= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_employees'] ) ? $data[$prefix.'company_employees'] : '' ) );
			$annual_income			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'annual_income'] ) ? $data[$prefix.'annual_income'] : '' ) );
			$company_currency		= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_currency'] ) ? $data[$prefix.'company_currency'] : '' ) );
			$company_url			= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_url'] ) ? $data[$prefix.'company_url'] : '' ) );
			$customer				= $this->model->dx_crm_escape_slashes_deep( ( ! empty ( $data[$prefix.'company_assign_customer'] ) ? $data[$prefix.'company_assign_customer'] : '' ) );
			
			/**
			 * Prepare post agruments for wp_insert_post()
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			$post_arr = array(
				'post_title'    => $post_title,
				'post_content'  => $post_content,
				'post_status'   => 'publish',
				'post_author'   => 1,							  
				'post_type'     => DX_CRM_POST_TYPE_COMPANY,
			);			

			/**
			 * Do the insertion for company post type and get_browser
			 * the ID of new record for post meta updates
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			$post_id = wp_insert_post( $post_arr );
			
			/**
			 * Start save the post metadata if $post_id is not null
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			if( ! empty( $post_id ) ) {				
				update_post_meta( $post_id, $prefix.'company_assigned_by', $resp_person );
				update_post_meta( $post_id, $prefix.'company_type', $company_type );
				update_post_meta( $post_id, $prefix.'company_industry', $company_industry );
				update_post_meta( $post_id, $prefix.'company_employees', $company_employees );
				update_post_meta( $post_id, $prefix.'company_annual_income', $annual_income );
				update_post_meta( $post_id, $prefix.'company_currency', $company_currency );
				update_post_meta( $post_id, $prefix.'company_url', $company_url );
				update_post_meta( $post_id, $prefix.'joined_customer', $customer );
				
				/**
				 * Return success message
				 *
				 * @package CRM System
				 * @since 1.0.0
				 */
				return __( 'New company entry has been added!', 'dxcrm' );
			} else {
				
				/**
				 * Fire error using WP_Error class if wp_insert_post
				 * failed to add new company entry
				 *
				 * @package CRM System
				 * @since 1.0.0
				 */
				return new WP_Error( 'failed_saving', __( 'Failed to add new company! Please try again.', 'dxcrm' ) );
			}
		}
	}
		
	/**
	 * Add admin_notices alike on page template
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_notice_action( $data ){
		echo sprintf( 
			'<p class="crm-error-field">%s</p>',
			$data
		);
	}
	
	/**
	 * Check User
	 *
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dxcrm_check_user_public( $cust_assign_customers ) {
						
		$prefix = DX_CRM_META_PREFIX;			
		
		if( !empty( $cust_assign_customers ) ) {
						
			$args = array(
						//'posts_per_page'=>-1,
						'post_type' => DX_CRM_POST_TYPE_CUSTOMERS,
						'meta_key'  => $prefix . 'cust_assign_customer',
						'meta_query'=>array(
											'relation' => 'AND',
											array (
												'key'     => $prefix . 'cust_assign_customer',
											)
										)
						);
			
			$assign_customer_posts = get_posts( $args );
			
			if( !empty( $assign_customer_posts ) ) {
				foreach ( $assign_customer_posts as $assign_post ) {
					
					$assign_user_mata = get_post_meta( $assign_post->ID, $prefix . 'cust_assign_customer', true );
					$assign_user_mata = (array) $assign_user_mata;
					
					foreach ( $cust_assign_customers as $cust_assign_customer ) {
						if( in_array( $cust_assign_customer, $assign_user_mata ) ) {
							return $cust_assign_customer;
						}
					}
				}
			}
		}
		
	}
	
	/**
	 * Display admin metabox at front side,
	 * For
	 * crm_projects
	 * crm_company
	 * crm_customers
	 * crm_timesheets
	 * crm_milestones
	 * crm_company_expenses Post ype.
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_display_metabox( $content ) {
		
		global $post;
		$prefix = DX_CRM_META_PREFIX;

		//Validate Post Type
		$post_types = array(
			DX_CRM_POST_TYPE_PROJECTS,
			DX_CRM_POST_TYPE_COMPANY,
			DX_CRM_POST_TYPE_CUSTOMERS
		);
		
		$post_type  = get_post_type( $post );
				
		if( !in_array( $post_type, $post_types ) )
			return $content;

		if( current_user_can( DX_CRM_CUSTOMER_ROLE ) || current_user_can( 'administrator' ) ) {
			
			$id				= isset($GLOBALS['post']) ? $GLOBALS['post']->ID : get_the_ID() ;
			$title			= isset($GLOBALS['post']) ? $GLOBALS['post']->post_title : '';
			$post_content 	= isset($GLOBALS['post']) ? $GLOBALS['post']->post_content : '';
			$post_type		= isset($GLOBALS['post']) ? $GLOBALS['post']->post_type : '';
			
			// For display crm_projects post type metaboxes values
			if( !empty( $id ) && $post_type == DX_CRM_POST_TYPE_PROJECTS ) {

				$user_data = get_post_custom( $id );

				if( ! empty( $user_data ) ) {
					
					$company_data = get_posts( array( 'post_type' => DX_CRM_POST_TYPE_COMPANY ) );
					$company_id = isset( $user_data[$prefix.'company_project'][0] ) ? $user_data[$prefix.'company_project'][0] : '';
					
					foreach( $company_data as $key=>$val ) {
						
						if( $val->ID == $company_id ) {
						
							$company_name = $val->post_title;	
						}
					}
					?><table class="form-table">
						<tbody>
							<tr>
								<th width="35%"><?php _e('Project Title', 'dxcrm'); ?></th>
								<td><?php echo $title; ?></td>
							</tr>
							<tr>
								<th><?php _e('Company Name', 'dxcrm'); ?></th>
								<td><?php echo (isset($company_name) && !empty($company_name)) ? $company_name : '<span class="dashicons dashicons-minus"></span>'; ?></td>
							</tr>
							<tr>
								<th><?php _e('Start Date', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Project start date
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'pro_start_date'][0] ) 
											&& ! empty ( $user_data[$prefix.'pro_start_date'][0] ) ){
											echo date( "M d, Y", strtotime( $user_data[$prefix.'pro_start_date'][0] ) );
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Planed End Date', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Project end date
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'pro_end_date'][0] ) 
											&& ! empty ( $user_data[$prefix.'pro_end_date'][0] ) ){
											echo date( "M d, Y", strtotime( $user_data[$prefix.'pro_end_date'][0] ) );
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Ongoing Project', 'dxcrm'); ?></th>
								<td><?php echo (isset($user_data[$prefix.'pro_ongoing'][0]) && !empty($user_data[$prefix.'pro_ongoing'][0])) ? 'Yes' : 'No'; ?></td>
							</tr>
							<tr>
								<th><?php _e('Real End of First Milestone', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Real End of First Milestone
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'pro_real_end_date_first_mile'][0] ) 
											&& ! empty ( $user_data[$prefix.'pro_real_end_date_first_mile'][0] ) ){
											echo date( "M d, Y", strtotime( $user_data[$prefix.'pro_real_end_date_first_mile'][0] ) );
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Real End Date', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Real End Date
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'pro_real_end_date_last_conversation'][0] ) 
											&& ! empty ( $user_data[$prefix.'pro_real_end_date_last_conversation'][0] ) ){
											echo date( "M d, Y", strtotime( $user_data[$prefix.'pro_real_end_date_last_conversation'][0] ) );
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Agreed Cost', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Agreed Cost
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'pro_agreed_cost'][0] ) 
											&& ! empty ( $user_data[$prefix.'pro_agreed_cost'][0] )){
											if( isset ( $user_data[$prefix.'project_currency'][0] ) ){
												// Display currency symbol
												switch( $user_data[$prefix.'project_currency'][0] ){
													case 'USD':
													default:
														echo '$';
													break;
													case 'EUR':
														echo '€';
													break;
												}
											}
											/**
											 * Format the currency
											 * Check data type
											*/
											if( is_double( $user_data[$prefix.'pro_agreed_cost'][0] )
												|| is_integer( $user_data[$prefix.'pro_agreed_cost'][0] )
												){
												echo number_format( $user_data[$prefix.'pro_agreed_cost'][0], 2 );													
											}else{
												echo $user_data[$prefix.'pro_agreed_cost'][0];
											}
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Project Status', 'dxcrm'); ?></th>
								<td>
									<?php
										/**
										 * Display Project Status
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset($user_data[$prefix.'project_status'][0]) ){
											$status = Sales_CRM_Project::display_status_string( $user_data[$prefix.'project_status'][0] );
											if( ! is_wp_error( $status ) ){
												echo $status;
											}else{
												echo $status->get_error_message();
											}
										}else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Project Cost', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Agreed Cost
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'project_total'][0] ) 
											&& ! empty ( $user_data[$prefix.'project_total'][0] )){
											if( isset ( $user_data[$prefix.'project_currency'][0] ) ){
												// Display currency symbol
												switch( $user_data[$prefix.'project_currency'][0] ){
													case 'USD':
													default:
														echo '$';
													break;
													case 'EUR':
														echo '€';
													break;
												}
											}
											/**
											 * Format the currency
											 * Check data type
											*/
											if( is_double( $user_data[$prefix.'project_total'][0] )
												|| is_integer($user_data[$prefix.'project_total'][0] )
												){
												echo number_format( $user_data[$prefix.'project_total'][0], 2 );													
											}else{
												echo $user_data[$prefix.'project_total'][0];
											}
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>						
							<tr>
								<th><?php _e('Project Responsible person', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Real End Date
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'project_assigned_by'][0] ) 
											&& ! empty ( $user_data[$prefix.'project_assigned_by'][0] ) ){
											echo $user_data[$prefix.'project_assigned_by'][0];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Customers', 'dxcrm'); ?></th>
								<td>
								<?php
									/**
									 * Display Project Customer
									 *
									*/
									if( ! empty ( $user_data[$prefix.'joined_pro_customer']   ) ){
										$customers = get_posts( array( 
											'post_type' => DX_CRM_POST_TYPE_CUSTOMERS, 
											'include' => $user_data[$prefix.'joined_pro_customer']  
										) );										
										$customers_count = count( $customers );
										foreach ( $customers as $customer ) {
											echo ( --$customers_count > 0 ) ? $customer->post_title . ', ' : $customer->post_title;
										}
									} else{
										echo '<span class="dashicons dashicons-minus"></span>';
									}
								?>
								</td>
							</tr>
						</tbody>		
					</table><?php
				}
				
			} // End for crm_projects post type
			
			/**
			 * For display crm_company post type metaboxes values
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			if( ! empty( $id ) && $post_type == DX_CRM_POST_TYPE_COMPANY ) {
				
				/**
				 * Get custom post by ID
				 *
				 * @package CRM System
				 * @since 1.0.0
				*/
				$user_data = get_post_custom( $id );
			
				if( ! empty( $user_data ) ) {
					
					/**
					 * Get default Company Type List using filter
					 *
					 * @package CRM System
					 * @since 1.0.0
					 */
					$company_type = apply_filters( 'dx_crm_company_type', array() );
					
					/**
					 * Get default Company Employee size using filter
					 *
					 * @package CRM System
					 * @since 1.0.0
					*/					
					$company_employees = apply_filters( 'dx_crm_company_employees', array() );
					
					/**
					 * Get default Company Industry List using filter
					 *
					 * @package CRM System
					 * @since 1.0.0
					 */
					$company_industry = apply_filters( 'dx_crm_company_industry', array() );
					
					/**
					 * Display company data
					 *
					 * @package CRM System
					 * @since 1.0.0
					 *
					 * Check if company logo index is set first to avoid PHP warning
					 *
					 * @package CRM System
					 * @since 1.0.0
					*/
					$logo_url = isset( $user_data[$prefix.'company_logo'][0] ) ? unserialize( $user_data[$prefix.'company_logo'][0] ) : '' ;
					?>
					<table class="form-table">
						<tbody>
							<?php
								/**
								 * Display company logo if not empty
								 *
								 * @package CRM System
								 * @since 1.0.0
								*/
								if( ! empty ( $logo_url ) ){
							?>
							<tr>
								<td colspan="2">
								<?php 
									$img='<img class="crm_center_company_logo" src="'. $logo_url['src'] .'" style="margin: 0 auto; display: block;" alt="logo">';
									echo ( isset( $user_data[$prefix.'company_logo'][0] ) && ! empty ( $user_data[ $prefix.'company_logo' ][0] ) ) ? $img : '<span class="dashicons dashicons-minus"></span>'; 
								?>
								</td>
							</tr>
							<?php
								}
							?>
							<tr>
								<th width="35%"><?php _e('Company Name', 'dxcrm'); ?></th>
								<td><?php echo $title; ?></td>
							</tr>							
							<tr>
								<th><?php _e('Responsible Person', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Company assignment
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'company_assigned_by'][0] ) 
											&& ! empty ( $user_data[$prefix.'company_assigned_by'][0] ) ){
											echo $user_data[$prefix.'company_assigned_by'][0];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Company Type', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Company type
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'company_type'][0] ) 
											&& ! empty ( $user_data[$prefix.'company_type'][0] ) ){
											echo ucfirst( strtolower( $user_data[$prefix.'company_type'][0] ) );
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Industry', 'dxcrm'); ?></th>								
								<td>
									<?php 
										/**
										 * Display Company Industry
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'company_industry'][0] ) 
											&& ! empty ( $user_data[$prefix.'company_industry'][0] )
											&& isset ( $company_industry[$user_data[$prefix.'company_industry'][0]] )
											&& ! empty ( $company_industry[$user_data[$prefix.'company_industry'][0]] )	){
											echo $company_industry[$user_data[$prefix.'company_industry'][0]];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('No. Of Employees', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Company Employee total
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'company_employees'][0] ) 
											&& ! empty ( $user_data[$prefix.'company_employees'][0] )
											&& isset ( $company_employees[$user_data[$prefix.'company_employees'][0]] )
											&& ! empty ( $company_employees[$user_data[$prefix.'company_employees'][0]] )	){
											echo $company_employees[$user_data[$prefix.'company_employees'][0]];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Annual Income', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Annual Income
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'company_annual_income'][0] ) 
											&& ! empty ( $user_data[$prefix.'company_annual_income'][0] )){
											if( isset ( $user_data[$prefix.'company_currency'][0] ) ){
												// Display currency symbol
												switch( $user_data[$prefix.'company_currency'][0] ){
													case 'USD':
													default:
														echo '$';
													break;
													case 'EUR':
														echo '€';
													break;
												}
											}
											/**
											 * Format the currency
											 * Check data type
											*/
											if( is_double( $user_data[$prefix.'company_annual_income'][0] )
												|| is_integer( $user_data[$prefix.'company_annual_income'][0] )
												){
												echo number_format( $user_data[$prefix.'company_annual_income'][0], 2 );													
											}else{
												echo $user_data[$prefix.'company_annual_income'][0];
											}
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Company URL', 'dxcrm'); ?></th>
								<td>
								<?php 
									/**
									 * Display Company URL
									 *
									 * @package CRM System
									 * @since 1.0.0
									*/
									$url = (isset($user_data[$prefix.'company_url'][0])) ? $user_data[$prefix.'company_url'][0] : '';
									if (isset($url) && !empty($url)) {
									    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
									        $url = "http://" . $url;
									    }

										echo "<a href=\"$url\">$url</a>";
									} else {
										echo '<span class="dashicons dashicons-minus"></span>';
									}
								?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Customer', 'dxcrm'); ?></th>
								<td>
									<?php 
									/**
									 * Display Company Customer
									 *
									*/
									if( ! empty ( $user_data[$prefix.'joined_customer'] ) ){
										$customers = get_posts( array( 

											'post_type' => DX_CRM_POST_TYPE_CUSTOMERS, 
											'include' => $user_data[$prefix.'joined_customer']  
										) );
										
										$customers_count = count( $customers );

										foreach ( $customers as $customer ) {
											echo ( --$customers_count > 0 ) ? $customer->post_title . ', ' : $customer->post_title;
										}
									} else {
										echo '<span class="dashicons dashicons-minus"></span>';
									}
									?>
									
								</td>
							</tr>
							<?php
							/**
							 * Action for display country from external country plugin
							 *
							 * @package CRM System
							 * @since 1.0.0
							 */
							do_action( 'dx_crm_get_country_details', $user_data, DX_CRM_POST_TYPE_COMPANY );
							?>
						</tbody>
					</table>
					<?php					
				}
				
			} // End for crm_company post type
			
			// For display crm_customers post type metaboxes values
			if( ! empty( $id ) && $post_type == DX_CRM_POST_TYPE_CUSTOMERS ) {
				
				$user_data = get_post_custom( $id );
				
				if( ! empty( $user_data ) ) {
					
					$pro_cats		= get_terms( DX_CRM_PRO_TAXONOMY, array( 'hide_empty' => 0 ) );
					$pro_type_id	= isset( $user_data[$prefix.'cust_first_pro_type'][0]) ? $user_data[$prefix.'cust_first_pro_type'][0] : '';
					
					foreach( $pro_cats as $pro_cat ) {
						
						if( $pro_cat->term_id == $pro_type_id ) {
						
							$pro_type_name = $pro_cat->name;
						}
					}
					$contact_type_list = apply_filters( 'dx_crm_contact_type', array() );
					?><table class="form-table">
						<tbody>
							<tr>
								<th width="30%"><?php _e('Client Name', 'dxcrm'); ?></th>
								<td>
									<?php 
										if( ! empty ( $title ) ){
											echo $title; 
										} else {
											echo '<span class="dashicons dashicons-minus"></span>';
										}
									?>
								</td>
							</tr>							
							<tr>
								<th><?php _e('Project Type', 'dxcrm'); ?></th>
								<td><?php echo (isset($pro_type_name) && !empty($pro_type_name)) ? $pro_type_name : '<span class="dashicons dashicons-minus"></span>'; ?></td>
							</tr>
							<tr>
								<th><?php _e('Initial Investment', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Initial investment
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'cust_initial_investment'][0] ) 
											&& ! empty ( $user_data[$prefix.'cust_initial_investment'][0] ) ){
											$initial_investment = $user_data[$prefix.'cust_initial_investment'][0];
											/**
											 * Format the currency
											 * Check data type
											*/
											if( is_double( $initial_investment )
												|| is_integer( $initial_investment )
												){
												echo number_format( $initial_investment, 2 );													
											}else{
												echo $initial_investment;
											}
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Referral', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Referral
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'cust_referral'][0] ) 
											&& ! empty ( $user_data[$prefix.'cust_referral'][0] ) ){
											echo $user_data[$prefix.'cust_referral'][0];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>							
							</tr>
							<tr>
								<th><?php _e('Contact Date', 'dxcrm'); ?></th>
								<td><?php echo (isset($user_data[$prefix.'cust_contact_date'][0]) && !empty($user_data[$prefix.'cust_contact_date'][0])) ? $user_data[$prefix.'cust_contact_date'][0] : '<span class="dashicons dashicons-minus"></span>'; ?></td>
							</tr>
							<tr>
								<th><?php _e('Contact Type', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Customer Contact Type
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'contact_type'][0] ) 
											&& ! empty ( $user_data[$prefix.'contact_type'][0] )
											&& isset ( $contact_type_list[$user_data[$prefix.'contact_type'][0]] )
											&& ! empty ( $contact_type_list[$user_data[$prefix.'contact_type'][0]] )	){
											echo $contact_type_list[$user_data[$prefix.'contact_type'][0]];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>							
							</tr>
							<tr>
								<th><?php _e('Company Role', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Company Role
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'company_role'][0] ) 
											&& ! empty ( $user_data[$prefix.'company_role'][0] ) ){
											echo $user_data[$prefix.'company_role'][0];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>							
							</tr>
							<tr>
								<th><?php _e('Email', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Email
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'cust_email'][0] ) 
											&& ! empty ($user_data[$prefix.'cust_email'][0]) ){
											echo $user_data[$prefix.'cust_email'][0];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Phone Number', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Phone Number
										 *
										 * @package CRM System
										 * @since 1.0.0
										*/
										if( isset( $user_data[$prefix.'contact_number'][0] ) 
											&& ! empty ($user_data[$prefix.'contact_number'][0]) ){
											echo $user_data[$prefix.'contact_number'][0];
										} else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}  
									?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Customer Skills', 'dxcrm'); ?></th>
								<td>
								<?php 
									/**
									 * Display Customer Skills
									 *
									 * @package CRM System
									 * @since 1.0.0
									*/
									$skills = wp_get_post_terms( $id, DX_CRM_SKILL_TAXONOMY );
									$skill	= '';									
									if( ! empty ( $skills ) && ! is_wp_error( $skills ) ){
										$total_skills 	= count ( $skills );
										$i 				= 0;
										foreach ( $skills as $skill ) {
											if ( ! empty ( $skill->slug ) ){
												if( $i == $total_skills - 1 ){
													printf( '<span class="customer-skills">%s<span>', $skill->name );
												}else{
													printf( '<span class="customer-skills">%s,<span>', $skill->name );
												}
											}
											$i++;
										}
									} else {
										if( is_wp_error( $skills ) ){
											printf( '<span class="error">%s<span>', $skills->get_error_message() );
										}else{
											echo '<span class="dashicons dashicons-minus"></span>';
										}
									}
								?>
								</td>
							</tr>
							<tr>
								<th><?php _e('Project', 'dxcrm'); ?></th>
								<td>
									<?php 
										/**
										 * Display Projects
										 *
										*/
										if( ! empty ( $user_data[$prefix.'joined_project'] ) ){
											$projects = get_posts( array( 

												'post_type' => DX_CRM_POST_TYPE_PROJECTS, 
												'include' => $user_data[$prefix.'joined_project']  
											) );
											
											$projects_count = count( $projects );

											foreach ( $projects as $project ) {
												echo ( --$projects_count > 0 ) ? $project->post_title . ', ' : $project->post_title;
											}
										} else {
											echo '<span class="dashicons dashicons-minus"></span>';
										}
									?>
								</td>
							</tr>
							<?php
							
							//Action for display country from external country plugin
							do_action( 'dx_crm_get_country_details', $user_data, DX_CRM_POST_TYPE_CUSTOMERS );
							
							//Action for display customer invoice details
							do_action( 'dx_crm_customer_invoice_details', $user_data );
							
						?></tbody>
					</table><?php
					
				}
				
			} // End for crm_customers post type
			
		} 
		
	}
	/* Redirect 404 page */
	function redirect_404() {
		
		$post_type	= isset( $GLOBALS['post'] ) ? $GLOBALS['post']->post_type : '';
		
		$prefix = DX_CRM_META_PREFIX;		
		if( ! current_user_can( 'administrator' ) ) {
			// For redirect when user is not admin and post types are  projects, company, or clients
			if( ! empty( $post_type ) && ( $post_type !== DX_CRM_POST_TYPE_PROJECTS || $post_type !== DX_CRM_POST_TYPE_COMPANY || $post_type !== DX_CRM_POST_TYPE_CUSTOMERS ) ) {
				//echo '<script> window.location = "'.home_url('404.php').'"; </script>';
				//wp_redirect( home_url( '404.php' ) );				
			}
		}
	}
		
	/**
	 * Adding Hooks
	 *
	 * Adding proper hooks for the admin class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function add_hooks() {

	  	//filter for adding metaboxes values at front side pages
	  	add_filter( 'the_content', array( $this, 'dx_crm_display_metabox' ) );
	  	add_action( 'wp', array( $this, 'redirect_404'), 10  );
		
		// Add action for notices on page template
		add_action( 'dx_crm_notice', array( $this, 'dx_crm_notice_action' ), 10, 1 );
	}

}