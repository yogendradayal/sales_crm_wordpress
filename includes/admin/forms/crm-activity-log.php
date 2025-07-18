<?php
/**
 * Crm Roadmap Tracking Settings
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $current_user, $dx_crm_roadmap;
?>
<div class="wrap">
	<h1><?php _e( 'Activity Log', 'dxcrm' ); ?></h1>	
	<p><?php _e( 'All Projects progress and updates', 'dxcrm' ); ?></p>
	<?php
		global $dx_crm_roadmap;
		
		//  && wp_verify_nonce( $_POST['filter-nonce-activity-log'], 'activity-log-filter' ) )

		if ( ( ( isset( $_POST['month'] ) || isset( $_POST['user'] ) ) && ( ! isset( $_POST['filter-nonce-activity-log'] ) || ! wp_verify_nonce( $_POST['filter-nonce-activity-log'], 'activity-log-filter' ) ) || ( isset( $_POST['s'] ) && ( ! isset( $_POST['search-nonce-activity-log'] ) || ! wp_verify_nonce( $_POST['search-nonce-activity-log'], 'activity-log-search' ) ) ) )
		) {
			echo __( 'Problem occurred! Please try again.', 'dxcrm' );
			exit();
		}

		/**
		 * Filters and Search
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		$page = ! empty ( $_GET['page'] ) ? $_GET['page'] : '';
		$month =  ! empty ( $_POST['month'] ) ? $_POST['month'] : '';
		$s =  ! empty ( $_POST['s'] ) ? $_POST['s'] : '';
		$user = ! empty ( $_POST['user'] ) ? $_POST['user'] : '';	
		
		/**
		 * Retrieve pagination variable from URL
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		$roadmap_tracking_page = 1;
		if ( isset($_GET['rt_page']) && !empty($_GET['rt_page']) ) {
			$roadmap_tracking_page = $_GET['rt_page'];
		}
		
		/**
		 * Create pagination html links
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		$roadmap_count = $dx_crm_roadmap->count_all_roadmaps( $month, $s, $user );
		$total_pages = ceil( $roadmap_count / 10 );

		$page_links = paginate_links( array(
		    'base' => add_query_arg( 'rt_page', '%#%' ),
		    'format' => '',
		    'mid_size' => 1,
		    'prev_text' => __( '&laquo;', 'dxcrm' ),
		    'next_text' => __( '&raquo;', 'dxcrm' ),
		    'total' => $total_pages,
		    'current' => $roadmap_tracking_page
		) );

		/**
		 * Get data
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		$roadmaps = $dx_crm_roadmap->get_some_roadmap( $roadmap_tracking_page, $month, $user, $s );		
		
		/**
		 * Display log data
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( ! empty ( $roadmaps ) ){
			
			/**
			 * Display pagination only if there's data
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			if ( $page_links ) {
				echo '<div class="pagination"><span class="pagination">' . $roadmap_count . __( ' items', 'dxcrm' ) . '</span>' . $page_links . '</div>';
			}

			do_action( 'dx_crm_roadmap_filters', 10 );
			do_action( 'dx_crm_roadmap_search', 10 );

			$html = '';
			$html .= '<table border="0" width="100%">';
			foreach( $roadmaps as $roadmap ){
				$html .= '<tr>';
				$html .= '<td>';
					$html .= '<table border="0" width="100%">';
					$html .= '<tr>';
						$html .= '<td rowspan="4" valign="top" width="5%">' . get_avatar( $roadmap->roadmap_user ) . '</td>';	
					$html .= '</tr>';
					$html .= '<tr>';					
						if( $current_user->ID == $roadmap->roadmap_user ){
							$html .= '<td><p class="user">' . __( 'You', 'dxcrm' ) . '</p></td>';
						} else {
							$html .= '<td><p class="user">' . get_userdata( $roadmap->roadmap_user )->user_login . '</p></td>';
						}
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td><p class="date">' . date( "F dS, Y g:i a", strtotime( $roadmap->roadmap_time ) ) . '</td>';
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td class="summary">' . wpautop( $roadmap->roadmap_summary ) . '</td>';
					$html .= '</tr>';
					$html .= '</table>';
				$html .= '</td>';
				$html .= '</tr>';
			}	
			$html .= '</table><br />';
			
			echo $html;
			
			/**
			 * Display pagination only if there's data
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			if ( $page_links ) {
			    echo '<div class="pagination"><span class="pagination">' . $roadmap_count . __( ' items', 'dxcrm' ) . '</span>' . $page_links . '</div>';
			}
		} else {
			/**
			 * Apply filter
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			do_action( 'dx_crm_roadmap_filters', 10 );
			do_action( 'dx_crm_roadmap_search', 10 );
			
			/**
			 * Display the message
			 *
			 * @package CRM System
			 * @since 1.0.0
			 */
			$log_message = isset( $_GET['s'] ) ? __( 'No log found!', 'dxcrm' ) : __( 'Roadmap tracking is empty!', 'dxcrm' ) ;			
			printf( '<div class="activity-log-error"><p>%s</p></div>', $log_message );
		}
	?>
</div>