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
		'id' 			=> 'crm_meta_quote',						// meta box id, unique per meta box
		'title' 		=> __( 'Quote information', 'dxcrm'),			// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_QUOTE),		//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',								// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',									// order of meta box: high (default), low; optional
		'fields' 		=> array(),									// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,									// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',				// this has to be unique for each plugin
	);			
	
	$dx_meta_quotes =  new Dx_Crm_Meta_Box( $config1 );
	
	/*
	 * Initiate your meta box
	 */
	
	$quote_currency = array( 
							'USD' => __('US Dollar', 'dxcrm'),
							'EUR' => __('Euro', 'dxcrm'),
					  );
	
	$quote_status = $model->crm_get_project_status();
	
	$args = array(
					'post_type'=> DX_CRM_POST_TYPE_PROJECTS,
					'posts_per_page'=> -1,
				);
	$projects = get_posts( $args );
	$quote_projects = array( '0' => __('Select Project', 'dxcrm') );
	
	foreach ($projects as $project_data ){
		
		$quote_projects[$project_data->ID] = $project_data->post_title;
	}
	
	$args = array(
					'post_type'=> DX_CRM_POST_TYPE_CUSTOMERS,
					'posts_per_page'=> -1
			);
			
	$customers 		 = get_posts( $args );
	$quote_customers = array( '0' => __('Select Customer', 'dxcrm') );
	
	foreach ($customers as $customer_data ){
		
		$quote_customers[$customer_data->ID] = $customer_data->post_title;
	}
	
	//Quote Currency
	$dx_meta_quotes->addSelect( $prefix . 'quote_currency', $quote_currency, array( 'name'=> __( 'Currency', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select Currency.', 'dxcrm' ) ) );
	
	//Quote status
	$dx_meta_quotes->addSelect( $prefix . 'quote_status', $quote_status, array( 'name'=> __( 'Quote status', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select status.', 'dxcrm' ) ) );

	//Quote Total
	$dx_meta_quotes->addText( $prefix . 'quote_total', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Total', 'dxcrm' ) ) );
	
	//Responsible person
	$dx_meta_quotes->addText( $prefix . 'quote_assigned_by', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Responsible person', 'dxcrm' ) ) );
	
	//Created Date
	$dx_meta_quotes->addDate( $prefix . 'quote_created_on', array('name' => __('Created on', 'dxcrm'),'std' => array(''), 'format'=>'dd-mm-yy' ) );

	//Expiration Date
	$dx_meta_quotes->addDate( $prefix . 'quote_expire_on', array('name' => __('Expiration date', 'dxcrm'),'std' => array(''), 'format'=>'dd-mm-yy' ) );
	
	//Quote Project
	$dx_meta_quotes->addSelect( $prefix . 'quote_assigned_to', $quote_projects, array( 'name'=> __( 'Project', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select Project.', 'dxcrm' ) ) );
	
	//Quote Customer
	$dx_meta_quotes->addSelect( $prefix . 'quote_customer', $quote_customers, array( 'name'=> __( 'Customer', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Select Customer.', 'dxcrm' ) ) );
	
	//Finish Meta Box Decleration
	$dx_meta_quotes->Finish();

?>