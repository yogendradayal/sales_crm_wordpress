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
		'id' 			=> 'crm_meta_timesheet',						// meta box id, unique per meta box
		'title' 		=> __( 'Timesheet Details', 'dxcrm'),			// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_TIMESHEETS),			//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',									// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',										// order of meta box: high (default), low; optional
		'fields' 		=> array(),										// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,										// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',					// this has to be unique for each plugin
	);			
	
	$dx_meta_timesheet =  new Dx_Crm_Meta_Box( $config1 );
	
	/*
	 * Initiate your meta box
	 */
		
	// Employee Data
	$dx_meta_timesheet->addText( $prefix . 'time_employee_data', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Employee Data', 'dxcrm' ) ) );
		
	// Start Time
	$dx_meta_timesheet->addTime( $prefix . 'time_start_time', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Start Time', 'dxcrm' ) ) );

	// End Time
	$dx_meta_timesheet->addTime( $prefix . 'time_end_time', array( 'validate_func'=> 'escape_html', 'name'=> __( 'End Time', 'dxcrm' ) ) );
	
	// Date
	$dx_meta_timesheet->addDate( $prefix . 'time_date', array('name' => __('Date', 'dxcrm'),'std' => array(''), 'format'=>'dd-mm-yy' ) );	

	// Title
	$dx_meta_timesheet->addText( $prefix . 'time_title', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Title', 'dxcrm' ) ) );
	
	// Description
	$dx_meta_timesheet->addTextarea( $prefix . 'time_description', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Description', 'dxcrm' ), 'desc' => __( 'Enter Description.', 'dxcrm' ) ) );
	
	//Finish Meta Box Decleration
	$dx_meta_timesheet->Finish();

?>