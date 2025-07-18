<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

	/* 
	 * prefix of meta keys, optional
	 * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
	 *  you also can make prefix empty to disable it
	 * 
	 */
	
	global $post, $dx_crm_model;

	$model = $dx_crm_model;

	//metaname should be starts with _ ( UNDERSCORE ) to prevent custom fields meta in wordpress
	$prefix = DX_CRM_META_PREFIX;
		
	/* 
	 * configure your meta box
	 */
	$config1 = array(
		'id' 			=> 'crm_meta_project',						// meta box id, unique per meta box
		'title' 		=> __( 'Project Details', 'dxcrm'),			// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_PROJECTS),		//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',								// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',									// order of meta box: high (default), low; optional
		'fields' 		=> array(),									// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,									// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',				// this has to be unique for each plugin
	);			
	
	$dx_meta_projects =  new Dx_Crm_Meta_Box( $config1 );
	
	
	$project_currency = array( 
							'USD' => __('US Dollar', 'dxcrm'),
							'EUR' => __('Euro', 'dxcrm'),
					  );
	
	$project_status = apply_filters( 'dx_crm_project_status', array() );	
	$company_project = array();
	$company_data = get_posts(array('post_type' => DX_CRM_POST_TYPE_COMPANY, 'posts_per_page' => 5 ));
	
	$post_id = isset($_REQUEST['post']) ? $_REQUEST['post'] : '';
	$post_meta = "";
	
	if(!empty($post_id)) {
		$post_meta = get_post_meta( $post_id, $prefix . 'company_project', true );
	}
	
	$post_data = get_post($post_meta);
	
	// Company data
	foreach($company_data as $key=>$val){
		$company_project[$val->ID] = $val->post_title;
	}
	
	if( !empty($post_data) ) {
		$company_project[$post_data->ID] = $post_data->post_title;
		
	}
	
	// Get customer data
	$args = array(
					'post_type'=> DX_CRM_POST_TYPE_CUSTOMERS,
					'posts_per_page'=> 150
			);
	$customers 	= get_posts( $args );
	
	$company_customers = array( );
	foreach ($customers as $customer_data ){
		
		$company_customers[$customer_data->ID] = $customer_data->post_title;
	}
	/*
	 * Initiate your meta box
	 */
		
	//Select Company
	$dx_meta_projects->addSelect( $prefix . 'company_project', $company_project, array( 'name'=> __( 'Select Company', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select the DX Sales CRM Company for the project. Make sure you have already entered the company information in the "Companies" CPT. So that it will appear in the dropdown.', 'dxcrm' ), 'class' => 'dx_crm_custom_select_company' ) );
	
	// Start Date
	$dx_meta_projects->addDate( $prefix . 'pro_start_date', array('name' => __('Start Date', 'dxcrm'), 'desc' => __( 'Enter the starting date of your project.', 'dxcrm' ),'std' => array(''), 'format'=>'yy-mm-dd' ) );

	// Planned End Date
	$dx_meta_projects->addDate( $prefix . 'pro_end_date', array('name' => __('Planned End Date', 'dxcrm'), 'desc' => __( 'Expected date or a specific date when the project will end.', 'dxcrm' ),'std' => array(''), 'format'=>'yy-mm-dd' ) );
	
	// Ongoing Project
	$dx_meta_projects->addCheckbox( $prefix . 'pro_ongoing', array('name' => __('Ongoing', 'dxcrm'), 'desc' => __( 'Tick the box if your project is still ongoing.', 'dxcrm' ) ) );
	
	//For Send a reminder
	do_action( 'dx_crm_get_reminder_meta', $dx_meta_projects, DX_CRM_POST_TYPE_PROJECTS );
	
	// Real End Date for first milestone
	$dx_meta_projects->addDate( $prefix . 'pro_real_end_date_first_mile', array('name' => __('Real End Date for first milestone', 'dxcrm'), 'desc' => __( 'Estimated date of completion of a project milestone.', 'dxcrm' ),'std' => array(''), 'format'=>'yy-mm-dd' ) );

	// Real End Date for last conversation
	$dx_meta_projects->addDate( $prefix . 'pro_real_end_date_last_conversation', array('name' => __('Real End Date for last conversation', 'dxcrm'), 'desc' => __( 'Last date of communication.', 'dxcrm' ),'std' => array(''), 'format'=>'yy-mm-dd' ) );
	
	// Agreed Cost
	$dx_meta_projects->addText( $prefix . 'pro_agreed_cost', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Agreed Cost', 'dxcrm' ), 'desc' => __( 'Enter project cost that has been agreed upon.', 'dxcrm' ) ) );
	
	//extra information for quote
	//Quote Currency
	$dx_meta_projects->addSelect( $prefix . 'project_currency', $project_currency, array( 'name'=> __( 'Currency', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select the currency type for "Agreed Cost" and "Total Paid".', 'dxcrm' ) ) );
	
	//Project status
	$dx_meta_projects->addSelect( $prefix . 'project_status', $project_status, array( 'name'=> __( 'Project status', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select current project status. You can update according to your project status.', 'dxcrm' ) ) );

	//Total Paid
	$dx_meta_projects->addText( $prefix . 'project_total', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Total Paid', 'dxcrm' ), 'desc' => __( 'Enter the total amount paid for this project. Your currency type will be same as "Agreed Cost".', 'dxcrm' ) ) );
	
	//Responsible person
	$dx_meta_projects->addText( $prefix . 'project_assigned_by', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Responsible person', 'dxcrm' ), 'desc' => __( 'Enter project\'s accountable person.', 'dxcrm' ) ) );
	
	//Connected Customer
	$dx_meta_projects->addSelect( $prefix . 'joined_pro_customer', $company_customers, array( 'name'=> __( 'Customers', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'DX Sales CRM Customer(s) connected with this project. Make sure you already first entered the customer in the Customers CPT so that it will appear in the dropdown. If not, click "Add" button to add quickly.', 'dxcrm' ), 'multiple' => true, 'customer_html_attr' => 'add_customer', 'class' => 'dx_crm_custom_select_customers_list' ) );
	
	//Finish Meta Box Decleration
	$dx_meta_projects->Finish();

?>