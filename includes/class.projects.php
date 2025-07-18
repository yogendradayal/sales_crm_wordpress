<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Sales_CRM_Project' ) ){
	
	/* 
	 * Project CPT functionality. Custom WP_List_table,
	 * metabox and more.
	 *
	 * @package CRM System
	 * @since 1.0
	*/
	class Sales_CRM_Project{
		
		/** 
		 * WP action and filter hooks
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		public function add_hooks(){
			
		}
		
		/** 
		 * Use this public static function to convert
		 * status ID to readable string
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		public static function display_status_string( $status_id ){
			$status = apply_filters( 'dx_crm_project_status', array() );
			if( isset( $status[$status_id] ) ){
				return $status[$status_id];
			}
			return new WP_Error( 'undefined_status', __( 'This project status does not exist! Please update this record..', 'dxcrm' ) );
		}
	}
	
}