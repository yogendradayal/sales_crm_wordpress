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
		'id' 			=> 'crm_meta_company',						// meta box id, unique per meta box
		'title' 		=> __( 'Company Information', 'dxcrm'),			// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_COMPANY),		//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',								// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',									// order of meta box: high (default), low; optional
		'fields' 		=> array(),									// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,									// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',				// this has to be unique for each plugin
	);			
	
	$dx_meta_companies =  new Dx_Crm_Meta_Box( $config1 );
	
	/*
	 * Initiate your meta box
	 */
	
	$company_currency 	= apply_filters( 'dx_crm_company_currency', array( '' => __( 'Please select..', 'dxcrm' ) ), 1 );	
	$company_industry 	= apply_filters( 'dx_crm_company_industry', array( '' => __( 'Please select..', 'dxcrm' ) ), 1 );	
	$company_type 		= apply_filters( 'dx_crm_company_type', array( '' => __( 'Please select..', 'dxcrm' ) ), 1 );	
	$company_employees 	= apply_filters( 'dx_crm_company_employees', array( '' => __( 'Please select..', 'dxcrm' ) ), 1 );
	
	$args = array(
		'post_type'=> DX_CRM_POST_TYPE_PROJECTS,
		'posts_per_page'=> -1,
	);
	
	$projects = get_posts( $args );	
	$company_projects = array( '0' => __('Select Project', 'dxcrm') );	
	foreach ($projects as $project_data ){		
		$company_projects[$project_data->ID] = $project_data->post_title;
	}
	
	$args = array(
		'post_type'=> DX_CRM_POST_TYPE_CUSTOMERS,
		'posts_per_page'=> 5
	);

	$customers 		 = get_posts( $args );
	$company_customers = array( );	
	foreach ($customers as $customer_data ){		
		$company_customers[$customer_data->ID] = $customer_data->post_title;
	}
	
	//Responsible person
	$dx_meta_companies->addText( $prefix . 'company_assigned_by', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Responsible Person', 'dxcrm' ), 'desc' => __( 'Enter the company\'s accountable person for all the transactions.', 'dxcrm' ) ) );
	
	//Company Logo
	$dx_meta_companies->addImage( $prefix . 'company_logo', array( 'name'=> __( 'Logo:', 'dxcrm' ), 'desc' => __( 'The company\'s logo, trademark or emblem. Select JPEG, PNG or any other valid web image format.', 'dxcrm' ) ) );
	
	//Company type
	$dx_meta_companies->addSelect( $prefix . 'company_type', $company_type, array( 'name'=> __( 'Company Type', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select the type of your company.', 'dxcrm' ) ) );
	
	//Company Currency
	$dx_meta_companies->addSelect( $prefix . 'company_industry', $company_industry, array( 'name'=> __( 'Industry', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select the type of your business.', 'dxcrm' ) ) );
	
	//Company Employees
	$dx_meta_companies->addSelect( $prefix . 'company_employees', $company_employees, array( 'name'=> __( 'Employees', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select the number of employees working for your company.', 'dxcrm' ) ) );
	
	//Company Total
	$dx_meta_companies->addText( $prefix . 'company_annual_income', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Annual Income', 'dxcrm' ), 'desc' => __( 'Enter the company\'s yearly estimated income.', 'dxcrm' ) ) );
	
	//Quote Currency
	$dx_meta_companies->addSelect( $prefix . 'company_currency', $company_currency, array( 'name'=> __( 'Currency', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select the currency type for the annual income.', 'dxcrm' ) ) );

	//Company Website URL
	$dx_meta_companies->addText( $prefix . 'company_url', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Company URL', 'dxcrm' ), 'desc' => __( 'The URL can be the official website of the company or social media account.', 'dxcrm' ) ) );
	
	do_action( 'dx_crm_get_country', $dx_meta_companies, DX_CRM_POST_TYPE_COMPANY );
	
	//Connected Customer
	$dx_meta_companies->addSelect( $prefix . 'joined_customer', $company_customers, array( 'name'=> __( 'Customers', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'DX Sales CRM Customer(s) connected with the company. Make sure you already first entered the customer in the Customers CPT so that it will appear in the dropdown, if not, click "Add" button to add quickly.', 'dxcrm' ), 'multiple' => true, 'customer_html_attr' => 'add_customer', 'class' => 'dx_crm_custom_select_customers_list' ) );
		
	//Finish Meta Box Decleration
	$dx_meta_companies->Finish();

?>