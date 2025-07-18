<?php
/**
 * Admin Class
 *
 * Handles all admin functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

//session start for google contact
session_start();

/**
 * Admin Class
 *
 * Handles all admin functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */
class Dx_Crm_Admin{
	
	public $model;
	
	function __construct(){
		
		global $dx_crm_model, $dx_crm_scripts;
		
		$this->model = $dx_crm_model;
		$this->scripts = $dx_crm_scripts;		
	}
	
	/**
	 * Display Main admin menu
	 * 	
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_dashboard_admin_menu() {	
		$dx_crm_dashboard_settings_menu = add_menu_page( __( 'Sales CRM Dashboard', 'dxcrm' ), __( 'Sales CRM','dxcrm' ), 'manage_crm', DX_CRM_DASHBOARD, array( $this, 'dx_crm_dashboard_settings' ), DX_CRM_IMG_URL . 'Sales_CRM_hover.png', 25 );
		add_action( "admin_head-$dx_crm_dashboard_settings_menu", array( $this->scripts, 'crm_dashboard_postboxes_toggle_scripts' ) );
		add_action( "admin_footer-$dx_crm_dashboard_settings_menu", array($this, 'dx_crm_dashboard_popup' ) );		
	}
	
	/**
	 * Add Roadmap Tracking Menu
	 * 	
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_roadmap_tracking_menu(){
		// Restrict Activity Log accessibility to administrator only
		if( current_user_can( 'administrator' ) ){		
			add_submenu_page(
				DX_CRM_DASHBOARD, 
				__( 'Activity Log', 'dxcrm' ),
				__( 'Activity Log', 'dxcrm' ),
				'manage_options',
				'dx-crm-activity-log',
				array( $this, 'dx_crm_roadmap_tracking' )
			);
		}
	}
	
	/**
	 * Display Roadmap Tracking page content
	 * 	
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_roadmap_tracking(){
		if( is_admin() ){
			require_once( DX_CRM_ADMIN_DIR . '/forms/crm-activity-log.php' );
		} else {
			_e( 'You are not permitted to access this page!.', 'dxcrm' );
		}
	}
	
	/**
	 * Display Roadmap tracking settings
	 * 	
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_dashboard_settings() {		
		require_once( DX_CRM_ADMIN_DIR . '/forms/crm-dashboard-settings.php' );		
	}
	
	/**
	 * Display the activation message
	 * 	
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_notice_box() {
		if(isset($_GET['msg_hide']) && !empty($_GET['msg_hide'])){
			delete_option('dx_crm_activation_message');
		}
			
		if (get_option('dx_crm_activation_message', false)) {
		?><div class="update-nag is-dismissible">
		        <div>
		        	<?php
		        	_e( sprintf( 'Welcome to DX Sales CRM. Begin adding new data by heading to <a href="%s">DX Sales CRM Dashboard</a>', admin_url( 'admin.php?page=' . DX_CRM_DASHBOARD ) ), 'dxcrm' ); 
					?>
		        </div>
		    </div><?php
		}
	}
	
	/**
	 * action function for show popup in Dashbord page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_dashboard_popup() {
		
		// Add New Company HTML file Include
		include_once( DX_CRM_ADMIN_DIR . '/forms/crm-add-company-popup.php' );
		
		// Add New Customer HTML file Include
		include_once( DX_CRM_ADMIN_DIR . '/forms/crm-add-customer-popup.php' );
		
		// Add New Project HTML file Include
		include_once( DX_CRM_ADMIN_DIR . '/forms/crm-add-project-popup.php' );

	}
	
	/**
	 * Display User Profile Meta
	 * 
	 * Handles to display user profile meta
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function user_profile_metabox( $user ) {
		
		global $current_user;
			
		$user_group_id = '';		
		$current_user_role[] = '';	
		
		$user_role = $current_user->roles;	
		
		if( isset( $user->ID ) && !empty( $user->ID ) ) { //Check user id			
			
			$user_data 			= get_userdata( $user->ID );			
			$current_user_role	= $user_data->roles;
		}	
		
		/*$user_role			= $current_user->roles;
		$user_data 			= get_userdata( $user->ID );		
		$current_user_role	= $user_data->roles;*/
		
		if( isset( $user->ID ) && !empty( $user->ID ) ) { //Check user id
			
			$user_group_id	= get_user_meta( $user->ID, DX_CRM_META_PREFIX.'user_group_id', true );
		}
		$customers_list	= $this->model->get_customers_dropdown( $user_group_id );
		
		// require_once(  DX_CRM_ADMIN_DIR . '/forms/crm-user-profile-metabox.php' );	
	}
	
	/**
	 * Save User Profile Meta
	 * 
	 * Handles to save user profile meta
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function save_user_profile_metabox( $user_id ) {
		
		$group_id = ( isset($_POST['_crm_user_group_id'] ) && !empty( $_POST['_crm_user_group_id'] ) ) ? (int)$_POST['_crm_user_group_id'] : null;
		
		if( isset($group_id) && !empty($group_id) ) {
			update_user_meta( $user_id, DX_CRM_META_PREFIX . 'user_group_id', $group_id);
		}
		
	}
	
	/**
	 * Add New Column to Company listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_company_columns($new_columns) {
		
 		unset($new_columns['author']);
		unset($new_columns['taxonomy-crm_emp_skill']);
		unset($new_columns['date']);
		unset($new_columns['comments']);
		
		$new_columns['company_customer'] = __('Customer','dxcrm');
		$new_columns['company_type'] = __('Type','dxcrm');
		$new_columns['company_industry']   = __('Industry','dxcrm');
		$new_columns['date']	 		= _x('Date','column name','dxcrm');
		
		return $new_columns;
	}
	
	/**
	 * Add New Column to Customer listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_customer_columns($new_columns) {
 		
		unset($new_columns['author']);
		unset($new_columns['taxonomy-crm_emp_skill']);
		unset($new_columns['date']);
		unset($new_columns['comments']);

		$new_columns['customer_company'] = __('Company','dxcrm');
		$new_columns['customer_project'] = __('Projects','dxcrm');
		
		/** We add campaign column if DX Campaign is installed */
		if( defined( 'DX_CRM_CAMPAIGN' ) ){
			$new_columns['customer_campaign'] = __('Campaign','dxcrm');		
		}
		
		$new_columns['customer_contact'] = __('Contacts','dxcrm');
		$new_columns['customer_email']   = __('Email','dxcrm');
		$new_columns['customer_phone']   = __('Phone','dxcrm');
		$new_columns['date']	 		= _x('Date','column name','dxcrm');
		//$new_columns['comments']	 	= _x('<span title="Comments" class="vers comment-grey-bubble"><span class="screen-reader-text">Comments</span></span>','column name','dxcrm');
		
		return $new_columns;
	}
	
	/**
	 * Add New Column to Project listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_project_columns($new_columns) {
 		
		unset($new_columns['author']);
	 	unset($new_columns['date']);
		unset($new_columns['comments']);
		
		if ( ! $this->is_customer() ) {
			$new_columns['project_status']	= __('Quote Status','dxcrm');
		}

		$new_columns['pro_customer'] 	= __('Customer','dxcrm');
		$new_columns['pro_companies'] 	= __('Companies','dxcrm');
		$new_columns['author']			= _x('Author','column name','dxcrm');
		$new_columns['date']	 		= _x('Date','column name','dxcrm');
		
		return $new_columns;
	}
	
	/**
	 * Add New Column to Quote listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_quote_columns($new_columns) {
 		
		unset($new_columns['author']);
	 	unset($new_columns['date']);
		
		$new_columns['quote_status']	= __('Project Status','dxcrm');
		$new_columns['author']		 	= _x('Author','column name','dxcrm');
		$new_columns['date']	 	 	= _x('Date','column name','dxcrm');
		
		return $new_columns;
	}
	
	/**
	 * Add new columns to Staff listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_staff_columns($new_columns) {
 		
		unset($new_columns['author']);
	 	unset($new_columns['date']);
		unset($new_columns['comments']);
		
		$new_columns['staff_contact']		= __('Contact','dxcrm');
		$new_columns['staff_skills'] 		= __('Skills','dxcrm');
		$new_columns['staff_availability']	= __('Availability','dxcrm');
		$new_columns['staff_hourly_rate']	= __('Hourly Rate','dxcrm');
		
		return $new_columns;
	}
	
	/**
	 * Add New Column to Timesheet listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_timesheet_columns($new_columns) {
 		
		unset($new_columns['author']);
	 	unset($new_columns['date']);
		unset($new_columns['comments']);
		
		$new_columns['timesheet_employee_data']	= __('Employee Data','dxcrm');
		$new_columns['timesheet_start_time'] 	= __('Start Time','dxcrm');
		$new_columns['timesheet_end_time'] 		= __('End Time','dxcrm');
		$new_columns['timesheet_date']			= __('Date','dxcrm');
		
		return $new_columns;
	}
	
	
	/**
	 * Add new columns to Milestone listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_milestone_columns($new_columns) {
 		
		unset($new_columns['author']);
	 	unset($new_columns['date']);
		unset($new_columns['comments']);
		
		$new_columns['milestone_project_referred']	= __('Project','dxcrm');
		$new_columns['milestone_start_date'] 		= __('Start Date','dxcrm');
		$new_columns['milestone_end_date'] 			= __('End Date','dxcrm');
		$new_columns['milestone_extra_cost']		= _x('Extra Cost','column name','dxcrm');
		
		return $new_columns;
	}
	
	/**
	 * Add new columns to Document Management listing page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function add_new_crm_document_columns($new_columns) {
 		
		unset($new_columns['author']);
		unset($new_columns['date']);
		unset($new_columns['comments']);
		
		$new_columns['document_view_link']		= __('View Link','dxcrm');
		$new_columns['document_uploader_name'] 	= __('Uploader Name','dxcrm');
		$new_columns['document_date'] 			= __('Date','dxcrm');
		
		return $new_columns;
	}	
	
	/**
	 * Custom column
	 *
	 * Handles the custom columns to company listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_company($column_name,$post_id) {
		
		global $wpdb,$post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ($column_name) {
				
			case 'company_customer' :
				$post_id = get_post_meta($post_id, $prefix . 'joined_customer');
				$c_title = array();
				if( ! empty ( $post_id ) ){
					foreach( $post_id as $id ){
						$get_title = get_the_title( $id );
						// Display only if has title
						if( ! empty ( $get_title ) ){
							$c_title[] = $get_title;
						}
					}
					echo implode(', ', $c_title);
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}				
			break;	
			case 'company_type' :
				$company_type = get_post_meta( $post_id, $prefix . 'company_type', true );
				if( ! empty ( $company_type ) ){
					echo $company_type;
				} else {
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;	
			case 'company_industry' :
				$_dx_crm_company_industry = get_post_meta($post_id, $prefix . 'company_industry', true);
				if( ! empty ( $_dx_crm_company_industry ) ){
					echo $_dx_crm_company_industry;
				} else {
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;				
		}
	}
	
	/**
	 * Custom column
	 *
	 * Handles the custom columns to customer listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_customer($column_name,$post_id) {
		
		global $wpdb,$post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ($column_name) {
				
			case 'customer_company' :
				$post_id = get_post_meta( $post_id, $prefix. 'joined_company');
				$c_title = array();
				if( ! empty( $post_id ) ) {
					foreach($post_id as $id){
						$c_title[] = get_the_title( $id );
					}
					echo implode(', ', $c_title);
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}				
			break;	
			case 'customer_project':
				$post_id = get_post_meta($post_id, $prefix. 'joined_project');
				$c_title = array();
				if( ! empty ( $post_id ) ){
					foreach($post_id as $id){
						$c_title[] = get_the_title( $id );
					}
					echo implode(', ', $c_title);
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}				
			break;
			case 'customer_campaign' :
				$post_id = get_post_meta($post_id, $prefix. 'joined_campaigns');
				//print_r( $post_id );
				$c_title = array();
				if( ! empty( $post_id ) ){
					foreach($post_id as $id){
						$c_title[] = get_the_title( $id );
					}
					echo implode(', ', $c_title);
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;
			case 'customer_contact' :
				$contact_number = get_post_meta($post_id, $prefix. 'contact_type', true);
				if( ! empty( $contact_number ) ){
					echo $contact_number ;				
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;	
			case 'customer_email' :
				$_dx_crm_cust_email = get_post_meta($post_id, $prefix. 'cust_email', true);
				if( ! empty( $_dx_crm_cust_email ) ){
					echo $_dx_crm_cust_email;				
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;
			case 'customer_phone' :
				$_dx_crm_contact_number = get_post_meta($post_id, $prefix. 'contact_number', true);
				if( ! empty( $_dx_crm_contact_number ) ){
					echo $_dx_crm_contact_number;				
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}						
		}
	}
	
	/**
	 * Custom column on Projects Table
	 * Handles the custom columns to project listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_project($column_name,$post_id) {
		
		global $wpdb,$post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ($column_name) {
				
			case 'pro_customer' :
				$pro_assign_customer = get_post_meta( $post_id	, $prefix . 'joined_pro_customer' );
				$pro_customer_name = array();
				if( ! empty( $pro_assign_customer ) ) {					
					foreach ($pro_assign_customer as $key => $value) {
						$the_title = get_the_title($value);
						// Display only if has title
						if( ! empty ( $the_title ) ){
							$pro_customer_name[] = $the_title;
						}
					}
					echo implode(',', $pro_customer_name);
				} else {
					echo '<span class="dashicons dashicons-minus"></span>';
				}
				
			break;				
			case 'pro_companies' :
				$pro_assign_company = get_post_meta( $post_id	, $prefix . 'company_project', true );					
				if( !empty( $pro_assign_company ) ) {					
					echo get_the_title($pro_assign_company);				
				} else {
					echo '<span class="dashicons dashicons-minus"></span>';
				}		
			break;			
			case 'project_status' :				
				$quote_status = get_post_meta( $post_id	, $prefix . 'project_status', true );

				if ( $quote_status || $quote_status == 0 ) {
					$quote_status_array = apply_filters( 'dx_crm_project_status', array() );
					
					if( isset( $quote_status_array[$quote_status] ) ){
						echo $quote_status_array[$quote_status];
					}else{
						echo '<span class="dashicons dashicons-minus"></span>';
					}
				} else {
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;

		}
	}
	
	/**
	 * Custom column
	 * Handles the custom columns to Staff listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_staff($column_name,$post_id) {
		
		global $wpdb,$post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ($column_name) {
				
			case 'staff_contact' :
				$staff_phone = get_post_meta( $post_id	, $prefix . 'emp_phone', true );
				$staff_email = get_post_meta( $post_id	, $prefix . 'emp_email', true );
				$staff_contact_text = '';
				if ( isset( $staff_phone ) && ! empty( $staff_phone ) ) {
					$staff_contact_text .= '<b>Tel+: </b>' . $staff_phone;
				}
				if ( isset( $staff_email ) && ! empty( $staff_email ) ) {
					$staff_contact_text .= '<br /><b>Email: </b>' . $staff_email;
				}
				if( ! empty ( $staff_contact_text ) ){
					echo $staff_contact_text;
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;				
			case 'staff_skills' :
				$staff_skills = get_post_meta( $post_id	, $prefix . 'emp_skills', true );
				if ( ! empty ( $staff_skills ) ) {
					$staff_skill_name = '';
					$staff_categories = get_terms( DX_CRM_STAFF_TAXONOMY, array( 'hide_empty' => 0 ) );
					foreach( $staff_categories as $staff_category ) {						
						if( $staff_category->term_id == $staff_skills ) {						
							$staff_skill_name = $staff_category->name;
							break;
						}
					}
					echo $staff_skill_name;
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;				
			case 'staff_availability' :
				$staff_availability = get_post_meta( $post_id	, $prefix . 'emp_availability', true );
				if ( ! empty( $staff_availability ) ) {
					echo $staff_availability;
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;				
			case 'staff_hourly_rate' :
				$staff_hourly_rate = get_post_meta( $post_id	, $prefix . 'emp_hourly_rate', true );
				if ( !empty($staff_hourly_rate) ) {
					echo $staff_hourly_rate;
				}else{
					echo '<span class="dashicons dashicons-minus"></span>';
				}
			break;
		}
	}
	
	/**
	 * Custom column
	 *
	 * Handles the custom columns to Quote listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_quote($column_name,$post_id) {
		
		global $wpdb, $post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ( $column_name ) {
				
			case 'quote_status' :
				$quote_status_array = $this->model->crm_get_project_status();
			$quote_status = get_post_meta( $post_id	, $prefix . 'quote_status', true );
			
			$html = '<div class="input select rating-b">';
			$html .= '<select name="quote_status" class="crm_quote_status" id="quote_status_'.$post_id.'">';
				
				foreach ( $quote_status_array as $key => $status ){
					
					$selected = !empty( $quote_status ) && $quote_status == $key ? 'Selected=Selected' : '';
					
					$html .= '<option value="'.$key.'_'.$post_id.'" '.$selected.'>'.$status.'</option>';
				}
				
			$html .= '<select>';
			$html .= '</div>';
			
			echo $html;
			break;					
		}
	}
	/**
	 * Custom column
	 *
	 * Handles the custom columns to timesheet listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_timesheet($column_name,$post_id) {
		
		global $wpdb,$post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ($column_name) {
				
			case 'timesheet_employee_data' :
				$timesheet_employee_data = get_post_meta( $post_id	, $prefix . 'time_employee_data' );
				if ( !empty($timesheet_employee_data) ) {
					echo $timesheet_employee_data[0];
				}
				break;
				
			case 'timesheet_start_time' :
				$timesheet_start_time = get_post_meta( $post_id	, $prefix . 'time_start_time' );
				if ( !empty($timesheet_start_time) ) {
					echo $timesheet_start_time[0];
				}
				break;
				
			case 'timesheet_end_time' :
				$timesheet_end_time = get_post_meta( $post_id	, $prefix . 'time_end_time' );
				if ( !empty($timesheet_end_time) ) {
					echo $timesheet_end_time[0];
				}
				break;
				
			case 'timesheet_date' :
				$timesheet_date = get_post_meta( $post_id	, $prefix . 'time_date' );
				if ( !empty($timesheet_date) ) {
					echo $timesheet_date[0];
				}
				break;
				
		}
	}
	/**
	 * Custom column
	 *
	 * Handles the custom columns to Milestone listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_milestone($column_name,$post_id) {
		
		global $wpdb,$post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ($column_name) {
				
			case 'milestone_project_referred' :
				$milestone_project_referred = get_post_meta( $post_id	, $prefix . 'mile_pro_ref_to' );
				if ( isset($milestone_project_referred[0]) && !empty($milestone_project_referred[0]) ) {
					echo get_the_title ( $milestone_project_referred[0] );
				} else {
					echo '--';
				}
				break;
				
			case 'milestone_start_date' :
				$milestone_start_date = get_post_meta( $post_id	, $prefix . 'mile_start_date' );
				if ( !empty($milestone_start_date[0]) ) {
					echo date ( 'M d, Y', strtotime($milestone_start_date[0]) );
				}
				break;
				
			case 'milestone_end_date' :
				$milestone_end_date = get_post_meta( $post_id	, $prefix . 'mile_end_date' );
				if ( !empty($milestone_end_date[0]) ) {
					echo date ( 'M d, Y', strtotime($milestone_end_date[0]) );
				}
				break;
				
			case 'milestone_extra_cost' :
				$milestone_extra_cost = get_post_meta( $post_id	, $prefix . 'mile_extra_cost' );
				if ( !empty($milestone_extra_cost[0]) ) {
					echo $milestone_extra_cost[0];
				}
				break;

		}
	}
	
	/**
	 * Custom column
	 *
	 * Handles the custom columns to Document listing page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	
	public function dx_crm_manage_custom_column_document($column_name,$post_id) {
		
		global $wpdb,$post;
		
		$prefix = DX_CRM_META_PREFIX;
		
		switch ($column_name) {
				
			case 'document_view_link' :
				$document_file_attachment = get_post_meta( $post_id	, $prefix . 'document_file_upload' );
				if ( isset($document_file_attachment[0][0]) && !empty($document_file_attachment[0][0]) ) {
					$document_file_link = get_the_guid($document_file_attachment[0][0]);
					echo '<a href="'.$document_file_link.'" target="_blank" >'.$document_file_link.'</a>';
				} else {
					echo '--';
				}
				break;
				
			case 'document_uploader_name' :
				$document_author = $post->post_author;
				if ( $document_author  ) {
					echo the_author_meta( 'display_name' , $document_author );
				}
				break;
			
			case 'document_date' :
				$document_date = get_the_date($post->post_id);
				if ( $document_date  ) {
					echo $document_date;
				}
				break;
		}
	}
	
	/**
	 * Quick Edit
	 * 
	 * Handles quick edit of crm system
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 * 
	 */
	function crm_customer_quick_edit_values($column_name, $post_type) {
		
		global $wpdb,$post;
		
		if($post_type == DX_CRM_POST_TYPE_CUSTOMERS) {
			
			$prefix = DX_CRM_META_PREFIX;
			
			$args = array(
						'role' => DX_CRM_CUSTOMER_ROLE
					);
			$customer_users = get_users( $args );
			
			$html ='<fieldset class="inline-edit-col-left">
					<div class="inline-edit-col">';
				
					switch($column_name){
					
						case "customer_users"	:  
											$html.='<span class="span_title checkbox-title wpsc-quick-edit">'.__('Users', 'dxcrm' ) . '</span>
													<select class="postform" name="'.$prefix.'cust_assign_customer">
													<option value="">None</option>';
													
														$cust_assign_customer = get_post_meta( $post->ID, $prefix . 'cust_assign_customer', true );
														
														foreach ( $customer_users as $customer_user ) {
															
															if( !empty( $cust_assign_customer ) && $cust_assign_customer == $customer_user->ID ) { $selected = 'selected=selected';  } else { $selected = ''; }
															
															//$html.='<li><label><input type="checkbox" class="crm-user-check" id="crm_user_'.$customer_user->ID.'" name="'.$prefix.'cust_assign_customers[]'.'" value="'.$customer_user->ID.'" >'. ' '.$customer_user->display_name .'</label> </li>';
															$html.='<option value="'.$customer_user->ID.'" '.$selected.' >'.$customer_user->display_name.'</option>';
														}
											$html.='</select>';
											break;
						default:	
					}	 
			$html .= '</div>
		    </fieldset>';
			echo $html;
		}
	}
	
	/**
	 * Quick Edit Save
	 * 
	 * Handles quick edit save meta boxes to database
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	function crm_customer_quick_save_post_data($post_id,$post1) { // For Quick Edit
		
		$prefix = DX_CRM_META_PREFIX;
		
		if ($post1->post_type == 'revision') return; // Imp Line
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; //Imp Line When auto save run to prevent the post meta box data
		
		if(isset($post_id) && $post1->post_type == DX_CRM_POST_TYPE_CUSTOMERS && (isset($_POST['post_ID']) && $_POST['post_ID'] == $post_id)) {
			
			$cust_assign_customer = isset( $_POST[ $prefix . 'cust_assign_customer' ] ) ? $_POST[ $prefix . 'cust_assign_customer' ] : '';
			
			update_post_meta( $post_id, $prefix . 'cust_assign_customer', $cust_assign_customer );
			
		}
	}
		
	function dxcrm_set_post_data( $postdata, $submit_data ) {
		
		if( is_admin() ){
			$post_id = $submit_data['ID'];
			
			$prefix = DX_CRM_META_PREFIX;			
				
			//$cust_assign_customers = $submit_data[$prefix . 'cust_assign_customer'];
			
			$cust_assign_customers = isset( $submit_data[ $prefix . 'cust_assign_customer' ] ) ? $submit_data[ $prefix . 'cust_assign_customer' ] : '';
			
			$check_users = $this->dxcrm_check_user( $post_id, $cust_assign_customers );
			
			if( !empty( $check_users ) ) {// check if user assign to someone else or not.
				
				$user_data = get_user_by('id', $check_users);
				
				wp_die( __( 'Sorry '.$user_data->display_name.' user already assign to some.', 'dxcrm' ), null, array('back_link' => true) );
			}
		}
		return $postdata;
		
	}
	
	/**
	 * Check User
	 *
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dxcrm_check_user( $post_id ,$cust_assign_customers ) {
		
		$prefix = DX_CRM_META_PREFIX;
		
		if( !empty( $cust_assign_customers ) ){
			
			$args = array(
						//'posts_per_page'=>-1,
						'post_type' => DX_CRM_POST_TYPE_CUSTOMERS,
						'exclude'	=> $post_id,
						'meta_key'  => $prefix . 'cust_assign_customer',
						'meta_query'=>array(
											'relation' => 'AND',
											array (
												'key'     => $prefix . 'cust_assign_customer',
											)
										)
						);
			
			$assign_customer_posts = get_posts( $args );
			//$assign_user_mata_array = array();
			
			if( !empty($assign_customer_posts) ) {
				foreach ($assign_customer_posts as $assign_post) {
					
					$assign_user_mata = get_post_meta($assign_post->ID, $prefix . 'cust_assign_customer', true);
					//$assign_user_mata = (array)$assign_user_mata;
					
					//$assign_user_mata_array[$assign_post->ID] = $assign_user_mata;
					
					/*foreach ( $cust_assign_customers as $cust_assign_customer )*/ {
						if( $cust_assign_customers == $assign_user_mata ) {
							return $cust_assign_customers;
						}
					}
				}
			}
		}
		
	}
	
	
	/**
	 * Manage Admin Listing
	 *
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function manage_listing_page() {
		
		global $post_type;
		
		if( $post_type == DX_CRM_POST_TYPE_CUSTOMERS || $post_type == DX_CRM_POST_TYPE_PROJECTS || $post_type == DX_CRM_POST_TYPE_COMPANY_EXPENSES || $post_type == DX_CRM_POST_TYPE_COMPANY ){//check post type
			
			$exportcsvurl = add_query_arg( array( 
													'dx-crm-exp-csv'=>	'1',
													'crm_post_type'	=>	$post_type
												));
			echo '<span class="crm-download-csv"><a class="button" href="'.$exportcsvurl.'">Export To CSV</a></span>';		
		}
	}
	
	/**
	 * Add Custom Meta Box
	 * 
	 * Handles to add custom meta box
	 * in post and page
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function crm_custom_meta_box() {
		
		$pro_cats = get_terms( 'crm_pro_type');		
		
		$cust_post_types = array( DX_CRM_POST_TYPE_CUSTOMERS, DX_CRM_POST_TYPE_PROJECTS );
		
		// add meta box for Customers
		$pages_customer = array( DX_CRM_POST_TYPE_CUSTOMERS );
		
		foreach ( $pages_customer as $page ) {
				
			//add_meta_box( 'crm_meta_users', __( 'Users', 'dxcrm' ), array( $this, 'crm_meta_box_customer_advance' ), $page, 'side', 'default' );
		}
	}
	
	public function crm_meta_box_customer_advance() {
		
		include_once( DX_CRM_ADMIN_DIR . '/forms/crm-meta-box-customers-advance.php');	
	}
		
	/**
	 * Save Custom Meta
	 * 
	 * Handles to save custom meta
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function crm_save_meta( $post_id ) {
		
		global $post_type;
		
		$prefix = DX_CRM_META_PREFIX;
		
		$post_type_object = get_post_type_object( $post_type );
		
		// Check for which post type we need to add the meta box
		$pages = array( DX_CRM_POST_TYPE_CUSTOMERS, DX_CRM_POST_TYPE_PROJECTS, DX_CRM_POST_TYPE_COMPANY );

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )                // Check Autosave
		|| ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )        // Check Revision
		|| ( ! in_array( $post_type, $pages ) )              // Check if current post type is supported.
		//|| ( ! check_admin_referer( DX_CRM_BASENAME, 'at_dx_crm_meta_box_nonce') )      // Check nonce - Security
		|| ( ! current_user_can( $post_type_object->cap->edit_post, $post_id ) ) )       // Check permission
		{
			return $post_id;
		}
		
		if (isset($post_type) && $post_type == DX_CRM_POST_TYPE_CUSTOMERS) {
			
			
			// Update Assign company for customer
			$connected_companies = isset( $_POST[ $prefix . 'joined_company' ] ) ? $_POST[ $prefix . 'joined_company' ] : '';
			
			$old_company = get_post_meta($_POST['post_ID'],$prefix.'joined_company');
			
			/**
			 *
			 * Check if this CUSTOMER has COMPANY Assigned
			 * 
			 * IF true, DELETE then Add
			 * ELSE DELETE
			 *
			*/
			if(!empty($connected_companies)) {
				
				// DELETE OLD COMPANY
				if(!empty ( $old_company ) ){
					foreach ( $old_company as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_customer',
											$_POST['post_ID']
										);
					}
				}
				
				// ADD NEW META
				foreach ($connected_companies as $connected_company) {					
					add_post_meta( $connected_company,
									$prefix.'joined_customer',
									$_POST['post_ID']
								);
				}
				
			} else {
				
				// This CUSTOMER does not assign ANY COMPANY
				// DELETE OLD COMPANY
				if(!empty ( $old_company ) ){
					foreach ( $old_company as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_customer',
											$_POST['post_ID']
										);
					}
				}
				
			}
			
			// Update Customer Detail for project
			
			$connected_projects = isset( $_POST[ $prefix . 'joined_project' ] ) ? $_POST[ $prefix . 'joined_project' ] : '';
			
			$old_project = get_post_meta($_POST['post_ID'],$prefix.'joined_project');
			
			/**
			 *
			 * Check if this CUSTOMER has PROJECT Assigned
			 * 
			 * IF true, DELETE then Add
			 * ELSE DELETE
			 *
			*/
			if(!empty($connected_projects)) {
				
				// DELETE OLD PROJECT
				if(!empty ( $old_project ) ){
					foreach ( $old_project as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_pro_customer',
											$_POST['post_ID']
										);
					}
				}
				
				// ADD NEW META
				foreach ($connected_projects as $connected_company) {					
					add_post_meta( $connected_company,
									$prefix.'joined_pro_customer',
									$_POST['post_ID']
								);
				}
				
			} else {
				
				// This CUSTOMER does not assign ANY PROJECT
				// DELETE OLD PROJECT
				if(!empty ( $old_project ) ){
					foreach ( $old_project as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_pro_customer',
											$_POST['post_ID']
										);
					}
				}
				
			}
			
			// Update Assign Customer
			$cust_assign_customers = isset( $_POST[ $prefix . 'cust_assign_customer' ] ) ? $_POST[ $prefix . 'cust_assign_customer' ] : '';
			update_post_meta( $post_id, $prefix . 'cust_assign_customer', $cust_assign_customers );
						
		}
		
		if (isset($post_type) && $post_type == DX_CRM_POST_TYPE_COMPANY) {
			
			// Update Assign customer for company
			$connected_customers = isset( $_POST[ $prefix . 'joined_customer' ] ) ? $_POST[ $prefix . 'joined_customer' ] : '';
			
			$old_customers = get_post_meta($_POST['post_ID'],$prefix.'joined_customer');
			
			/**
			 *
			 * Check if this COMPANY has CUSTOMER Assigned
			 * 
			 * IF true, DELETE then Add
			 * ELSE DELETE
			 *
			*/
			if(!empty($connected_customers)) {
				
				// DELETE OLD CUSTOMER
				if(!empty ( $old_customers ) ){
					foreach ( $old_customers as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_company',
											$_POST['post_ID']
										);
					}
				}
				
				// ADD NEW META
				foreach ($connected_customers as $connected_company) {					
					add_post_meta( $connected_company,
									$prefix.'joined_company',
									$_POST['post_ID']
								);
				}
				
			} else {
				
				// This COMPANY does not assign ANY CUSTOMER
				// DELETE OLD CUSTOMER
				if(!empty ( $old_customers ) ){
					foreach ( $old_customers as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_company',
											$_POST['post_ID']
										);
					}
				}
				
			}
			
		}
		
		if (isset($post_type) && $post_type == DX_CRM_POST_TYPE_PROJECTS) {
			
			// Update Assign customer for company
			$connected_pro_customers = isset( $_POST[ $prefix . 'joined_pro_customer' ] ) ? $_POST[ $prefix . 'joined_pro_customer' ] : '';
			
			$old_customers = get_post_meta($_POST['post_ID'],$prefix.'joined_pro_customer');
			
			/**
			 *
			 * Check if this PROJECT has CUSTOMER Assigned
			 * 
			 * IF true, DELETE then Add
			 * ELSE DELETE
			 *
			*/
			if(!empty($connected_pro_customers)) {
				
				// DELETE OLD CUSTOMER
				if(!empty ( $old_customers ) ){
					foreach ( $old_customers as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_project',
											$_POST['post_ID']
										);
					}
				}
				
				// ADD NEW META
				foreach ($connected_pro_customers as $connected_company) {					
					add_post_meta( $connected_company,
									$prefix.'joined_project',
									$_POST['post_ID']
								);
				}
				
			} else {
				
				// This COMPANY does not assign ANY CUSTOMER
				// DELETE OLD CUSTOMER
				if(!empty ( $old_customers ) ){
					foreach ( $old_customers as $meta ){
						delete_post_meta( $meta,
											$prefix.'joined_project',
											$_POST['post_ID']
										);
					}
				}
				
			}
			
			// Update pro customer
			$pro_assign_customer = isset( $_POST[ $prefix . 'pro_assign_customer' ] ) ? $this->model->dx_crm_escape_slashes_deep( $_POST[ $prefix . 'pro_assign_customer' ] ) : '';
			update_post_meta( $post_id, $prefix . 'pro_assign_customer', $pro_assign_customer );
		
		}
		
	}
	
	/**
	 * Change Title for quote post type
	 * 
	 * Handles to Change Title for quote post type
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dxcrm_enter_title_here( $input ){
		
		global $post_type;
		
	    if ( is_admin() && DX_CRM_POST_TYPE_QUOTE == $post_type )
	        $input = __( 'Subject', 'dxcrm' );
	
	    return $input;
	}
	
	/**
	 * Change quote status
	 * 
	 * Handles to Change quote status
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function crm_change_quote_status(){

		$post_id		= isset( $_POST['post_id'] ) ? $_POST['post_id'] : '';
		$project_status	= isset( $_POST['project_status'] ) ? $_POST['project_status'] : '';

		global $dx_crm_roadmap;
		$prefix = DX_CRM_META_PREFIX;

		$summary = "";
		$old_meta = get_post_meta( $post_id, $prefix.'project_status', true );
		$project_title = get_the_title( $post_id );

		$compare = $dx_crm_roadmap->compare( $project_status, $old_meta );							
		if( !empty ( $compare ) ){
			$summary .= sprintf( 
							__( '%s has been updated into %s', 'dxcrm' ), 
							 sprintf(
							 	__( 'Project %s\'s status', 'dxcrm' ), $project_title ), 
							$project_status
						) . "\n";
		}
		if( ! empty( $summary ) ){
				$dx_crm_roadmap->record( $post_id, $summary );
		}
	
		$update_status = update_post_meta( $post_id	, $prefix . 'project_status', $project_status );
		if( $update_status )
			echo "success";
		else
			echo "fail";
		exit;
	}
	
	/**
	 * Filter By Customer
	 * 
	 * Handles to filter the data by customer
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 **/
	public function dx_crm_restrict_manage_posts() {
		
		global $post_type, $dx_crm_model, $post;
	
		$prefix = DX_CRM_META_PREFIX;
		
		if ( $post_type == DX_CRM_POST_TYPE_PROJECTS ) {
			
			$html = '';

			$filter_by_status = ( isset( $_GET[$prefix.'filter_by_status'] ) && strlen( $_GET[$prefix.'filter_by_status'] ) > 0 ) ? (int)$_GET[$prefix.'filter_by_status'] : '';


			$html .= '<select name="'.$prefix.'filter_by_status'.'" id="crm_filter_by_status">
							<option value="">Select Status</option>';
			
			$projects_status = apply_filters( 'dx_crm_project_status', array() );

			//$pro_assign_customer = get_post_meta( $post->ID, $prefix . 'pro_assign_customer', true );
				
				foreach ( $projects_status as $key => $project_status ) {
					if( $filter_by_status === (int)$key ){
						$selected = 'Selected=Selected';
					} else {
						$selected = '';
					}
					
					$html.='<option class="crm-customer-check" id="crm_filter_status_'.$key.'" value="'.$key.'" '.$selected.'>'.$project_status.'</option>';
				}
			
		
			$html .= '</select>';
			
			echo $html;
	    }
	
		if ( $post_type == DX_CRM_POST_TYPE_CUSTOMERS ) {
			
			$html = '';
			$project_id = isset( $_GET[$prefix.'filter_by_project'] ) ? $_GET[$prefix.'filter_by_project'] : '';
			$html .= '<select name="'.$prefix.'filter_by_project'.'" id="crm_filter_by_project">
							<option value="">Select a Project</option>';
			
			$args = array(
							'post_type'=> DX_CRM_POST_TYPE_PROJECTS,
						);
						
			$projects = get_posts( $args );
				
				//$pro_assign_customer = get_post_meta( $post->ID, $prefix . 'pro_assign_customer', true );
				
				foreach ( $projects as $project ) {
				
					if( $project_id == $project->ID ){
						$selected = 'Selected=Selected';
					} else {
						$selected = '';
					}	
					
					$html.='<option class="crm-project-check" id="crm_filter_project_'.$project->ID.'" value="'.$project->ID.'" '.$selected.'>'.$project->post_title .'</option>';
				}
			
		
			$html .= '</select>';
			
			echo $html;
	    }
	}
	
	/**
	 * Customer wise Search
	 * 
	 * Handles to show customer wise search
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 **/
	public function dx_crm_project_search( $query ) {
		
		global $wpdb;
		
		$prefix = DX_CRM_META_PREFIX;
		
		if ( isset( $_GET[$prefix.'filter_by_project'] ) && !empty( $_GET[$prefix.'filter_by_project'] ) ) {
	
			$project_id = $_GET[$prefix.'filter_by_project'];
			$connected_projects = get_post_meta($project_id, $prefix.'joined_pro_customer');
			
			$connected_projects = !empty($connected_projects) ? $connected_projects : array(-1) ;
	    	
		    if ( $query->is_main_query() ) {
	    	
		        $query->set( 'post__in', $connected_projects );       
		    }
		}
		
		if ( isset( $_GET[$prefix.'filter_by_status'] ) && strlen( $_GET[$prefix.'filter_by_status'] ) > 0 ) {
	
			$filter_by_status = $_GET[$prefix.'filter_by_status'];
			
		    if ( $query->is_main_query() ) {
	    	
		        $query->set( 'meta_key', $prefix . 'project_status' );
		        $query->set( 'meta_value', $filter_by_status );
		    }
		}
		
		if ( isset( $_GET[$prefix.'filter_by_company_type'] ) && !empty( $_GET[$prefix.'filter_by_company_type'] ) ) {
	
			$filter_by_company_type = $_GET[$prefix.'filter_by_company_type'];
			
		    if ( $query->is_main_query() ) {
		        $query->set( 'meta_value', $filter_by_company_type );
		    }
		}
	}
	/**
	 * Adding Submenu
	 *
	 * Adding proper hooks for the admin class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_stat_admin_menu(){
		$settings_page = add_submenu_page( DX_CRM_DASHBOARD, __( 'Report','dxcrm' ), __( 'Report','dxcrm' ), 'manage_crm', 'dx-crm-stat-setting', array( $this, 'dx_crm_stat_admin_settings' ) ); // add setting page
		
		
	}
	/**
	 * Adding Stat report page template
	 *
	 * Adding proper hooks for the admin class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_stat_admin_settings(){
		
		require_once( DX_CRM_ADMIN_DIR . '/forms/crm-stat-report.php' );
	}
	
	/**
	 * Redirect when plugin is activated
	 *	
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_plugin_activation(){
		
		if (get_option('dx_crm_do_activation_redirect', false)) {	        
			delete_option('dx_crm_do_activation_redirect');			
			add_option('dx_crm_activation_message', true);
	        wp_redirect('admin.php?page=' . DX_CRM_DASHBOARD );
	        exit;
	    }
	}
	
	/**
	 * Remove screen option from admin header for customers and companies
	 *	
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_in_admin_header() {
		
	    global $wp_meta_boxes, $post_type;
		
		if ( $post_type == DX_CRM_POST_TYPE_CUSTOMERS || $post_type == DX_CRM_POST_TYPE_COMPANY 
			|| $post_type == DX_CRM_POST_TYPE_PROJECTS || $post_type == DX_CRM_POST_TYPE_ROADMAP
			|| $post_type == DX_CRM_POST_TYPE_MILESTONES || $post_type == DX_CRM_POST_TYPE_TIMESHEETS 
			|| $post_type == DX_CRM_POST_TYPE_DOC_MNGR || $post_type == DX_CRM_POST_TYPE_STAFF
			|| $post_type == DX_CRM_POST_TYPE_COMPANY_EXPENSES ) {
		
			if(isset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['postexcerpt']) && !empty($wp_meta_boxes[get_current_screen()->id]['normal']['core']['postexcerpt'])) {

		    	unset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['postexcerpt']);
		    }
				
			if(isset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['commentstatusdiv']) && !empty($wp_meta_boxes[get_current_screen()->id]['normal']['core']['commentstatusdiv'])) {
	
		    	unset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['commentstatusdiv']);
		    }
		   
		    if(isset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['authordiv']) && !empty($wp_meta_boxes[get_current_screen()->id]['normal']['core']['authordiv'])) {
	
		    	unset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['authordiv']);
		    }
		    
		    if(isset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['commentsdiv']) && !empty($wp_meta_boxes[get_current_screen()->id]['normal']['core']['commentsdiv'])) {
	
		    	unset($wp_meta_boxes[get_current_screen()->id]['normal']['core']['commentsdiv']);
		    }
	    }
	}
  	
	/**
	 * Adding Submenu
	 *
	 * Adding proper hooks for the admin class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_settings() {		
		$dx_crm_settings = add_submenu_page( DX_CRM_DASHBOARD, __( 'Settings','dxcrm' ), __( 'Settings','dxcrm' ), 'manage_options', 'dx-crm-setting', array( $this, 'dx_crm_admin_settings' ) ); // add setting page
		add_action( "admin_head-$dx_crm_settings", array( $this->scripts, 'crm_settings_postboxes_toggle_scripts' ) );
	}
	
	/**
	 * Adding Country Settings Page
	 *
	 * Adding proper hooks for the admin class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_admin_settings(){		
		require_once( DX_CRM_ADMIN_DIR . '/forms/crm-settings.php' );
	}
	
	/**
	 * Register Settings
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */

	public function dx_crm_admin_init() {		
		register_setting( 'dx_crm_plugin_options', 'dx_crm_options', array($this, 'dx_crm_validate_options') );
	}

	public function dx_crm_validate_options( $input ) {
			
		// sanitize text input (strip html tags, and escape characters)
		$input = apply_filters('crm_validate_options', $input);
		
		return $input;
	}
	
	/**
	 * Filter by Company Type
	 *
	 *
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_company_type_filter()
	{
		global $post_type, $dx_crm_model, $post;
	
		$prefix = DX_CRM_META_PREFIX;
	
		if ( $post_type == DX_CRM_POST_TYPE_COMPANY ) {
			
			$html = '';
			$project_id = isset( $_GET[$prefix.'filter_by_company_type'] ) ? $_GET[$prefix.'filter_by_company_type'] : '';
			$html .= '<select name="'.$prefix.'filter_by_company_type'.'" id="crm_filter_by_company_type">
							<option value="">Select a Type</option>';
			
			$company_type = array( 
									'CUSTOMER'		=> __('Client', 'dxcrm'),
									'PARTNER'		=> __('Partner', 'dxcrm'),									
									'PROSPECT'		=> __('Prospect', 'dxcrm'),									
					  			);
				
				foreach ( $company_type as $company => $company_label) {
				
					if( $project_id == $company ){
						$selected = 'Selected=Selected';
					} else {
						$selected = '';
					}	
					
					$html.='<option class="crm-project-check" id="crm_filter_by_company_type_'.$company.'" value="'.$company.'" '.$selected.'>'.$company_label.'</option>';
				}
			
		
			$html .= '</select>';
			
			echo $html;
	    }
	}
	
	/**
	 * action function for show popup
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_add_company_popup() {
		
		global $post_type;
		
		if ( $post_type == DX_CRM_POST_TYPE_CUSTOMERS ) {
		
			include( DX_CRM_ADMIN_DIR . '/forms/crm-add-company-popup.php' ); // Including HTML  file
		}
	}
	
	/**
	 * ajax action function for add new company
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_ajax_add_company() {
	
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		$company_title = isset( $_POST['company_title'] ) ? $_POST['company_title'] : '';
		
		if(!empty($company_title)){
			
			$post_arr 		= array(
							  'post_title'    => $company_title,
							  'post_status'   => 'publish',
							  'post_author'   => 1,
							  'post_type'     => DX_CRM_POST_TYPE_COMPANY,
			);
			
			$post_id = wp_insert_post( $post_arr );
			
			if( !empty( $post_id ) ){
				
				$add_company = array( "success" => "1", 'post_id' => $post_id, 'company_title' => $company_title );
				echo json_encode( $add_company );
				die;
			}			
		}
		
		exit;
	}
	
	/**
	 * action function for show popup
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_add_project_popup() {
		
		global $post_type;
		
		if ( $post_type == DX_CRM_POST_TYPE_CUSTOMERS ) {
		
			include( DX_CRM_ADMIN_DIR . '/forms/crm-add-project-popup.php' ); // Including HTML  file					
		}
		
	}
	
	/**
	 * action function for show popup
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_add_project_type_popup() {
		
		global $post_type;
		
		if ( $post_type == DX_CRM_POST_TYPE_CUSTOMERS ) {
		
			include( DX_CRM_ADMIN_DIR . '/forms/crm-add-project-type-popup.php' ); // Including HTML  file					
		}
		
	}
	
	/**
	 * ajax action function for add new project
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_ajax_add_project() {
		
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		$title = isset( $_POST['title'] ) ? $_POST['title'] : '';
		
		if(!empty($title)){
			
			$post_arr 		= array(
							  'post_title'    => $title,
							  'post_status'   => 'publish',
							  'post_author'   => 1,
							  'post_type'     => DX_CRM_POST_TYPE_PROJECTS,
			);
			
			$post_id = wp_insert_post( $post_arr );
			
			if( !empty( $post_id ) ){
				
				$add_project = array( "success" => "1", 'post_id' => $post_id, 'project_title' => $title );
				echo json_encode( $add_project );
				die;
			}			
		}
		
		exit;
	}
	
	/**
	 * ajax action function for add new project type
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_ajax_add_project_type() {
		
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		$title = isset( $_POST['title'] ) ? $_POST['title'] : '';
		
		if(!empty($title)){
			
			$term_id = wp_insert_term( $title, DX_CRM_PRO_TAXONOMY );
			
			if( !empty( $term_id ) ){
				
				$add_projec_type = array( "success" => "1", 'term_id' => $term_id['term_id'], 'term_title' => $title );
				echo json_encode( $add_projec_type );
				die;
			}
		}
		
		exit;
	}
	
	/**
	 * action function for show popup
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_add_customer_popup() {
		
		global $post_type;
		
		if ( $post_type == DX_CRM_POST_TYPE_COMPANY || $post_type == DX_CRM_POST_TYPE_PROJECTS ) {
		
			include( DX_CRM_ADMIN_DIR . '/forms/crm-add-customer-popup.php' ); // Including HTML  file					
		}
		
	}
	
	/**
	 * ajax action function for add new customer
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_ajax_add_customer() {
		
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		$customer_title = isset( $_POST['customer_title'] ) ? $_POST['customer_title'] : '';
		$customer_email = isset( $_POST['customer_email'] ) ? $_POST['customer_email'] : '';
		
		if(!empty($customer_title) && !empty($customer_email)){
			
			$post_arr 		= array(
							  'post_title'    => $customer_title,
							  'post_status'   => 'publish',
							  'post_author'   => 1,
							  'post_type'     => DX_CRM_POST_TYPE_CUSTOMERS,
			);
			
			$post_id = wp_insert_post( $post_arr );
			
			if( !empty( $post_id ) ){
				
				$prefix = DX_CRM_META_PREFIX;
				
				// Updating/Adding customer email address
				update_post_meta($post_id, $prefix.'cust_email', $customer_email);
				
				$add_customer = array( "success" => "1", 'post_id' => $post_id, 'customer_title' => $customer_title );
				echo json_encode( $add_customer );
				die;
			}			
		}
		
		exit;
	}
			
	/**
	 * search companies on ajax basis
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_ajax_company_list() {
		
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
		
		global $wpdb;
		
		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
		
		$companies = get_posts(array('post_type' => DX_CRM_POST_TYPE_COMPANY, 's' => $term, 'search_columns' => array( 'post_title' ), 'posts_per_page' => 10 ));
		
		$found_companies = array();
		if ( $companies ) {
			foreach ( $companies as $company ) {
				$found_companies[ $company->ID ] = $company->post_title ;
			}
		}
	
		echo json_encode( $found_companies );
		exit;
	}
	
	/**
	 * search projects on ajax basis
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_ajax_projects_list() {
		
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
	
		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
		
		$projects = get_posts(array('post_type' => DX_CRM_POST_TYPE_PROJECTS, 's' => $term, 'search_columns' => array( 'post_title' ), 'posts_per_page' => 10 ));
		
		$found_projects = array();
		if ( $projects ) {
			foreach ( $projects as $project ) {
				$found_projects[ $project->ID ] = $project->post_title ;
			}
		}
	
		echo json_encode( $found_projects );
		exit;
	}
	
	/**
	 * search company type(categories) on ajax basis
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_ajax_company_type_list() {
		
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
	
		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
		
		$cats_args = array(
						'hide_empty' => 0,
						'search'     => $term,
						);
	
		// Creating projects terms combo
		$pro_cats = get_terms( DX_CRM_PRO_TAXONOMY, $cats_args );
		
		$found_pro_cats = array();
		if ( $pro_cats ) {
			foreach ( $pro_cats as $pro_cat ) {
				$found_pro_cats[ $pro_cat->term_id ] = $pro_cat->name ;
			}
		} 
		
		echo json_encode( $found_pro_cats );
		exit;
	}
	
	/**
	 * search customers on ajax basis
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_ajax_customers_list() {
		
		if( ! current_user_can( 'administrator' ) ){
			echo json_encode( array( 'success' => 0, 'message' => __( 'Only administrator is allowed to do this!', 'dxcrm' ) ) );
			exit;
		}
		
		header( 'Content-Type: application/json; charset=utf-8' );
	
		$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
		
		$customers = get_posts(array('post_type' => DX_CRM_POST_TYPE_CUSTOMERS, 's' => $term, 'search_columns' => array( 'post_title' ), 'posts_per_page' => 10 ));
		
		$found_customers = array();
		if ( $customers ) {
			foreach ( $customers as $customer ) {
				$found_customers[ $customer->ID ] = $customer->post_title ;
			}
		}
	
		echo json_encode( $found_customers );
		exit;
	}
	
	/**
	 * Remove Add New from all post types admin menu
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_remove_add_new_menu() {	
		remove_submenu_page( 'edit.php?post_type=crm_customers', 'post-new.php?post_type=crm_customers' );
		remove_submenu_page( 'edit.php?post_type=crm_doc_mngr', 'post-new.php?post_type=crm_doc_mngr' );
		
		// Customer can't access these if removed:
		//remove_submenu_page( 'edit.php?post_type=crm_projects', 'post-new.php?post_type=crm_projects' );		
		//remove_submenu_page( 'edit.php?post_type=crm_company', 'post-new.php?post_type=crm_company' );		
	}
	
	
	function dx_crm_upload_file($dir)
	{
		/*
	    // xxx Lots of $_REQUEST usage in here, not a great idea.

	    // Are we where we want to be?
	    if (!isset($_REQUEST['action']) || 'upload-attachment' !== $_REQUEST['action']) {
	        return $dir;
	    }

	    // make sure we have a post ID
	    if (!isset($_REQUEST['post_id'])) {
	        return $dir;
	    }

	    // modify the path and url.
		$type        = 'dx-crm';
		$uploads     = apply_filters("upload_directory", $type);
		$dir['path'] = path_join($dir['basedir'], $uploads);
		$dir['url']  = path_join($dir['baseurl'], $uploads);
		*/
		
		//specify unique upload directory for Document Management
		global $post;
		
		if ( isset($post->post_type) && (DX_CRM_POST_TYPE_DOC_MNGR == $post->post_type) ) {
			$wp_root_path = get_home_path();
			$wp_base_url = get_site_url();
			
			$dm_dir_exists = file_exists( $wp_root_path . DX_CRM_DM_UPLOADS_DIRECTORY);			
			
			if($dm_dir_exists) {
				$dir['path'] = path_join($wp_root_path, DX_CRM_DM_UPLOADS_DIRECTORY);
				$dir['url']  = path_join($wp_base_url, DX_CRM_DM_UPLOADS_DIRECTORY);
			}
	    }

	    return $dir;
	}
	
	function test_init(){
		echo "<pre>";
		print_r($GLOBALS);
		echo "</pre>";
		$upload_dir = wp_upload_dir();

	    echo "test";exit;
	}
	
	/** 
	 * Auto create Roadmap on new Project creation
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function roadmap_auto_create( $post_id, $post, $update ){
		
		// If Revision, exit
		if ( wp_is_post_revision( $post_id ) ){ 
			return; 
		}	
		
		// Append "Roadmap" on new Roadmap name
		$post_title = $post->post_title . " Roadmap";
		
		// For Project only
		if( DX_CRM_POST_TYPE_PROJECTS == $post->post_type ){		
			
			// Check if already exist
			// Set Roadmap project meta key
			$joined_project_key = DX_CRM_META_PREFIX . 'rm_project';
			
			// Check post argument
			$args = array(
						'post_title' 	=> $post_title,
						'post_type'		=> DX_CRM_POST_TYPE_ROADMAP,
						'meta_query'	=> array(
												array(
													'key' 	=> $joined_project_key,
													'value'	=>	$post_id
												)
											)
					);			
			$chck_rdmp = new WP_Query( $args );
			
			// Check if post already exist
			// IF TRUE, check for post title changes
			// Append "Roadmap"
			// ELSE, create new Roadmap
			if( !empty( $chck_rdmp->post_count ) ){
				
				// Check if "Roadmap" exist on 
				$cmp_case = preg_match( '/Roadmap/', $post->post_title );

				if ( !empty( $cmp_case ) ) {
					$new_post_title = $chck_rdmp->post->post_title;
				} else {
					$new_post_title = $post_title;
				}
								
				$update_roadmap = array(
										'ID'			=> $chck_rdmp->post->ID,
										'post_title' 	=> $new_post_title,
										'post_content' 	=> $post->post_content
									);

				wp_update_post( $update_roadmap );
			
			} else {				
				$roadmap = wp_insert_post(
										array(
											'post_title' 	=> $post_title,
											'post_content'	=> $post->post_content,
											'post_type'		=> DX_CRM_POST_TYPE_ROADMAP,
											'post_status'	=> 'publish'
										)
									);
				if( !empty( $roadmap ) ){
					$assign_project = update_post_meta( $roadmap, $joined_project_key, $post->ID );					
				}
			}
			
			wp_reset_postdata();
			
		}
	}
	
	/** 
	 * Add Project column on roadmap table
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function roadmap_project_column($new_columns){
		
		unset( $new_columns['comments'] );
		unset( $new_columns['author'] );
		unset( $new_columns['date'] );
		
		$new_columns['projects']	= __( 'Project', 'dxcrm' );
		$new_columns['author']		= __( 'Author', 'dxcrm' );
		$new_columns['date']		= __( 'Date', 'dxcrm' );
		
		return $new_columns;
	}
	
	/** 
	 * Add data on Project column in roadmap listing table
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function roadmap_project_manage($column_name,$post_id){
		global $wpdb,$post;

		switch( $column_name ){
			case 'projects' :
				$project_id = get_post_meta( $post_id, DX_CRM_META_PREFIX . 'rm_project', false );
				$project = get_the_title( $project_id );
				
				if( ! empty ( $project ) ){
					echo $project;
				}
			break;
		}
		
	}
	
	/**
	 * Adding settings for Document Management
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_dm_admin_settings(){
		
		require_once( DX_CRM_ADMIN_DIR . '/forms/crm-dm-settings.php' );
	}
	
	/**
	 * Creating a folder for Document Management
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_dm_create_folder(){
		
		$dm_path_created = false;
		
		//check if DM folder is created already
		$wp_root_path = get_home_path();
		$upload_path  = $wp_root_path . DX_CRM_DM_UPLOADS_DIRECTORY;
		$dm_dir_exists = file_exists( $upload_path );
		
		if($dm_dir_exists) {
			$dm_path_created = true;
		} else { 
			//create DM folder
			$dm_path_created = mkdir( $upload_path );
		}
		
		//create .htaccess file to secure the directory
		if ( !file_exists( $upload_path . '/.htaccess' ) && wp_is_writable( $upload_path ) ) {
			$htaccess_rules = "Options -Indexes\n";
			$htaccess_rules .= "RewriteCond %{REQUEST_FILENAME} -s\n";
			$htaccess_rules .= "RewriteRule ^(.*)$ ../wp-content/plugins/crm-system/includes/admin/dx-crm-download-verifier.php?file=$1 [QSA,L]\n";
			@file_put_contents( $upload_path . '/.htaccess', $htaccess_rules );
		}
		
		if($dm_path_created) {
			echo 'success';
		} else {
			echo 'fail';
		}
		exit;
	}
	
		/**
	 * Allow additional upload file types for Document Management
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_dm_allow_upload_file_types( $existing_mimes = array() ) {
		
		$dx_crm_options = get_option( 'dx_crm_options' );
		$file_types_to_add = isset ( $dx_crm_options['dm_allowed_file_types'] ) ? $dx_crm_options['dm_allowed_file_types'] : '';
		$file_types_to_remove = isset ( $dx_crm_options['dm_disallowed_file_types'] ) ? $dx_crm_options['dm_disallowed_file_types'] : '';
		
		//add allowed file types
		if( isset($dx_crm_options['dm_allowed_file_types']) ) {
			$file_types_to_add = explode(',', $dx_crm_options['dm_allowed_file_types']);
			foreach( $file_types_to_add as $type_to_add ) {
				$type_to_add = trim($type_to_add);
				if( !empty($type_to_add) ) {
					if( !array_key_exists($type_to_add, $existing_mimes) ) {
						$existing_mimes[$type_to_add] = 'application/'.$type_to_add;
					}
				}
			}
		}
		
		return $existing_mimes;
	}
	
	/**
	 * Add script for meta field validation of Document Management CPT
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_metafield_validation_script() {
		global $post;
		
		if ( is_admin() && (DX_CRM_POST_TYPE_DOC_MNGR == $post->post_type) ) {
			?>
			<script language="javascript" type="text/javascript">
				(function($){
					jQuery(document).ready(function() {
						jQuery('#publish').click(function() {
							if(jQuery(this).data("valid")) {
								return true;
							}
							
							var upload_file_extension = '';
							var upload_filename = jQuery('[name="_dx_crm_document_file_upload[]"]').val();
							if(upload_filename) {
								upload_file_extension = upload_filename.split('.').pop().toLowerCase();
							}
							
							//hide loading icon, return Publish button to normal
							jQuery('#publishing-action .spinner').addClass('is-active');
							jQuery('#publish').addClass('button-primary-disabled');
							jQuery('#save-post').addClass('button-disabled');

							var data = {
								action: 'crm_dm_pre_submit',
								security: '<?php echo wp_create_nonce( "pre_publish_validation" ); ?>',
								'upload_file_extension': upload_file_extension
							};
							
							jQuery.post(ajaxurl, data, function(response) {
								jQuery('#publishing-action .spinner').removeClass('is-active');
								if ( response.success ){
									jQuery("#post").data("valid", true).submit();
								} else {
									jQuery('#crm_dm_error').remove();
									jQuery('#wpbody-content').prepend('<div id="crm_dm_error" class="error"><p>' + response.data.message +'</p></div>');
									$('html, body').animate({ scrollTop: 0 }, 'fast');
									jQuery("#post").data( "valid", false );
								}
								//hide loading icon, return Publish button to normal
								jQuery('#publish').removeClass('button-primary-disabled');
								jQuery('#save-post').removeClass('button-disabled');
							});
							return false;
						});
					});
				})(jQuery);
			</script>
			<?php
		}
	}
	
	/**
	 * Checks file upload meta field of Document Management CPT
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	function dx_crm_dm_metafield_validation() {	
		check_ajax_referer( 'pre_publish_validation', 'security' );
		
		$upload_file_extension = trim($_POST['upload_file_extension']);
		
		if ( empty($upload_file_extension)) {
			$data = array( 'message' => __('Please upload a valid file attachment.'), );
			wp_send_json_error( $data );
		} else {
			$extension_allowed = false;
			$allowed_mimes = get_allowed_mime_types();
			foreach ($allowed_mimes as $type => $mime) {
				if (strpos($type, $upload_file_extension) !== false) {
					$extension_allowed = true;
					break;
				}
			}			
			if ( !$extension_allowed ) {
				$data = array( 'message' => __('File extension <b>".'. $upload_file_extension .'"</b> is not allowed.'), );
				wp_send_json_error( $data );
			}
		}
		wp_send_json_success();
	}
	
	/** 
	 * Report page tabbing content display in filter
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function report_tab_items( $filter_data = array() ){
		
		/** 
		 * Default tab items on CRM main plugin
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( current_user_can('administrator') ){
			$items = array(
				'customer' => __( 'Customer', 'dxcrm' ),
				'project' => __( 'Project', 'dxcrm' ),
				'company' => __( 'Company', 'dxcrm' )
			);
		} else {
			$items = array(
				'project' => __( 'Project', 'dxcrm' ),
				'company' => __( 'Company', 'dxcrm' )
			);
		}
		
		/** 
		 * Merge default item with custom tab provided
		 * from add ons
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( ! empty ( $filter_data ) && is_array( $filter_data ) ){
			$items = array_merge( $items, $filter_data );
		}
		
		/** 
		 * Return values
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		return $items;
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
		
		/** 
		 * Add filter for Report page tab items
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		add_filter( 'dx_crm_report_tab_item', array( $this, 'report_tab_items' ), 10 );
		
		// Auto create and update Roadmap
		add_action( 'wp_insert_post', array( $this, 'roadmap_auto_create'), 10, 3  );
		
		// Roadmap listing page
		add_action('manage_'.DX_CRM_POST_TYPE_ROADMAP.'_posts_custom_column', array($this,'roadmap_project_manage'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_ROADMAP.'_columns', array($this,'roadmap_project_column'));
		
		// Admin menu
		add_action( 'admin_menu', array($this,'dx_crm_dashboard_admin_menu'));
		add_action( 'admin_menu', array($this,'dx_crm_roadmap_tracking_menu'));
		
		//Redirect when plugin activated
		add_action( 'admin_init', array( $this, 'dx_crm_plugin_activation') );
		
		//add new field to post listing page
		add_action('manage_'.DX_CRM_POST_TYPE_CUSTOMERS.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_customer'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_CUSTOMERS.'_columns', array($this,'add_new_crm_customer_columns'));
		
		// For Quick Edit
		add_action( 'save_post', array($this,'crm_customer_quick_save_post_data'),10,2);
		add_action('quick_edit_custom_box',  array($this,'crm_customer_quick_edit_values'), 10, 2);
		
		//add new field to post listing for projects
		add_action('manage_'.DX_CRM_POST_TYPE_PROJECTS.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_project'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_PROJECTS.'_columns', array($this,'add_new_crm_project_columns'));
		
		//add new fiel to post listing for company
		add_action('manage_'.DX_CRM_POST_TYPE_COMPANY.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_company'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_COMPANY.'_columns', array($this,'add_new_crm_company_columns'));
		
		//add new field to post listing for projects
		//add_action('manage_'.DX_CRM_POST_TYPE_QUOTE.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_quote'), 10, 2);
		//add_filter('manage_edit-'.DX_CRM_POST_TYPE_QUOTE.'_columns', array($this,'add_new_crm_quote_columns'));
		
		// add action to add custom meta box in custom post
		add_action( 'add_meta_boxes', array( $this, 'crm_custom_meta_box' ) );
		
		// add action to save custom meta
		add_action( 'save_post', array( $this, 'crm_save_meta' ) );
		
		// custom meta field in user signup
		add_action( 'user_new_form', array( $this, 'user_profile_metabox' ) );
		add_action( 'show_user_profile', array( $this, 'user_profile_metabox' ) );
		add_action( 'edit_user_profile', array( $this, 'user_profile_metabox' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_profile_metabox' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_profile_metabox' ) );
		add_action( 'user_register', array( $this, 'save_user_profile_metabox' ) );
		
		// Filter to change post status
	  	add_filter( 'wp_insert_post_data', array($this, 'dxcrm_set_post_data'), 99, 2 );
	  	
	  	//Filter to Change Title for quote post type
	  	add_filter( 'enter_title_here', array($this, 'dxcrm_enter_title_here') );
	  	
	  	//action to add custom button in listing page.
	  	add_action( 'restrict_manage_posts', array( $this, 'manage_listing_page' ) );
	  	
	  	// Ajax call to create visit in table when content is shared
		add_action( 'wp_ajax_crm_change_status', array( $this, 'crm_change_quote_status' ) );
		add_action( 'wp_ajax_nopriv_crm_change_status', array( $this, 'crm_change_quote_status' ) );
        
        // Add category filter in deals list page
		add_action( 'restrict_manage_posts', array( $this, 'dx_crm_restrict_manage_posts' ) );
		
		// Add company type filter in deals list page
		add_action( 'restrict_manage_posts', array( $this, 'dx_crm_company_type_filter' ) );
		
        // Add action for display deals using deal type
		add_filter( 'pre_get_posts', array( $this, 'dx_crm_project_search' ) );
		add_action( 'admin_menu', array( $this, 'dx_crm_stat_admin_menu' ), 15 );	
	
		//Action for remove option from header screen option
		add_action( 'in_admin_header', array( $this, 'dx_crm_in_admin_header' ) );
		
		//Action for add settings menu under DX CRM menu
		//add_action( 'admin_menu', array( $this, 'dx_crm_settings' ), 15 );
		
		//Action for save the crm-system global settings
		add_action( 'admin_init', array($this, 'dx_crm_admin_init') );
		
		//Action for popup for add new company
		add_action('admin_footer', array($this, 'dx_crm_add_company_popup') );
		
		//Action for add new company using ajax
		add_action( 'wp_ajax_crm_add_company', array($this, 'dx_crm_ajax_add_company'));
  		add_action( 'wp_ajax_nopriv_crm_add_company', array($this, 'dx_crm_ajax_add_company'));
		
		//Action for popup for add new project
		add_action('admin_footer', array($this, 'dx_crm_add_project_popup') );
		
		//Action for add new project using ajax
		add_action( 'wp_ajax_crm_add_project', array($this, 'dx_crm_ajax_add_project'));
  		add_action( 'wp_ajax_nopriv_crm_add_project', array($this, 'dx_crm_ajax_add_project'));
  		
  		//Action for popup for add new project
		add_action('admin_footer', array($this, 'dx_crm_add_project_type_popup') );
		
		//Action for add new project type using ajax
		add_action( 'wp_ajax_crm_add_project_type', array($this, 'dx_crm_ajax_add_project_type'));
  		add_action( 'wp_ajax_nopriv_crm_add_project_type', array($this, 'dx_crm_ajax_add_project_type'));
	
  		//Action for popup for add new customer
		add_action('admin_footer', array($this, 'dx_crm_add_customer_popup') );
		
		//Action for add new customer using ajax
		add_action( 'wp_ajax_crm_add_customer', array($this, 'dx_crm_ajax_add_customer'));
  		add_action( 'wp_ajax_nopriv_crm_add_customer', array($this, 'dx_crm_ajax_add_customer'));
  				
		//ajax call to search companies
		add_action('wp_ajax_dx_crm_ajax_company_list', array( $this , 'dx_crm_ajax_company_list' ) );
		add_action('wp_ajax_nopriv_dx_crm_ajax_company_list', array( $this , 'dx_crm_ajax_company_list' ) );
		
		//ajax call to search projects
		add_action('wp_ajax_dx_crm_ajax_projects_list', array( $this , 'dx_crm_ajax_projects_list' ) );
		add_action('wp_ajax_nopriv_dx_crm_ajax_projects_list', array( $this , 'dx_crm_ajax_projects_list' ) );
		
		//ajax call to search company type(categories)
		add_action('wp_ajax_dx_crm_ajax_company_type_list', array( $this , 'dx_crm_ajax_company_type_list' ) );
		add_action('wp_ajax_nopriv_dx_crm_ajax_company_type_list', array( $this , 'dx_crm_ajax_company_type_list' ) );
		
		//ajax call to search customers
		add_action('wp_ajax_dx_crm_ajax_customers_list', array( $this , 'dx_crm_ajax_customers_list' ) );
		add_action('wp_ajax_nopriv_dx_crm_ajax_customers_list', array( $this , 'dx_crm_ajax_customers_list' ) );
		
		//Remove Add New from all post types admin menu
		add_action( 'admin_menu', array( $this, 'dx_crm_remove_add_new_menu' ), 16 );
		
		add_action( 'admin_notices', array( $this, 'dx_crm_notice_box' ) );
		add_filter('upload_dir', array( $this, 'dx_crm_upload_file'));
		//add_action( 'init', array( $this ,'test_init' ));
		
		//add new field to post listing page for milestones
		add_action('manage_'.DX_CRM_POST_TYPE_MILESTONES.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_milestone'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_MILESTONES.'_columns', array($this,'add_new_crm_milestone_columns'));
		
		//add new field to post listing page for timesheets
		add_action('manage_'.DX_CRM_POST_TYPE_TIMESHEETS.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_timesheet'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_TIMESHEETS.'_columns', array($this,'add_new_crm_timesheet_columns'));

		//add new field to post listing page for milestones
		add_action('manage_'.DX_CRM_POST_TYPE_STAFF.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_staff'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_STAFF.'_columns', array($this,'add_new_crm_staff_columns'));
		
		//add new field to post listing page for documents
		add_action('manage_'.DX_CRM_POST_TYPE_DOC_MNGR.'_posts_custom_column', array($this,'dx_crm_manage_custom_column_document'), 10, 2);
		add_filter('manage_edit-'.DX_CRM_POST_TYPE_DOC_MNGR.'_columns', array($this,'add_new_crm_document_columns'));
		
		//Action for DM settings
		add_action( 'dx_crm_dm_admin_settings', array( $this, 'dx_crm_dm_admin_settings' ), 15 );
		
		//Ajax call for creating DM folder
		add_action( 'wp_ajax_crm_dm_create_dir', array( $this, 'dx_crm_dm_create_folder' ) );

		// Filter to allow additional file types
		add_filter('upload_mimes', array( $this, 'dx_crm_dm_allow_upload_file_types'));

		//Action for adding DM meta field validations
		add_action('admin_head-post.php', array( $this, 'dx_crm_metafield_validation_script'));
		add_action('admin_head-post-new.php', array( $this, 'dx_crm_metafield_validation_script'));
		add_action('wp_ajax_crm_dm_pre_submit', array( $this, 'dx_crm_dm_metafield_validation'));

		// Action which doesnt allow users with Customer role to create new projects/companies
		add_action('admin_head-post-new.php', array( $this, 'limit_customers_access_addnew' ) );

		// Action which is adding user role in body html tag
		add_action('admin_body_class', array( $this, 'add_user_role_body_backend' ));
		
		if ( $this->check_current_page() ) {
			// Change get_posts query. Customers can see projects/companies that are only involved in
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

			// Filter which fix WP_List_Table counters depends on our custom get_posts function
			add_filter( 'views_edit-' . DX_CRM_POST_TYPE_PROJECTS, array( $this, 'counters_fix' ) );
			add_filter( 'views_edit-' . DX_CRM_POST_TYPE_COMPANY, array( $this, 'counters_fix' ) );

			// Filter which removes bulk actions
			add_filter( 'bulk_actions-edit-' . DX_CRM_POST_TYPE_PROJECTS, array( $this, 'hide_bulk_actions' ) );
			add_filter( 'bulk_actions-edit-' . DX_CRM_POST_TYPE_COMPANY, array( $this, 'hide_bulk_actions' ) );

			// Filter which remove columns
			add_filter( 'manage_edit-' . DX_CRM_POST_TYPE_PROJECTS . '_columns', array( $this, 'hide_columns' ) );
			add_filter( 'manage_edit-' . DX_CRM_POST_TYPE_COMPANY . '_columns', array( $this, 'hide_columns' ) );
		}		
	}

	/**
	 * Fixing the counters above WP_List_Table on CPT Project/Company page
	 *
	 * @package CRM System
	 * @version 1.0.0
	 */	
	public function counters_fix( $views ) {
		global $current_user;

		if ( in_array( DX_CRM_CUSTOMER_ROLE, $current_user->roles ) ) {
			global $wp_query;

			return array( 
				'all' => sprintf(
					__( '<a href="%s" class="current">All <span class="count">(%d)</span></a>', 'all' ), 
					admin_url('edit.php?post_type=' . $wp_query->query['post_type'] ), 
					$wp_query->found_posts 
				) 
			);
		} else return $views;
	}

	/**
	 * Return true if page post type is crm_project or crm_company and false if not
	 * 
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function check_current_page() {
		return ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array( DX_CRM_POST_TYPE_PROJECTS, DX_CRM_POST_TYPE_COMPANY ) ) ) ? true : false;
	}

	/**
	 * Check which projects and companies to show on customers
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function pre_get_posts( $query ) {
		// Add roles in this array which will see projects and companies in which they are involved
		$post_types = array( DX_CRM_POST_TYPE_PROJECTS, DX_CRM_POST_TYPE_COMPANY );

		if ( is_admin() && $query->is_main_query() && in_array( $query->get( 'post_type' ), $post_types ) && $this->is_customer() ) {
			$curr_user = wp_get_current_user();

			if ( in_array( DX_CRM_CUSTOMER_ROLE, $curr_user->roles ) ) {
				$curr_user_customer_id = get_user_meta( $curr_user->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', true );

				if ( $query->get( 'post_type' ) == DX_CRM_POST_TYPE_PROJECTS ) {
					$key = DX_CRM_META_PREFIX . 'joined_pro_customer';
				} elseif ( $query->get( 'post_type' ) == DX_CRM_POST_TYPE_COMPANY ) {
					$key = DX_CRM_META_PREFIX . 'joined_customer';
				}

				$query->set( 'meta_query', array( array(
					'key' => $key,
					'value' => $curr_user_customer_id,
					'compare' => 'IN',
				)));
			}

		}
	}

	/**
	 * Limits Customer's access to "Add New" page in CPT Project/Company
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function limit_customers_access_addnew() {
		global $post_type;
		global $current_user;

		if ( in_array( $post_type, array( DX_CRM_POST_TYPE_PROJECTS, DX_CRM_POST_TYPE_COMPANY ) ) && in_array( DX_CRM_CUSTOMER_ROLE, $current_user->roles ) ) {
			wp_die( 'You don\'t have access to this page! ', 'No access', array( 'back_link' => admin_url() ) );
		}
	}

	/**
	 * Adding user roles in body(html tag)
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function add_user_role_body_backend( $classes ) {
		global $current_user;

		foreach ( $current_user->roles as $role )
			$classes .= $role;

		return rtrim( $classes );		
	}

	/**
	 * Check if current user is customer or not
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function is_customer() {
		return in_array( DX_CRM_CUSTOMER_ROLE, wp_get_current_user()->roles );
	}

	/**
	 * Hide bulk actions from customers
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function hide_bulk_actions( $actions ) {
		if ( $this->is_customer() ) {
			unset( $actions['trash'] );
			unset( $actions['edit'] );
		}

		return $actions;
	}

	/**
	 * Hide columns from customers
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function hide_columns( $columns ) {
		if ( $this->is_customer() ) {
			unset( $columns['cb'] );
		}

		return $columns;
	}
}

?>