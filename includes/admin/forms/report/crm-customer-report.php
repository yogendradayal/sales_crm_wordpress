<?php

/**
 * Customer report printing
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ){ exit; }

/**
 * For administrator only
 *
 * @package CRM System
 * @since 1.0.0
 */
if ( ! current_user_can( 'administrator' ) ){
	return;
}

/**
 * Set global variables
 *
 * @package CRM System
 * @since 1.0.0
 */
global $dx_crm_model, $dx_crm_report;

/**
 * Add export URL
 *
 * @package CRM System
 * @since 1.0.0
 */
$exportcsvurl = add_query_arg( 
	array( 
		'dx-crm-exp-csv'=>	'1',
		'crm_post_type'	=>	DX_CRM_POST_TYPE_DOC_MNGR
	)
);

/*
 * Determine which vertical tab is currently active
 *
 * @package CRM System
 * @since 1.0.0
 */ 
$activetab = isset($_GET['type']) ? $_GET['type'] : 'general' ;

?>
<div id="primary" class="site-content dx-crm-report">	

<h2><?php _e( 'Customer Filters' , 'dxcrm' ); ?></h2>
<p><?php _e( 'Generate report by choosing criteria below.' , 'dxcrm' ); ?></p>

<table border="0" width="100%">
	<tr>
		<td width="20%" valign =" top">
		<?php
			include_once( DX_CRM_ADMIN_DIR.'/forms/report/sub-forms/crm-report-sub-menu-customer.php' );		
		?>
		</td>
		<td valign ="top">
		<?php
			echo '<div class="report-tab-content" id="'.$activetab.'">';
				include_once( DX_CRM_ADMIN_DIR.'/forms/report/sub-forms/crm-customer-' . $activetab . '-report.php' );
		 	echo '</div>';
		?>
		</td>
	</tr>
</table>
<?php 

/**
 * Display report in table
 *
 * @package CRM System
 * @since 1.0.0
 */
if( isset( $_POST['customer-nonce-report'] ) && ! wp_verify_nonce( $_POST['customer-nonce-report'], 'report-customer-nonce' ) ){
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
		'dx_crm_report' => 'customer',
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
	if( ( ! empty ( $_POST['dx_crm_report_type'] ) && $_POST['dx_crm_report_type'] == "general" ) && ( isset( $_POST['customer-nonce-report'] ) && wp_verify_nonce( $_POST['customer-nonce-report'], 'report-customer-nonce' ) ) ){
		
		/** 
		 * This table will display when there's a POST request
		 * and the SQL returns some records
		*/
		if( ! empty ( $data ) ){
			
			/* Display table */
			$html = '<div id="report-table"><h2>' . __( 'General Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">
						<a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $_POST['dx_crm_report'] . '&type=' . $_POST['dx_crm_report_type'] . '" class="button button-primary">' . __( 'New Report' , 'dxcrm' ) . '</a>';
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $_POST['dx_crm_report'] . '&type=' . $_POST['dx_crm_report_type'] . '&generate_csv=1&' . http_build_query($_POST) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
					</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contract Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contact Type' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contract Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contact Type' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){	
					
					/** 
					 * Check if total paid is integer/float
					 * Convert to integer 
					*/
					$ttl_pd = (int) $gnrtd_rprt->total_paid;
					
					if( is_integer( $ttl_pd ) ){					
						$total_paid = number_format( $gnrtd_rprt->total_paid, 2 );
					}else{
						$total_paid = '';
					}

					$date = ( strtotime( $gnrtd_rprt->cust_contact_date ) > 0 ) ? date( "M d, Y", strtotime( $gnrtd_rprt->cust_contact_date ) ) : '--';
					
					$html .= '	<tr>';	
						$html .= '	<td>' . $gnrtd_rprt->cust_name . '</td>';
						$html .= '	<td align="right">' . $gnrtd_rprt->total_project . '</td>';
						$html .= '	<td align="right">' . $total_paid . '</td>';
						$html .= '	<td align="right">' . $date . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->cust_contact_type . '</td>';	
					$html .= '	</tr>';
				}
					$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
			
			echo $html;
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
			/** Display table */
			$html = '<div id="report-table"><h2>' . __( 'General Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">' ;
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $args['dx_crm_report'] . '&type=' . $args['dx_crm_report_type'] . '&generate_csv=1&' . http_build_query($args) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
					</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contract Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contact Type' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contract Date' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Contact Type' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){	
					
					/** 
					 * Check if total paid is integer/float
					 * Convert to integer 
					*/
					$ttl_pd = (int) $gnrtd_rprt->total_paid;
					
					if( is_integer( $ttl_pd ) ){					
						$total_paid = number_format( $gnrtd_rprt->total_paid, 2 );
					}else{
						$total_paid = '';
					}

					$date = ( strtotime( $gnrtd_rprt->cust_contact_date ) > 0 ) ? date( "M d, Y", strtotime( $gnrtd_rprt->cust_contact_date ) ) : '--';
					
					$html .= '	<tr>';	
						$html .= '	<td>' . $gnrtd_rprt->cust_name . '</td>';
						$html .= '	<td align="right">' . $gnrtd_rprt->total_project . '</td>';
						$html .= '	<td align="right">' . $total_paid . '</td>';
						$html .= '	<td align="right">' . $date . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->cust_contact_type . '</td>';	
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
			/** Throw admin notice */
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
	 * If BY LARGEST INCOME
	 *
	*/
	if( ! empty ( $_POST['dx_crm_report_type'] ) && $_POST['dx_crm_report_type'] == "largest-income" ){
		
		if( ! empty ( $data ) && 
			( isset( $_POST['dx_crm_report_type'] ) && $_POST['dx_crm_report_type'] == "largest-income" )
		){
			/** Display table */
			$html = '<div id="report-table"><h2>' . __( 'By Largest Income Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">';
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $_POST['dx_crm_report'] . '&type=' . $_POST['dx_crm_report'] . '&generate_csv=1&' . http_build_query($_POST) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
					</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';							
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';							
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){	
					
					/** 
					 * Check if total paid is integer/float
					 * Convert to integer 
					*/
					$ttl_pd = (int) $gnrtd_rprt->total_paid;
					
					if( is_integer( $ttl_pd ) ){					
						$total_paid = number_format( $gnrtd_rprt->total_paid, 2 );
					}else{
						$total_paid = '';
					}
					
					$html .= '	<tr>';	
						$html .= '	<td>' . $gnrtd_rprt->cust_name . '</td>';
						$html .= '	<td align="right">' . $total_paid . '</td>';						
						$html .= '	<td align="right">' . $gnrtd_rprt->total_project . '</td>';
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
						"order": [[ 1, "<?php echo strtolower( $_POST['total_paid'] ); ?>" ]]
				   });
				} );
			</script>
			<?php
			/** Throw admin notice */
			$dx_crm_report->crm_rprt_sccss_ntc(); 			
		} else {
			/** Throw admin notice */
			$dx_crm_report->crm_rprt_err_ntc();			
		}
	}	
	
	/**
	 *
	 * Check what type of report we are going to generate
	 * If BY LARGEST PROJECTS
	 *
	*/
	if( !empty ( $_POST['dx_crm_report_type'] ) && $_POST['dx_crm_report_type'] == "largest-project" ){
		
		if( ! empty ( $data ) && 
			( isset( $_POST['dx_crm_report_type'] ) && $_POST['dx_crm_report_type'] == "largest-project" )
		){
			/** Display table */
			$html = '<div id="report-table"><h2>' . __( 'By Largest Project Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">';
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $_POST['dx_crm_report'] . '&type=' . $_POST['dx_crm_report'] . '&generate_csv=1&' . http_build_query($_POST) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
					</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';		
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';						
							$html .= '	<th>' . __( 'Total Project' , 'dxcrm' ) . '</th>';							
							$html .= '	<th>' . __( 'Total Paid' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){	
					
					/** 
					 * Check if total paid is integer/float
					 * Convert to integer 
					*/
					$ttl_pd = (int) $gnrtd_rprt->total_paid;
					
					if( is_integer( $ttl_pd ) ){					
						$total_paid = number_format( $gnrtd_rprt->total_paid, 2 );
					}else{
						$total_paid = '';
					}
					
					$html .= '	<tr>';	
						$html .= '	<td>' . $gnrtd_rprt->cust_name . '</td>';											
						$html .= '	<td align="right">' . $gnrtd_rprt->total_project . '</td>';
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
				   jQuery('.dx-crm-report-table-result').DataTable({
						"order": [[ 1, "<?php echo strtolower( $_POST['total_project'] ); ?>" ]]
				   });
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
?>
</div>