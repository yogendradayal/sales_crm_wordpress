<?php
/**
 * Model Class
 *
 * Handles generic plugin functionality.
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Script Class
 *
 * Handles generic plugin functionality.
 *
 * @package CRM System
 * @since 1.0.0
 */
class Dx_Crm_Model{

	function __construct(){

	}

	/**
	 * Escape Tags & Slashes
	 *
	 * Handles escapping the slashes and tags
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_escape_attr( $data ) {
		return esc_attr( stripslashes($data) );
	}

	/**
	 * Strip Slashes From Array
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_escape_slashes_deep($data = array(), $flag = false) {

		if( $flag != true ) {
			$data = $this->dx_crm_nohtml_kses($data);
		}
		$data = stripslashes_deep($data);
		return $data;
	}

	/**
	 * Strip Html Tags
	 *
	 * It will sanitize text input (strip html tags, and escape characters)
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function dx_crm_nohtml_kses( $data = array() ) {

		if ( is_array($data) ) {

			$data = array_map(array($this,'dx_crm_nohtml_kses'), $data);

		} elseif ( is_string( $data ) ) {

			$data = wp_filter_nohtml_kses($data);
		}

		return $data;
	}

	/**
	 * Customer User Role
	 *
	 * @package CRM System
	 * @since 1.0.0
	 */
	public function get_customers_dropdown( $selected_value = '' ) {

		$args	= array( 'role' => DX_CRM_CUSTOMER_ROLE, 'orderby' => 'display_name', 'order' => 'ASC' );

		$query 	= new WP_User_Query( $args );

		$output	= '<option value="0">'.__( 'Select Customer' , 'dxcrm' ).'</option>';

		if( $query->get_total() ) {

			$users	=	$query->get_results();

			foreach( $users as $key => $value) {

				$selected = '';
				if( $selected_value == $value->ID ) $selected = 'selected="selected"';

				$output	.= '<option value="'.$value->ID.'" '.$selected.'>' .$value->display_name. '</option>';
			}
		}
		return $output;
	}

	public function crm_get_project_status(){

		$project_status = array(
							'Draft'						=> __('Draft', 'dxcrm'),
							'Sent'						=> __('Sent to client', 'dxcrm'),
							'Reviewed'					=> __('Being reviewed by client', 'dxcrm'),
							'Approved'					=> __('Approved', 'dxcrm'),
							'UnAnswered'				=> __('No response', 'dxcrm'),
							'Declined'					=> __('Declined', 'dxcrm'),
							'In Development'			=> __('In Development', 'dxcrm'),
							'Awaiting Review'			=> __('Awaiting Review', 'dxcrm'),
							'Successfully Completed'	=> __('Successfully Completed', 'dxcrm'),
					  );
		return $project_status;
	}

	public function crm_get_project_status_listing(){

		$project_status = array(
							'0'	=> __('Draft', 'dxcrm'),
							'3'	=> __('Approved', 'dxcrm'),
							'5'	=> __('Declined', 'dxcrm'),
							'6'	=> __('In Development', 'dxcrm'),
							'8'	=> __('Successfully Completed', 'dxcrm'),
					  );
		return $project_status;
	}

	public static function crm_object_to_array( $result ) {

			$array = array();
		    foreach ( $result as $key=>$value ) {
		        if( is_object($value) ) {
		            $array[$key] = Dx_Crm_Model::crm_object_to_array($value);
		        } else {
		        	$array[$key]=$value;
		        }
		    }
		    return $array;
	}

	/**
	 * Sets the string attached to a custom query for limitting results
	 *
	 * @access private
	 *
	 * @param  array $args  sorting and pagination parameters
	 *
	 * @return string   sql order by and limit parameters
	 */
	private function crm_get_customer_details_query_params( $args = array() ){

		// defaults.
		$orderby 		= 'customer_name';
		$order 			= 'desc';
		$offset 		= 0;
		$page 			= 0;
		// this should be changed later to get plugin profided user settings
		$posts_per_page = get_option( 'posts_per_page' ); // wordpress default.
		$sOrderby 		= '';
		$sLimit 		= '';

		// if paranoid mode, we should be comparing to a set of valid column names
		if( isset( $args['orderby'] ) && ( trim( $args['orderby'] ) > 0 ) ){
			$orderby = trim( $args['orderby'] );
		}

		if( isset( $args['order'] ) ){

			$sOrder 		= strtolower(trim( $args['order'] ));
			$aValid_orders 	= array( 'asc',  'desc' );

			if( in_array( $sOrder, $aValid_orders) ){
				$order = trim( $sOrder );
			}
		}

		// offset
		if( isset( $args['offset'] ) &&  ( intval( $args['offset'] ) > 0 ) ){
			$offset = intval( $args['offset'] );
		}

		// page - this value represents current page
		if( isset( $args['page'] ) &&  ( intval( $args['page'] ) > 0 ) ){
			$page = intval( $args['page'] );
		}

		// posts_per_page
		if( isset( $args['posts_per_page'] ) &&  ( intval( $args['posts_per_page'] ) > 0 ) ){
			$page = intval( $args['posts_per_page'] );
		}

		$sOrderby = ' ORDER BY '. trim($orderby) .' '. trim($order);
		$sLimit = ' LIMIT ' . intval( $offset ) . ',' . intval($posts_per_page);

		return $sOrderby.' '.$sLimit;
	}

	/**
	 * Method retrieves basic customer info including
	 * customer project data
	 *
	 * @param  array  $args  sorting parameters
	 *
	 * @return array  $result array formated sql result
	 *
	 */
	public function crm_get_customer_details_custom( $args = array() ){
			global $wpdb;

			$queryargs	=	array(
								'post_type'		=> DX_CRM_POST_TYPE_CUSTOMERS,
								'post_status' 	=> 'publish'
							);

			$query = "  SELECT 	p.ID,
								p.post_author,
								p.post_content,
								p.post_title as customer_name,
								p.post_status,
								p.post_name,
								SUM(CASE WHEN p.ID = proj.cust_id THEN proj.amount ELSE 0 END ) as total_paid,
								SUM(CASE WHEN p.ID = proj.cust_id THEN 1 ELSE 0 END ) as total_project
					    FROM  ". $wpdb->prefix . "posts p
						LEFT JOIN
							( SELECT jp.post_id as cust_id, jp.meta_value as project_id,
								( 	SELECT SUM(meta_value) FROM ". $wpdb->prefix . "postmeta amt
									WHERE ((jp.meta_value = amt.post_id ) AND (amt.meta_key = %s))
								) as amount
							FROM ". $wpdb->prefix . "postmeta jp WHERE jp.meta_key = %s
							) as proj

						ON ( p.ID = proj.cust_id)
					    WHERE ( p.post_type = %s AND p.post_status = %s )
						GROUP BY p.ID ";

			$prep = $wpdb->prepare( $query,
									DX_CRM_META_PREFIX . 'project_total',
									DX_CRM_META_PREFIX . 'joined_project',
									$queryargs['post_type'],
									$queryargs['post_status']
								  );

			// set the order/sort and page params
			$conditions = $this->crm_get_customer_details_query_params( $args );
			// append to query
			$result = $wpdb->get_results( $prep . $conditions, ARRAY_A);

 			return $result;
	}

	/**
	 * get all customer data from the post table. Note this is a plain grab.
	 * It gets all customer rows but not all their respective data.
	 * uses WP_Query
	 *
	 * @param  string $fields no current use.
	 * @param  array  $args   query meta
	 *
	 * @return array  customer info
	 */
	public function crm_get_all_customer( $args = array(), $fields = '' ){
		if( empty($args) || !is_array( $args ) ){
			$args =	array(
				  			'post_type' => DX_CRM_POST_TYPE_CUSTOMERS,
				  			'post_status' => 'publish',
				  			'posts_per_page' => -1
		  				);
		}

		$my_query = new WP_Query($args);
		return $my_query->posts;
	}

	public function crm_get_customer_details( $args=array() ){

			$companies = array();

			$queryargs	=	array(
								'post_type'		=> DX_CRM_POST_TYPE_CUSTOMERS,
								'post_status' 	=> 'publish'

							);

			$query_args = wp_parse_args( $args, $queryargs );

			$result = new WP_Query( $query_args );

			//retrived data is in object format so assign that data to array for listing
			$postslist = $this->crm_object_to_array( $result->posts );

			$data['data'] = $postslist;

			$data['total'] = isset($result->found_posts) ? $result->found_posts : '';

			return $data;
		}

		public function crm_get_project_details( $args=array() ){

			$companies = array();
			$prefix = DX_CRM_META_PREFIX;
			$start 	= isset($_GET['start_date'])?$_GET['start_date']:"";
			$end 	= isset($_GET['end_date'])?$_GET['end_date']:"";

			$queryargs	=	array(
									'post_type'		=> DX_CRM_POST_TYPE_PROJECTS,
									'post_status' 	=> 'publish',
									);

			if(!empty($start) && !empty($end) ){
				$queryargs['meta_query']	=	 array(
											        array(
											            'key' => $prefix.'pro_start_date',
											            'value' => array($this->dateformat($start), $this->dateformat($end)),
											            'compare' => 'BETWEEN',
											            'type' => 'DATE'
											        )
											    );
			}elseif (!empty($start))  {
					$queryargs['meta_query']	= array(
											        array(
											            'key' => $prefix.'pro_start_date',
											            'value' => $this->dateformat($start),
											            'compare' => '>=',
											            'type' => 'DATE'
											        )
											    );
			}elseif (!empty($end))  {
					$queryargs['meta_query']	= array(
											        array(
											            'key' => $prefix.'pro_start_date',
											            'value' => $this->dateformat($end),
											            'compare' => '<=',
											            'type' => 'DATE'
											        )
											    );
			}

			$query_args = wp_parse_args( $args, $queryargs );

			$result = new WP_Query( $query_args );

			//retrived data is in object format so assign that data to array for listing
			$postslist = $this->crm_object_to_array( $result->posts );

			$data['data'] = $postslist;

			$data['total'] = isset($result->found_posts) ? $result->found_posts : '';

			return $data;
		}
		// Date format
		public function dateformat($date){
			$date = strtotime( $date );
			$date = date('Y-m-d',$date);
			return $date;
		}
	/**
	 *
	 * Display customers in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	*/
	public function crm_customer_dropdown( $nid="customers", $multi=true, $cselect=true ){
		
		// Check if has customers
		$cmpgn_cstmrs = get_posts( array( 'posts_per_page' => -1, 'post_type' => DX_CRM_POST_TYPE_CUSTOMERS ) );
		
		// If has Customers: return Select
		// Else: return Error
		if( !empty( $cmpgn_cstmrs ) ){
			
			if( $multi == true ){
				$n_arr = '[]';
				$multi = 'multiple';
			} else {
				$n_arr = '';
				$multi = '';
			}
			$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
			
			
			$cstmrs = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
				foreach ( $cmpgn_cstmrs as $cstmr ) {
					$cstmrs .= '<option value="' . $cstmr->post_title . '">' . $cstmr->post_title . '</option>';
				}
			$cstmrs .= '</select>';
			
			return $cstmrs;
		} else {
			return 'No customers found!';
		}
	}
	
	/**
	 *
	 * Display company in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	*/
	public function crm_company_dropdown( $nid="companies", $multi=true, $cselect=true ){
		
		// Check if has company
		$cust = get_posts( array( 'posts_per_page' => -1, 'post_type' => DX_CRM_POST_TYPE_COMPANY ) );
		
		// If has Company: return Select
		// Else: return Error
		if( !empty( $cust ) ){
			
			if( $multi == true ){
				$n_arr = '[]';
				$multi = 'multiple';
			} else {
				$n_arr = '';
				$multi = '';
			}
			
			$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
			
			$cmpnys = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$cmpnys .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';
				
			foreach ( $cust as $usr ) {
				$cmpnys .= '<option value="' . $usr->post_title . '">' . $usr->post_title . '</option>';
			} 
			
			$cmpnys .= '<select>';
			
			return $cmpnys;
			
		} else {
				return 'No company found!';
		}
		
	}
	
	/**
	 *
	 * Display project in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	*/
	public function crm_project_dropdown( $nid="projects", $multi=true, $cselect=true ){
		
		// Check if has project
		$prjcts = get_posts( array( 'posts_per_page' => -1, 'post_type' => DX_CRM_POST_TYPE_PROJECTS ) );
		
		// If has Project: return Select
		// Else: return Error
		if( !empty( $prjcts ) ){
			
			if( $multi == true ){
				$n_arr = '[]';
				$multi = 'multiple';
			} else {
				$n_arr = '';
				$multi = '';
			}
			
			$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
			
			$prjct = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$prjct .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';
				
			foreach ( $prjcts as $usr ) {
				$prjct .= '<option value="' . $usr->post_title . '">' . $usr->post_title . '</option>';
			} 
			
			$prjct .= '<select>';
			
			return $prjct;
			
		} else {
				return 'No project found!';
		}
		
	}
	
	/**
	 *
	 * Display campaign in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	*/
	public function crm_campaign_dropdown( $nid="campaigns", $multi=true, $cselect=true ){
		
		// Check if has campaign
		$cmpgns = get_posts( array( 'posts_per_page' => -1, 'post_type' => 'crm_campaign' ) );
		
		// If has Campaign: return Select
		// Else: return Error
		if( !empty( $cmpgns ) ){
			
			if( $multi == true ){
				$n_arr = '[]';
				$multi = 'multiple';
			} else {
				$n_arr = '';
				$multi = '';
			}
			
			$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
			
			$cmpgn = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$cmpgn .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';
				
			foreach ( $cmpgns as $usr ) {
				$cmpgn .= '<option value="' . $usr->post_title . '">' . $usr->post_title . '</option>';
			} 
			
			$cmpgn .= '<select>';
			
			return $cmpgn;
			
		} else {
				return 'No campaign found!';
		}
		
	}
	
	/**
	 *
	 * Display Project Type in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	*/
	public function crm_project_type_dropdown( $nid="project_type",$multi=true, $cselect=true ){
		
		
		$cats_args = array(
			'hide_empty' => 0,
			'number'     => '5',
		);
		
		// Project Type
		$project_cats = get_terms( DX_CRM_PRO_TAXONOMY, $cats_args );
		$prjct_types = array();
		if ( ! empty( $project_cats ) ) {
			foreach ( $project_cats as $project_cat ) {
				$prjct_types[ $project_cat->term_id ] = ( isset( $project_cat->name ) ) ? ucfirst( $project_cat->name ) : '';
			}
		}	

		// If has Project Type: return Select
		// Else: return Error
		if( ! empty( $prjct_types ) ){
			
			if( $multi == true ){
				$n_arr = '[]';
				$multi = 'multiple';
			} else {
				$n_arr = '';
				$multi = '';
			}
			
			$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
			
			$prjct_type = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$prjct_type .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';
				
			foreach ( $prjct_types as $key => $value ) {
				$prjct_type .= '<option value="' . $key . '">' . $value . '</option>';
			} 
			
			$prjct_type .= '<select>';
			
			return $prjct_type;
			
		} else {
				return 'No project type found!';
		}
		
	}
	
	/**
	 *
	 * Display Contact Type in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	*/
	public function crm_contact_type_dropdown( $nid="contact_type", $multi=true, $cselect=true ){

		$cntct_ctypes = apply_filters( 'dx_crm_contact_type', array() );					
			
		if( $multi == true ){
			$n_arr = '[]';
			$multi = 'multiple';
		} else {
			$n_arr = '';
			$multi = '';
		}
			
		$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
			
		$cntct_ctype = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
		$cntct_ctype .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';
				
		foreach ( $cntct_ctypes as $usr ) {
			$cntct_ctype .= '<option value="' . $usr . '">' . $usr . '</option>';
		} 
			
		$cntct_ctype .= '<select>';
		
		return $cntct_ctype;
	}
	
	/**
	 *
	 * Display Project Status in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	*/
	public function crm_project_status_dropdown( $nid="project_status", $multi=true, $cselect=true ){		

		$prjct_sttss = apply_filters( 'dx_crm_project_status', array() );

		if( $multi == true ){
			$n_arr = '[]';
			$multi = 'multiple';
		} else {
			$n_arr = '';
			$multi = '';
		}
			
		$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
			
		$prjct_stts = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
		$prjct_stts .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';
				
		foreach ( $prjct_sttss as $usr ) {
			$prjct_stts .= '<option value="' . $usr . '">' . $usr . '</option>';
		} 
			
		$prjct_stts .= '<select>';
		
		return $prjct_stts;
	}
	
	/**
	 * Display Currency in dropdown.
	 * @param name & id, multiple, class
	 *
	 * @package CRM System
	 * @since 1.0.0
	 *
	*/
	public function crm_currency_dropdown( $nid="currency", $multi=true, $cselect=true ){
		
		/**
		 * Default currency
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$cmpny_crrncys = apply_filters( 'dx_crm_company_currency', array() );
		
		/**
		 * Set proper variable if multi select is true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		if( $multi == true ){
			$n_arr = '[]';
			$multi = 'multiple';
		} else {
			$n_arr = '';
			$multi = '';
		}
		
		/**
		 * Add 'chosen-select' class if set to true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
		
		/**
		 * Create select dropdown markup
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		if( ! empty ( $cmpny_crrncys ) && is_array( $cmpny_crrncys ) ){
			$cmpny_crrncy = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$cmpny_crrncy .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';				
				foreach ( $cmpny_crrncys as $usr ) {
					$cmpny_crrncy .= '<option value="' . $usr . '">' . $usr . '</option>';
				}
			$cmpny_crrncy .= '<select>';
		}else{
			return new WP_Error( 'invalid_data', __( "Invalid company currency!" , "dxcrm" ) );
		}
		
		return $cmpny_crrncy;
	}
	
	/**
	 * Display Company type in dropdown.
	 * 
	 * @param name & id, multiple, class
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function crm_company_type_dropdown( $nid="company_type", $multi=true, $cselect=true ){
		
		/**
		 * Get default company types
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$cmpny_types = apply_filters( 'dx_crm_company_type', array() );

		/**
		 * Set proper variable if multi select is true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/		
		if( $multi == true ){
			$n_arr = '[]';
			$multi = 'multiple';
		} else {
			$n_arr = '';
			$multi = '';
		}
		
		/**
		 * Add 'chosen-select' class if set to true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
		
		/**
		 * Create select dropdown markup
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		if( ! empty ( $cmpny_types ) && is_array( $cmpny_types ) ){
			$cmpny_type = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$cmpny_type .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';	
				foreach ( $cmpny_types as $key => $value ) {
					$cmpny_type .= '<option value="' . $key . '">' . $value . '</option>';
				}			
			$cmpny_type .= '</select>';
		}else{
			return new WP_Error( 'invalid_data', __( "Invalid company type!" , "dxcrm" ) );
		}
		
		return $cmpny_type;
	}
	
	/**
	 * Display Company Industry in dropdown.
	 * @param name & id, multiple, class
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function crm_company_industry_dropdown( $nid="company_industry", $multi=true, $cselect=true ){
		
		/**
		 * Get default company industries
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$c_indsts = apply_filters( 'dx_crm_company_industry', array() );

		/**
		 * Set proper variable if multi select is true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/		
		if( $multi == true ){
			$n_arr = '[]';
			$multi = 'multiple';
		} else {
			$n_arr = '';
			$multi = '';
		}
		
		/**
		 * Add 'chosen-select' class if set to true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
		
		/**
		 * Create select dropdown markup
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		if( ! empty ( $c_indsts ) && is_array( $c_indsts ) ){
			$c_indst = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$c_indst .= '<option value="">' . __( 'Please select..' , 'dxcrm' ) . '</option>';			
			foreach ( $c_indsts as $key => $value ) {
				$c_indst .= '<option value="' . $key . '">' . $value . '</option>';
			} 			
			$c_indst .= '</select>';
		}else{
			return new WP_Error( 'invalid_data', __( "Invalid company industries!" , "dxcrm" ) );
		}
		
		return $c_indst;
	}
	
	/**
	 * Display Company employees in dropdown.
	 * @param name & id, multiple, class
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	public function crm_company_employees_dropdown( $nid="company_employees", $multi=true, $cselect=true ){
		
		/**
		 * Get default company employees
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$cmpny_emplys = apply_filters( 'dx_crm_company_employees', array() );
		
		/**
		 * Set proper variable if multi select is true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/		
		if( $multi == true ){
			$n_arr = '[]';
			$multi = 'multiple';
		} else {
			$n_arr = '';
			$multi = '';
		}
		
		/**
		 * Add 'chosen-select' class if set to true
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		$cselect = ( $cselect == true ) ? 'chosen-select' : '' ;
		
		/**
		 * Create select dropdown markup
		 *
		 * @package CRM System
		 * @since 1.0.0
		 *
		*/
		if( ! empty ( $cmpny_emplys ) && is_array( $cmpny_emplys ) ){
			$cmpny_emply = '<select name ="' . $nid . $n_arr . '" id="' . $nid . '" ' . $multi . ' class="' . $cselect . '">';
			$cmpny_emply .= '<option value="">' . __( 'Please select..', 'dxcrm' ) . '</option>';
				foreach ( $cmpny_emplys as $key => $value ) {
					$cmpny_emply .= '<option value="' . $key . '">' . $value . '</option>';
				}
			$cmpny_emply .= '</select>';
		}else{
			return new WP_Error( 'invalid_data', __( "Invalid company employees!" , "dxcrm" ) );
		}
		
		return $cmpny_emply;
	}	

	/** 
	 * Creates months dropdown for roadmap
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	private function dx_crm_roadmap_months_dropdown() {
		global $dx_crm_roadmap;
		
		$month = ( ! empty ( $_POST['month'] ) && wp_verify_nonce( $_POST['filter-nonce-activity-log'], 'activity-log-filter' ) ) ? $_POST['month'] : '';
		$crm_roadmap_months = $dx_crm_roadmap->get_crm_roadmap_months();
		$html = '<select name="month" id="filter-by-date">';
		$html .= '<option selected="selected" value="">' . __( 'All Months', 'dxcrm' ) . '</option>';
		foreach( $crm_roadmap_months as $roadmap_month ) {
			if( $month === $roadmap_month ){
				$html .='<option selected="selected" value="' . $roadmap_month . '">'. $roadmap_month .'</option>';
			} else {
				$html .='<option value="'. $roadmap_month.'">' . $roadmap_month . '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}
	
	/** 
	 * Merge sub menu fields
	 * @param $new_sub_items - Tne items for the sub menu (name, slug, href) that should be added to the default
	 * @param $sub_items - The default sub menu items
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	private function dx_crm_merge_sub_items ( $new_sub_items, $sub_items ) {
		// Merge only if not empty
		if ( ! empty( $new_sub_items ) && is_array( $new_sub_items ) ){
			
			foreach ( $new_sub_items as $new_sub_item ) {
				if( empty ( $new_sub_item['name'] ) || empty ( $new_sub_item['slug'] ) || empty ( $new_sub_item['href'] )  ) {
					return $sub_items;
				}
			}

			if( $priority > 0 ){
				$sub_items = array_merge( $new_sub_items, $sub_items );
			}else{
				$sub_items = array_merge( $sub_items, $new_sub_items );
			}
		}
		return $sub_items;
	}

	/** 
	 * Merge sub menu fields for project
	 * @param $new_sub_items - Tne items for the sub menu (name, slug, href) that should be added to the default
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	private function dx_crm_get_project_sub_menu ( $new_sub_items ) {
		$menu_slug_url = admin_url( 'admin.php?page=' . DX_CRM_DASHBOARD );
		$general = add_query_arg(array( 'page' => 'dx-crm-stat-setting','tab' => 'project', 'type' => 'general' ), $menu_slug_url );
		$prjct_rvn = add_query_arg(array( 'page' => 'dx-crm-stat-setting','tab' => 'project', 'type' => 'highest-cost' ), $menu_slug_url );

		$sub_items = array(
			array(
				'name' => 'General',
				'slug' => 'general',
				'href' => $general,
			),
			array(
				'name' => 'By Project Price',
				'slug' => 'highest-cost',
				'href' => $prjct_rvn,
			),
		);

		$sub_items = $this->dx_crm_merge_sub_items( $new_sub_items, $sub_items );
		return $sub_items;
	}


	/** 
	 * Merge sub menu fields for customer
	 * @param $new_sub_items - Tne items for the sub menu (name, slug, href) that should be added to the default
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	private function dx_crm_get_customer_sub_menu ( $new_sub_items ) {
		$menu_slug_url = admin_url( 'admin.php?page=' . DX_CRM_DASHBOARD );
		$general = add_query_arg(array( 'page' => 'dx-crm-stat-setting','tab' => 'customer', 'type' => 'general' ), $menu_slug_url );
		$lrgst_ncm = add_query_arg(array( 'page' => 'dx-crm-stat-setting','tab' => 'customer', 'type' => 'largest-income' ), $menu_slug_url );
		$lrgst_prjct = add_query_arg(array( 'page' => 'dx-crm-stat-setting','tab' => 'customer', 'type' => 'largest-project' ), $menu_slug_url );

		$sub_items = array(
			array(
				'name' => 'General',
				'slug' => 'general',
				'href' => $general,
			),
			array(
				'name' => 'By Largest Income',
				'slug' => 'largest-income',
				'href' => $lrgst_ncm,
			),
			array(
				'name' => 'By Largest Project',
				'slug' => 'largest-project',
				'href' => $lrgst_prjct,
			),
		);

		$sub_items = $this->dx_crm_merge_sub_items( $new_sub_items, $sub_items );
		return $sub_items;
	}

	/** 
	 * Filter for report sub menu
	 * @param $new_sub_items - Tne items for the sub menu(name, slug, href)  that should be added to the default 
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function dx_crm_report_sub_menu_filter( $new_sub_items = array(), $priority = 0 ) {
		// check role before set default tab
		$default = ( current_user_can( 'administrator' ) ) ? 'customer' : 'project';
		//get the active horizontal tab
		$activetab = isset( $_GET['tab'] ) ? $_GET['tab'] : $default;
		//get the active vertical tab
		$actvt_typ = isset($_GET['type']) ? $_GET['type'] : 'general';

		if ( 'project' === $activetab ) {
			$sub_items = $this->dx_crm_get_project_sub_menu( $new_sub_items );
		} elseif( 'customer' === $activetab) {
			$sub_items = $this->dx_crm_get_customer_sub_menu( $new_sub_items );
		}

		$output = '<ul id="crm-vertical-report-tab">';
			foreach ($sub_items as $sub_item) {
				//determine which tab should be active
				if ( $actvt_typ === $sub_item['slug'] ) {
					$output .= '<li class="sub-item active"><a href="'. $sub_item['href'] .'">'. __( $sub_item["name"] , "dxcrm").'</a></li>';
				} else {
					$output .= '<li class="sub-item"><a href="'. $sub_item['href'] .'">'. __( $sub_item["name"] , "dxcrm").'</a></li>';
				}
			}
		$output .= '</ul>';
		
		return $output;
	}

	/** 
	 * Filter for company currency
	 * This can be re-use to add new data
	 * @param		$new_currency		array		New value for currency
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function dx_crm_company_currency_filter( $new_currency = array(), $priority = 0 ){
		
		// Default currency
		$currency = array(
			'USD' => __('US Dollar', 'dxcrm'),
			'EUR' => __('Euro', 'dxcrm'),
		);
		
		// Merge only if not empty
		if ( ! empty ( $new_currecy ) && is_array( $new_currecy ) ){
			if( $priority > 0 ){
				$currency = array_merge( $new_currency, $currency );
			} else {
				$currency = array_merge( $currency, $new_currency );
			}
		}
		
		// Return currency
		return $currency;
		
	}
	
	/** 
	 * Filter for company type
	 * This can be re-use to add new data
	 * @param		$new_company_type		array		New value for type
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function dx_crm_company_type_filter( $new_company_type = array(), $priority = 0 ){
		
		// Default type
		$company_type = array(
			'CUSTOMER'		=> __('Client', 'dxcrm'),
			'PARTNER'		=> __('Partner', 'dxcrm'),
			'RESELLER'		=> __('Reseller', 'dxcrm'),
			'COMPETITOR'	=> __('Competitor', 'dxcrm'),
			'INVESTOR'		=> __('Investor', 'dxcrm'),
			'INTEGRATOR'	=> __('Integrator', 'dxcrm'),
			'PROSPECT'		=> __('Prospect', 'dxcrm'),
			'PRESS'			=> __('Media', 'dxcrm'),
			'OTHER'			=> __('Other', 'dxcrm'),
		);
		
		// Merge only if not empty
		if ( ! empty ( $new_company_type ) && is_array( $new_company_type ) ){
			if( $priority > 0 ){
				$company_type = array_merge( $new_company_type, $company_type );
			} else {
				$company_type = array_merge( $company_type, $new_company_type );
			}
		}
		
		// Return type
		return $company_type;
		
	}

	/**
	* Project type filter
	*
	**/
		function dx_crm_project_status_filter( $new_project_status = array(), $priority = 0 ){
			
			// Default type
			$project_status = array(
							__( 'Draft' , 'dxcrm' ),
							__( 'Sent to client' , 'dxcrm' ),
							__( 'Being reviewed by client' , 'dxcrm' ),
							__( 'Approved' , 'dxcrm' ),
							__( 'No response' , 'dxcrm' ),
							__( 'Declined' , 'dxcrm' ),
							__( 'In Development' , 'dxcrm' ),
							__( 'Awaiting Review' , 'dxcrm' ),
							__( 'Successfully Completed' , 'dxcrm' )
						);	
			
			// Merge only if not empty
			if ( ! empty ( $new_project_status ) && is_array( $new_project_status ) ){
				if( $priority > 0 ){
					$project_status = array_merge( $new_project_status, $project_status );
				} else {
					$project_status = array_merge( $project_status, $new_project_status );
				}
			}
			
			// Return type
			return $project_status;
			
		}

	/**
	* Contact type filter
	*
	**/
		function dx_crm_contact_type_filter( $new_contact_type = array(), $priority = 0 ){
			
			// Default type
			$contact_type = array(
				'Customer' 			=> __( 'Customer' , 'dxcrm' ),
				'Prospect' 			=> __( 'Prospect' , 'dxcrm' ),
				'Partner'  			=> __( 'Partner' , 'dxcrm' ),
				'Service Provider' 	=> __( 'Service Provider' , 'dxcrm' )
			);
			
			// Merge only if not empty
			if ( ! empty ( $new_contact_type ) && is_array( $new_contact_type ) ){
				if( $priority > 0 ){
					$contact_type = array_merge( $new_contact_type, $contact_type );
				} else {
					$contact_type = array_merge( $contact_type, $new_contact_type );
				}
			}
			
			// Return type
			return $contact_type;
			
		}
		
	/** 
	 * Filter for company industry
	 * This can be re-use to add new data
	 * @param		$new_industry		array		New value for industry
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function dx_crm_company_industry_filter( $new_industry = array(), $priority = 0 ){
		
		// Default industry
		$company_industry = array(
			'IT'			=> __('Information Technology', 'dxcrm'),
			'TELECOM'		=> __('Telecommunication', 'dxcrm'),
			'MANUFACTURING'	=> __('Manufacturing', 'dxcrm'),
			'BANKING'		=> __('Banking Services', 'dxcrm'),
			'CONSULTING'	=> __('Consulting', 'dxcrm'),							
			'GOVERNMENT'	=> __('Government', 'dxcrm'),							
			'TRANSPORT'		=> __('Transportation Services', 'dxcrm'),
			'MARKETING'		=> __('Marketing', 'dxcrm'),
			'WHOLESALE'		=> __('Wholesale and Retail Trade', 'dxcrm'),
			'FINANCE'		=> __('Finance and Insurance', 'dxcrm'),
			'SCIENTIFIC'	=> __('Scientific', 'dxcrm'),
			'AGRICULATURE'	=> __('Agriculture', 'dxcrm'),
			'REALESTATE'	=> __('Real Estate', 'dxcrm'),
			'SPORTS'		=> __('Sports', 'dxcrm'),
			'HEALTHCARE'	=> __('Healthcare', 'dxcrm'),
			'EDUCATION'		=> __('Education', 'dxcrm'),
			'DELIVERY'		=> __('Delivery', 'dxcrm'),
			'ENTERTAINMENT'	=> __('Entertainment', 'dxcrm'),
			'NOTPROFIT'		=> __('Non-profit', 'dxcrm'),
			'OTHER'			=> __('Other', 'dxcrm'),
		);
		
		// Merge only if not empty
		if ( ! empty ( $new_industry ) && is_array( $new_industry ) ){
			if( $priority > 0 ){
				$company_industry = array_merge( $new_industry, $company_industry );
			}else{
				$company_industry = array_merge( $company_industry, $new_industry );
			}
		}
		
		// Return industry
		return $company_industry;
		
	}
	
	/** 
	 * Filter for company employees
	 * This can be re-use to add new data
	 * @param		$new_employee		array		New value for employee
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function dx_crm_company_employees_filter ( $new_employee = array(), $priority = 0 ) {
		
		// Default employee
		$company_employee = array(
			'EMPLOYEES_1'	=> __('less than 50', 'dxcrm'),
			'EMPLOYEES_2'	=> __('50 - 250'	, 'dxcrm'),
			'EMPLOYEES_3'	=> __('250 - 500'	, 'dxcrm'),
			'EMPLOYEES_4'	=> __('over 500'	, 'dxcrm')
		);
		
		// Merge only if not empty
		if ( ! empty ( $new_employee ) && is_array( $new_employee ) ){
			if( $priority > 0 ){
				$company_employee = array_merge( $new_employee, $company_employee );
			}else{
				$company_employee = array_merge( $company_employee, $new_employee );
			}
		}
		
		// Return industry
		return $company_employee;
		
	}

	/** 
	 * Action to show filters in activity log
	 * This can be re-use to add new data
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function dx_crm_roadmap_filters_action ( $priority = 0 ) {
		$page = ! empty ( $_GET['page'] ) ? $_GET['page'] : '';
		$selected = ( ! empty ( $_POST['user'] ) && wp_verify_nonce( $_POST['filter-nonce-activity-log'], 'activity-log-filter' ) ) ? $_POST['user'] : false;
		
		$html = '<form method="post">';
		$html .= '<div class="actions">';
		$html .= '<input type="hidden" name="page" value='.$page.'>';
		$html .= '<label for="filter-by-date" class="screen-reader-text">' . __( 'Filter by date', 'dxcrm' ) . '</label>';
		$html .= $this->dx_crm_roadmap_months_dropdown();
		$html .= '<label class="screen-reader-text" for="users">' . __( 'Filter by users', 'dxcrm' ) . '</label>';
		$html .= wp_dropdown_users( array( 'echo' => 0, 'show_option_none' => __( 'All users', 'dxcrm' ), 'option_none_value'=>'', 'selected' => $selected ) );
		$html .= '<input type="submit" name="filter_action" id="post-query-submit" class="button" value="' . __( 'Filter', 'dxcrm' ) . '">';
		$html .= '</div>';
		$html .= wp_nonce_field( 'activity-log-filter', 'filter-nonce-activity-log' );	
		$html .= '</form>';
		
		echo $html;
	} 

	/** 
	 * Action to show search in activity log
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function dx_crm_roadmap_search_action ( $priority = 0 ) {
		$page = ! empty ( $_GET['page'] ) ? $_GET['page'] : '';

		$html = '<form method="post">';
		$html .= '<div class="actions">';
		$html .= '<input type="hidden" name="page" value='.$page.'>';
		$html .= '<label class="screen-reader-text" for="users">' . __( 'Search', 'dxcrm' ) . '</label>';
		$html .= '<input type="text" name="s" placeholder="' . __( 'Search', 'dxcrm' ) . '">';
		$html .= '<input type="submit" name="filter_action" id="post-query-submit" class="button" value="' . __( 'Search', 'dxcrm' ) . '">';
		$html .= wp_nonce_field( 'activity-log-search', 'search-nonce-activity-log' );	
		$html .= '</div>';
		$html .= '</form>';

		echo $html;
	}
	
	
	/** 
	 * Add action and filter hook
	 *
	 * @package CRM System
	 * @since 1.0.0
	*/
	function add_hooks(){
		add_action( 'dx_crm_roadmap_filters', array( $this, 'dx_crm_roadmap_filters_action'), 10, 0 );
		add_action( 'dx_crm_roadmap_search', array( $this, 'dx_crm_roadmap_search_action'), 10, 0 );
		add_filter( 'dx_crm_company_currency', array( $this, 'dx_crm_company_currency_filter' ), 10, 1 );
		add_filter( 'dx_crm_company_type', array( $this, 'dx_crm_company_type_filter' ), 10, 2 );
		add_filter( 'dx_crm_company_industry', array( $this, 'dx_crm_company_industry_filter' ), 10, 2 );
		add_filter( 'dx_crm_company_employees', array( $this, 'dx_crm_company_employees_filter' ), 10, 2 );
		add_filter( 'dx_crm_report_sub_menu', array( $this, 'dx_crm_report_sub_menu_filter' ), 10, 1 );
		add_filter( 'dx_crm_project_status', array( $this, 'dx_crm_project_status_filter' ), 10, 1 );
		add_filter( 'dx_crm_contact_type', array( $this, 'dx_crm_contact_type_filter' ), 10, 1 );
	}
	
}
?>