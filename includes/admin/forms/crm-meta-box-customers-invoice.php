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
		'id' 			=> 'crm_meta_customer_invoice',						// meta box id, unique per meta box
		'title' 		=> __( 'Customer Invoice details', 'dxcrm'),		// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_CUSTOMERS),				//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',										// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',											// order of meta box: high (default), low; optional
		'fields' 		=> array(),											// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,											// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',						// this has to be unique for each plugin
	);			
	
	$dx_meta_customer_invoice =  new Dx_Crm_Meta_Box( $config1 );
	
	/*
	 * Initiate your meta box
	 */
	
	// Bank Info
	$dx_meta_customer_invoice->addTextarea( $prefix . 'cust_bank_info', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Bank Info', 'dxcrm' ), 'desc' => __( 'Enter your Bank Info.', 'dxcrm' ) ) );
	
	// VAT number
	$dx_meta_customer_invoice->addText( $prefix . 'cust_vat_number', array( 'validate_func'=> 'escape_html', 'name'=> __( 'VAT number', 'dxcrm' ), 'desc' => __( 'Enter your VAT number.', 'dxcrm' ) ) );

	// Country 
	$dx_meta_customer_invoice->addText( $prefix . 'cust_country', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Country', 'dxcrm' ), 'desc' => __( 'Enter your Country.', 'dxcrm' ) ) );
	
	//Finish Meta Box Decleration
	$dx_meta_customer_invoice->Finish();

?>