<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Template Class
 *
 * Handles all public functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */
class Dx_Crm_Templates {
	
	public $model,$scripts,$render,$templates,$templates_path;
	
	public function __construct() {
		
		global $dx_crm_model, $dx_crm_scripts;
		$this->model	= $dx_crm_model;
		$this->scripts	= $dx_crm_scripts;
		
		$templates = $templates_path = array();
		
		//New Template Add For CRM Customer
		add_filter( 'dx_crm_create_page_templates', array( $this, 'dx_crm_create_page_templates' ) );
		
		// Add your templates to this array.
		$templates_arrays = apply_filters( 'dx_crm_create_page_templates', array() );
		
		if( !empty( $templates_arrays ) ) {
			foreach ( $templates_arrays as $templates_key => $templates_array ) {
				
				$templates[$templates_key]		= isset( $templates_array['name'] ) ? $templates_array['name'] : '';
				$templates_path[$templates_key] = isset( $templates_array['dir'] ) ? $templates_array['dir'] : '';
			}
		}
		
		// Add your templates to this array.
		$this->templates = $templates;
		$this->templates_path = $templates_path;
	}
	
	/**
	 * CRM System custom page Template
	 * 	 
	 * templates for CRM System
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 **/
	public function dx_crm_create_page_templates( $templates_array = array() ) {
		
		$templates_array['crm-customer-template.php']	= array( 
															'name' 	=> __( 'CRM Customer Template', 'dxcrm' ),
															'dir' 	=> DX_CRM_TPL_DIR
														);
	    $templates_array['crm-project-template.php']	= array( 
															'name' 	=> __( 'CRM Project Template', 'dxcrm' ),
															'dir' 	=> DX_CRM_TPL_DIR
														);
														
		$templates_array['crm-company-template.php']	= array( 
															'name' 	=> __( 'CRM Company Template', 'dxcrm' ),
															'dir' 	=> DX_CRM_TPL_DIR
														);													
		
		return $templates_array;
	}
	
	/**
	 * Add Templates
	 *
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$themes_templates = wp_get_theme()->get_page_templates();

		// Retrieve the cache list. If it doesn't exist, or it's empty prepare an array
		$templates = wp_cache_get( $cache_key, 'themes' );
		
		// Merge templates with themes templates
		$templates = array_merge( $themes_templates, $templates );
		
		if ( empty( $templates ) ) {
			
			$templates = array();
		}

		// Since we've updated the cache, we need to delete the old cache
		wp_cache_delete( $cache_key , 'themes' );

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;
	}
	
	/**
	 * Assign template to page
	 * 
	 * Checks if the template is assigned to the page
	 * 
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_view_template( $template ) {

		global $post;
		
		if( isset( $post->ID ) ) {
			
			if ( !isset( $this->templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) {
				
				return $template;
			}
			
			$file = $this->templates_path[ get_post_meta( $post->ID, '_wp_page_template', true ) ] .'/'. get_post_meta( $post->ID, '_wp_page_template', true );
			
			// Just to be safe, we check if the file exist first
			if( file_exists( $file ) ) {
				
				return $file;
			}
		}
		
		return $template;
	}
	
	/**
	 * Adding Hooks
	 *
	 * Adding proper hooks for the public class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		// Add a filter to the page attributes metabox to inject our template into the page template cache.
		add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'dx_crm_register_project_templates' ) );

		// Add a filter to the save post in order to inject out template into the page cache
		add_filter( 'wp_insert_post_data', array( $this, 'dx_crm_register_project_templates' ) );

		// Add a filter to the template include in order to determine if the page has our template assigned and return it's path
		add_filter( 'template_include', array( $this, 'dx_crm_view_template' ) );
		
	}
}
