<?php

/**
 * Export report to CSV
 *
 * @package CRM System
 * @since ?
*/

class Dx_Crm_Export_Csv {

	public function __construct() {

		add_action( 'admin_init', array( $this, 'generate_csv' ) );

	}

	/**
	 * Check $_GET for generate_csv param and if exist generate CSV
	*/
	public function generate_csv() {

		if ( isset( $_GET['generate_csv'] ) && ! empty( $_GET['generate_csv'] ) && $_GET['generate_csv'] == 1 ) {

			$this->check_report_type( $_GET['dx_crm_report'], array_slice( $_GET, 4 ) );

		}

		if ( isset( $_GET['export_error'] ) && ! empty( $_GET['export_error'] ) && $_GET['export_error'] == 1 ) {

			$this->error_notice();

		}

	}

	/**
	 * Checks for which report has to generate CSV
	*/
	private function check_report_type( $type, $report_args ) {

		if ( 'customer' == $type) {

			$this->customer_csv( $report_args );

		} elseif ( 'project' == $type ) {

			$this->project_csv( $report_args );

		} elseif ( 'company' == $type ) {

			$this->company_csv( $report_args );

		}

	}

	/**
	 * Generates CSV for Customer
	*/
	private function customer_csv( $report_args ) {

		$data = $this->get_data( $report_args );

		if ( ! empty( $data ) ) {

			$columns = array(
				'cust_name' 		=> __( 'Name', 'dxcrm' ),
				'total_project' 	=> __( 'Total Project', 'dxcrm' ),
				'total_paid' 		=> __( 'Total Paid', 'dxcrm' ),
				'cust_contact_date' => __( 'Contract Date', 'dxcrm' ),
				'cust_contact_type' => __( 'Contact Type', 'dxcrm' ),
			);

			$export = $this->prepare_csv_file( $columns, $data );

			$this->output_csv( $export, 'dx-crm-company-report-' );

		} else {

			$this->redirect_report_page( $report_args['dx_crm_report'] );
		
		}

	}

	/**
	 * Generates CSV for Project
	*/
	private function project_csv( $report_args ) {

		$data = $this->get_data( $report_args );

		if ( ! empty( $data ) ) {

			$columns = array(
				'project_name' 				=> __( 'Name', 'dxcrm' ),
				'project_start_date' 		=> __( 'Start Date', 'dxcrm' ),
				'project_planned_end_date' 	=> __( 'End Date', 'dxcrm' ),
				'project_status' 			=> __( 'Status', 'dxcrm' ),
				'project_total_paid' 		=> __( 'Total Paid', 'dxcrm' ),
			);

			$export = $this->prepare_csv_file( $columns, $data );

			$this->output_csv( $export, 'dx-crm-project-report-' );

		} else {

			$this->redirect_report_page( $report_args['dx_crm_report'] );
		
		}
		
	}

	/**
	 * Generates CSV for Company
	*/
	private function company_csv( $report_args ) {

		$data = $this->get_data( $report_args );

		if ( ! empty( $data ) ) {

			$columns = array(
				'comp_name' 		=> __( 'Name', 'dxcrm' ),
				'comp_type' 		=> __( 'Type', 'dxcrm' ),
				'comp_industry' 	=> __( 'Industry', 'dxcrm' ),
				'comp_employees' 	=> __( 'Employees', 'dxcrm' ),
				'comp_currency' 	=> __( 'Currency', 'dxcrm' ),
			);

			$export = $this->prepare_csv_file( $columns, $data );

			$this->output_csv( $export, 'dx-crm-company-report-' );

		} else {

			$this->redirect_report_page( $report_args['dx_crm_report'] );
		
		}
		
	}

	/**
	 * Get data for CSV file
	*/
	private function get_data( $report_args ) {

		global $dx_crm_report;

		return $dx_crm_report->dx_get_report( $report_args );

	}

	/**
	 * Generate CSV file content
	*/
	private function prepare_csv_file( $columns, $data ) {

		// Define content variable
		$export = '';

		foreach ( $columns as $column ) {

			$export .= $column . ',';

		}

		$export .= "\n";

		foreach ( $data as $item ) {

			foreach ( $columns as $key => $column ) {


				// Check if it is company employees and get its range
				if ( 'comp_employees' == $key ) {
					
					$element = $this->comp_employees_gen( $item->$key );

				// Check if project date
				} elseif ( in_array( $key, array( 'project_start_date', 'project_planned_end_date', 'cust_contact_date' ) ) ) {

					$element = $this->date_generate( $item->$key );

				// If it is normal element without needs to be edit before show
				} else {

					$element = $item->$key;
				
				}

				$export .= $element . ',';

			}

			$export .= "\n";

		}

		return $export;

	}

	/**
	 * Gets company employees range 
	*/
	private function comp_employees_gen( $value ) {

		switch ( $value ) {

				case 'EMPLOYEES_1':
					return 'less than 50';
				break;
				
				case 'EMPLOYEES_2':
					return '50 - 250';
				break;

				case 'EMPLOYEES_3':
					return '250 - 500';
				break;
				
				case 'EMPLOYEES_4':
					return 'over 500';
				break;		
				
				default:
					return '';
				break;

		}

	}

	/**
	 * Checks if dates are empty(0000-00-00) or not
	*/
	private function date_generate( $value ) {

		return ( strtotime( $value ) < 0 || ! strtotime( $value ) ) ? '--' : $value;
		
	}

	/**
	 * Outputs the CSV file
	*/
	private function output_csv( $export, $filename ) {
		
		header( 'Content-type: text/x-csv' );
		header( 'Content-Disposition: attachment; filename=' . $filename . date( 'd-m-Y' ) . '.csv' );

		echo $export;

		exit;

	}

	/**
	 * Redirect on report page on error
	*/
	private function redirect_report_page( $tab ) {

		wp_redirect( admin_url( 'admin.php?page=' . DX_CRM_DASHBOARD . '&page=dx-crm-stat-setting&tab=' . $tab . '&export_error=1' ) );

	}

	/**
	 * Show error notice when something went wrong
	*/
	private function error_notice() {

		echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Wrong report criteria or something went wrong! Try again.', 'dxcrm' ) . '</p></div>';	

	}

}