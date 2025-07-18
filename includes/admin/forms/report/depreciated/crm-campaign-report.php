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

// If not Administrator
if ( !current_user_can( 'administrator' ) ){
	return;
}

global $dx_crm_model, $dx_crm_report;

$post_type 	= ( !empty( $_GET['post_type'] ) ) ? $_GET['post_type'] : '' ;
$page 		= ( !empty( $_GET['page'] ) ) ? $_GET['page'] : '' ;
$tab		= ( !empty( $_GET['tab'] ) ) ? $_GET['tab'] : '' ;

$exportcsvurl = add_query_arg( 
					array( 
						'dx-crm-exp-csv'=>	'1',
						'crm_post_type'	=>	DX_CRM_POST_TYPE_ROADMAP
					)
				);	

// Contact Type
$cmpgn_cntct_type = array(
						//__( 'Please select..' , 'dxcrm' ),
						__( 'Email' , 'dxcrm' ),
						__( 'Phone' , 'dxcrm' ),
						__( 'Social Network' , 'dxcrm' )
					);

?>
<div id="primary" class="site-content dx-crm-report">
<?php 

/**
 *
 * Display report in table
 *
*/
if( !empty ( $_POST['dx_crm_report'] ) && $_POST['dx_crm_report'] == "campaign" ){
	
	$data = $dx_crm_report->dx_get_report( $_POST );
	
	if( !empty ( $data ) ){
				
		// Display table
		$html = '<div id="report-table"><h2>' . __( 'Query Result' , 'dxcrm' ) . '</h2>';
		$html .= '<span class="dx-crm-bttn-hldr"><a href="edit.php?post_type=' . DX_CRM_POST_TYPE_ROADMAP . '&page=dx-crm-stat-setting&tab=campaign" class="button button-primary">' . __( 'New Report' , 'dxcrm' ) . '</a></span>';
		$html .= '<table id="customer_report" class="display dx-crm-report-table-result" cellspacing="0" width="100%">';
			$html .= '<thead>';
					$html .= '	<tr>';
						$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
						$html .= '	<th>' . __( 'Type' , 'dxcrm' ) . '</th>';
						$html .= '	<th>' . __( 'Customers' , 'dxcrm' ) . '</th>';
					$html .= '	</tr>';
				$html .= '	</thead>';
				$html .= '	<tfoot>';
					$html .= '	<tr>';
						$html .= '	<th>' . __( 'Name' , 'dxcrm' ) . '</th>';
						$html .= '	<th>' . __( 'Type' , 'dxcrm' ) . '</th>';
						$html .= '	<th>' . __( 'Customers' , 'dxcrm' ) . '</th>';
					$html .= '	</tr>';
				$html .= '	</tfoot>';
				$html .= '<tbody>';		
			foreach( $data as $gnrtd_rprt ){					
				$html .= '	<tr>';	
					
					// Employees					
					switch( $gnrtd_rprt->camp_contact_type ){
						case'0':
							$cntct_typ = 'Email';
						break;
						case'1':
							$cntct_typ = 'Phone';
						break;
						case'2':
							$cntct_typ = 'Social Network';
						break;
						default:
							$cntct_typ = '';
						break;
					}
					
					$html .= '	<td width="30%">' . $gnrtd_rprt->camp_name . '</td>';					
					$html .= '	<td align="center" width="35%">' . $cntct_typ . '</td>';
					$html .= '	<td align="left" width="35%">' . $gnrtd_rprt->camp_customers . '</td>';	
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
		
		// Throw success message
		do_action( 'admin_notices', $dx_crm_report->crm_rprt_sccss_ntc() ); 
		
	} else {

		// Throw admin notice
		do_action( 'admin_notices', $dx_crm_report->crm_rprt_err_ntc() );
		
		goto showform;
	}	
	
}else{

showform: 

?>	
<h2><?php _e( 'Campaign Filters' , 'dxcrm' );?></h2>
<p><?php _e( 'Generate report by choosing criteria below.' , 'dxcrm' );?></p>

<form action="" method="post" id="crm-project-form">
	
	<input type="hidden" name="dx_crm_report" value="campaign">
	
	<table border="0" class="aligncenter" width="100%" id="dx-crm-report-table">
		<tr>
		
			<td><?php _e( 'Contact Type' , 'dxcrm' );?>:</td>
			<td>
				<select name = "contact_type" id = "contact_type" class="chosen-select">
					<?php
						/**
						 * Contact Type
						*/
						foreach( $cmpgn_cntct_type as $key => $ctype ){
							echo '<option value="' . $key . '">' . $ctype . '</option>';
						}
					?>
				</select>
			</td>
			
			<td><?php _e( 'Customers' , 'dxcrm' );?>:</td>
			<td><?php echo $dx_crm_model->crm_customer_dropdown('customers'); ?></td>
			
		</tr>
	</table>
	
	<input type="submit" class="button button-primary" name="submit" value="<?php _e( 'Generate Report' , 'dxcrm' );?>" />
</form>
<?php
}
?>
</div>