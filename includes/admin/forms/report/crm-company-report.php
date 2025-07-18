<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $dx_crm_model, $dx_crm_report;

$prefix = DX_CRM_META_PREFIX;

$exportcsvurl = add_query_arg( 
					array( 
						'dx-crm-exp-csv'=>	'1',
						'crm_post_type'	=>	DX_CRM_POST_TYPE_ROADMAP,						
					)
				);
?>
<div id="primary" class="site-content dx-crm-report">
<h2><?php _e( 'Company Filters' , 'dxcrm' );?></h2>
<p><?php _e( 'Generate report by choosing criteria below.' , 'dxcrm' );?></p>

<form action="" method="post" id="crm-compani-form" class="crm-company-form form-dx-crm-report">
	
	<?php 
		/* Determine what type of report we are going to generate */
	?>
	<input type="hidden" name="dx_crm_report" value="company">
	
	<table border="0" class="aligncenter" id="dx-crm-report-table">
		<tr>
		
			<td><?php _e( 'Company Type' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_company_type_dropdown('company_type',false); ?></td>
			
			<td><?php _e( 'Industry' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_company_industry_dropdown('company_industry', false); ?></td>
			
		</tr>
		<tr>
		
			<td><?php _e( 'Employees' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_company_employees_dropdown('company_employees',false); ?></td>
			
			<td><?php _e( 'Currency' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_currency_dropdown( 'currency', false ); ?></td>
			
		</tr>
		<tr>
			<?php
				/**
				 * Remove customer if current user is SALES_CRM_CUSTOMER
				*/
				if( current_user_can( 'administrator' ) ){
			?>
			<td><?php _e( 'Customers' , 'dxcrm' );?>:</td>
			<td>
				<?php echo $dx_crm_model->crm_customer_dropdown('company_assign_customer'); ?>
			</td>
			<?php
				}
			?>
			
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			
		</tr>
	</table>

	<?php wp_nonce_field( 'report-company-nonce', 'company-nonce-report' );	?>
	
	<input type="submit" class="button button-primary" name="submit" value="<?php _e( 'Generate Report' , 'dxcrm' );?>" />
	
</form>
<?php 

/**
 *
 * Display report in table
 *
*/
if( isset( $_POST['company-nonce-report'] ) && ! wp_verify_nonce( $_POST['company-nonce-report'], 'report-company-nonce' ) ){
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
		'dx_crm_report' => 'company',
		'dx_crm_report_type' => 'general',
		'dx_crm_report_is_default' => true
	);

	if( empty( $_POST['dx_crm_report'] ) ){
		$data = $dx_crm_report->dx_get_report( $args );
	} else {
		$is_default = false;
		$data = $dx_crm_report->dx_get_report( $_POST );
	}
	
	if( ! empty ( $_POST['dx_crm_report'] ) && $_POST['dx_crm_report'] == "company" ){
		if( ! empty ( $data ) ){

			/* Display table */
			$html = '<div id="report-table"><h2>' . __( 'Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr"><a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=company" class="button button-primary">' . __( 'New Report' , 'dxcrm' ) . '</a>';
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $_POST['dx_crm_report'] . '&type=' . $_POST['dx_crm_report'] . '&generate_csv=1&' . http_build_query($_POST) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
						</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Type' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Industry' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Employees' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Currency' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Type' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Industry' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Employees' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Currency' , 'dxcrm' ) . '</th>';
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){					
					$html .= '	<tr>';	
						
						// Employees					
						switch( $gnrtd_rprt->comp_employees ){
							case'EMPLOYEES_1':
								$mplys = 'less than 50';
							break;
							case'EMPLOYEES_2':
								$mplys = '50-250';
							break;
							case'EMPLOYEES_3':
								$mplys = '250-500';
							break;
							case'EMPLOYEES_4':
								$mplys = 'over 500';
							break;
							default:
								$mplys = '';
							break;
						}
						
						$html .= '	<td>' . $gnrtd_rprt->comp_name . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->comp_type . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->comp_industry . '</td>';
						$html .= '	<td align="center">' . $mplys . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->comp_currency . '</td>';	
					$html .= '	</tr>';
				}
					$html .= '</tbody>';
			$html .= '</table>';
			$html .= '</div>';
			
			echo $html;
			// Throw success message
			$dx_crm_report->crm_rprt_sccss_ntc(); 
			
		} else {		
			$dx_crm_report->crm_rprt_err_ntc();
		}	
	}
	
	/*
	 * This is the default report when you go to General tab
	 * This tab should always return a report by default even without
	 * POST Request has been detected
	*/
	if( ! empty ( $data ) ){
		
		/** 
		 * Check if is_default is true. This is necessary as the
		 * General report tends to display double tables if POST
		 * request has been fired.
		*/
		if( ! empty ( $is_default ) ){
			/* Display table */
			$html = '<div id="report-table"><h2>' . __( 'Query Result' , 'dxcrm' ) . '</h2>';
			$html .= '<span class="dx-crm-bttn-hldr">';
			$html .= ' <a href="admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $args['dx_crm_report'] . '&type=' . $args['dx_crm_report'] . '&generate_csv=1&' . http_build_query($args) . '" class="button button-primary">' . __( 'Export as CSV' , 'dxcrm' ) . '</a>
						</span>';
			$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
				$html .= '<thead>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Type' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Industry' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Employees' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Currency' , 'dxcrm' ) . '</th>';	
						$html .= '	</tr>';
					$html .= '	</thead>';
					$html .= '	<tfoot>';
						$html .= '	<tr>';
							$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Type' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Industry' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Employees' , 'dxcrm' ) . '</th>';
							$html .= '	<th>' . __( 'Currency' , 'dxcrm' ) . '</th>';
						$html .= '	</tr>';
					$html .= '	</tfoot>';
					$html .= '<tbody>';		
				foreach( $data as $gnrtd_rprt ){
					$html .= '	<tr>';							
						// Employees					
						switch( $gnrtd_rprt->comp_employees ){
							case'EMPLOYEES_1':
								$mplys = 'less than 50';
							break;
							case'EMPLOYEES_2':
								$mplys = '50-250';
							break;
							case'EMPLOYEES_3':
								$mplys = '250-500';
							break;
							case'EMPLOYEES_4':
								$mplys = 'over 500';
							break;
							default:
								$mplys = '';
							break;
						}
						
						$html .= '	<td>' . $gnrtd_rprt->comp_name . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->comp_type . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->comp_industry . '</td>';
						$html .= '	<td align="center">' . $mplys . '</td>';
						$html .= '	<td align="center">' . $gnrtd_rprt->comp_currency . '</td>';	
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
			do_action( 'wp_settings_admin_notices', $dx_crm_report->crm_rprt_err_ntc() );
		}
	}	
?>
</div>