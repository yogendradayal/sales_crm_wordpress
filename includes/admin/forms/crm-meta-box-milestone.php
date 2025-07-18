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
		'id' 			=> 'crm_meta_milestone',					// meta box id, unique per meta box
		'title' 		=> __( 'Milestone Details', 'dxcrm'),		// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_MILESTONES),		//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',								// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',									// order of meta box: high (default), low; optional
		'fields' 		=> array(),									// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,									// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',				// this has to be unique for each plugin
	);			
	
	$dx_meta_milestone =  new Dx_Crm_Meta_Box( $config1 );
	
	/*
	 * Initiate your meta box
	 */

	// List of projects
	$project_list = array();
	$project_data = get_posts(array('post_type' => DX_CRM_POST_TYPE_PROJECTS, 'posts_per_page' => 5));	
	foreach($project_data as $key=>$val){
		$project_list[$val->ID] = $val->post_title;
	}

	// Milestone project that we refer to
	$dx_meta_milestone->addSelect( $prefix . 'mile_pro_ref_to', $project_list, array( 'name'=> __( 'Project', 'dxcrm' ), 'std'=> array( '' ), 'desc' => __( 'Milestone project that we refer to', 'dxcrm' ) ) );
	
	// Start Date
	$dx_meta_milestone->addDate( $prefix . 'mile_start_date', array('name' => __('Start Date', 'dxcrm'),'std' => array(''), 'format'=>'dd-mm-yy' ) );

	// Planned End Date
	$dx_meta_milestone->addDate( $prefix . 'mile_end_date', array('name' => __('Planned End Date', 'dxcrm'),'std' => array(''), 'format'=>'dd-mm-yy' ) );
	
	// Extra cost for the given milestone
	$dx_meta_milestone->addText( $prefix . 'mile_extra_cost', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Extra cost for the given milestone', 'dxcrm' ) ) );
	
	//Finish Meta Box Decleration
	$dx_meta_milestone->Finish();

?>