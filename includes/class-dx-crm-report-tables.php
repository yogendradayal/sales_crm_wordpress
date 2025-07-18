<?php
/**
 * Report Table Class
 *
 * Handles report table actions functionality.
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Dx_Crm_Report_Tables{

	function __construct(){

	}
	
	public function add_hooks() {
	  	
		// Scripts. Load only on DX CRM->Report
		if( ! empty ( $_GET['page'] ) && $_GET['page'] == 'dx-crm-stat-setting' ){			
			add_action('admin_enqueue_scripts', array( $this, 'dx_crm_report_script'));			
		}	
		
		//Action for report query checking using ajax
		add_action( 'wp_ajax_ajaxcrm_chckrprtquery', array($this, 'ajaxcrm_chckrprtquery'));
		add_action( 'wp_ajax_nopriv_ajaxcrm_chckrprtquery', array($this, 'ajaxcrm_chckrprtquery'));
		
	  	// action to copy after saved post into report tables
	  	add_action( 'wp_insert_post', array( $this, 'crm_copy_record'), 10, 3  );
		
		// add action to move post into trash 
		add_action( 'wp_trash_post', array( $this, 'crm_trash_record')  );
		
		// add action to restore post to publish
		add_action( 'untrash_post', array( $this, 'crm_restore_record')  );
		
		// add action to permanently delete post
		// use delete_post hook for wp pre deletion
		// @https://codex.wordpress.org/Plugin_API/Action_Reference/delete_post
		add_action( 'delete_post', array( $this, 'crm_delete_record')  );
				
	}
	
	/**
	 *
	 * Report Section script and css
	 *
	*/
	function dx_crm_report_script(){
		
		//wp_register_style( 'datepicker', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' ); 
		//wp_enqueue_style( 'datepicker' );

		wp_register_style( 'dx-crm-report', DX_CRM_ASSETS_URL.'/css/min/dx-crm-report.min.css' ); 
		wp_register_style( 'chosen', DX_CRM_ASSETS_URL . '/css/min/chosen.min.css' ); 
		wp_enqueue_style( 'dx-crm-report' );
		wp_enqueue_style( 'chosen' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'chosen', DX_CRM_ASSETS_URL . '/scripts/min/chosen.jquery.min.js', array( 'jquery' ), false, true );  
		wp_enqueue_script( 'jquery-ui-datepicker' );  
		wp_enqueue_script( 'dx-crm-report', DX_CRM_ASSETS_URL . '/scripts/min/dx-crm-report.min.js', array(), false, true ); 
		
		//wp_enqueue_script( 'dx-crm-admin-script' );
		/* wp_localize_script( 'dx-crm-admin-script', 
							'CrmSystem', 
								array(
									'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) )
									)); */
	}
	
	/**
	 *
	 * Save/Update data copy on report tables
	 *
	 * On post editing/saving, check if post types
	 * Company, Customer, Project or Campaign
	 * Save the saved data from post table into respective table
	 * based on post type.
	 *
	 * This function is triggered right once a post has been saved.
	 * thus, get_{hook}_{function} will work normally as same inside the loop
	 * @https://codex.wordpress.org/Plugin_API/Action_Reference/wp_insert_post
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 *
	*/

	function crm_copy_record( $post_id, $post, $update ) {

		// Publish post only
		if ( wp_is_post_revision( $post_id ) ){ return; }	
		
		global $wpdb;
		global $dx_crm_model;
		
		$project_tbl = $wpdb->prefix . 'crm_project';
		$customer_tbl = $wpdb->prefix . 'crm_customer';
		$company_tbl = $wpdb->prefix . 'crm_company';
		$campaign_tbl  = $wpdb->prefix . 'crm_campaign';
		
		/**
		 *
		 * If customer post type
		 *
		*/
		if( $post->post_type == DX_CRM_POST_TYPE_CUSTOMERS ){	
			
			/**
			 *
			 * Get Total Project and Total Paid
			 *
			*/
			$total_paid = '0';
			$total_project = '0.00';
			$cs_tppd = $dx_crm_model->crm_get_customer_details_custom();
			
			if ( !empty( $cs_tppd ) && is_array( $cs_tppd ) ) {
				
				foreach( $cs_tppd as $c_tppd ){	
					
					if( $c_tppd['ID'] == $post_id ) {
						$total_paid = (int) $c_tppd['total_paid'];
						$total_project = (int) $c_tppd['total_project'];
					}
					
				}
				
			}
			
			/**
			 *
			 * Get SKILLS name by ID
			 *
			*/
			$skills_raw	= wp_get_post_terms( $post_id, DX_CRM_SKILL_TAXONOMY );
			$skill_ids = array();
			
			if ( !empty( $skills_raw ) ){
				
				$skills_arr = array();
				
				foreach( $skills_raw as $value ){
					$skill_ids[] = $value->term_id;
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
			$c_fpt_id = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_first_pro_type', true );	
			if( !empty( $c_fpt_id ) ){
				$c_fpt_terms = get_term( $c_fpt_id );
				
				if( !empty( $c_fpt_terms->term_id ) ){
					$c_fpt = $c_fpt_terms->term_id;
				} else {
					$c_fpt = '';
				}
			}else{
				$c_fpt = '';
			}	
			
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
			 * Get joined COMPANY Name by ID
			 *
			*/
			$c_company_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_company' );
			
			if( !empty ( $c_company_ids ) ){
				
				$c_cmpnys = array();
				
				foreach( $c_company_ids as $value ){
					
					// check if valid
					$title = get_the_title( $value );
					if( !empty( $title ) ){
						$c_cmpnys[] = $title;
					}
					
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
			 * Get joined PROJECT Name by ID
			 *
			*/
			$projects_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_project' );
			if( !empty ( $projects_raw ) ){
				
				$projects = array();
				
				foreach( $projects_raw as $value ) {
					
					// check if valid
					$title = get_the_title( $value );
					if( !empty( $title ) ){
						$projects[] = $title;
					}
					
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
			 * Get joined CAMPAIGNS Name by ID
			 *
			*/
			$c_cmpgns_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_campaigns' );
			
			if( !empty ( $c_cmpgns_ids ) ){
				
				$c_cmpgns = array();
				
				foreach( $c_cmpgns_ids as $value ){
					
					// check if valid
					$title = get_the_title( $value );
					if( !empty( $title ) ){
						$c_cmpgns[] = $title;
					}
					
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
			 * INSERT into custom DB. USE $wpdb->replace
			 * $wpdb->replace DEF: Replace a row in a table if 
			 * it exists or insert a new row in a table if the row 
			 * did not already exist.
			 *		
			*/
			if ( ! empty( $post->post_title ) ) {
				$wpdb->replace( 
							$customer_tbl,
							array( 
								'ID' 						=> $post->ID,
								'company_rltn_ID' 			=> serialize( $c_company_ids ),						
								'project_rltn_ID' 			=> serialize( $projects_raw ),
								'campaign_rltn_ID' 			=> serialize( $c_cmpgns_ids ),
								'skills_rltn_ID' 			=> serialize( $skill_ids ),
								'cust_name' 				=> $post->post_title,
								'cust_desc' 				=> $post->post_content,
								'post_status' 				=> $post->post_status,
								'cust_skills' 				=> $c_skills,
								'cust_project_type' 		=> $c_fpt,
								'cust_initial_investment' 	=> $c_ciivstmnt,
								'cust_referral' 			=> $c_referral,
								'cust_contact_date' 		=> date( "Y-m-d", strtotime( $c_cdate_raw ) ),
								'cust_contact_type' 		=> $c_cntcttype,
								'cust_company_role' 		=> $c_cmpnyrole,
								'cust_email' 				=> $c_email,
								'cust_phone_number' 		=> $c_pnum,
								'cust_companies' 			=> $c_company,
								'cust_projects' 			=> $c_project,
								'cust_campaigns' 			=> $c_cmpgn,
								'cust_bank_info' 			=> $c_bankinfo,
								'cust_vat_number' 			=> $c_vatnum,
								'cust_country' 				=> $c_country,
								'total_project'				=> $total_project,
								'total_paid'				=> $total_paid
							),
							array(
								'%s', 	// ID
								'%s', 	// company_rltn_ID				
								'%s',	// project_rltn_ID
								'%s',	// campaign_rltn_ID
								'%s',	// skills_rltn_ID
								'%s',	// cust_name
								'%s',	// cust_desc
								'%s',	// post status
								'%s',	// cust_skills
								'%s',	// cust_project_type
								'%f',	// cust_initial_investment
								'%s',	// cust_referral
								'%s',	// cust_contact_date
								'%s',	// cust_contact_type 
								'%s',	// cust_company_role
								'%s',	// cust_email
								'%s',	// cust_phone_number
								'%s',	// cust_companies
								'%s',	// cust_projects
								'%s',	// cust_campaigns
								'%s',	// cust_bank_info
								'%s',	// cust_vat_number
								'%s',	// cust_country
								'%d',	// total_project
								'%f'	// total_paid
							)
						);	
					
				$wpdb->flush();
			}
			
			/**
			 *
			 * BUG FIX
			 *
			 * Project report table does not update record
			 * on Project -> Customer relation
			 * on Company -> Customer relation
			 *
			*/
			$this->crm_rprt_blk_update_prjct();	
			$wpdb->flush();
			
			$this->crm_rprt_blk_update_cmpny();
			$wpdb->flush();
			
		}
		
		/**
		 *
		 * If project post type
		 *
		*/
		if( DX_CRM_POST_TYPE_PROJECTS 	== $post->post_type ){		
			
			$post_id 		= $post->ID;
			$post_name 		= $post->post_title;	
			$post_content	= $post->post_content;				
			
			/**
			 *
			 * Prepare raw info from taxonomy table
			 *
			*/
			$project_type = array();
			$project_types 	= wp_get_post_terms( $post_id, DX_CRM_PRO_TAXONOMY );
			
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
			$p_sdate = ( !empty ( $p_sdate_raw ) ) ? date( "Y-m-d", strtotime( $p_sdate_raw ) ) : '' ;
			
			// Planned End Date
			$p_edate_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_end_date', true );		
			$p_edate = ( !empty ( $p_edate_raw ) ) ? date( "Y-m-d", strtotime( $p_edate_raw ) ) : '' ;
			
			// Ongoing
			$p_ongoing_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_ongoing', true );		
			
			// Real End Date for Last Milestone
			$p_redlm_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_real_end_date_first_mile', true );
			$p_redlm = ( !empty ( $p_redlm_raw ) ) ? date( "Y-m-d", strtotime( $p_redlm_raw ) ) : '' ;
			
			// Real End Date for Last Conversation
			$p_redlc_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_real_end_date_last_conversation', true );
			$p_redlc = ( !empty ( $p_redlc_raw ) ) ? date( "Y-m-d", strtotime( $p_redlc_raw ) ) : '' ;
			
			// Currency
			$p_curr = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'project_currency', true );
			
			// Cost
			$p_cost = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_agreed_cost', true );
			
			// Status
			$p_status = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'project_status', true );
			$get_status = Sales_CRM_Project::display_status_string( $p_status );
			if( ! is_wp_error( $get_status ) ){
				$p_status = $get_status;
			}
			
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
			
			/**
			 *
			 * INSERT into custom DB. USE $wpdb->replace
			 * $wpdb->replace DEF: Replace a row in a table if 
			 * it exists or insert a new row in a table if the row 
			 * did not already exist.
			 *		
			*/
			$wpdb->replace( 
						$project_tbl,
						array( 
							'ID' 									=> $post->ID,
							'customer_rltn_ID' 						=> serialize( $p_cstmr_ids ),						
							'company_rltn_ID' 						=> serialize( $p_company_ids ),
							'project_type_rltn_ID' 					=> serialize( $project_types ),
							'project_name' 							=> $post->post_title,
							'project_description' 					=> $post->post_content,
							'post_status' 							=> $post->post_status,
							'project_type' 							=> $type,
							'project_company' 						=> $p_company,
							'project_start_date' 					=> $p_sdate,
							'project_planned_end_date' 				=> $p_edate,
							'project_ongoing' 						=> $p_ongoing_raw,
							'project_end_date_first_milestone' 		=> $p_redlm,
							'project_end_date_last_conversation' 	=> $p_redlc,
							'project_agreed_cost' 					=> $p_cost,
							'project_currency' 						=> $p_curr,
							'project_status' 						=> $p_status,
							'project_total_paid' 					=> $p_total,
							'project_responsible_person' 			=> $p_rprsn,
							'project_customers' 					=> $p_cstmr
						),
						array(
							'%s', 	// ID
							'%s', 	// customer_rltn_ID			
							'%s',	// company_rltn_ID
							'%s',	// project_type_rltn_ID
							'%s',	// project_name
							'%s',	// project_description
							'%s',	// post status
							'%s',	// project_type
							'%s',	// project_company
							'%s',	// project_start_date
							'%s',	// project_planned_end_date
							'%s',	// project_ongoing
							'%s',	// project_end_date_first_milestone
							'%s',	// project_end_date_last_conversation
							'%f',	// project_agreed_cost
							'%s',	// project_currency
							'%s',	// project_status
							'%f',	// project_total_paid
							'%s',	// project_responsible_person
							'%s'	// project_customers
						)
					);	
					
			/**
			 *
			 * BUG: Customer Total Paid and Total Project not updated when generating report
			 *
			 * We need to update Total Paid and Total Project on Customer table
			 * Call crm_rprt_blk_update_cstmr()
			 * 
			*/
			$this->crm_rprt_blk_update_cstmr();			
			
			$wpdb->flush();
			
		}
		
		/**
		 *
		 * If company post type
		 *
		*/
		if( DX_CRM_POST_TYPE_COMPANY == $post->post_type ){		
			$post_id 		= $post->ID;
			$post_name 		= $post->post_title;	
			$post_content	= $post->post_content;	
			
			// Rsponsible Person
			$c_resp = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_assigned_by', true );
			
			// Logo
			$c_logo = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_logo' );
			
			// Company Type
			$c_type = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_type', true );
			
			// Industry
			$c_indstry = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_industry', true );
			
			// Employees
			$c_emplys = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_employees', true );
			
			// Annual Income
			$c_annual_income = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_annual_income', true );
			
			// Currency
			$c_curr = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_currency', true );
			
			// Company URL	
			$c_url = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_url', true );			
			
			/**
			 *
			 * Get Customer.
			 *
			 * Customer is a custom post not meta
			 *
			*/
			$c_cstmr_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_customer' );
			
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

			/**
			 *
			 * INSERT into custom DB. USE $wpdb->replace
			 * $wpdb->replace DEF: Replace a row in a table if 
			 * it exists or insert a new row in a table if the row 
			 * did not already exist.
			 *		
			*/
			$wpdb->replace( 
						$company_tbl,
						array( 
							'ID' 						=> $post->ID,
							'customer_rltn_ID' 			=> serialize( $c_cstmr_ids ),						
							'comp_name' 				=> $post->post_title,
							'comp_description' 			=> $post->post_content,
							'post_status' 				=> $post->post_status,
							'comp_responsible_person' 	=> $c_resp,
							'comp_logo' 				=> serialize( $c_logo ),
							'comp_type' 				=> $c_type,
							'comp_industry' 			=> $c_indstry,
							'comp_employees' 			=> $c_emplys,
							'comp_annual_income' 		=> $c_annual_income,
							'comp_currency' 			=> $c_curr,
							'comp_url' 					=> $c_url,
							'comp_customers' 			=> $c_cstmr
						),
						array(
							'%d', 	// ID
							'%s', 	// customer_rltn_ID			
							'%s',	// comp_name
							'%s',	// comp_description
							'%s',	// post status
							'%s',	// comp_responsible_person
							'%s',	// comp_logo
							'%s',	// comp_type
							'%s',	// comp_industry
							'%s',	// comp_employees
							'%f',	// comp_annual_income
							'%s',	// comp_currency
							'%s',	// comp_url
							'%s'	// comp_customers
						)
					);	
					
			/**
			 *
			 * BUG FIX
			 *
			 * Customer report table does not update record
			 * on Company -> Customer relation
			 *
			*/
			$this->crm_rprt_blk_update_cstmr();		
			
			$wpdb->flush();
		}
		
		/**
		 *
		 * If campaign post type
		 *
		*/
		if( DX_CRM_POST_TYPE_CAMPAIGN == $post->post_type ){	
			/**
			 *
			 * Get Customer.
			 *
			 * Customer is a custom post not meta
			 *
			*/
			$c_cstmr_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_cmp_customer' );
			
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
			
			// Contact Type
			$c_ctype = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cmp_contact_type', true );
			
			/**
			 *
			 * INSERT into custom DB. USE $wpdb->replace
			 * $wpdb->replace DEF: Replace a row in a table if 
			 * it exists or insert a new row in a table if the row 
			 * did not already exist.
			 *		
			*/
			$wpdb->replace( 
						$campaign_tbl,
						array( 
							'ID' 				=> $post->ID,
							'customer_rltn_ID' 	=> serialize( $c_cstmr_ids ),						
							'camp_name' 		=> $post->post_title,
							'camp_description' 	=> $post->post_content,
							'post_status' 		=> $post->post_status,
							'camp_customers' 	=> $c_cstmr,
							'camp_contact_type' => $c_ctype
						),
						array(
							'%d', 	// ID
							'%s', 	// customer_rltn_ID			
							'%s',	// camp_name
							'%s',	// camp_description
							'%s',	// post status
							'%s',	// camp_customers
							'%s'	// camp_contact_type
						)
					);	
					
			$wpdb->flush();
		}		
	}
	
	/**
	 *
	 * If user trash post, we set post status to trash
	 * No actual database delete operation
	 *
	*/
	function crm_trash_record( $post_id ) {
		
		global $wpdb, $post;
				
		$project_tbl 	= $wpdb->prefix . 'crm_project';
		$customer_tbl 	= $wpdb->prefix . 'crm_customer';
		$company_tbl 	= $wpdb->prefix . 'crm_company';
		$campaign_tbl  	= $wpdb->prefix . 'crm_campaign';
		
		$post_id = $_REQUEST['post'];
		$post_type = get_post_type( $post_id );
		
		/**
		 *
		 * If customer post type
		 *
		*/
		if( $post_type == DX_CRM_POST_TYPE_CUSTOMERS ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $customer_tbl SET post_status = 'trash' WHERE ID = %d",
							$post_id
							)
						);
			
		}
		
		/**
		 *
		 * If project post type
		 *
		*/
		if( DX_CRM_POST_TYPE_PROJECTS 	== $post_type ){	
		
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $project_tbl SET post_status = 'trash' WHERE ID = %d",
							$post_id
							)
						);
			
		}
		
		/**
		 *
		 * If company post type
		 *
		*/
		if( DX_CRM_POST_TYPE_COMPANY == $post_type ){	
		
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $company_tbl SET post_status = 'trash' WHERE ID = %d",
							$post_id
							)
						);
						
		}
		
		/**
		 *
		 * If campaign post type
		 *
		*/
		if( $post_type == DX_CRM_POST_TYPE_CAMPAIGN  ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $campaign_tbl SET post_status = 'trash' WHERE ID = %d",
							$post_id
							)
						);
			
		}
		
		$wpdb->flush();
		
	}
	
	/**
	 *
	 * If user untrash post, we set post status to publish
	 * No actual database delete operation
	 *
	*/
	function crm_restore_record( $post_id ) {
						
		global $wpdb;
				
		$project_tbl 	= $wpdb->prefix . 'crm_project';
		$customer_tbl 	= $wpdb->prefix . 'crm_customer';
		$company_tbl 	= $wpdb->prefix . 'crm_company';
		$campaign_tbl  	= $wpdb->prefix . 'crm_campaign';
		
		$post_id = $_REQUEST['post'];
		$post_type = get_post_type( $post_id );
		
		/**
		 *
		 * If customer post type
		 *
		*/
		if( $post_type == DX_CRM_POST_TYPE_CUSTOMERS ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $customer_tbl SET post_status = 'publish' WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		/**
		 *
		 * If project post type
		 *
		*/
		if( DX_CRM_POST_TYPE_PROJECTS 	== $post_type ){		
			
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $project_tbl SET post_status = 'publish' WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		/**
		 *
		 * If company post type
		 *
		*/
		if( DX_CRM_POST_TYPE_COMPANY == $post_type ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $company_tbl SET post_status = 'publish' WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		/**
		 *
		 * If campaign post type
		 *
		*/
		if( DX_CRM_POST_TYPE_CAMPAIGN == $post_type ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"UPDATE $campaign_tbl SET post_status = 'publish' WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		$wpdb->flush();
		
	}
	
	/**
	 *
	 * If user permanently delete post
	 * Delete from CRM Table first before
	 * actual database delete operation
	 *
	*/
	function crm_delete_record( $post_id ) {
				
		global $wpdb;
				
		$project_tbl 	= $wpdb->prefix . 'crm_project';
		$customer_tbl 	= $wpdb->prefix . 'crm_customer';
		$company_tbl 	= $wpdb->prefix . 'crm_company';
		$campaign_tbl  	= $wpdb->prefix . 'crm_campaign';
		
		$post_type = get_post_type( $post_id );
		
		/**
		 *
		 * If customer post type
		 *
		*/
		if( $post_type == DX_CRM_POST_TYPE_CUSTOMERS ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"DELETE FROM $customer_tbl WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		/**
		 *
		 * If project post type
		 *
		*/
		if( DX_CRM_POST_TYPE_PROJECTS 	== $post_type ){		
			
			$wpdb->query( 
						$wpdb->prepare( 
							"DELETE FROM $project_tbl WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		/**
		 *
		 * If company post type
		 *
		*/
		if( DX_CRM_POST_TYPE_COMPANY == $post_type ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"DELETE FROM $company_tbl WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		/**
		 *
		 * If campaign post type
		 *
		*/
		if( DX_CRM_POST_TYPE_CAMPAIGN == $post_type ){	
			
			$wpdb->query( 
						$wpdb->prepare( 
							"DELETE FROM $campaign_tbl WHERE ID = %d",
						$post_id
						)
					);
				
		}
		
		$wpdb->flush();
		
	}
	
	/**
	 *
	 * Get record from database
	 *
	 * DATA:
	 * post_type, page, tab, dx-crm-exp-csv, crm_post_type
	 * start_date, end_date, end_date_milestone, end_date_conversation
	 * project_status
	 *
	*/
	
	function dx_get_report( $data=array() ){
		
		global $wpdb;
		
		if( empty( $data ) || !is_array( $data ) ){
			return;
		}
		
		extract( $data, EXTR_PREFIX_SAME, "dx_crm" );
		
		/**
		 *
		 * Determine query by dx_crm_report
		 *
		*/
		switch( $dx_crm_report ){
			
			/**
			 *
			 * Build Query on Project post type
			 *
			*/
			case 'project':
			
				$tbl = $wpdb->prefix . 'crm_project';
				
				$where = '';
				
				// if user is customer get only projects in which he is involved
				if ( in_array( DX_CRM_CUSTOMER_ROLE, wp_get_current_user()->roles ) ) {
					$customer_role_id = get_user_meta( wp_get_current_user()->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', true );

					$where .= " AND (customer_rltn_ID LIKE '%$customer_role_id%')";
				}
				
				/** For default report in general tab( Without $_POST request ) */
				if ( ! empty( $dx_crm_report_is_default ) && $dx_crm_report_type == "general" ){
					$where .= " ORDER BY ID DESC";
				}
				
				
				// agreed cost
				if ( !empty( $agreed_cost ) ){
					$where .= "AND project_agreed_cost='$agreed_cost'";
				}
				
				// agreed cost sort
				if ( !empty( $agreed_cost_sort ) ){
					$where .= " ORDER BY project_agreed_cost $agreed_cost_sort";
				}
				
				// project_status
				if ( !empty( $project_status ) && $project_status != "Please Select.." ){
					$where .= "AND project_status='$project_status'";
				}
				
				// start_date
				if ( !empty( $start_date ) ){
					$where .= "AND project_start_date >='" . date( "Y-m-d", strtotime( $start_date ) ) . "'";
				} 
				
				// end_date
				if ( !empty( $end_date ) ){
					$where .= "AND project_planned_end_date <='" . date( "Y-m-d", strtotime( $end_date ) ) . "'";
				}
				
				// end_date_milestone
				if ( !empty( $end_date_milestone ) ){
					$where .= "AND project_end_date_first_milestone='$end_date_milestone'";
				}
				
				// end_date_conversation
				if ( !empty( $end_date_conversation ) ){
					// COnvert to Y-m-d format
					$end_date_conversation = date( "Y-m-d", strtotime( $end_date_conversation ) );
					$where .= "AND project_end_date_last_conversation='$end_date_conversation'";
				}

				// Customers
				if ( !empty( $customers ) ){		
					$where .= "AND project_customers LIKE ";
					
					$imp = implode( ",", $customers );
					
					$ttl_compcust=count( $customers );
					$x=0;
					foreach( $customers as $customer ){
						
						$x++;
						
						$where .= "'%$customer%'";
						
						if( $ttl_compcust != $x ){
							$where .= " OR ";
						}
					}	
					
				}
				
				// AVOID querying empty WHERE
				if( !empty( $where ) ){
					$sql = "SELECT * FROM $tbl WHERE post_status = 'publish' $where";
					
					$wp_data = $wpdb->get_results( $sql );
					
					//error_log( $sql ); 						// FOR DEBUGGING
					//error_log( print_r( $wp_data, true ) );	// FOR DEBUGGING
				} else {
					$wp_data = '';
				}
				
			break;
			
			/**
			 *
			 * Build Query on Project post type
			 *
			*/
			case 'customer':
			
				$tbl = $wpdb->prefix . 'crm_customer';
				
				$where = '';
				
				/** For default report in general tab( Without $_POST request ) */
				if ( ! empty( $dx_crm_report_is_default ) && $dx_crm_report_type == "general" ){
					$where .= " ORDER BY ID DESC";
				}
				
				// Contact Date
				if ( !empty( $contact_date ) ){
					$where .= "AND cust_contact_date ='" . date( "Y-m-d", strtotime( $contact_date ) ) . "'";
				} 
				
				// Contact Type
				if ( !empty( $contact_type ) ){
					$where .= "AND cust_contact_type ='$contact_type'";
				}
				
				// Project Type
				if ( !empty( $project_type ) ){
					$where .= "AND cust_project_type='$project_type'";
				}
				
				// Companies
				if ( !empty( $companies ) ){		
					$where .= "AND cust_companies LIKE ";
					
					$imp = implode( ",", $companies );
					
					$ttl_compcust=count( $companies );
					$x=0;
					foreach( $companies as $company ){
						
						$x++;
						
						$where .= "'%$company%'";
						
						if( $ttl_compcust != $x ){
							$where .= " OR ";
						}
					}	
					
				}	
				
				// Projects
				if ( !empty( $projects ) ){		
					$where .= "AND cust_projects LIKE ";
					
					$imp = implode( ",", $projects );
					
					$ttl_compcust=count( $projects );
					$x=0;
					foreach( $projects as $project ){
						
						$x++;
						
						$where .= "'%$project%'";
						
						if( $ttl_compcust != $x ){
							$where .= " OR ";
						}
					}	
					
				}	
				
				// Campaigns
				if ( !empty( $campaigns ) ){		
					$where .= "AND cust_campaigns LIKE ";
					
					$imp = implode( ",", $campaigns );
					
					$ttl_compcust=count( $campaigns );
					$x=0;
					foreach( $campaigns as $campaign ){
						
						$x++;
						
						$where .= "'%$campaign%'";
						
						if( $ttl_compcust != $x ){
							$where .= " OR ";
						}
					}	
					
				}	
				
				/** Total Project and Paid */
				if ( !empty( $total_project ) && empty( $total_paid ) ){
					$where .= " ORDER BY total_project $total_project";
				}
				
				if ( empty( $total_project ) && !empty( $total_paid ) ){
					$where .= " ORDER BY total_paid $total_paid";
				}
				
				if ( !empty( $total_project ) && !empty( $total_paid ) ){
					$where .= " ORDER BY total_project $total_project, total_paid $total_paid";
				}
				/** END of Total Project and Paid */
								
				// AVOID querying empty WHERE
				if( !empty( $where ) ){
					$sql = "SELECT * FROM $tbl WHERE post_status = 'publish' $where";		
					
					$wp_data = $wpdb->get_results( $sql );
					
					//error_log( $sql ); 						// FOR DEBUGGING
					//error_log( print_r( $wp_data, true ) );	// FOR DEBUGGING
				} else {
					$wp_data = '';
				}			
				
			break;			
			
			case 'company' :
			
				$tbl = $wpdb->prefix . 'crm_company';
				
				$where = '';
				
				// if user is customer get only companies in which he is involved
				if ( in_array( DX_CRM_CUSTOMER_ROLE, wp_get_current_user()->roles ) ) {
					$customer_role_id = get_user_meta( wp_get_current_user()->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', true );

					$where .= " AND (customer_rltn_ID LIKE '%$customer_role_id%')";
				}
				
				/** For default report in general tab( Without $_POST request ) */
				if ( ! empty( $dx_crm_report_is_default ) ){
					$where .= " ORDER BY ID DESC";
				}
				
				// Company type
				if ( !empty( $company_type ) && $company_type != "Please Select.." ){
					$where .= "AND comp_type ='$company_type'";
				} 
				
				// Industry
				if ( !empty( $company_industry ) && $company_industry != "Please Select.." ){
					$where .= "AND comp_industry ='$company_industry'";
				}
				
				// Employees
				if ( !empty( $company_employees ) ){
					$where .= "AND comp_employees ='$company_employees'";
				}
				
				// Currency
				if ( ! empty( $currency ) ){					
					// Quick fix on Currency filter issue
					// @TODO, on next version use public static function 
					switch( $currency ){
						case 'US Dollar':
							$currency = 'USD';
						break;
						case 'Euro':
							$currency = 'EUR';
						break;
					}
					$where .= "AND comp_currency='$currency'";
				}
				
				// Customers
				if ( !empty( $company_assign_customer ) ){		
					$where .= "AND comp_customers LIKE ";
					
					$imp = implode( ",", $company_assign_customer );
					
					$ttl_compcust=count( $company_assign_customer );
					$x=0;
					foreach( $company_assign_customer as $comp_customer ){
						
						$x++;
						
						$where .= "'%$comp_customer%'";
						
						if( $ttl_compcust != $x ){
							$where .= " OR ";
						}
					}	
					
				}			

				// AVOID querying empty WHERE
				if( !empty( $where ) ){
					$sql = "SELECT * FROM $tbl WHERE post_status = 'publish' $where";		
					
					$wp_data = $wpdb->get_results( $sql );
					
					//error_log( $sql ); 						// FOR DEBUGGING
					//error_log( print_r( $wp_data, true ) );	// FOR DEBUGGING
				} else {
					$wp_data = '';
				}
				
			break;
			
			case 'campaign':
			
				$tbl = $wpdb->prefix . 'crm_campaign';
				
				$where = '';
				
				// Contact Type
				if ( !empty( $contact_type ) ){	
					$where .= "AND camp_contact_type ='$contact_type'";
				}
				
				// Customers
				if ( !empty( $customers ) ){		
				
					$where .= "AND camp_customers LIKE ";
					
					$imp = implode( ", ", $customers );
					
					$ttl_compcust=count( $customers );
					$x=0;
					foreach( $customers as $customer ){
						
						$x++;
						
						$where .= "'%$customer%'";
						
						if( $ttl_compcust != $x ){
							$where .= " OR ";
						}
					}	
					
				}	
				
				// AVOID querying empty WHERE
				if( !empty( $where ) ){
					$sql = "SELECT * FROM $tbl WHERE post_status = 'publish' $where";		
					
					$wp_data = $wpdb->get_results( $sql );
					
					// var_dump( $sql ); 						// FOR DEBUGGING
					//error_log( print_r( $wp_data, true ) );	// FOR DEBUGGING
				} else {
					$wp_data = '';
				}
				
			break;
			
		}
		
		$wpdb->flush();
		
		return $wp_data;
		
	}
	
	/**
	 *
	 * Check query filter before generating report
	 *
	 * Return true if Query is valid
	 * False if not
	 * [true/false]
	 *
	*/
	function ajaxcrm_chckrprtquery(){
			
		$params = array();
		
		parse_str( $_POST['content'], $params );
		
		// Remove key if null value
		foreach( $params as $key => $value ){
			if( $value == "" ){
				unset( $params[ $key ] );
			}
		}
		
		// Check query
		$result = "";
		
		$data = $this->dx_get_report( $params );
		
		if( !empty ( $data ) ){
			$result = "success";
		} else {
			$result = "error";
		}
		
		wp_die( $result );
	}
	
	
	/**
	 *
	 * Display error message if no query found
	 *
	*/
	function crm_rprt_err_ntc(){
		echo '<div class="error notice is-dismissible"><p>' . __( 'No report matched your criteria. Please try again!', 'dxcrm' ) . '</p></div>';
	}

	/**
	 *
	 * Display info if query found
	 *
	*/
	function crm_rprt_sccss_ntc(){
		echo '<div class="updated notice is-dismissible"><p>' . __( 'Report successfully generated!', 'dxcrm' ) . '</p></div>';
	}
	
	/**
	 *
	 * Bulk update customers
	 *
	 * Bug fix on: 
	 *
	 * Customer Total Paid and Total Project not updated when generating report
	 * Customer Total Paid and Total Project is not updated until you edit/save the customer.
	 *
	*/	
	function crm_rprt_blk_update_cstmr(){
		
		global $post, $wpdb, $dx_crm_model;
		
		$customer_tbl = $wpdb->prefix . 'crm_customer';
		
		$args = array(
				'posts_per_page'   => -1,
				'post_type'        => DX_CRM_POST_TYPE_CUSTOMERS,
				);
		
		$cstmrs = get_posts( $args );
		
		foreach( $cstmrs as $post ){
			
			if ( !wp_is_post_revision( $post->ID ) ){ 
				
				$post_id = $post->ID;
				
				/**
				 *
				 * Get Total Project and Total Paid
				 *
				*/
				$total_paid = '0';
				$total_project = '0.00';
				$cs_tppd = $dx_crm_model->crm_get_customer_details_custom();
				
				if ( !empty( $cs_tppd ) && is_array( $cs_tppd ) ) {
					
					foreach( $cs_tppd as $c_tppd ){	
						
						if( $c_tppd['ID'] == $post_id ) {
							$total_paid = (int) $c_tppd['total_paid'];
							$total_project = (int) $c_tppd['total_project'];
						}
						
					}
					
				}
				
				/**
				 *
				 * Get SKILLS name by ID
				 *
				*/
				$skills_raw	= wp_get_post_terms( $post_id, DX_CRM_SKILL_TAXONOMY );
				$skill_ids = array();
				
				if ( !empty( $skills_raw ) ){
					
					$skills_arr = array();
					
					foreach( $skills_raw as $value ){
						$skill_ids[] = $value->term_id;
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
				$c_fpt_id = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_first_pro_type', true );	
				if( !empty( $c_fpt_id ) ){
					$c_fpt_terms = get_term( $c_fpt_id );
					
					if( !empty( $c_fpt_terms->name ) ){
						$c_fpt = $c_fpt_terms->name;
					} else {
						$c_fpt = '';
					}
				}else{
					$c_fpt = '';
				}	
				
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
				 * Get joined COMPANY Name by ID
				 *
				*/
				$c_company_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_company' );
				
				if( !empty ( $c_company_ids ) ){
					
					$c_cmpnys = array();
					
					foreach( $c_company_ids as $value ){
						
						// check if valid
						$title = get_the_title( $value );
						if( !empty( $title ) ){
							$c_cmpnys[] = $title;
						}
						
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
				 * Get joined PROJECT Name by ID
				 *
				*/
				$projects_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_project' );
				if( !empty ( $projects_raw ) ){
					
					$projects = array();
					
					foreach( $projects_raw as $value ) {
						
						// check if valid
						$title = get_the_title( $value );
						if( !empty( $title ) ){
							$projects[] = $title;
						}
						
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
				 * Get joined CAMPAIGNS Name by ID
				 *
				*/
				$c_cmpgns_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_campaigns' );
				
				if( !empty ( $c_cmpgns_ids ) ){
					
					$c_cmpgns = array();
					
					foreach( $c_cmpgns_ids as $value ){
						
						// check if valid
						$title = get_the_title( $value );
						if( !empty( $title ) ){
							$c_cmpgns[] = $title;
						}
						
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
				 * INSERT into custom DB. USE $wpdb->replace
				 * $wpdb->replace DEF: Replace a row in a table if 
				 * it exists or insert a new row in a table if the row 
				 * did not already exist.
				 *		
				*/
				$wpdb->replace( 
							$customer_tbl,
							array( 
								'ID' 						=> $post->ID,
								'company_rltn_ID' 			=> serialize( $c_company_ids ),						
								'project_rltn_ID' 			=> serialize( $projects_raw ),
								'campaign_rltn_ID' 			=> serialize( $c_cmpgns_ids ),
								'skills_rltn_ID' 			=> serialize( $skill_ids ),
								'cust_name' 				=> $post->post_title,
								'cust_desc' 				=> $post->post_content,
								'post_status' 				=> $post->post_status,
								'cust_skills' 				=> $c_skills,
								'cust_project_type' 		=> $c_fpt,
								'cust_initial_investment' 	=> $c_ciivstmnt,
								'cust_referral' 			=> $c_referral,
								'cust_contact_date' 		=> date( "Y-m-d", strtotime( $c_cdate_raw ) ),
								'cust_contact_type' 		=> $c_cntcttype,
								'cust_company_role' 		=> $c_cmpnyrole,
								'cust_email' 				=> $c_email,
								'cust_phone_number' 		=> $c_pnum,
								'cust_companies' 			=> $c_company,
								'cust_projects' 			=> $c_project,
								'cust_campaigns' 			=> $c_cmpgn,
								'cust_bank_info' 			=> $c_bankinfo,
								'cust_vat_number' 			=> $c_vatnum,
								'cust_country' 				=> $c_country,
								'total_project'				=> $total_project,
								'total_paid'				=> $total_paid
							),
							array(
								'%s', 	// ID
								'%s', 	// company_rltn_ID				
								'%s',	// project_rltn_ID
								'%s',	// campaign_rltn_ID
								'%s',	// skills_rltn_ID
								'%s',	// cust_name
								'%s',	// cust_desc
								'%s',	// post status
								'%s',	// cust_skills
								'%s',	// cust_project_type
								'%f',	// cust_initial_investment
								'%s',	// cust_referral
								'%s',	// cust_contact_date
								'%s',	// cust_contact_type 
								'%s',	// cust_company_role
								'%s',	// cust_email
								'%s',	// cust_phone_number
								'%s',	// cust_companies
								'%s',	// cust_projects
								'%s',	// cust_campaigns
								'%s',	// cust_bank_info
								'%s',	// cust_vat_number
								'%s',	// cust_country
								'%d',	// total_project
								'%f'	// total_paid
							)
						);	
						
				$wpdb->flush();
				
			}
			
		}
		
	}
	
	/**
	 *
	 * Bulk update projects
	 *
	 * Bug fix on: 
	 *
	 * We have 3 main post types: Customer, Project and Company. 
	 *
	 * In reporting table, all relations had been joined into single row. 
	 * If we edit any of those especially Customer and Project, it will not 
	 * update on report table.
	 *
	*/	
	function crm_rprt_blk_update_prjct(){
		
		global $post, $wpdb, $dx_crm_model;
		
		$project_tbl = $wpdb->prefix . 'crm_project';
		
		$args = array(
				'posts_per_page'   => -1,
				'post_type'        => DX_CRM_POST_TYPE_PROJECTS,
				);
		
		$cstmrs = get_posts( $args );
		
		foreach( $cstmrs as $post ){
			
			if ( !wp_is_post_revision( $post->ID ) ){ 
				
				$post_id = $post->ID;
				
				/**
				 *
				 * Prepare raw info from taxonomy table
				 *
				*/
				$project_type = array();
				$project_types 	= wp_get_post_terms( $post_id, DX_CRM_PRO_TAXONOMY );
				
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
				$p_sdate = ( !empty ( $p_sdate_raw ) ) ? date( "Y-m-d", strtotime( $p_sdate_raw ) ) : '' ;
				
				// Planned End Date
				$p_edate_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_end_date', true );		
				$p_edate = ( !empty ( $p_edate_raw ) ) ? date( "Y-m-d", strtotime( $p_edate_raw ) ) : '' ;
				
				// Ongoing
				$p_ongoing_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_ongoing', true );		
				
				// Real End Date for Last Milestone
				$p_redlm_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_real_end_date_first_mile', true );
				$p_redlm = ( !empty ( $p_redlm_raw ) ) ? date( "Y-m-d", strtotime( $p_redlm_raw ) ) : '' ;
				
				// Real End Date for Last Conversation
				$p_redlc_raw = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'pro_real_end_date_last_conversation', true );
				$p_redlc = ( !empty ( $p_redlc_raw ) ) ? date( "Y-m-d", strtotime( $p_redlc_raw ) ) : '' ;
				
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
				
				/**
				 *
				 * INSERT into custom DB. USE $wpdb->replace
				 * $wpdb->replace DEF: Replace a row in a table if 
				 * it exists or insert a new row in a table if the row 
				 * did not already exist.
				 *		
				*/
				$wpdb->replace( 
							$project_tbl,
							array( 
								'ID' 									=> $post->ID,
								'customer_rltn_ID' 						=> serialize( $p_cstmr_ids ),						
								'company_rltn_ID' 						=> serialize( $p_company_ids ),
								'project_type_rltn_ID' 					=> serialize( $project_types ),
								'project_name' 							=> $post->post_title,
								'project_description' 					=> $post->post_content,
								'post_status' 							=> $post->post_status,
								'project_type' 							=> $type,
								'project_company' 						=> $p_company,
								'project_start_date' 					=> $p_sdate,
								'project_planned_end_date' 				=> $p_edate,
								'project_ongoing' 						=> $p_ongoing_raw,
								'project_end_date_first_milestone' 		=> $p_redlm,
								'project_end_date_last_conversation' 	=> $p_redlc,
								'project_agreed_cost' 					=> $p_cost,
								'project_currency' 						=> $p_curr,
								'project_status' 						=> $p_status,
								'project_total_paid' 					=> $p_total,
								'project_responsible_person' 			=> $p_rprsn,
								'project_customers' 					=> $p_cstmr
							),
							array(
								'%s', 	// ID
								'%s', 	// customer_rltn_ID			
								'%s',	// company_rltn_ID
								'%s',	// project_type_rltn_ID
								'%s',	// project_name
								'%s',	// project_description
								'%s',	// post status
								'%s',	// project_type
								'%s',	// project_company
								'%s',	// project_start_date
								'%s',	// project_planned_end_date
								'%s',	// project_ongoing
								'%s',	// project_end_date_first_milestone
								'%s',	// project_end_date_last_conversation
								'%f',	// project_agreed_cost
								'%s',	// project_currency
								'%s',	// project_status
								'%f',	// project_total_paid
								'%s',	// project_responsible_person
								'%s'	// project_customers
							)
						);		
				
				$wpdb->flush();
			
			}
			
		}
		
	}
	
	/**
	 *
	 * Bulk update company
	 *
	 * Bug fix on: 
	 *
	 * We have 3 main post types: Customer, Project and Company. 
	 *
	 * In reporting table, all relations had been joined into single row. 
	 * If we edit any of those especially Customer and Project, it will not 
	 * update on report table.
	 *
	*/	
	function crm_rprt_blk_update_cmpny(){
		
		global $post, $wpdb, $dx_crm_model;
		
		$company_tbl = $wpdb->prefix . 'crm_company';
		
		$args = array(
				'posts_per_page'   => -1,
				'post_type'        => DX_CRM_POST_TYPE_COMPANY,
				);
		
		$cstmrs = get_posts( $args );
		
		foreach( $cstmrs as $post ){
			
			if ( !wp_is_post_revision( $post->ID ) ){ 
				
				$post_id = $post->ID;
				
				// Rsponsible Person
				$c_resp = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_assigned_by', true );
				
				// Logo
				$c_logo = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_logo' );
				
				// Company Type
				$c_type = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_type', true );
				
				// Industry
				$c_indstry = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_industry', true );
				
				// Employees
				$c_emplys = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_employees', true );
				
				// Annual Income
				$c_annual_income = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_annual_income', true );
				
				// Currency
				$c_curr = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_currency', true );
				
				// Company URL	
				$c_url = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'company_url', true );			
				
				/**
				 *
				 * Get Customer.
				 *
				 * Customer is a custom post not meta
				 *
				*/
				$c_cstmr_ids = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'joined_customer' );
				
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

				/**
				 *
				 * INSERT into custom DB. USE $wpdb->replace
				 * $wpdb->replace DEF: Replace a row in a table if 
				 * it exists or insert a new row in a table if the row 
				 * did not already exist.
				 *		
				*/
				$wpdb->replace( 
							$company_tbl,
							array( 
								'ID' 						=> $post->ID,
								'customer_rltn_ID' 			=> serialize( $c_cstmr_ids ),						
								'comp_name' 				=> $post->post_title,
								'comp_description' 			=> $post->post_content,
								'post_status' 				=> $post->post_status,
								'comp_responsible_person' 	=> $c_resp,
								'comp_logo' 				=> serialize( $c_logo ),
								'comp_type' 				=> $c_type,
								'comp_industry' 			=> $c_indstry,
								'comp_employees' 			=> $c_emplys,
								'comp_annual_income' 		=> $c_annual_income,
								'comp_currency' 			=> $c_curr,
								'comp_url' 					=> $c_url,
								'comp_customers' 			=> $c_cstmr
							),
							array(
								'%d', 	// ID
								'%s', 	// customer_rltn_ID			
								'%s',	// comp_name
								'%s',	// comp_description
								'%s',	// post status
								'%s',	// comp_responsible_person
								'%s',	// comp_logo
								'%s',	// comp_type
								'%s',	// comp_industry
								'%s',	// comp_employees
								'%f',	// comp_annual_income
								'%s',	// comp_currency
								'%s',	// comp_url
								'%s'	// comp_customers
							)
						);
						
				$wpdb->flush();
				
			}
			
		}
		
	}
}
?>
