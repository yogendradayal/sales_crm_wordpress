<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Sales_CRM_Customer' ) ){
	
	/* 
	 * Customer CPT functionality. Custom WP_List_table,
	 * metabox and more.
	 *
	 * @package CRM System
	 * @since 1.0
	*/
	class Sales_CRM_Customer{
		
		/**
		 * Company private variable
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		private $company;
		
		/**
		 * Project private variable
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		private $project;

		/** 
		 * WP action and filter hooks
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function add_hooks(){
			//add_action( 'edit_form_top', array( $this , 'sales_crm_summary_view' ) );
			add_action( 'save_post', array( $this , 'sales_crm_save_post' ), 10, 3 );
			add_action( 'save_post', array( $this , 'sales_crm_late_save_post' ), 1000, 3 );
			add_filter( 'enter_title_here', array( $this , 'sales_crm_change_title_text' ) );
			
			/* Change Publish text to Create */
			add_filter( 'gettext', array( $this , 'sales_crm_change_publish_button' ) , 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this , 'sales_crm_admin_scripts' ) );	
			add_action( 'before_delete_post', array( $this , 'sales_crm_before_delete_post' ) );
			
			/** Hook on user profiles */
			add_action( 'show_user_profile', array( $this, 'sales_crm_extra_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'sales_crm_extra_profile_fields' ) );
			
			/** Hook on saving the custom field on user profile */
			add_action( 'personal_options_update', array( $this, 'sales_crm_save_extra_profile_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'sales_crm_save_extra_profile_fields' ) );
			
			/** Hook when user role change on WP_User */
			add_action( 'set_user_role', array( $this, 'sales_crm_set_user_role' ), 1000, 3 );
			
			/** Hook when User is being deleted */
			add_action( 'delete_user', array( $this, 'sales_crm_delete_user' ) );
			
			/** Hook when new user is registered. */
			add_action( 'user_register', array( $this, 'sales_crm_profile_add' ), 10, 1 );
			add_action( 'profile_update',  array( $this, 'sales_crm_profile_update' ), 10, 2 );
			
			// Action on deleteing the customer post {status}_{post_type}
			add_action( 'trash_dxcrm_customers', array( $this, 'on_customer_delete' ), 10, 2 );
			// Action, when post is goes from trash to publish
			add_action( 'trash_to_publish',  array( $this, 'on_trash_to_publish' ) );
			// Add filter, to not allow user to login, if there are it post has been deleted
			add_filter( 'authenticate', array( $this, 'check_customer_user' ), 99 );
			// Check if current customer user is can be logged in
			add_action( 'init', array( $this, 'check_logged_in_user' ) );
			// For the DX_CRM_CUSTOMER_ROLE show the message when it was logged out on the it CTP deleted
			add_filter( 'login_message', array( $this, 'show_user_logout_message' ) );
		}
		
		/**
		 * On the customers post type deleting, remove the it user
		 * 
		 * @param int $ID
		 * @param WP_Post $post
		 * @package CRM System
		 * @since 1.0
		*/
		public function on_customer_delete( $ID, $post ) {
		   $joined_customer_email = get_post_meta( $ID, DX_CRM_META_PREFIX . 'cust_email', true );
		   $joined_customer = get_user_by( 'email', $joined_customer_email );
		   if( !empty( $joined_customer ) && ! is_wp_error( $joined_customer ) ){// user is ok, exists
			   update_user_meta( $joined_customer->ID, DX_CRM_META_PREFIX . 'not_allow_user_to_login', '1' );
		   }
		}

		/**
		 * Callback for post status changing from trash to publish
		 * 
		 * @param WP_Post $post
		 * @package CRM System
		 * @since 1.0
		*/
		public function on_trash_to_publish( $post ) {
		   if( !empty( $post ) && ! is_wp_error( $post ) && DX_CRM_POST_TYPE_CUSTOMERS === $post->post_type ){
			   $joined_customer_email = get_post_meta( $post->ID, DX_CRM_META_PREFIX . 'cust_email', true );
			   $joined_customer = get_user_by( 'email', $joined_customer_email );
			   if( !empty( $joined_customer ) && ! is_wp_error( $joined_customer ) ){// user is ok, exist				
				   update_user_meta( $joined_customer->ID, DX_CRM_META_PREFIX . 'not_allow_user_to_login', '0' );
			   }
		   }
		}

		/**
		 * Check if user can login
		 * 
		 * @param WP_User $user
		 * @package CRM System
		 * @since 1.0
		*/
		public function check_customer_user( $user ) {
		   if( is_wp_error( $user ) ){
			   return $user;
		   }
		   if ( $user instanceof WP_User ){
			   $allow_user_to_login = get_user_meta( $user->ID, DX_CRM_META_PREFIX . 'not_allow_user_to_login', true );
			   if( !empty( $allow_user_to_login ) ){
				   return new WP_Error( 'invalid_username',
					   sprintf(
						   '<strong>%s</strong>: %s',
						   __( 'ERROR', 'dxcrm' ),
						   __( 'Sorry, but You are not allowed to login!', 'dxcrm' )						
					   )
				   );
			   }
		   }
		   return $user;
		}
		
		/**
		 * Check if current logged in user can is active customer.
		 * If not logout user.
		 * 
		 * @package CRM System
		 * @since 1.0
		 */
		public function check_logged_in_user() {
			if( is_user_logged_in() ){
				$current_user_id = get_current_user_id();
				$allow_user_to_login = get_user_meta( $current_user_id, DX_CRM_META_PREFIX . 'not_allow_user_to_login', true );
				if( ! empty( $allow_user_to_login ) ){	
					update_user_meta( $current_user_id, 'dxcrm_is_show_user_logout_message', 1 );
					setcookie( DX_CRM_META_PREFIX . 'logged_out_user_id', $current_user_id, time() + 60, COOKIEPATH, COOKIE_DOMAIN );					
					wp_logout();
				}
			}
		}
		
		/**
		 * Add message on logout for the logged out user
		 * 
		 * @param string $message
		 * @return string
		 */
		public function show_user_logout_message( $message ) {
			$_dx_crm_logged_out_user_id = !empty( $_COOKIE[DX_CRM_META_PREFIX . 'logged_out_user_id'] )? (int)$_COOKIE[DX_CRM_META_PREFIX . 'logged_out_user_id'] : false;
			if( false !== $_dx_crm_logged_out_user_id ){
				$dxcrm_is_show_user_logout_message = get_user_meta( $_dx_crm_logged_out_user_id, 'dxcrm_is_show_user_logout_message', true );
				if( !empty( $dxcrm_is_show_user_logout_message ) ){
					update_user_meta( $_dx_crm_logged_out_user_id, 'dxcrm_is_show_user_logout_message', 0 );
					$message .= sprintf( '<div id="login_error">%s</div>', __( 'Your access is denied please contact us.', 'dxcrm' ) );
				}
			}
			
			return $message;
		}
		
		/** 
		 * Concatenate the first and last name meta
		 * Save it for post_title
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_save_post( $post_id, $post, $update ){
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				|| ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )
				|| wp_is_post_revision( $post_id ) ){
				return $post_id;
			}
			
			// Only for customer
			if( $post->post_type !== DX_CRM_POST_TYPE_CUSTOMERS ){
				return $post_id;
			}
			
			/**
			 * Saves user meta.
			 * Check if email is provided, check if it's exist on WP_User
			 * before you can proceed
			*/
			if( ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_email' ] ) ){
				$get_email = get_user_by( 'email', $_POST[ DX_CRM_META_PREFIX . 'cust_email' ] );
				if( ! empty ( $get_email ) ){
					// Save Phone Number
					$meta = update_user_meta( $get_email->ID, DX_CRM_META_PREFIX . 'contact_number', $_POST[ DX_CRM_META_PREFIX . 'contact_number' ] );
					
					// Save Skills
					$skills = wp_get_post_terms( $post_id, DX_CRM_STAFF_TAXONOMY );
					if( ! is_wp_error ( $skills ) ){
						$skill_list = array();
						foreach ( $skills as $skill ){
							$skill_list[] = $skill->name;
						}
						$skill = implode( ", ", $skill_list );
						update_user_meta( $get_email->ID, DX_CRM_META_PREFIX . 'skills', $skill );
					}
					
					// Connecting normal WP_User with CPT Customer
					// by creating user_meta, which contains CPT Customer post id
					add_user_meta( $get_email->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', $post_id );

					// Update first and last name
					if( isset( $_POST[ DX_CRM_META_PREFIX . 'cust_first_name' ] ) && ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_first_name' ] ) ){
						update_user_meta( $get_email->ID, 'first_name', sanitize_text_field( $_POST[ DX_CRM_META_PREFIX . 'cust_first_name' ] ) );
					}
					if( isset( $_POST[ DX_CRM_META_PREFIX . 'cust_last_name' ] ) && ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_last_name' ] ) ){
						update_user_meta( $get_email->ID, 'last_name', sanitize_text_field( $_POST[ DX_CRM_META_PREFIX . 'cust_last_name' ] ) );
					}
				}
			}
			// Customer name
			$customer_name = "";
			if( ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_first_name' ] ) ){
				$customer_name = $_POST[ DX_CRM_META_PREFIX . 'cust_first_name' ];
			}
			if( ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_last_name' ] ) ){
				$customer_name = $_POST[ DX_CRM_META_PREFIX . 'cust_last_name' ];
			}
			if( ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_first_name' ] ) && ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_last_name' ] ) ){
				$customer_name = $_POST[ DX_CRM_META_PREFIX . 'cust_first_name' ] . ' ' . $_POST[ DX_CRM_META_PREFIX . 'cust_last_name' ];
			}
			
			// Unhook this function so it doesn't loop infinitely
			// See https://codex.wordpress.org/Function_Reference/wp_update_post
			remove_action( 'save_post', array( $this , 'sales_crm_save_post' ) );

			// Update post_title to use customer name
			wp_update_post( 
				array(
					'ID' 			=> $post_id,
					'post_title' 	=> $customer_name
				) 
			);

			// Re-hook this function
			add_action( 'save_post', array( $this , 'sales_crm_save_post' ), 10, 3 );
		}
		
		/** 
		 * This save_post callback will run at very late stage
		 * We use this for data validation or anything that should
		 * run after the metabox class saves the meta data
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_late_save_post( $post_id, $post, $update ){
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				|| ( ! isset( $_POST['post_ID'] ) || $post_id != $_POST['post_ID'] )
				|| wp_is_post_revision( $post_id ) ){
				return $post_id;
			}
			
			// Only for customer
			if( $post->post_type !== DX_CRM_POST_TYPE_CUSTOMERS ){
				return $post_id;
			}
			
			// Validate email address, if invalid, remove it
			if( ! empty ( $_POST[ DX_CRM_META_PREFIX . 'cust_email' ] ) ){
				if ( ! is_email( $_POST[ DX_CRM_META_PREFIX . 'cust_email' ] ) ) {
					update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_email', '' );
				}
			}
		}
		
		/** 
		 * Change customer CPT 'Enter title' to 
		 * custom text specifically for Customer
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_change_title_text( $title ){
			$screen = get_current_screen();
			if  ( DX_CRM_POST_TYPE_CUSTOMERS == $screen->post_type ) {
				$title = __( 'Enter customer name..', 'dxcrm' );
			}
				return $title;
		}
				
		/**
		 * Change Publish  meta title to Create
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_change_publish_button( $translation, $text ) {
			global $post_type;
			if ( $post_type == DX_CRM_POST_TYPE_COMPANY || $post_type == DX_CRM_POST_TYPE_PROJECTS  || $post_type == DX_CRM_POST_TYPE_CUSTOMERS ) {
			  if ( $text == 'Publish' ){
					return __( 'Create', 'dxcrm' );
				}
			}
			return $translation;
		}
		
		/**
		 * Script and CSS enqueues for administrator end only
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_admin_scripts( $hook ){
			global $post;
			if( ( ! empty ( $post ) ) && ( $post->post_type == DX_CRM_POST_TYPE_CUSTOMERS )  ){
				/** Hide permalink box under post title */
				add_filter( 'get_sample_permalink_html',  array( $this , 'sales_crm_hide_permalink' ) );
				add_action( 'admin_head',  array( $this , 'remove_post_title_field' ) );
			}
		}
		
		/**
		 * Hide permalink under post title
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_hide_permalink(){
			return null;
		}
		
		/**
		 * Hide post title
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function remove_post_title_field(){
			echo '<style>#post-body-content {display: none !important;}</style>';
		}
		
		/**
		 * 'before_delete_post' callback function.
		 * Do things here before we delete the Customer CPT if you still need the post ID
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_before_delete_post( $postid ){
			global $post;
			global $wpdb;
			
			if( empty ( $post ) ){
				return;
			}
			
			if ( $post->post_type != DX_CRM_POST_TYPE_CUSTOMERS ){
				return;
			}
			/** 
			 * Delete WP_User if exist
			 * If the current post has email and this email exist on WP_User
			 * and this user role is Sales CRM Customer, delete it
			*/
			$email = get_post_meta( $postid, DX_CRM_META_PREFIX . 'cust_email', true );
			$user = get_user_by( 'email', $email );
			if( ! empty ( $user ) ) {				
				if ( in_array( DX_CRM_CUSTOMER_ROLE, (array) $user->roles ) ) {
					wp_delete_user( $user->ID );
				}
			}
			
			/**
			 * Remove assigned Company and Project if 
			 * Customer CPT has been deleted.
			 * delete_post_meta needs post ID( Project/Company ) and this lengthen the 
			 * process. Direct delete on _postmeta table using meta key 
			 * and value( Customer CPT ID)
			*/
			$post_meta_table = $wpdb->prefix . 'postmeta';
			
			// Company
			$company_joined_customer = DX_CRM_META_PREFIX . 'joined_customer';
			$wpdb->query( "DELETE FROM {$post_meta_table} WHERE meta_key = '" . $company_joined_customer . "' AND meta_value='" . $postid . "'" );
				
			// Project
			$project_joined_customer = DX_CRM_META_PREFIX . 'joined_pro_customer';
			$wpdb->query( "DELETE FROM {$post_meta_table} WHERE meta_key = '" . $project_joined_customer . "' AND meta_value='" . $postid . "'" );
		}
		
		/**
		 * Display Sales CRM Data on profile page
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_extra_profile_fields( $user ){
			/*
			 * Display CRM additional data on user's profile
			 * only if they are DX_CRM_CUSTOMER_ROLE
			*/
			if( ! current_user_can( DX_CRM_CUSTOMER_ROLE ) ){
				return;
			}
			?>
			<h3><?php _e( 'Sales CRM Information', 'dxcrm' ); ?></h3>
			<table class="form-table">
				<tr>
					<th><label for="<?php echo DX_CRM_META_PREFIX . 'contact_number'; ?>"><?php _e( 'Phone Number', 'dxcrm' ); ?></label></th>
					<td>
						<input type="text" name="<?php echo DX_CRM_META_PREFIX . 'contact_number'; ?>" id="<?php echo DX_CRM_META_PREFIX . 'contact_number'; ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, DX_CRM_META_PREFIX . 'contact_number', true ) ); ?>" class="regular-text" /><br />
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Skills', 'dxcrm' ); ?></th>
					<td>
						<?php echo esc_attr( get_user_meta( $user->ID, DX_CRM_META_PREFIX . 'skills', true ) ); ?>
					</td>
				</tr>
			</table>
			<?php
		}
		
		/**
		 * Save Sales CRM customer metadata from user profile
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_save_extra_profile_fields( $user_id ){
			if ( ! current_user_can( 'edit_user', $user_id ) ){
				return false;
			}
			if( isset( $_POST[ DX_CRM_META_PREFIX . 'contact_number' ] ) && ! empty ( $_POST[ DX_CRM_META_PREFIX . 'contact_number' ] ) ){
				update_user_meta( $user_id, DX_CRM_META_PREFIX . 'contact_number', $_POST[ DX_CRM_META_PREFIX . 'contact_number' ] );
			}
		}
		
		/**
		 * Create Customer CPT when user change to Sales CRM Customer role
		 * Check first if $role is Sales CRM Customer, get user email
		 * check customer CPT if has value in meta email. Proceed accordingly..
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_set_user_role( $user_id, $role, $old_roles ){
			global $post;
			global $wpdb;
			
			if( $role != DX_CRM_CUSTOMER_ROLE ){
				return;
			}
			$user = get_user_by( 'ID', $user_id );			
			
			/** Check if email exist on post meta in CRM cust_email key */
			$check_customer = $this->check_if_customer_exist( $user->data->user_email );
			
			// Name for post title
			$first_name = get_user_meta( $user->ID, 'first_name', true ); 
			$last_name = get_user_meta( $user->ID, 'last_name', true ); 
			$post_title = $first_name . " " . $last_name;
				
			// If last and first name empty, user login for post_title
			if( empty ( $post_title ) ){
				$post_title = $user->data->user_login;
			}
			
			$post_id = "";
			
			/** 
			 * If no user found after changing the role to Sales CRM Customer
			 * create new Customer CPT
			*/
			if( is_wp_error ( $check_customer ) ){
				// Add new Customer CPT
				$args = array(
					'post_title' 	=> $post_title,
					'post_type'  	=> DX_CRM_POST_TYPE_CUSTOMERS,
					'post_status'	=> 'publish',
				);
				$post_id = wp_insert_post( $args );
			}
			
			/**
			 * Save meta data
			 * use update_post_meta instead of add
			 * it do the latter first if no key exist
			*/
			if( ! is_wp_error ( $post_id ) || ! empty ( $post_id ) ){
				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'set_wp_user', '1' );
				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_email', $user->data->user_email );
				if( ! empty ( $first_name ) ){
					update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_first_name', $first_name );
				}
				if( ! empty ( $last_name ) ){
					update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_last_name', $last_name );
				}
			}
			
		}
		
		/**
		 * Delete the user. Hooks run before actual removal as we still need the user ID
		 * When user is being delete, check the role if Sales CRM Customer, remove the 
		 * corresponding joined Project and Customer.
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_delete_user( $user_id ){
			global $wpdb;
			$user_data = get_userdata( $user_id );
			if( empty( $user_data ) ){
				return;
			}
			if( ! user_can ( $user_id, DX_CRM_CUSTOMER_ROLE ) ){
				return;
			}
			$args = array(
				'post_type'  	=> DX_CRM_POST_TYPE_CUSTOMERS,
				'post_status'	=> 'publish',
				'posts_per_page'=> 1, 
				'meta_query' 	=> array(
					array(
						'key'     => DX_CRM_META_PREFIX . 'cust_email',
						'value'   => $user_data->data->user_email,
						'compare' => '=',
					)
				)
			);			
			$customer = get_posts( $args );
			if( ! empty ( $customer ) ){
				$postid = $customer[0]->ID;
				$post_meta_table = $wpdb->prefix . 'postmeta';
			
				// Company
				$company_joined_customer = DX_CRM_META_PREFIX . 'joined_customer';
				$wpdb->query( "DELETE FROM {$post_meta_table} WHERE meta_key = '" . $company_joined_customer . "' AND meta_value='" . $postid . "'" );
				
				// Project
				$project_joined_customer = DX_CRM_META_PREFIX . 'joined_pro_customer';
				$wpdb->query( "DELETE FROM {$post_meta_table} WHERE meta_key = '" . $project_joined_customer . "' AND meta_value='" . $postid . "'" );
				
				// Force delete to bypass trash
				wp_delete_post( $postid, true );
			}
		}
		
		/**
		 * After Profile add callback hook
		 * Check if has Sales CRM Customer role
		 * Check if has Customer CPT, update corresponding metadata..
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_profile_add( $user_id ) {
			
			global $wpdb;
			if( ! user_can ( $user_id, DX_CRM_CUSTOMER_ROLE ) ){
				return;
			}

			$user = get_user_by( 'ID', $user_id );	
			/** Check if email exist on post meta in CRM cust_email key */
			$check_customer = $this->check_if_customer_exist( $user->data->user_email );
			
			// Name for post title
			$first_name = get_user_meta( $user->ID, 'first_name', true ); 
			$last_name = get_user_meta( $user->ID, 'last_name', true ); 
			$post_title = $first_name . " " . $last_name;
				
			// If last and first name empty, user login for post_title
			if( empty ( $post_title) ){
				$post_title = $user->data->user_login;
			}
			$post_id = "";
			
			/** 
			 * If no user found after changing the role to Sales CRM Customer
			 * create new Customer CPT
			*/
			if( is_wp_error ( $check_customer ) ){
				// Add new Customer CPT
				$args = array(
					'post_title' 	=> $post_title,
					'post_type'  	=> DX_CRM_POST_TYPE_CUSTOMERS,
					'post_status'	=> 'publish',
				);
				$post_id = wp_insert_post( $args );
			} else {
				$post_id = $check_customer->post_id;
			}
			
			/**
			 * Save meta data
			 * use update_post_meta instead of add
			 * it do the latter first if no key exist
			*/
			if( ! is_wp_error ( $post_id ) && ! empty ( $post_id ) ){
				$phone = get_user_meta( $user->ID, DX_CRM_META_PREFIX . 'contact_number', true );

				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'contact_number', $phone );
				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'set_wp_user', '1' );
				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'contact_type', 'Customer' );
				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_email', $user->data->user_email );
				update_user_meta( $user->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', $post_id );
				
				if( ! empty ( $first_name ) ){
					update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_first_name', $first_name );
				}
				if( ! empty ( $last_name ) ){
					update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_last_name', $last_name );
				}				
			}
		}
		
		/**
		 * Update Profile add callback hook
		 * Check if has Sales CRM Customer role
		 * Check if has Customer CPT, update corresponding metadata
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		function sales_crm_profile_update( $user_id, $old_user_data ) {
			global $wpdb;
			if( ! user_can ( $user_id, DX_CRM_CUSTOMER_ROLE ) ){
				return;
			}
			$user = get_user_by( 'ID', $user_id );	
			/** Check if email exist on post meta in CRM cust_email key */
			$check_customer = $this->check_if_customer_exist( $user->data->user_email );
			
			// Name for post title
			$first_name = get_user_meta( $user->ID, 'first_name', true ); 
			$last_name = get_user_meta( $user->ID, 'last_name', true ); 
			$post_title = $first_name . " " . $last_name;
				
			// If last and first name empty, user login for post_title
			if( empty ( $post_title) ){
				$post_title = $user->data->user_login;
			}
			$post_id = "";
			
			/** 
			 * If user found after changing the role to Sales CRM Customer
			 * Update Customer CPT
			*/
			if( ! is_wp_error ( $check_customer ) ){
				$args = array(
					'ID'			=> $check_customer->post_id,
					'post_title' 	=> $post_title
				);
				$post_id = wp_update_post( $args );
			}else{
				/** 
				 * If no user found after changing the role to Sales CRM Customer
				 * create new Customer CPT
				*/
				$args = array(
					'post_title' 	=> $post_title,
					'post_type'  	=> DX_CRM_POST_TYPE_CUSTOMERS,
					'post_status'	=> 'publish',
				);
				$post_id = wp_insert_post( $args );
			}
			
			/**
			 * Save meta data
			 * use update_post_meta instead of add
			 * it do the latter first if no key exist
			*/
			if( ! is_wp_error ( $post_id ) && ! empty ( $post_id ) ){
				$phone = get_user_meta( $user->ID, DX_CRM_META_PREFIX . 'contact_number', true );

				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'contact_number', $phone );				
				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'set_wp_user', '1' );
				update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_email', $user->data->user_email );
				update_user_meta( $user->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', $post_id );
				if( ! empty ( $first_name ) ){
					update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_first_name', $first_name );
				}
				if( ! empty ( $last_name ) ){
					update_post_meta( $post_id, DX_CRM_META_PREFIX . 'cust_last_name', $last_name );
				}				
			}
		}
		
		/**
		 * Check if post meta has email
		 *
		 * @package CRM System
		 * @since 1.0
		*/
		private function check_if_customer_exist( $email ){
			global $wpdb;
			$check_customer = $wpdb->get_row( 
				$wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . 'postmeta' . " WHERE meta_key = %s AND meta_value = %s", 
					DX_CRM_META_PREFIX . 'cust_email', 
					$email ) 
			);
			
			if( empty ( $check_customer ) ){
				return new WP_Error( 'empty_record', __( 'Customer not found!', 'dxcrm' ) );
			}
			return $check_customer;
		}	
	}	
}

?>