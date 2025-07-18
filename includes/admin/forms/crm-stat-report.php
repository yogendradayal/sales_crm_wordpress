<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) { exit; }

/**
 * CRM Report Listing Page
 * Determine tab name, current selection and content ( form + data )
 * The html markup for the product list
 * 
 * @package CRM System
 * @since 1.0.0
 */

?>
<div class="wrap">
    
    <h2 class="crm-settings-title">
    	<?php _e( 'Reports', 'dxcrm' ); ?>
    </h2>
	<p id="report-notice"></p>
    
    <?php 
		/**
		 * Get currently active tab based on $_GET request
		 * 
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( current_user_can('administrator') ){
			$activetab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'customer';
		}else{
			$activetab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'project';
		}
		
		/**
		 * Default menu slug
		 * 
		 * @package CRM System
		 * @since 1.0.0
		*/
		$menu_slug_url = admin_url( 'admin.php?page=' . DX_CRM_DASHBOARD );
		
		/**
		 * Get reporting tab items using filter
		 * 
		 * @package CRM System
		 * @since 1.0.0
		*/
		$tabs = apply_filters( 'dx_crm_report_tab_item', '' );
	?>	
	<div class="content crm-content-section crm-reports-content">			
		<h2 class="nav-tab-wrapper crm-reports-h2">
			<?php
				/**
				 * Display tab only if it's not empty and is array
				 * 
				 * @package CRM System
				 * @since 1.0.0
				*/
				if( ! empty ( $tabs ) && is_array( $tabs ) ){
					
					/**
					 * Begin tabs display inside loop
					 * 
					 * @package CRM System
					 * @since 1.0.0
					*/
					foreach( $tabs as $tab_key => $tab_value ){
						/**
						 * If active tab is equal to key
						 * add 'nav-tab-active' class
						 * 
						 * @package CRM System
						 * @since 1.0.0
						*/
						$selected = ( $activetab == $tab_key ? 'nav-tab-active' : '' );
						
						/**
						 * Set item href query
						 * 
						 * @package CRM System
						 * @since 1.0.0
						*/
						$url = add_query_arg( array( 'page' => 'dx-crm-stat-setting', 'tab' => $tab_key ), $menu_slug_url );
						
						/**
						 * Echo the tab items
						 * 
						 * @package CRM System
						 * @since 1.0.0
						*/
						echo '<a class = "nav-tab ' . $selected . ' " href="' . $url . '">' . $tab_value . '</a>';
					}
				}
			?>	
		</h2>
		<div class="crm-content">
			<?php
				/**
				 * Display tab content based on active item
				 * 
				 * @package CRM System
				 * @since 1.0.0
				*/
				echo '<div class="tab-content" id="' . $activetab . '">';
				
					/**
					 * For add-on compatibility, check if file exist first
					 * if none, use 'dx_crm_report_tab_content' action
					 * 
					 * @package CRM System
					 * @since 1.0.0
					*/
					if( file_exists( DX_CRM_ADMIN_DIR . '/forms/report/crm-' . $activetab . '-report.php' ) ){
						
						/**
						 * File exist on main plugin, use include_once
						 * 
						 * @package CRM System
						 * @since 1.0.0
						*/
						include_once( DX_CRM_ADMIN_DIR . '/forms/report/crm-' . $activetab . '-report.php' );
						
					}else{
						
						/**
						 * Display tab content using Action API
						 * 
						 * @package CRM System
						 * @since 1.0.0
						*/
						do_action( 'dx_crm_report_tab_content' );
						
					}
		 		echo '</div>';
			?>			
		</div>
	</div>
</div>