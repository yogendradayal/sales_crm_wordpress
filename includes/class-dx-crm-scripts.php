<?php
/**
 * Script Class
 *
 * Handles script functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Script Class
 *
 * Handles all admin functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */
class Dx_Crm_Scripts{
	
	function __construct(){
		
	}
	
	/**
	 * Enqueue Scripts for adminside
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_admin_scripts( $hook ) {
		global  $post;
		
		// register styles
		wp_register_style('dx-crm-admin', DX_CRM_ASSETS_URL . '/css/min/dx-crm-admin.min.css' );
		wp_register_style('dx-crm-progress_rating', DX_CRM_ASSETS_URL . '/css/min/progress-rating.min.css' );
		wp_enqueue_style( 'dx-crm-admin' );
		// register scripts
		wp_register_script('dx-crm-admin-jquery-barrating', DX_CRM_ASSETS_URL.'/scripts/min/jquery.barrating.min.js',array('jquery') , DX_CRM_VERSION, false );
		wp_register_script(	'dx-crm-admin-script',  DX_CRM_ASSETS_URL.'/scripts/min/dx-crm-admin.min.js',array(), DX_CRM_VERSION,true);
		wp_register_script('dx-crm-admin-livequery-script', DX_CRM_ASSETS_URL.'/scripts/min/dx-crm-livequery.min.js',array('jquery') , DX_CRM_VERSION, true );
		wp_register_script('timepicker-addon',DX_CRM_ASSETS_URL . '/scripts/datetimepicker/jquery-date-timepicker-addon.js',array( 'datepicker-slider'),DX_CRM_VERSION,true);
		wp_register_script('datepicker-slider', DX_CRM_ASSETS_URL . '/scripts/datetimepicker/jquery-ui-slider-Access.js', array(), DX_CRM_VERSION );
		
		// We only need to hide pop up on edit-tags.php
		// Add CSS file only, no need for jquery
		if( ( !empty( $post ) ) && ( $post->post_type == DX_CRM_POST_TYPE_CUSTOMERS || $post->post_type == DX_CRM_POST_TYPE_PROJECTS 
			|| $post->post_type == DX_CRM_POST_TYPE_MILESTONES || $post->post_type == DX_CRM_POST_TYPE_TIMESHEETS || $post->post_type == DX_CRM_POST_TYPE_COMPANY
			|| $post->post_type == DX_CRM_POST_TYPE_STAFF || $post->post_type == DX_CRM_POST_TYPE_COMPANY_EXPENSES ) || $hook == 'edit-tags.php' ){
			wp_enqueue_style( 'dx-crm-admin' );
		}
		//Check CRM System post type
		if( ( !empty( $post ) ) && ( $post->post_type == DX_CRM_POST_TYPE_CUSTOMERS || $post->post_type == DX_CRM_POST_TYPE_PROJECTS 
			|| $post->post_type == DX_CRM_POST_TYPE_MILESTONES || $post->post_type == DX_CRM_POST_TYPE_TIMESHEETS || $post->post_type == DX_CRM_POST_TYPE_COMPANY
			|| $post->post_type == DX_CRM_POST_TYPE_STAFF || $post->post_type == DX_CRM_POST_TYPE_COMPANY_EXPENSES )
			|| $hook == 'user-new.php' || $hook == 'user-edit.php' || $hook == 'dxcrm_doc_mngr_page_dx-crm-setting' || $hook == 'toplevel_page_dxcrm_dashboard' || $hook == 'edit.php' ) {

			wp_enqueue_style( 'dx-crm-admin' );
			wp_enqueue_style( 'dx-crm-progress_rating' );		
				
		    // Enqueue for datepicker
			wp_enqueue_style( 'dx-crm-meta-jquery-ui-css', DX_CRM_ASSETS_URL . '/css/datetimepicker/date-time-picker.css', array(), DX_CRM_VERSION );
			
			wp_enqueue_script('dx-crm-admin-jquery-barrating');
			
			// Enqueue for datepicker
			wp_enqueue_script( array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
			
			//wp_deregister_script( 'datepicker-slider' );
			wp_enqueue_script( 'datepicker-slider' );
			wp_enqueue_script('dx-crm-admin-livequery-script');
			//wp_deregister_script( 'timepicker-addon' );
			wp_enqueue_script('timepicker-addon');
			
			wp_enqueue_script( 'dx-crm-admin-script' );
			wp_localize_script( 'dx-crm-admin-script', 
				'CrmSystem', 
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) )
				)
			);

		}
		
		//Table Row Sorting JS
		wp_register_script(	'dx-crm-sort-script',  DX_CRM_ASSETS_URL.'/scripts/min/dx-crm-sort.min.js',array(), DX_CRM_VERSION, true );
		wp_enqueue_script( 'dx-crm-sort-script' );
	}
	
	/* Add Hook Script	*/
	public function dx_crm_report_script($hook){
		 if($hook == 'sales-crm_page_dx-crm-stat-setting'){

			// Enqueue for DataTables			
			wp_enqueue_style( 'jquery-ui-datatables', DX_CRM_ASSETS_URL . '/css/min/jquery-ui.min.css', array(), '1.11.3' ); // JQUERY UI CSS
			wp_enqueue_style( 'jquery-datatables', DX_CRM_ASSETS_URL . '/css/min/dataTables.jqueryui.min.css' ); // STYLE
			
			wp_register_script( 'jquery-datatables', DX_CRM_ASSETS_URL . '/scripts/min/jquery.dataTables.min.js', array() ); // JS LIB
			wp_register_script( 'jquery-ui-datatables', DX_CRM_ASSETS_URL . '/scripts/min/dataTables.jqueryui.min.js', array(), '1.11.3' ); // JQUERY UI JS
			
			wp_register_script('dx-crm-admin-livequery-script', DX_CRM_ASSETS_URL.'/scripts/min/dx-crm-livequery.min.js',array('jquery') , null, true );

			wp_enqueue_script( 'jquery-datatables' );
			wp_enqueue_script( 'jquery-ui-datatables' );
			wp_enqueue_script( 'dx-crm-admin-livequery-script' );
			
			// Enqueue for datepicker
			wp_enqueue_style( 'dx-crm-meta-jquery-ui-css', DX_CRM_ASSETS_URL . '/css/datetimepicker/date-time-picker.css', array(), DX_CRM_VERSION );
			
			// Enqueue for datepicker
			wp_enqueue_script( 'dx-crm-custom-js', DX_CRM_ASSETS_URL . '/scripts/min/custom.min.js', array(), DX_CRM_VERSION );
			
			// Enqueue for datepicker
			wp_enqueue_script( array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
		} 
	}
	
	/* Add Hook Script	*/
	public function dx_crm_dashboard_script($hook){
		if($hook == 'toplevel_page_dxcrm_dashboard'){
			
			wp_register_style('dx-crm-dashboard', DX_CRM_ASSETS_URL . '/css/min/dx-crm-dashboard.min.css', array(), DX_CRM_VERSION );
			wp_enqueue_style( 'dx-crm-dashboard' );
			
			wp_register_style('dx-crm-dashboard-bootstrap', DX_CRM_ASSETS_URL . '/css/min/bootstrap.min.css', array(), DX_CRM_VERSION );
			wp_enqueue_style( 'dx-crm-dashboard-bootstrap' );
			
			wp_register_style('dx-crm-dashboard-font-awesome', DX_CRM_ASSETS_URL . '/css/min/font-awesome.css', array(), DX_CRM_VERSION );
			wp_enqueue_style( 'dx-crm-dashboard-font-awesome' );
		}
	}

	/* Add Hook Script for dashboard page toggle scripts	*/
	public function crm_dashboard_postboxes_toggle_scripts() {
		
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		
		echo '<script type="text/javascript">
			
			//<![CDATA[

			jQuery(document).ready( function($) {

				$(".if-js-closed").removeClass("if-js-closed").addClass("closed");
				
				postboxes.add_postbox_toggles( "admin_page_crm_dashboard" );
				
			});

			//]]>

		</script>';	
	}
	
	/* Add Hook Script for settings page toggle scripts	*/
	public function crm_settings_postboxes_toggle_scripts() {
		
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		
		echo '<script type="text/javascript">
			
			//<![CDATA[

			jQuery(document).ready( function($) {

				$(".if-js-closed").removeClass("if-js-closed").addClass("closed");
				
				postboxes.add_postbox_toggles( "admin_page_crm_settings" );
				
			});

			//]]>

		</script>';	
	}
	
	/**
	 * Centralized all script and style for CRM templates
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_template_scripts(){
		/**
		 * Get the global $post variable
		 * We're going to need the post ID
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		global $post;
		
		/**
		 * This fix when you're on taxonomies/dashboard/comments etc
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		if( empty ( $post ) ){
			return;
		}
		
		/**
		 * Get the template data
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		$template_file = get_post_meta( $post->ID, '_wp_page_template', true );
		
		/**
		 * Proceed only if crm-customer-template.php, crm-company-template.php or crm-project-template.php
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		if( $template_file == 'crm-customer-template.php' || $template_file == 'crm-company-template.php' || $template_file == 'crm-project-template.php' ){
			
			// Register style
			wp_register_style( 'dx-crm-tmp', DX_CRM_INC_URL.'/css/dx-crm-templates.css', array(), DX_CRM_VERSION );  
			wp_register_style( 'dx-crm-datepicker-css', DX_CRM_INC_URL.'/css/datetimepicker/date-time-picker.css', array(), DX_CRM_VERSION ); 
			
			// Enqueue style
			wp_enqueue_style( 'dx-crm-tmp');
			wp_enqueue_style( 'dx-crm-datepicker-css' );
			wp_enqueue_style( 'dx-crm-chosen-css', DX_CRM_INC_URL . '/js/chosen/chosen.min.css', array(), DX_CRM_VERSION );
			wp_enqueue_style( 'dx-crm-chosen-custom-css', DX_CRM_INC_URL . '/js/chosen/chosen-custom.css', array(), DX_CRM_VERSION );
			
			// Register JS
			wp_register_script( 'dx-js-form-validator', DX_CRM_ASSETS_URL . '/scripts/min/jquery.form-validator.min.js', array( 'jquery' ), '2.2.8' );
			wp_register_script( 'dx-crm-template-js', DX_CRM_INC_URL.'/js/dx-crm-template.min.js',array(), DX_CRM_VERSION, true );  
			
			// Enqueue JS
			wp_enqueue_script( 'jquery-ui-datepicker' );			
			wp_enqueue_script( 'dx-crm-chosen-js', DX_CRM_INC_URL . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), DX_CRM_VERSION, true );  
			wp_enqueue_script( 'dx-crm-chosenajax-js', DX_CRM_INC_URL . '/js/chosen/ajax-chosen.jquery.js', array( 'jquery' ), DX_CRM_VERSION, true );   
			wp_enqueue_script( 'dx-js-form-validator' );
			wp_enqueue_script( 'dx-crm-template-js' );
			
			// Localize script
			wp_localize_script( 
				'dx-crm-template-js', 
				'CrmSystem', 
				array(
					'ajax_url' => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) )
				)
			);
			
			/**
			 * Enqueue scripts for company but not on project and customer
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			if( $template_file == 'crm-company-template.php' ){				
				wp_enqueue_script( 'jquery-form', array( 'jquery' ), false, true );
			}
			
		}
	}
	
	/**
	 * Adding Hooks
	 *
	 * Adding proper hooks for the script class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function include_jquery_form_plugin(){
		/**
		 * Get the global $post variable
		 * We're going to need the post ID
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		global $post;
		
		/**
		 * This fix when you're on taxonomies/dashboard/comments etc
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		if( empty ( $post ) ){
			return;
		}
		
		/**
		 * Get the template data
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		$template_file = get_post_meta( $post->ID, '_wp_page_template', true );
		
		/**
		 * For company template only
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		if( $template_file == 'crm-company-template.php' ){	
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-form', array( 'jquery' ), false, true );
		}
	}
	
	/**
	 * We're going to style the post type views
	 * on front page for Custome, Project and Company
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function post_view_script(){
		
		/**
		 * Get global post. We need to use it for get_post_type()
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		global $post;
		
		/**
		 * Get current page post type. Allowed: crm_ customer, project and company
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		$post_type  = get_post_type( $post );
		
		/**
		 * Enqueue script only if 'administrator'
		 * and it should only for allowed post types
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( current_user_can( 'administrator' ) ) {
			if( $post_type == DX_CRM_POST_TYPE_PROJECTS || $post_type == DX_CRM_POST_TYPE_COMPANY || $post_type == DX_CRM_POST_TYPE_CUSTOMERS ){
				wp_register_style( 'dx-crm-post-view', DX_CRM_ASSETS_URL . '/css/min/dx-crm-post-view.min.css', array(), DX_CRM_VERSION );
				wp_enqueue_style( 'dx-crm-post-view' ); 
			}
		}		
	}
		
	/**
	 * Adding Hooks
	 *
	 * Adding proper hooks for the script class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function add_hooks() {

		//script for admin side
		add_action( 'admin_enqueue_scripts', array( $this, 'dx_crm_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'dx_crm_report_script' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'dx_crm_dashboard_script' ) );
		
		/**
		 * We're using 'wp_enqueue_scripts' when enqueuing the srcipt
		 * on project, customer, company page template
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'dx_crm_template_scripts' ) );
		
		/**
		 * 'wp_print_scripts' for company form and jquery
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		add_action( 'wp_print_scripts', array( $this, 'include_jquery_form_plugin' ) );
		
		/**
		 * Enqueue scripts, css on custom post type view
		 *
		 * @package CRM System
		 * @since 1.0.0
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'post_view_script' ) );
	}
}
?>
