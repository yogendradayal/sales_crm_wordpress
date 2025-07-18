<?php
/**
 * Project Report
 *
 * Handles Project reporting functionality.
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Global variables that we need
*/
global $dx_crm_model, $dx_crm_report;

/*
 *
 * Determine which vertical tab is currently active
 *
*/ 
$activetab = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : 'general';
$post_type = ( ! empty( $_GET['post_type'] ) ) ? sanitize_text_field( $_GET['post_type'] ) : '';
$page      = ( ! empty( $_GET['page'] ) ) ? sanitize_text_field( $_GET['page'] ) : '';
$tab       = ( ! empty( $_GET['tab'] ) ) ? sanitize_text_field( $_GET['tab'] ) : '';


$exportcsvurl = add_query_arg( 
					array( 
						'dx-crm-exp-csv'=>	'1',
						'crm_post_type'	=>	DX_CRM_POST_TYPE_DOC_MNGR
					)
				);
?>
<div id="primary" class="site-content dx-crm-report">
<h2><?php _e( 'Project Filters' , 'dxcrm' );?></h2>
<p><?php _e( 'Generate report by choosing criteria below.' , 'dxcrm' );?></p>

<table border="0" width="100%">
	<tr>
		<td width="20%" valign =" top">
		<?php
			include_once( DX_CRM_ADMIN_DIR.'/forms/report/sub-forms/crm-report-sub-menu-project.php' );		
		?>
		</td>
		<td valign ="top">
		<?php
			echo '<div class="report-tab-content" id="'.$activetab.'">';
				include_once( DX_CRM_ADMIN_DIR.'/forms/report/sub-forms/crm-project-' . $activetab . '-report.php' );
		 	echo '</div>';
		?>
		</td>
	</tr>
</table>
<?php 

/**
 *
 * Display report in table
 *
*/
if( isset( $_POST['project-nonce-report'] ) && ! wp_verify_nonce( $_POST['project-nonce-report'], 'report-project-nonce' ) ){
	/** Throw admin notice */
	do_action( 'wp_settings_admin_notices', $dx_crm_report->crm_rprt_err_ntc() );
	wp_die();
}
	
	$is_default = true;
	
	/** 
	 * General tab should return default report.
	 * Order by ID descending
	*/
	$args = array(
		'dx_crm_report' => 'project',
		'dx_crm_report_type' => 'general',
		'dx_crm_report_is_default' => true
	);

	if( empty( $_POST['dx_crm_report'] ) ){
		$data = $dx_crm_report->dx_get_report( $args );
	} else {
		$is_default = false;
		$data = $dx_crm_report->dx_get_report( $_POST );
	}
	
	/**
	 *
	 * Check what type of report we are going to generate
	 * If GENERAL
	 *
	*/
	if( ! empty ( $_POST['dx_crm_report_type'] ) && $_POST['dx_crm_report_type'] == "general" ){
		
		/** 
		 * This table will display when there's a POST request
		 * and the SQL returns some records
		*/
		if( !empty ( $data ) ){

			/* Display table */
			$html = '<div id="report-table"><h2>' . __( 'Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">';
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $_POST['dx_crm_report'] . '&type=' . $_POST['dx_crm_report_type'] . '&generate_csv=1&' . http_build_query($_POST) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
					</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Start Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'End Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Status' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Start Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'End Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Status' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){	
					
					/** 
					 * Check if total paid is integer/float
					 * Convert to integer 
					*/
					$ttl_pd = (int) $gnrtd_rprt->project_total_paid;
					
					if( is_integer( $ttl_pd ) ){					
						$total_paid = number_format( $gnrtd_rprt->project_total_paid, 2 );
					}else{
						$total_paid = '';
					}
					
					// Start Date
					$strt_dt = ( !empty ( $gnrtd_rprt->project_start_date )  && $gnrtd_rprt->project_start_date != "0000-00-00"  ) ? date( "M d, Y", strtotime( $gnrtd_rprt->project_start_date ) ) : '-' ;
					
					// End Date
					$end_dt = ( !empty ( $gnrtd_rprt->project_planned_end_date ) && $gnrtd_rprt->project_planned_end_date != "0000-00-00" ) ? date( "M d, Y", strtotime( $gnrtd_rprt->project_planned_end_date ) ) : '-' ;
					
					$html .= '	<tr>';	
						$html .= '	<td>' . $gnrtd_rprt->project_name . '</td>';
						$html .= '	<td align="center">' . $strt_dt . '</td>';
						$html .= '	<td align="center">' . $end_dt . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->project_status . '</td>';
						$html .= '	<td align="right">' . $total_paid . '</td>';	
					$html .= '	</tr>';
				}
					$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
			
			echo $html;
			?>
			<script>
				jQuery(document).ready(function() {
				   jQuery('.dx-crm-report-table-result').DataTable();
				} );
			</script>
			<?php
			/** Throw admin notice */
			do_action( 'wp_settings_admin_notices', $dx_crm_report->crm_rprt_sccss_ntc() ); 
			
		} else {
			/** Throw admin notice */
			do_action( 'wp_settings_admin_notices', $dx_crm_report->crm_rprt_err_ntc() );
		}

	}
	
	/*
	 * This is the default report when you go to General tab
	 * This tab should always return a report by default even without
	 * POST Request has been detected
	*/
	if( ! empty ( $data ) && $activetab == "general" ){
		
		/** 
		 * Check if is_default is true. This is necessary as the
		 * General report tends to display double tables if POST
		 * request has been fired.
		*/
		if( ! empty ( $is_default ) ){
			/* Display table */
			$html = '<div id="report-table"><h2>' . __( 'Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">' ;
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $args['dx_crm_report'] . '&type=' . $args['dx_crm_report_type'] . '&generate_csv=1&' . http_build_query($args) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
					</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Start Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'End Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Status' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Start Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'End Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Status' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){	
					
					/** 
					 * Check if total paid is integer/float
					 * Convert to integer 
					*/
					$ttl_pd = (int) $gnrtd_rprt->project_total_paid;
					
					if( is_integer( $ttl_pd ) ){					
						$total_paid = number_format( $gnrtd_rprt->project_total_paid, 2 );
					}else{
						$total_paid = '';
					}
					
					// Start Date
					$strt_dt = ( !empty ( $gnrtd_rprt->project_start_date )  && $gnrtd_rprt->project_start_date != "0000-00-00"  ) ? date( "M d, Y", strtotime( $gnrtd_rprt->project_start_date ) ) : '-' ;
					
					// End Date
					$end_dt = ( !empty ( $gnrtd_rprt->project_planned_end_date ) && $gnrtd_rprt->project_planned_end_date != "0000-00-00" ) ? date( "M d, Y", strtotime( $gnrtd_rprt->project_planned_end_date ) ) : '-' ;
					
					$html .= '	<tr>';	
						$html .= '	<td>' . $gnrtd_rprt->project_name . '</td>';
						$html .= '	<td align="center">' . $strt_dt . '</td>';
						$html .= '	<td align="center">' . $end_dt . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->project_status . '</td>';
						$html .= '	<td align="right">' . $total_paid . '</td>';	
					$html .= '	</tr>';
				}
					$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
			
			echo $html;
		}
	} else {
		/** 
		 * This error message should only fire if no POST Request has been 
		 * called or fired from form. Avoid double firing of admin notices
		 * by checking the is_default
		*/
		if( ! empty ( $is_default ) && $activetab == "general" ){
			/** 
			 * Do we need this? 			 
			 * When you install the plugin for the first time
			 * with empty data, this keeps on firing.
			 * @NOTE: Remove for now..
			*/
			//do_action( 'wp_settings_admin_notices', $dx_crm_report->crm_rprt_err_ntc() );
		}
	}
	
	/**
	 *
	 * Check what type of report we are going to generate
	 * If HIGHEST COST
	 *
	*/
	if( !empty ( $_POST['dx_crm_report_type'] ) && $_POST['dx_crm_report_type'] == "highest-cost" ){
		
		if( !empty ( $data ) ){
					
			// Display table
			$html = '<div id="report-table"><h2>' . __( 'By Project Price Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">';
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $_POST['dx_crm_report'] . '&type=' . $_POST['dx_crm_report'] . '&generate_csv=1&' . http_build_query($_POST) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
					</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Project Price' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Date Interval( Start Date - End Date )' , 'dxcrm' ) . '</th>';
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Project Price' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Date Interval( Start Date - End Date )' , 'dxcrm' ) . '</th>';
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){	

					// Start Date
					$strt_dt = ( !empty ( $gnrtd_rprt->project_start_date )  && $gnrtd_rprt->project_start_date != "0000-00-00"  ) ? $gnrtd_rprt->project_start_date : '-' ;
					
					// End Date
					$end_dt = ( !empty ( $gnrtd_rprt->project_planned_end_date ) && $gnrtd_rprt->project_planned_end_date != "0000-00-00" ) ? $gnrtd_rprt->project_planned_end_date : '-' ;
					
					// Project agreed cost
					$agrd_cst = (int) $gnrtd_rprt->project_agreed_cost;
					
					if( is_integer( $agrd_cst ) ){					
						$p_agrd_cst = number_format( $agrd_cst, 2 );
					}else{
						$p_agrd_cst = '';
					}
					
					$html .= '	<tr>';	
						$html .= '	<td>' . $gnrtd_rprt->project_name . '</td>';
						$html .= '	<td align="right">' . $p_agrd_cst . '</td>';
						$html .= '	<td align="center">' . $strt_dt . ' - ' . $end_dt . '</td>';
					$html .= '	</tr>';
				}
					$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
			
			echo $html;
			?>
			<script>
				jQuery(document).ready(function() {
				   jQuery('.dx-crm-report-table-result').DataTable({
					   "order": [[ 1, "<?php echo strtolower( $_POST['agreed_cost_sort'] ); ?>" ]]
				   });
				} );
			</script>
			<?php
			
			// Throw success message
			$dx_crm_report->crm_rprt_sccss_ntc(); 
			
		} else {
					
			// Throw admin notice
			$dx_crm_report->crm_rprt_err_ntc();
		}

	}
?>
</div>