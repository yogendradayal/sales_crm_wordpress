<?php
/*
Plugin Name: DX Sales CRM
Description: DX Sales CRM - is a WordPress solution for Costumer Relationship Management system. Manage your ENTIRE business in your WordPress Dashboard. It is easy to setup and powerful solution for WordPress CMS.
Version: 1.1
Author: DevriX
Text Domain: dxcrm
Domain Path: /includes/languages
Author URI: https://devrix.com
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions 
 * 
 * @package CRM System
 * @since 1.0.0
 */

global $wpdb;

/**
 * Plugin directory path
 *
 * @package CRM System
 * @since 1.0.0
*/
if( ! defined( 'DX_CRM_DIR' ) ) {
	define( 'DX_CRM_DIR', dirname( __FILE__ ) );
}

include_once( DX_CRM_DIR . '/defines.php' );

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @package CRM System
 * @since 1.0.0
 */
load_plugin_textdomain( 'dxcrm', false, dirname( plugin_basename( __FILE__ ) ) . '/includes/languages' );


/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @package CRM System
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'dx_crm_install' );

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @package CRM System
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'dx_crm_uninstall');

/**
 * Plugin Setup (On Activation)
 *
 * Does the initial setup,
 * stest default values for the plugin options.
 *
 * @package CRM System
 * @since 1.0.0
 */
function dx_crm_install() {	
	global $wpdb, $current_user, $wp_roles;	
	
	// Table creation
	dx_crm_report_tbl_create();	
	dx_roadmap_create_tracking_table();
	
	// Register post type function
	dx_crm_register_post_types();

	// Add user capability for Sales CRM Customer
	$caps = dx_crm_customers_caps();	
	$crm_customer_role = add_role( DX_CRM_CUSTOMER_ROLE, 'DX CRM Sales Customer', $caps );
	
	// Add custom capability to Administrator
	// for accessing CRM Report
	$admin = get_role( 'administrator' );
	$admin->add_cap( 'manage_crm' ); 
	
	// Add caps to the adminisrator, for accessing the project CPT
	$admin->add_cap( 'read' );
	$admin->add_cap( 'read_'. DX_CRM_POST_TYPE_PROJECTS );//single
	$admin->add_cap( 'read_private_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	$admin->add_cap( 'edit_'. DX_CRM_POST_TYPE_PROJECTS );//single
	$admin->add_cap( 'edit_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	$admin->add_cap( 'edit_others_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	$admin->add_cap( 'edit_published_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	$admin->add_cap( 'publish_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	$admin->add_cap( 'delete_others_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	$admin->add_cap( 'delete_private_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	$admin->add_cap( 'delete_published_'. DX_CRM_POST_TYPE_PROJECTS .'s' );//musltiple
	// Add caps to the adminisrator, for accessing the company CPT
	$admin->add_cap( 'read' );
	$admin->add_cap( 'read_'. DX_CRM_POST_TYPE_COMPANY );//single
	$admin->add_cap( 'read_private_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
	$admin->add_cap( 'edit_'. DX_CRM_POST_TYPE_COMPANY );//single
	$admin->add_cap( 'edit_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
	$admin->add_cap( 'edit_others_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
	$admin->add_cap( 'edit_published_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
	$admin->add_cap( 'publish_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
	$admin->add_cap( 'delete_others_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
	$admin->add_cap( 'delete_private_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
	$admin->add_cap( 'delete_published_'. DX_CRM_POST_TYPE_COMPANY .'s' );//musltiple
		
	// IMP Call of Function
	// Need to call when custom post type is being used in plugin
	flush_rewrite_rules();	
}

/**
 * Plugin Setup (On Deactivation)
 *
 * Delete  plugin options.
 *
 * @package CRM System
 * @since 1.0.0
 */
function dx_crm_uninstall() {	
	global $wpdb;	
	
	/**
	 * Delete CRM user capability
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	if( get_role( DX_CRM_CUSTOMER_ROLE ) ){
		remove_role( DX_CRM_CUSTOMER_ROLE );
	}
	
	// IMP Call of Function
	// Need to call when custom post type is being used in plugin
	flush_rewrite_rules();
}

/**
 *
 * Create "Project", "Customer", "Company", "Campaign" table for reporting
 *
 * Minimize large loop and RAM hogs when generating
 * report. This can be done if main post types have 
 * dedicated table where all information can be accessed
 * directly.
 *
 * All metadata will be stored with main post data in a 
 * single row.
 *
*/
function dx_crm_report_tbl_create(){
	global $wpdb;
	
	$project_tbl = $wpdb->prefix . 'crm_project';
	$customer_tbl = $wpdb->prefix . 'crm_customer';
	$company_tbl = $wpdb->prefix . 'crm_company';
	
	$charset_collate = $wpdb->get_charset_collate();
	
	// Project
	$crm_proj_sql = "CREATE TABLE IF NOT EXISTS $project_tbl (
					  ID int(11) NOT NULL,
					  customer_rltn_ID text NOT NULL,
					  company_rltn_ID text NOT NULL,
					  project_type_rltn_ID text NOT NULL,
					  project_name varchar(150) NOT NULL,
					  project_description text NOT NULL,
					  post_status varchar(10) NOT NULL,
					  project_type varchar(250) NOT NULL,
					  project_company varchar(250) NOT NULL,
					  project_start_date date NOT NULL,
					  project_planned_end_date date NOT NULL,
					  project_ongoing tinyint(1) NOT NULL,
					  project_end_date_first_milestone date NOT NULL,
					  project_end_date_last_conversation date NOT NULL,
					  project_agreed_cost decimal(13,2) NOT NULL,
					  project_currency varchar(5) NOT NULL,
					  project_status varchar(50) NOT NULL,
					  project_total_paid decimal(13,2) NOT NULL,
					  project_responsible_person varchar(50) NOT NULL,
					  project_customers text NOT NULL,
					  PRIMARY KEY (ID)
					)  $charset_collate;";
	
	// Customer
	$crm_cust_sql = "CREATE TABLE IF NOT EXISTS $customer_tbl (
					  ID int(11) NOT NULL,
					  company_rltn_ID text NOT NULL,
					  project_rltn_ID text NOT NULL,
					  campaign_rltn_ID text NOT NULL,
					  skills_rltn_ID text NOT NULL,
					  cust_name varchar(150) NOT NULL,
					  cust_desc text NOT NULL,
					  post_status varchar(10) NOT NULL,
					  cust_skills text NOT NULL,
					  cust_project_type varchar(50) NOT NULL,
					  cust_initial_investment decimal(13,2) NOT NULL,
					  cust_referral varchar(100) NOT NULL,
					  cust_contact_date date NOT NULL,
					  cust_contact_type varchar(25) NOT NULL,
					  cust_company_role varchar(50) NOT NULL,
					  cust_email varchar(50) NOT NULL,
					  cust_phone_number varchar(25) NOT NULL,
					  cust_companies text NOT NULL,
					  cust_projects text NOT NULL,
					  cust_campaigns text NOT NULL,
					  cust_bank_info text NOT NULL,
					  cust_vat_number int(11) NOT NULL,
					  cust_country varchar(50) NOT NULL,
					  total_project int(11) NOT NULL,
					  total_paid decimal(13,2) NOT NULL,
					  PRIMARY KEY (ID)
					)  $charset_collate;";
	
	// Company
	$crm_comp_sql = "CREATE TABLE IF NOT EXISTS $company_tbl (
					  ID int(11) NOT NULL,
					  customer_rltn_ID text NOT NULL,
					  comp_name varchar(150) NOT NULL,
					  comp_description text NOT NULL,
					  post_status varchar(10) NOT NULL,
					  comp_responsible_person varchar(150) NOT NULL,
					  comp_logo varchar(250) NOT NULL,
					  comp_type varchar(50) NOT NULL,
					  comp_industry varchar(150) NOT NULL,
					  comp_employees varchar(25) NOT NULL,
					  comp_annual_income decimal(13,2) NOT NULL,
					  comp_currency varchar(3) NOT NULL,
					  comp_url varchar(150) NOT NULL,
					  comp_customers text NOT NULL,
					  PRIMARY KEY (ID)
					)  $charset_collate;";
					
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	dbDelta( $crm_proj_sql );
	dbDelta( $crm_cust_sql );
	dbDelta( $crm_comp_sql );
	
}

/**
 * Create "Roadmap" table for tracking
 *
 * @package CRM System
 * @since 1.0.0
*/
function dx_roadmap_create_tracking_table(){
	global $wpdb;
	
	$rmtt = $wpdb->prefix . 'crm_roadmap';
	$charset_collate = $wpdb->get_charset_collate();
	
	$rmtsql = "CREATE TABLE IF NOT EXISTS $rmtt (
				ID int(11) NOT NULL AUTO_INCREMENT,
				roadmap_time DATETIME NOT NULL,
				roadmap_user int(11) NOT NULL,
				roadmap_project_id int(11) NOT NULL,
				roadmap_summary longtext NOT NULL,				
				PRIMARY KEY (ID)
				) $charset_collate;";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	dbDelta( $rmtsql );
}

/**
 * Delete tables when uninstall/deactivate
 *
 * @package CRM System
 * @since 1.0.0
*/
function dx_crm_report_tbl_remove(){
	global $wpdb;
	
	$crm_report_tables = array(
		$wpdb->prefix . 'crm_project',
		$wpdb->prefix . 'crm_customer',
		$wpdb->prefix . 'crm_company',
		$wpdb->prefix . 'crm_roadmap',
	);
	
	foreach( $crm_report_tables as $table ){
		$wpdb->query( "DROP TABLE IF EXISTS $table" );
	}
}

/**
 * Set up array of vendor admin capabilities
 * Handles get all vendors from database
 * 
 * @package CRM System
 * @since 1.0.0
 */
function dx_crm_customers_caps() {
	
	$project_capability = DX_CRM_POST_TYPE_PROJECTS;
	$company_capability	= DX_CRM_POST_TYPE_COMPANY;
	
    $caps = array(
		"read" => 1,
		"manage_crm" => 1,
		//"manage_options" => 1,
        "read_{$company_capability}s" => 1,
        "edit_{$company_capability}s" => 1,
		"publish_{$company_capability}s" => 1,
		"delete_{$company_capability}s" => 1,
		"edit_published_{$company_capability}s" => 1,
		
		"read_{$project_capability}s" => 1,
        "edit_{$project_capability}s" => 1,		
		"publish_{$project_capability}s" => 1,
		"delete_{$project_capability}s" => 1,
		"edit_published_{$project_capability}s" => 1
    );
    
	return $caps;
}

// Global variables
global $dx_crm_scripts, $dx_crm_model, $dx_crm_admin, $dx_crm_public, $dx_crm_mc, $dx_crm_report, $dx_crm_roadmap;

// Registring Post type functionality
require_once( DX_CRM_INC_DIR . '/dx-crm-post-type.php' );

// Script class handles most of script functionalities of plugin
include_once( DX_CRM_INC_DIR . '/class-dx-crm-scripts.php' );
$dx_crm_scripts = new Dx_Crm_Scripts();
$dx_crm_scripts->add_hooks();

// Model class handles most of functionalities of plugin
require_once( DX_CRM_INC_DIR . '/class-dx-crm-model.php' );
$dx_crm_model = new Dx_Crm_Model();
$dx_crm_model->add_hooks();

// Admin class handles most of admin functionalities of plugin
require_once( DX_CRM_ADMIN_DIR . '/class-dx-crm-admin.php' );
$dx_crm_admin = new Dx_Crm_Admin();
$dx_crm_admin->add_hooks();

// Roadmap Tracking
require_once( DX_CRM_ADMIN_DIR . '/class-dx-crm-roadmap-tracker.php' );
$dx_crm_roadmap = new DX_CRM_ROADMAP_TRACKING();
$dx_crm_roadmap->add_hooks();

// Admin class handles public functionalities of plugin
require_once( DX_CRM_INC_DIR . '/class-dx-crm-public.php' );
$dx_crm_public = new Dx_Crm_Public();
$dx_crm_public->add_hooks();

// Template class handles tamplate feature
/*
 * Depreciated
 *
*/
/* require_once( DX_CRM_INC_DIR . '/class-dx-crm-templates.php' );
$dx_crm_tpl = new Dx_Crm_Templates();
$dx_crm_tpl->add_hooks(); */

//Export to CSV Process for used voucher codes
require_once( DX_CRM_INC_DIR . '/dx-crm-export-csv.php' );

//Metabox file to handle metaboxes using class
include_once( DX_CRM_ADMIN_DIR . '/dx-crm-meta-box.php' ); // meta box for customer option

// Model class handles report table actions
require_once( DX_CRM_INC_DIR . '/class-dx-crm-report-tables.php' );
$dx_crm_report = new Dx_Crm_Report_Tables();
$dx_crm_report->add_hooks();

require_once( DX_CRM_INC_DIR . '/class-dx-crm-export-csv.php' );
$dx_crm_export = new Dx_Crm_Export_Csv();

require_once( DX_CRM_INC_DIR . '/class.customers.php' );
$customer = new Sales_CRM_Customer();
$customer->add_hooks();

require_once( DX_CRM_INC_DIR . '/class.projects.php' );
$project = new Sales_CRM_Project();
$project->add_hooks();