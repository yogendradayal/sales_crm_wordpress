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
		'id' 			=> 'crm_meta_staff',							// meta box id, unique per meta box
		'title' 		=> __( 'Staff Member Details', 'dxcrm'),		// meta box title
		'pages' 		=> array( DX_CRM_POST_TYPE_STAFF ),			//add meta box for post and pages. if you would like to add it for custom post type(s) make it 'all'.
		'context' 		=> 'advanced',										// where the meta box appear: normal (default), advanced, side; optional
		'priority' 		=> 'high',											// order of meta box: high (default), low; optional
		'fields' 		=> array(),											// list of meta fields (can be added by field arrays)
		'local_images' 	=> false,											// Use local or hosted images (meta box images for add/remove)
		'nonce' 		=> 'at_dx_crm_box_nonce',						// this has to be unique for each plugin
	);			
	
	$dx_meta_potential =  new Dx_Crm_Meta_Box( $config1 );
	
	/*
	 * Initiate your meta box
	 */
	
	// Phone
	$dx_meta_potential->addText( $prefix . 'emp_phone', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Phone', 'dxcrm' ) ) );
	
	// Email
	$dx_meta_potential->addText( $prefix . 'emp_email', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Email', 'dxcrm' ) ) );
		
	// Creating skill terms combo		
	$skills = get_terms( DX_CRM_STAFF_TAXONOMY, array( 'hide_empty' => 0 ) );	
	
	$skills_result = array( '0' => __('Select Skill', 'dxcrm') );	
	if( !empty($skills) ) {
		foreach ( $skills as $skill ) {
			$skills_result[$skill->term_id] = (isset($skill->name)) ? ucfirst($skill->name) : '';
		}
	}
	
	// Skills
	$dx_meta_potential->addSelect( $prefix . 'emp_skills', $skills_result, array( 'name'=> __( 'Skills', 'dxcrm' ), 'std'=> array( '' ) ) );
		
	// Country
	$dx_meta_potential->addText( $prefix . 'emp_country', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Country', 'dxcrm' ) ) );

	// Availability
	$dx_meta_potential->addText( $prefix . 'emp_availability', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Availability', 'dxcrm' ) ) );
	
	// Hourly Rate
	$dx_meta_potential->addText( $prefix . 'emp_hourly_rate', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Hourly Rate', 'dxcrm' ) ) );

	// Social Profiles
	//$dx_meta_potential->addText( $prefix . 'emp_social_profiles', array( 'validate_func'=> 'escape_html', 'name'=> __( 'Social Profiles', 'dxcrm' ) ) );
	do_action( 'dx_crm_get_social_profiles', $dx_meta_potential, DX_CRM_POST_TYPE_STAFF );
	
	//Finish Meta Box Decleration
	$dx_meta_potential->Finish();

?>
