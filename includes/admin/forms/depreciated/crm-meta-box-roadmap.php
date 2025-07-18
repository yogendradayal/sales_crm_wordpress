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
	$roadmap_meta_config = array(
		'id' 			=> 'crm_meta_roadmap',						// meta box id, unique per meta box
		'title' 		=> __( 'Roadmap Details', 'dxcrm' ),		// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_ROADMAP ),		//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',								// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',									// order of meta box: high (default), low; optional
		'fields' 		=> array(),									// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,									// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',					// this has to be unique for each plugin
	);			
	
	$dx_meta_roadmap =  new Dx_Crm_Meta_Box( $roadmap_meta_config );
	
	/*
	 * Initiate your meta box
	*/
	$args = array(
				'post_type'=> DX_CRM_POST_TYPE_PROJECTS,
				'posts_per_page'=> 5,
			);
			
	$projects = get_posts( $args );
		
	foreach ($projects as $project_data ){		
		$project_list[$project_data->ID] = $project_data->post_title;
	}
	
	$project_list = (isset($project_list) && !empty($project_list)) ? $project_list : '';
	
	// Dropdown list of projects to track
	$dx_meta_roadmap->addSelect( $prefix . 'rm_project', 
								$project_list, 
								array( 
									'name'=> __( 'Project', 'dxcrm' ), 
									'std'=> array( '' ), 
									'desc' => __( 'Select Project for this Roadmap.', 'dxcrm' ), 
									'multiple' => false, 
								) 
							);
	// Admin option to allow other user to access or not.
	$dx_meta_roadmap->addSelect( $prefix . 'rm_access_option', 
								array( 
									'true' 	=> __( 'Yes', 'dxcrm' ),
									'false' => __( 'No', 'dxcrm' )
								), 
								array( 
									'name'=> __( 'Exclusive for Admin only?', 'dxcrm' ), 
									'std'=> array( '' ), 
									'desc' => __( 'Should this Roadmap accessible to Administrator exclusively?', 'dxcrm' ), 
									'multiple' => false, 
								) 
							);
	$dx_meta_roadmap->Finish();
?>