<?php
/**
 * Roadmap Logger Class
 *
 * Handles all roadmap activity logger functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

Class DX_CRM_ROADMAP_TRACKING{
	
	/**
	 * Use this variable for storing old post data
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	private $old_post_data = array();
	
	/**
	 * Use this variable for storing new post data
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	private $new_post_data = array();
	
	/**
	 * Set hooks, actions etc.
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function add_hooks() {
		/**
		 * Get old project data before saving
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		add_action("admin_action_editpost", array( $this, 'on_editpost_check'), 10, 3  );
		
		/**
		 * When Project post status changed, we do comparing data here
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		add_action("transition_post_status", array( $this, 'on_transition_post_status_check' ), 10, 3);
		
		add_action( 'admin_enqueue_scripts', array( $this, 'roadmap_tracking_script' ) );
		add_action( 'delete_post', array( $this, 'on_delete_post' ) );
	}
	
	/**
	 * Include the css file for roadmap tracking page only
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function roadmap_tracking_script( $hook ){	
		if( $hook == 'sales-crm_page_dx-crm-activity-log' ){
			wp_register_style( 'dx-crm-activity-log', DX_CRM_ASSETS_URL . '/css/min/dx-crm-activity-log.min.css', array(), '1.0' );
			wp_enqueue_style( 'dx-crm-activity-log' );
		}
	}
	
	/**
	 * Capture changes based on $_POST for new data
	 * and get_post for old data. Store each on private 
	 * variables to be used later
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function on_editpost_check(){		
		$post_ID = isset( $_POST["post_ID"] ) ? (int) $_POST["post_ID"] : 0;
		if ( ! $post_ID ) {
			return;
		}		
		$prev_post_data = get_post( $post_ID );
		
		/**
		 * If previous post is not Project, do nothing
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( DX_CRM_POST_TYPE_PROJECTS == $prev_post_data->post_type ){				
			// Store old data
			$this->old_post_data[$post_ID] = array(
				"post_data" => $prev_post_data,
				"post_meta" => get_post_custom( $post_ID )
			);			
			// Store new meta data
			$project_type = ! empty( $_POST['tax_input']['crm_pro_type'] ) ? $_POST['tax_input']['crm_pro_type'] : '' ;
			$company_project = ! empty( $_POST[DX_CRM_META_PREFIX . 'company_project'] ) ? $_POST[DX_CRM_META_PREFIX . 'company_project'] : '' ;
			$pro_start_date = ! empty( $_POST[DX_CRM_META_PREFIX . 'pro_start_date'] ) ? $_POST[DX_CRM_META_PREFIX . 'pro_start_date'] : '' ;
			$pro_end_date = ! empty( $_POST[DX_CRM_META_PREFIX . 'pro_end_date'] ) ? $_POST[DX_CRM_META_PREFIX . 'pro_end_date'] : '' ;
			$pro_ongoing = ! empty ( $_POST[DX_CRM_META_PREFIX . 'pro_ongoing'] ) ? $_POST[DX_CRM_META_PREFIX . 'pro_ongoing'] : '' ;
			$pro_real_end_date_first_mile = ! empty ( $_POST[DX_CRM_META_PREFIX . 'pro_real_end_date_first_mile'] ) ? $_POST[DX_CRM_META_PREFIX . 'pro_real_end_date_first_mile'] : '' ;
			$pro_real_end_date_last_conversation = ! empty ( $_POST[DX_CRM_META_PREFIX . 'pro_real_end_date_last_conversation'] ) ? $_POST[DX_CRM_META_PREFIX . 'pro_real_end_date_last_conversation'] : '' ;
			$pro_agreed_cost = ! empty ( $_POST[DX_CRM_META_PREFIX . 'pro_agreed_cost'] ) ? $_POST[DX_CRM_META_PREFIX . 'pro_agreed_cost'] : '' ;
			$project_currency = ! empty ( $_POST[DX_CRM_META_PREFIX . 'project_currency'] ) ? $_POST[DX_CRM_META_PREFIX . 'project_currency'] : '' ;
			$project_status = ! empty ( $_POST[DX_CRM_META_PREFIX . 'project_status'] ) ? $_POST[DX_CRM_META_PREFIX . 'project_status'] : '' ;
			$project_total = ! empty ( $_POST[DX_CRM_META_PREFIX . 'project_total'] ) ? $_POST[DX_CRM_META_PREFIX . 'project_total'] : '' ;
			$project_assigned_by = ! empty ( $_POST[DX_CRM_META_PREFIX . 'project_assigned_by'] ) ? $_POST[DX_CRM_META_PREFIX . 'project_assigned_by'] : '' ;
			$joined_pro_customer = ! empty ( $_POST[DX_CRM_META_PREFIX . 'joined_pro_customer'] ) ? $_POST[DX_CRM_META_PREFIX . 'joined_pro_customer'] : '' ;			
			// Store new post data
			$this->new_post_data[$post_ID] = array(
				'crm_pro_type' => $project_type,
				DX_CRM_META_PREFIX . 'company_project' => $company_project,
				DX_CRM_META_PREFIX . 'pro_start_date' => $pro_start_date,
				DX_CRM_META_PREFIX . 'pro_end_date' => $pro_end_date,
				DX_CRM_META_PREFIX . 'pro_ongoing' => $pro_ongoing,
				DX_CRM_META_PREFIX . 'pro_real_end_date_first_mile' => $pro_real_end_date_first_mile,
				DX_CRM_META_PREFIX . 'pro_real_end_date_last_conversation' => $pro_real_end_date_last_conversation,
				DX_CRM_META_PREFIX . 'pro_agreed_cost' => $pro_agreed_cost,
				DX_CRM_META_PREFIX . 'project_currency'	=> $project_currency,
				DX_CRM_META_PREFIX . 'project_status' => $project_status,
				DX_CRM_META_PREFIX . 'project_total' => $project_total,
				DX_CRM_META_PREFIX . 'project_assigned_by' => $project_assigned_by,
				DX_CRM_META_PREFIX . 'joined_pro_customer' => $joined_pro_customer
			);
			
		}		
	}
	
	/**
	 * Record project post changes based on post status transition_post_status
	 * We can handle almost every post transition except for "Delete Permanently"
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function on_transition_post_status_check($new_status, $old_status, $post) {		
		if ( wp_is_post_revision( $post ) ) {
			return;
		}

		$trackable = array( 
			DX_CRM_POST_TYPE_PROJECTS 			=> 'Project', 
			DX_CRM_POST_TYPE_CUSTOMERS 			=> 'Customer',
			DX_CRM_POST_TYPE_COMPANY 			=> 'Company',
			DX_CRM_POST_TYPE_COMPANY_EXPENSES 	=> 'Company Expense',
		);

		/**
		 * If previous post is not Project/Customer/Company, do nothing
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( array_key_exists( $post->post_type, $trackable ) ){
			$summary = "";			
			if ( $old_status == "auto-draft" && 
					( $new_status != "auto-draft" && $new_status != "inherit" ) 
				){
					// New Post has been created
					$summary .= sprintf( 
									__( 'Added new %s %s!', 'dxcrm' ),
									$trackable[ $post->post_type ],
									$post->post_title
								);
				}				
			if ( $new_status == "auto-draft" || 
					($old_status == "new" && $new_status == "inherit") 
				){
					// WP Autosave, no summary given
					$summary .= "";
				}			
			if ( $new_status == "trash" ) {
				// Post trashed
				$summary .= sprintf( 
								__( '%s %s has been trashed!', 'dxcrm' ),
								$trackable[ $post->post_type ],
								$post->post_title
							);
			}			
			if ( $new_status == "publish" ) {				
				// Check if trash-publish
				if( $old_status == "trash" ){
					$summary .= sprintf( 
								__( '%s %s has been restored from trash!', 'dxcrm' ),
								$trackable[ $post->post_type ],
								$post->post_title
							) . "\n";
				} else {
					$summary .= sprintf( 
								__( '%s %s has been updated!', 'dxcrm' ),
								$trackable[ $post->post_type ],
								$post->post_title
							) . "\n";
							
					// Post updated
					// Check changes per meta data
					if( ! empty ( $this->new_post_data[ $post->ID ] ) ){
						foreach( $this->new_post_data[ $post->ID ] as $key => $data ){						
							if( ! empty ( $this->old_post_data[ $post->ID ]['post_meta'][$key] ) ){
								$old_meta = $this->old_post_data[ $post->ID ]['post_meta'][$key][0];
								$compare = $this->compare( $data, $old_meta );							
								if( !empty ( $compare ) ){
									$summary .= sprintf( 
													__( '%s has been updated into <strong>%s</strong>', 'dxcrm' ), 
													$this->proper_key($key), 
													$this->proper_date( $data )
												) . "\n";
								}
							}
						}
					}					
				}				
			}			
			if( ! empty( $summary ) ){
				$this->record( $post->ID, $summary );
			}
		}
	}
	
	/**
	 * Format date properly. Use this for date metadata
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function proper_date( $data, $format = 'Y-m-d H:i:s' ) {
		if( preg_match( "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $data ) ){
			return date( "F dS, Y", strtotime( $data ) );
		}		
		return $data;
	}
	
	/**
	 * When permanently delete project/post.
	 * we can't get the post status anymore
	 * use post_id
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function on_delete_post($post_id){		
		$post = get_post($post_id);
		if ( wp_is_post_revision($post_id) ) {
			return;
		}
		if ( $post->post_status === "auto-draft" || $post->post_status === "inherit" ) {
			return;
		}		
		// We record the porject post type only
		if( $post->post_type == DX_CRM_POST_TYPE_PROJECTS ){
			$summary = sprintf( 
							__( 'Delete project %s permanently!', 'dxcrm' ),
							$post->post_title
						);
			$this->record( $post->ID, $summary );
		}
	}
	
	/**
	 * Change raw keys to readable string
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function proper_key( $key ){		
		if( empty ( $key ) ){
			return;
		}		
		switch( $key ){
			case DX_CRM_META_PREFIX . 'company_project' :
				return "Company";
			break;
			case DX_CRM_META_PREFIX . 'pro_start_date' :
				return "Start Date";
			break;
			case DX_CRM_META_PREFIX . 'pro_end_date' :
				return "End Date";
			break;
			case DX_CRM_META_PREFIX . 'pro_ongoing' :
				return "Project Ongoing";
			break;
			case DX_CRM_META_PREFIX . 'pro_real_end_date_first_mile' :
				return "Real End Date for first milestone";
			break;
			case DX_CRM_META_PREFIX . 'pro_real_end_date_last_conversation' :
				return "Real End Date for last conversation";
			break;
			case DX_CRM_META_PREFIX . 'pro_agreed_cost' :
				return "Agreed Cost";
			break;
			case DX_CRM_META_PREFIX . 'project_currency' :
				return "Currency";
			break;
			case DX_CRM_META_PREFIX . 'project_status' :
				return "Project status";
			break;
			case DX_CRM_META_PREFIX . 'project_total' :
				return "Total Paid";
			break;
			case DX_CRM_META_PREFIX . 'project_assigned_by' :
				return "Responsible person";
			break;
			case DX_CRM_META_PREFIX . 'joined_pro_customer' :
				return "Customers";
			break;
		}		
	}
	
	/**
	 * Compare data to check if updated or not
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function compare( $new, $old ){
		if( empty ( $old ) || is_array( $old ) ){
			return;
		}		
		if( empty ( $new ) || is_array( $new ) ){
			return;
		}		
		// If both are equal, no changes has been made
		$compare = ( $old === $new ) ? 0 : 1 ;		
		return $compare;		
	}
	
	/**
	 * Store roadmap record
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function record( $post_id, $summary ){		
		global $wpdb;		
		$table = $wpdb->prefix . 'crm_roadmap';		
		$wpdb->insert(
				$table,
				array(
					'roadmap_time' 			=> date( "Y-m-d H:i:s", strtotime("NOW") ),
					'roadmap_user' 			=> get_current_user_id(),
					'roadmap_project_id' 	=> $post_id,
					'roadmap_summary' 		=> $summary
				),
				array(
					'%s',
					'%d',
					'%d',
					'%s'
				)
		);		
		$wpdb->flush();		
	}

	/**
	 * Get all roadmap months
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function get_crm_roadmap_months() {
		global $wpdb;
		$crm_roadmap_table = $wpdb->prefix . 'crm_roadmap';
		$crm_roadmap_times = $wpdb->get_results( 'SELECT roadmap_time FROM '. $crm_roadmap_table .' ', OBJECT );
		$wpdb->flush();	
		$months = array();
		foreach ($crm_roadmap_times as $crm_roadmap_time ) {
			$month = date( "F Y", strtotime( $crm_roadmap_time->roadmap_time ) );
			if ( ! in_array( $month, $months ) ) {
				array_push( $months, $month );
			}
		}
		return $months;
	}
	
	/**
	 * Get all roadmap record
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function get_all_roadmap(){
		global $wpdb;		
		$table = $wpdb->prefix . 'crm_roadmap';
		$all_roadmap = $wpdb->get_results( "SELECT * FROM {$table} order by ID DESC", OBJECT );		
		$wpdb->flush();		
		return $all_roadmap;		
	}
	
	/**
	 * Build the sql query to select roadmap reord
	 *
	 * @param string $month - selected month
	 * @param int $user_id - selected user_id
	 * @package CRM System
	 * @since 1.0.0
	*/
	private function build_roadmap_sql_query( $page, $month, $user_id, $s ) {
		if( ! empty( $month ) && ! is_string( $month ) ) {
			return;
		}
		if( ! empty( $user_id ) && ! is_string( $user_id ) ) {
			return;
		}
		if( ! empty( $s ) && ! is_string( $s ) ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'crm_roadmap';
		$start_from = ( $page - 1 ) * 10;
		if ( ! empty( $month ) || ! empty( $user_id ) || ! empty( $s ) ) {
			$sql = "SELECT * FROM {$table} WHERE ";
			$sql .= $this->add_where_to_sql( $month, $s, $user_id );
		} else {
			$sql = "SELECT * FROM {$table}";
		}
		
		$sql .= " order by ID DESC LIMIT $start_from, 10";
		return $sql;
	}

	/**
	 * Get some roadmap record
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function get_some_roadmap( $page = 1, $month = '', $user_id = '', $s = '' ){
		global $wpdb;
		$sql = $this->build_roadmap_sql_query( $page, $month, $user_id, $s);
		$some_roadmap = $wpdb->get_results( $sql, OBJECT );
		$wpdb->flush();		
		return $some_roadmap;		
	}

	
	/**
	 * Count roadmap records in database
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function count_all_roadmaps( $month="", $s="", $user_id="" ){
		global $wpdb;
		$table = $wpdb->prefix . 'crm_roadmap';
		if ( ! empty( $month ) || ! empty( $user_id ) || ! empty( $s ) ) {
			$sql = "SELECT COUNT(ID) AS rcount FROM {$table} WHERE ";
			$sql .= $this->add_where_to_sql( $month, $s, $user_id );
		} else {
			$sql = "SELECT COUNT(ID) AS rcount FROM {$table}";
		}

		$roadmap_count = $wpdb->get_results( $sql, ARRAY_N );		
		$wpdb->flush();	
		if ( is_array($roadmap_count) && isset($roadmap_count[0][0]) ) {
			return $roadmap_count[0][0];		
		}
		return 0;
	}

	/**
	 * Add WHERE to SQL query if needed
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	private function add_where_to_sql( $month, $s, $user_id ) {
		$sql = '';

		if ( ! empty( $month ) ) {
			$month = strtotime( $month );
			$month = date( 'Y-m', $month );
			$sql .= " roadmap_time LIKE '{$month}%'";
		}

		if ( ! empty( $s ) ) {
			if ( ! empty( $month ) || ! empty( $user_id) ){
				$sql .= " AND ";
			}
			$sql .= "roadmap_time LIKE '%{$s}%' OR roadmap_project_id LIKE '%{$s}%' OR roadmap_summary LIKE '%{$s}%' ";
		}

		if ( ! empty( $user_id ) ) {
			if ( ! empty( $month ) ){
				$sql .= " AND ";
			} 
			$sql .= " roadmap_user = '{$user_id}'";			
		}

		return $sql;
	}
	
}

?>