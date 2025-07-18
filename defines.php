<?php
/**
 * Constant variable definition
 *
 * @package CRM System
 * @since 1.0.0
*/
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin Version
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_VERSION' ) ) {
	define( 'DX_CRM_VERSION', '1.0' );	
}

/**
 * Plugin directory path
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_DIR' ) ) {
	define( 'DX_CRM_DIR', dirname( __FILE__ ) );
}

/**
 * Plugin URL
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_URL' ) ) {
	define( 'DX_CRM_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Plugin assets URL
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_ASSETS_URL' ) ) {
	define( 'DX_CRM_ASSETS_URL', plugin_dir_url( __FILE__ ) . '/assets' );
}

/**
 * Plugin include URL
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_INC_URL' ) ) {
	define( 'DX_CRM_INC_URL', DX_CRM_URL.'includes' );
}

/**
 * Plugin include directory
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_INC_DIR' ) ) {
	define( 'DX_CRM_INC_DIR', DX_CRM_DIR.'/includes' );
}

/**
 * Plugin image directory URL
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_IMG_URL' ) ) {
	define( 'DX_CRM_IMG_URL', DX_CRM_ASSETS_URL.'/images/' );
}

/**
 * Plugin admin directory
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_ADMIN_DIR' ) ) {
	define( 'DX_CRM_ADMIN_DIR', DX_CRM_INC_DIR.'/admin' );
}

/**
 * Plugin template directory
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_TPL_DIR' ) ) {
	define( 'DX_CRM_TPL_DIR', DX_CRM_DIR.'/templates' );
}

/**
 * Plugin basename
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_BASENAME') ) {
	define( 'DX_CRM_BASENAME', dirname( plugin_basename(__FILE__) ) );
}

/**
 * Plugin meta prefix. Used accross CRM system meta data
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_META_PREFIX') ) {
	define( 'DX_CRM_META_PREFIX', 'dxcrm_' );
}

/**
 * Customer post type
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_CUSTOMERS' ) ) {
	define( 'DX_CRM_POST_TYPE_CUSTOMERS', 'dxcrm_customers' );	
}

/**
 * Project post type
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_PROJECTS' ) ) {
	define('DX_CRM_POST_TYPE_PROJECTS', 'dxcrm_projects');
}

/**
 * Roadmap post type. DEPRECIATED.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_ROADMAP' ) ) {
	define('DX_CRM_POST_TYPE_ROADMAP', 'dxcrm_roadmap');
}

/**
 * Milestones post type. DEPRECIATED.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_MILESTONES' ) ) {
	define('DX_CRM_POST_TYPE_MILESTONES', 'dxcrm_milestones');
}

/**
 * Time sheets post type. DEPRECIATED.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_TIMESHEETS' ) ) {
	define('DX_CRM_POST_TYPE_TIMESHEETS', 'dxcrm_timesheets');
}

/**
 * Document management post type
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_DOC_MNGR' ) ) {
	define('DX_CRM_POST_TYPE_DOC_MNGR', 'dxcrm_doc_mngr');
}

/**
 * Staff post type. DEPRECIATED.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_STAFF' ) ) {
	define('DX_CRM_POST_TYPE_STAFF', 'dxcrm_staff');
}

/**
 * Company expenses post type. DEPRECIATED.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_COMPANY_EXPENSES' ) ) {
	define('DX_CRM_POST_TYPE_COMPANY_EXPENSES', 'dxcrm_company_expenses');
}

/**
 * Company post type
 *
 * @package CRM System
 * @since 1.0.0
*/
if( ! defined( 'DX_CRM_POST_TYPE_COMPANY' ) ) {
	define('DX_CRM_POST_TYPE_COMPANY', 'dxcrm_company');
}

/**
 * Quote post type. DEPRECIATED.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_QUOTE' ) ) {
	define('DX_CRM_POST_TYPE_QUOTE', 'dxcrm_quote');
}

/**
 * Project type taxonomy.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_PRO_TAXONOMY' ) ) {
	define( 'DX_CRM_PRO_TAXONOMY','dxcrm_pro_type' );
}

/**
 * Employer Staff taxonomy.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_STAFF_TAXONOMY' ) ) {
	define( 'DX_CRM_STAFF_TAXONOMY','dxcrm_emp_staff' );
}

/**
 * Employer Skill taxonomy.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_SKILL_TAXONOMY' ) ) {
	define( 'DX_CRM_SKILL_TAXONOMY','dxcrm_emp_skill' );
}

/**
 * Customer user role
 *
 * @package CRM System
 * @since 1.0.0
*/
if( ! defined( 'DX_CRM_CUSTOMER_ROLE' ) ) {
	define( 'DX_CRM_CUSTOMER_ROLE', 'dxcrm_sales_customer' );
}

/**
 * Plugin dashboard menu slug
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_MENU' ) ) {
	define( 'DX_CRM_MENU','dxcrm_system' );
}

/**
 * Plugin menu capability
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'dxcrmlevel' ) ) {
	define( 'dxcrmlevel', 'manage_options' );
}

/**
 * Metabox files include url
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_METABOX_URL' ) ) {
	define( 'DX_CRM_METABOX_URL', DX_CRM_URL . 'includes/meta-boxes' );
}

/**
 * Report date format
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_DATE_META_FORMAT' ) ) {
	define( 'DX_DATE_META_FORMAT', 'M d, Y' );
}

/**
 * Plugin i18n. Not recommended. Only for version compatibility
 * @TODO: Depreciate this on next version release
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_TEXT_DOMAIN' ) ) {
	define( 'DX_CRM_TEXT_DOMAIN', 'dxcrm' );
}

/**
 * Campaign post type. DEPRECIATED.
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_POST_TYPE_CAMPAIGN' ) ) {
	define( 'DX_CRM_POST_TYPE_CAMPAIGN', 'dxcrm_campaign' ); 
}

/**
 * Plugin document management upload directory
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_DM_UPLOADS_DIRECTORY' ) ) {
	define( 'DX_CRM_DM_UPLOADS_DIRECTORY', 'dxcrm_dm_uploads' ); 
}

/**
 * Dashboard menu slug
 *
 * @package CRM System
 * @since 1.0.0
*/
if( !defined( 'DX_CRM_DASHBOARD' ) ) {
	define( 'DX_CRM_DASHBOARD', 'dxcrm_dashboard' ); 
}