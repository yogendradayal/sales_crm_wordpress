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
		'id' 			=> 'crm_meta_customer',							// meta box id, unique per meta box
		'title' 		=> __( 'Customer Details', 'dxcrm'),			// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_CUSTOMERS),			//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',									// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',										// order of meta box: high (default), low; optional
		'fields' 		=> array(),										// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,										// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',					// this has to be unique for each plugin
	);			
	
	$dx_meta_customer =  new Dx_Crm_Meta_Box( $config1 );
	
	/*
	 * Initiate your meta box
	 */
	$contact_type 	= apply_filters( 'dx_crm_contact_type', array(), 1 );
	
	$company_list = array();
	
	$company_data = get_posts(array('post_type' => DX_CRM_POST_TYPE_COMPANY, 'posts_per_page' => 5));
	// Company data
	foreach($company_data as $key=>$val){
		$company_list[$val->ID] = $val->post_title;
	}
	
	$project_data = get_posts(array('post_type' => DX_CRM_POST_TYPE_PROJECTS, 'posts_per_page' => 5));
	// projects data
	foreach($project_data as $key=>$val){
		$project_list[$val->ID] = $val->post_title;
	}
	
	// Company data
	foreach($company_data as $key=>$val){
		$company_list[$val->ID] = $val->post_title;
	}
	
	$cats_args = array(
		'hide_empty' => 0,
		'number'     => '5',
	);
	
	$pro_cats_result = array();
	
	// Project Type
	$project_cats = get_terms( DX_CRM_PRO_TAXONOMY, $cats_args );

	if ( ! empty( $project_cats ) ) {
		foreach ( $project_cats as $project_cat ) {
			$pro_cats_result[ $project_cat->term_id ] = ( isset( $project_cat->name ) ) ? ucfirst( $project_cat->name ) : '';
		}
	}
	
	/**
	 * Customer personal information
	*/
	// First name
	$dx_meta_customer->addText( $prefix . 'cust_first_name', array( 'validate_func'=> 'escape_html', 'name'=> __( 'First Name', 'dxcrm' ), 'desc' => __( 'Enter the first name of your customer.', 'dxcrm' ) ) );
	
	// Last name
	$dx_meta_customer->addText( $prefix . 'cust_last_name', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Last Name', 'dxcrm' ), 'desc' => __( 'Enter the last name of your customer.', 'dxcrm' ) ) );
	
	// Email
	$dx_meta_customer->addText( $prefix . 'cust_email', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Email', 'dxcrm' ), 'desc' => __( 'Enter the Email Address of the customer. This field is required if you wish to create new WP User for this customer using Sales CRM role.', 'dxcrm' ) ) );
		
	// Create WP user when create new customer
	$email = get_post_meta( isset( $_GET['post'] ) ? $_GET['post'] : '', $prefix . 'cust_email', true );

	if ( ! email_exists( $email ) ) {
		$dx_meta_customer->addCheckbox( $prefix . 'set_wp_user', array( 'name' => __( 'WP User create', 'dxcrm' ), 'desc' => __( 'Tick to make the customer a WordPress User with the role of DX Sales CRM Customer. Your customer will be able to log in as a DX Sales CRM Customer and will be able to see the Dashboard exclusive to customers only. To edit the Customer credential, go to Users > All Users or Sales CRM Customers. *Need email to tick this box.', 'dxcrm' ) ) );
	}
	
	// Contact Number
	$dx_meta_customer->addText( $prefix . 'contact_number', array( 'name'=> __( 'Phone Number', 'dxcrm' ), 'desc' => __( 'Enter Contact Number of the customer. You can use mobile or landline.', 'dxcrm' ) ) );
	
	/**
	 * Customer CRM information
	*/
	// First Project Type
	$dx_meta_customer->addSelect( $prefix . 'cust_first_pro_type', $pro_cats_result, array( 'name'=> __( 'First Project Type', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( ' Select the DX Sales CRM "Projects" > "Project Type" the customer is connected with.', 'dxcrm' ), 'project_type_html_attr' => 'add_project_type', 'class' => 'dx_crm_custom_select_company_type' ) );
	
	//$project_type_url = add_query_arg( array( 'taxonomy' => DX_CRM_PRO_TAXONOMY, 'post_type' => DX_CRM_POST_TYPE_PROJECTS ), admin_url( 'edit-tags.php' ) );
	/*?><span><a target="_blank" href="<?php echo $project_type_url; ?>">Add New Project Type</a></span><?php*/
		
	// Initial Investment
	$dx_meta_customer->addText( $prefix . 'cust_initial_investment', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Initial Investment', 'dxcrm' ), 'desc' => __('Enter the initial investment or contribution the customer has given/paid.', 'dxcrm') ) );

	// Referral
	$dx_meta_customer->addText( $prefix . 'cust_referral', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Referral', 'dxcrm' ), 'desc' => __('Enter name of the person referred the customer.', 'dxcrm') ) );
	
	// Contact Date
	$dx_meta_customer->addDate( $prefix . 'cust_contact_date', array('name' => __('Contact Date ', 'dxcrm'),'std' => array(''),'desc' => __('Enter the first date you contacted the customer.', 'dxcrm'),'format'=>'dd-mm-yy' ) );

	//Contact Type
	$dx_meta_customer->addSelect( $prefix . 'contact_type', $contact_type, array( 'name'=> __( 'Contact Type', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select customer\'s contact type from the dropdown.', 'dxcrm' ) ) );
	
	// Referral
	$dx_meta_customer->addText( $prefix . 'company_role', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Company Role', 'dxcrm' ), 'desc' => __( 'Enter the customer\'s role in the company if he/she is affiliated with one.', 'dxcrm' ) ) );
	
	do_action( 'dx_crm_ec_edit_customer_send_email', $dx_meta_customer, DX_CRM_POST_TYPE_CUSTOMERS );
	
	do_action( 'dx_crm_get_country', $dx_meta_customer, DX_CRM_POST_TYPE_CUSTOMERS );

	$company_list = (isset($company_list) && !empty($company_list)) ? $company_list : '';
	// Join Company
	$dx_meta_customer->addSelect( $prefix . 'joined_company', $company_list, array( 'name'=> __( 'Company', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'DX Sales CRM Company or Companies connected with the customer. Make sure you already first entered the company(s) in the Companies CPT so that it will appear in the dropdown. If not, click "Add" button to add quickly.', 'dxcrm' ), 'multiple' => true, 'company_html_attr' => 'add_company', 'class' => 'dx_crm_custom_select_company' ) );

	$project_list = (isset($project_list) && !empty($project_list)) ? $project_list : '';
	
	// Join Project
	$dx_meta_customer->addSelect( $prefix . 'joined_project', $project_list, array( 'name'=> __( 'Project', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'DX Sales CRM Project(s) the Customer is connected. Make sure you already first entered the project(s) in the Projects CPT so that it will appear in the dropdown. If not, click "Add" button to add quickly.', 'dxcrm' ), 'multiple' => true, 'project_html_attr' => 'add_project', 'class' => 'dx_crm_custom_select_project_list' ) );

	// do action for attach campaigns
	do_action( 'dx_crm_join_campaigns', $dx_meta_customer, DX_CRM_POST_TYPE_CUSTOMERS );
	
	//Finish Meta Box Decleration
	$dx_meta_customer->Finish();

?>