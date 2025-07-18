<?php
/**
 * Post Type Functionality
 *
 * Handles generic plugin functionality.
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Setup Post Type
 *
 * Handles register type functionality
 * 
 * @package CRM System
 * @since 1.0.0 
 */

function dx_crm_register_post_types() {

	/**
	 * Create Custom Post Type For Company
	 * 
	 * @package CRM System
	 * @since 1.0.0 
	 */
	$company_labels = array(
		'name'				=> __('Company','dxcrm'),
		'singular_name' 	=> __('Company','dxcrm'),
		'add_new' 			=> __('Add New','dxcrm'),
		'add_new_item' 		=> __('Add New Company','dxcrm'),
		'edit_item' 		=> __('Edit Company','dxcrm'),
		'new_item' 			=> __('New Company','dxcrm'),
		'all_items' 		=> __('Companies','dxcrm'),
		'view_item' 		=> __('View Company','dxcrm'),
		'search_items' 		=> __('Search Company','dxcrm'),
		'not_found' 		=> __('No Companies found','dxcrm'),
		'not_found_in_trash'=> __('No Companies found in Trash','dxcrm'),
		'parent_item_colon' => '',
		'menu_name' 		=> __('Companies','dxcrm'),
	);
	$company_cap_slug = DX_CRM_POST_TYPE_COMPANY;
	$company_caps = array(	
		'edit_post'          => "edit_{$company_cap_slug}", 
		'read_post'          => "read_{$company_cap_slug}", 
		'delete_post'        => "delete_{$company_cap_slug}", 
		'edit_posts'         => "edit_{$company_cap_slug}s", 
		'edit_others_posts'  => "edit_others_{$company_cap_slug}s", 
		'publish_posts'      => "publish_{$company_cap_slug}s",       
		'read_private_posts' => "read_private_{$company_cap_slug}s", 
		'create_posts'       => "edit_{$company_cap_slug}s", 
	);	
	$company_args = array(
		'labels' 			=> $company_labels,
		'public' 			=> true,
		'publicly_queryable'=> true,
		'show_ui' 			=> true, 
		'menu_icon'			=> DX_CRM_IMG_URL . 'Company_hover.png',
		'query_var' 		=> true,
		'rewrite' 			=> array( 'slug' => DX_CRM_POST_TYPE_COMPANY ),
		'capability_type'	=> DX_CRM_POST_TYPE_COMPANY,
		'capabilities' 		=> $company_caps,
		'map_meta_cap' 		=> true,
		'has_archive' 		=> true, 
		'hierarchical' 		=> false,
		'menu_position' 	=> null,
		'exclude_from_search' => false,
		'supports' 			=> array( 'title', 'editor', 'author' )
	);	

	/**
	 * Create Custom Post Type For Projects
	 * 
	 * @package CRM System
	 * @since 1.0.0 
	 */
	$pro_labels = array(
		'name'				=> __('Projects ','dxcrm'),
		'singular_name' 	=> __('Project','dxcrm'),
		'add_new' 			=> __('Add New','dxcrm'),
		'add_new_item' 		=> __('Add New Project','dxcrm'),
		'edit_item' 		=> __('Edit Project','dxcrm'),
		'new_item' 			=> __('New Project','dxcrm'),
		'all_items' 		=> __('Projects','dxcrm'),
		'view_item' 		=> __('View Project','dxcrm'),
		'search_items' 		=> __('Search Project','dxcrm'),
		'not_found' 		=> __('No projects found','dxcrm'),
		'not_found_in_trash'=> __('No projects found in Trash','dxcrm'),
		'parent_item_colon' => '',
		'menu_name' 		=> __('Projects','dxcrm'),
	);
	$project_args = array(
		'labels' 			=> $pro_labels,
		'public' 			=> true,
		'publicly_queryable'=> true,
		'show_ui' 			=> true, 
		'query_var' 		=> true,
		'menu_icon'			=> DX_CRM_IMG_URL . 'Project_hover.png',
		'rewrite' 			=> array( 'slug' => DX_CRM_POST_TYPE_PROJECTS ),
		'capability_type' 	=> DX_CRM_POST_TYPE_PROJECTS,
		'map_meta_cap' 		=> true,
		'has_archive' 		=> true, 
		'hierarchical' 		=> false,
		'menu_position' 	=> null,
		'supports' 			=> array( 'title', 'editor', 'author' )
	);
	
	/**
	 * Create Custom Post Type For Customers
	 * 
	 * @package CRM System
	 * @since 1.0.0 
	 */
	$cust_labels = array(
		'name'				=> __('Customers ','dxcrm'),
		'singular_name' 	=> __('Customer','dxcrm'),
		'add_new' 			=> __('Add New','dxcrm'),
		'add_new_item' 		=> __('Add New Customer','dxcrm'),
		'edit_item' 		=> __('Edit Customer','dxcrm'),
		'new_item' 			=> __('New Customer','dxcrm'),
		'all_items' 		=> __('Customers','dxcrm'),
		'view_item' 		=> __('View Customer','dxcrm'),
		'search_items' 		=> __('Search Customer','dxcrm'),
		'not_found' 		=> __('No customers found','dxcrm'),
		'not_found_in_trash'=> __('No customers found in Trash','dxcrm'),
		'parent_item_colon' => '',
		'menu_name' => __('Customers','dxcrm'),
	);
	$customer_args = array(
		'labels'				=> $cust_labels,
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true, 
		'show_in_menu'			=> true, 
		'menu_icon'				=> DX_CRM_IMG_URL . 'Customer_hover.png',
		'query_var'				=> true,
		'rewrite'				=> array( 'slug' => DX_CRM_POST_TYPE_CUSTOMERS ),
		'capability_type'		=> 'post',
		'map_meta_cap'			=> true,
		'has_archive'			=> true, 
		'hierarchical'			=> false,
		'menu_position'			=> null,
		'supports'				=> array( 'title', 'author' )				    
	);	
	
	/**
	 * Create Custom Post Type For Document Managements
	 * 
	 * @package CRM System
	 * @since 1.0.0 
	 */
	$doc_labels = array(
		'name'				=> __('Document','dxcrm'),
		'singular_name' 	=> __('Documents','dxcrm'),
		'add_new' 			=> __('Add New','dxcrm'),
		'add_new_item' 		=> __('Add New Document','dxcrm'),
		'edit_item' 		=> __('Edit Document','dxcrm'),
		'new_item' 			=> __('New Document','dxcrm'),
		'all_items' 		=> __('Documents','dxcrm'),
		'view_item' 		=> __('View Document','dxcrm'),
		'search_items' 		=> __('Search Document','dxcrm'),
		'not_found' 		=> __('No document found','dxcrm'),
		'not_found_in_trash'=> __('No document found in Trash','dxcrm'),
		'parent_item_colon' => '',
		'menu_name' => __('Document','dxcrm'),
	);
	$doc_man_args = array(
		'labels' 			=> $doc_labels,
		'public' 			=> false,
		'publicly_queryable'=> true,
		'show_ui' 			=> true, 
		'show_in_menu' 		=> true,
		'menu_icon'			=> DX_CRM_IMG_URL . 'Document_Management_hover.png',
		'query_var' 		=> true,
		'rewrite' 			=> array( 'slug' => DX_CRM_POST_TYPE_DOC_MNGR ),
		'capability_type' 	=> 'post',
		'map_meta_cap' 		=> true,
		'has_archive' 		=> true, 
		'hierarchical' 		=> false,
		'menu_position' 	=> null,
		'supports' 			=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);
	
	/**
	 * Drop the Document Management for now
	*/
	//register_post_type( DX_CRM_POST_TYPE_DOC_MNGR, $doc_man_args );
	register_post_type( DX_CRM_POST_TYPE_COMPANY, $company_args );
	register_post_type( DX_CRM_POST_TYPE_PROJECTS, $project_args );
	register_post_type( DX_CRM_POST_TYPE_CUSTOMERS, $customer_args );
	
}


/**
 * Register Category/Taxonomy
 *
 * Register Category like wordpress
 *
 * @package CRM System
 * @since 1.0.0
 */
function dx_crm_reg_taxonomy() {

	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name' => _x( 'Project Type', 'taxonomy general name','dxcrm' ),
		'singular_name' => _x( 'Project Type', 'taxonomy singular name','dxcrm' ),
		'search_items' =>  __( 'Search Project Type' ,'dxcrm'),
		'all_items' => __( 'Project Types' ,'dxcrm'),
		'parent_item' => __( 'Parent Project Type' ),
		'parent_item_colon' => __( 'Parent Project Type:','dxcrm' ),
		'edit_item' => __( 'Edit Project Type' ,'dxcrm'), 
		'update_item' => __( 'Update Project Type','dxcrm' ),
		'add_new_item' => __( 'Add New Project Type','dxcrm' ),
		'new_item_name' => __( 'New Project Type','dxcrm' ),
		'menu_name' => __( 'Project Type','dxcrm' ),
	);
	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false//array( 'slug' => WP_CPN_POST_TYPE )
	);
	register_taxonomy(DX_CRM_PRO_TAXONOMY,array(DX_CRM_POST_TYPE_PROJECTS), $args);
	
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name' => _x( 'Skill', 'taxonomy general name','dxcrm' ),
		'singular_name' => _x( 'Skill', 'taxonomy singular name','dxcrm' ),
		'search_items' =>  __( 'Search Skill' ,'dxcrm'),
		'all_items' => __( 'Skills' ,'dxcrm'),
		'parent_item' => __( 'Parent Skill' ),
		'parent_item_colon' => __( 'Parent Skill:','dxcrm' ),
		'edit_item' => __( 'Edit Skill' ,'dxcrm'), 
		'update_item' => __( 'Update Skill','dxcrm' ),
		'add_new_item' => __( 'Add New Skill','dxcrm' ),
		'new_item_name' => __( 'New Skill Name','dxcrm' ),
		'menu_name' => __( 'Skills','dxcrm' ),
	);
	$args = array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => false//array( 'slug' => WP_CPN_POST_TYPE )
	);
	register_taxonomy( DX_CRM_SKILL_TAXONOMY, array( DX_CRM_POST_TYPE_CUSTOMERS ), $args );
}

add_action( 'init', 'dx_crm_register_post_types' );

//add categories add/update/delete of wordpress without any extra tables
add_action('init','dx_crm_reg_taxonomy');

?>