<?php
/**
 * Crm Dashboard Settings
 *
 * Handles all Crm Dashboard Settings functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 *
 * Handles all Crm Dashboard Settings functionalities of plugin
 *
 * @package CRM System
 * @since 1.0.0
 */
class Dx_Crm_Dashboard_Settings {
	
	public $model;
	private $activity_log;
	
	function __construct(){
		
		global $dx_crm_model, $dx_crm_roadmap;
		
		$this->model = $dx_crm_model;
		$this->activity_log = $dx_crm_roadmap;
		
	}
	
	function dx_crm_dashboard_boxes (){
		?>
		<!-- Start wrap div -->
		<div class="wrap">
			<div>
				<h2><?php _e( 'DX Sales CRM Dashboard', 'dxcrm' ); ?></h2>
			</div>
			
			<!-- Start dashboard widget wrap div -->
			<div id="dashboard-widgets-wrap">
				<div class="metabox-holder" id="dashboard-widgets">
					
					<!-- Start postbox container 1 div -->
					<div class="postbox-container" id="postbox-container-1">		
					
						<div class="meta-box-sortables ui-sortable" id="normal-sortables">
							
							<!-- Start dashboard right now div -->
							<div class="postbox " id="dashboard_right_now">								
								<div title="Click to toggle" class="handlediv"><br></div>
								<h3 class="hndle"><span><?php _e( 'Companies', 'dxcrm' ); ?></span></h3>
								
								<!-- Start inside div -->
								<div class="inside">									
									<div class="main">
					
										<!-- Start row div -->
										<div class="row">
											<div class="col-lg-6 col-md-12">												
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light blue-soft">
													<div class="visual"><i class="fa fa-building"></i></div>
													<div class="details">
														<div class="number">
														 <?php
														 	if ( current_user_can( DX_CRM_CUSTOMER_ROLE ) ) {
																$query = new WP_Query( array(
																	'post_type'   => DX_CRM_POST_TYPE_COMPANY,
																	'posts_per_page' => -1,
																	'fields' => 'ids',
																	'no_found_rows' => true,
																	'meta_query' => array( array(
																		'key' => DX_CRM_META_PREFIX . 'joined_customer',
																		'value' => get_user_meta( wp_get_current_user()->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', true ),
																		'compare' => 'IN',
																	)),
																));

																$company_count = $query->post_count;
														 	} else {
														 		$company_data = wp_count_posts(DX_CRM_POST_TYPE_COMPANY);

															 	if(isset($company_data->publish) & !empty($company_data->publish)) { $company_count = $company_data->publish; } else { $company_count = '0'; }
														 	}

														 	echo $company_count;
														 ?>
														</div>
														<div class="desc"><?php _e( 'Total Companies', 'dxcrm' ); ?></div>
													</div>
													<div class="more">
													<?php if( current_user_can('administrator') ){ ?>
											            <a href="post-new.php?post_type=<?php echo DX_CRM_POST_TYPE_COMPANY; ?>" class="dx-crm-box-bottom">
															<label class="dx-crm-add-new"><?php _e( 'Add New', 'dxcrm' ); ?></label>
														</a>
														
														<a href="javascript:void(0);" title="Add New Company" class="dx-crm-box-bottom dx-crm-add-company-link">
											            	<label class="dx-crm-quick-add"><?php _e( 'Quick Add', 'dxcrm' ); ?></label>
											            </a>
													<?php } ?> 
														<a href="edit.php?post_type=<?php echo DX_CRM_POST_TYPE_COMPANY; ?>" title="View All Companies" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>													
													</div>
												</div>
											</div>
											<?php
												/** 
												 *
												 * We check if Company Expenses is active and running 
												 * Display this div if true
												 * 
												*/
												if( defined( 'DX_CRM_COMPANY_EXPENSES_INSTALLED' ) == true ){
											?>
											<div class="col-lg-6 col-md-12">												
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light purple-soft">
													<div class="visual"><i class="fa fa-usd"></i></div>
													<div class="details">
														<div class="number">															
														 <?php
														 	$ce_data = wp_count_posts(DX_CRM_POST_TYPE_COMPANY_EXPENSES);														
														 	if(isset($ce_data->publish) & !empty($ce_data->publish)) { echo $ce_data->publish; } else { echo '0'; }
														 ?>
														</div>
														<div class="desc"><?php _e( 'Total Company Expenses', 'dxcrm' ); ?></div>
													</div>
													<div class="more">														
														<a href="edit.php?post_type=crm_company_expenses" title="View All Company Expenses" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>
													</div>
												</div>
											</div>
											<?php
												}
												/** End checking Company Expenses */
											?>
										</div><!-- End row div -->
										
									</div>								
								</div><!-- End inside div -->
								
							</div><!-- End dashboard right now div -->
							
							<!-- Start dashboard activity div -->
							<div class="postbox " id="dashboard_activity">
								<div title="Click to toggle" class="handlediv"><br></div>
								<h3 class="hndle"><span><?php _e( 'Sales CRM', 'dxcrm' ); ?></span></h3>
								<!-- Start inside div -->
								<div class="inside">
									<div class="main">
					
										<!-- Start row div -->
										<div class="row">
											<?php if( current_user_can('administrator') ){ ?>
											<div class="col-lg-6 col-md-12">		
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light green-soft">
													<div class="visual"><i class="fa fa-history"></i></div>
													<div class="details">
														<div class="number">															
														 <?php
														 	echo $this->activity_log->count_all_roadmaps();
														 ?>
														</div>
														<div class="desc"><?php _e( 'Activity Log', 'dxcrm' ); ?></div>
													</div>
													<div class="more">	
													<?php if( current_user_can('administrator') ){ ?>
														<a href="admin.php?page=dx-crm-activity-log" title="<?php _e( 'View All Logs', 'dxcrm' ); ?>" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>
													<?php } ?>
													</div>
												</div>
											</div>
											<?php } ?>
											<div class="col-lg-6 col-md-12">												
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light pink-soft">	
													<div class="visual"><i class="fa fa-bar-chart-o"></i></div>
													<div class="details"><div class="number"><?php _e( 'Reports', 'dxcrm' ); ?></div></div>
													<div class="more">
														<a class="dx-crm-box-bottom"><label></label></a>
														<a href="admin.php?page=dx-crm-stat-setting" title="View All Reports" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>
													</div>
												</div>
											</div>
										</div><!-- End row div -->
										
									</div>									
								</div><!-- End inside div -->
								
							</div><!-- End dashboard activity div -->	
						
						</div>			
					</div><!-- End postbox container 1 div -->
					
					<!-- Start postbox container 2 div -->
					<div class="postbox-container" id="postbox-container-2">		
						<div class="meta-box-sortables ui-sortable" id="side-sortables">
							
							<?php if( current_user_can('administrator') ){ ?>
							<!-- Start dashboard primary div -->
							<div class="postbox" id="dashboard_primary">
								<div title="Click to toggle" class="handlediv"><br></div>
								<h3 class="hndle"><span><?php _e( 'Customers', 'dxcrm' ); ?></span></h3>
								<!-- Start inside div -->
								<div class="inside">
									<div class="main">
						
										<!-- Start row div -->
										<div class="row">
											<div class="col-lg-6 col-md-12">												
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light blue-soft">
													<div class="visual"><i class="fa fa-user"></i></div>
													<div class="details">
														<div class="number">															
														 <?php
														 	$company_data = wp_count_posts(DX_CRM_POST_TYPE_CUSTOMERS);	
														 	if(isset($company_data->publish) & !empty($company_data->publish)) { echo $company_data->publish; } else { echo '0'; }
														 ?>
														</div>
														<div class="desc"><?php _e( 'Total Customers', 'dxcrm' ); ?></div>
													</div>
													<div class="more">
													<?php if( current_user_can('administrator') ){ ?>
														<a href="post-new.php?post_type=<?php echo DX_CRM_POST_TYPE_CUSTOMERS; ?>" class="dx-crm-box-bottom">
															<label class="dx-crm-add-new"><?php _e( 'Add New', 'dxcrm' ); ?></label>
														</a>
														
														<a href="javascript:void(0);" title="Add New Customer" class="dx-crm-box-bottom dx-crm-add-customer-link">
											            	<label class="dx-crm-quick-add"><?php _e( 'Quick Add', 'dxcrm' ); ?></label>
											            </a>
														<a href="edit.php?post_type=<?php echo DX_CRM_POST_TYPE_CUSTOMERS; ?>" title="View All Customers" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>
													<?php } ?>
													</div>
												</div>
											</div>
											<div class="col-lg-6 col-md-12">												
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light red-soft">													
													<div class="visual"><i class="fa fa-globe"></i></div>
													<div class="details">
														<div class="number">															
														 <?php
														 	$company_data = wp_count_terms(DX_CRM_SKILL_TAXONOMY);														 	
														 	if(isset($company_data) & !empty($company_data)) { echo $company_data; } else { echo '0'; }
														 ?>
														</div>
														<div class="desc"><?php _e( 'Total Skills', 'dxcrm' ); ?></div>
													</div>
													<div class="more">
													<?php if( current_user_can('administrator') ){ ?>
														<a href="edit-tags.php?taxonomy=<?php echo DX_CRM_SKILL_TAXONOMY; ?>&post_type=<?php echo DX_CRM_POST_TYPE_CUSTOMERS; ?>" title="View All Skills" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>
													<?php } ?>
													</div>
												</div>
											</div>								
										</div><!-- End row div -->
										
									</div>
								</div><!-- End inside div -->
								
							</div><!-- End dashboard primary div -->
							<?php } ?>
							
							<!-- Start dashboard quick press div -->
							<div class="postbox" id="dashboard_quick_press">
								<div title="Click to toggle" class="handlediv"><br></div>
								<h3 class="hndle"><span><?php _e( 'Projects', 'dxcrm' ); ?></span></h3>
								<!-- Start inside div -->
								<div class="inside">
									<div class="main">
						
										<!-- Start row div -->
										<div class="row">									
											<div class="col-lg-6 col-md-12">
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light blue-soft">													
													<div class="visual"><i class="fa fa-files-o"></i></div>													
													<div class="details">
														<div class="number">												<?php
														 	if ( current_user_can( DX_CRM_CUSTOMER_ROLE ) ) {
																$query = new WP_Query( array(
																	'post_type'   => DX_CRM_POST_TYPE_PROJECTS,
																	'posts_per_page' => -1,
																	'fields' => 'ids',
																	'no_found_rows' => true,
																	'meta_query' => array( array(
																		'key' => DX_CRM_META_PREFIX . 'joined_pro_customer',
																		'value' => get_user_meta( wp_get_current_user()->ID, DX_CRM_META_PREFIX . 'customer_cpt_id', true ),
																		'compare' => 'IN',
																	)),
																));

																$project_count = $query->post_count;
														 	} else {
														 		$project_data = wp_count_posts(DX_CRM_POST_TYPE_PROJECTS);

															 	if(isset($project_data->publish) & !empty($project_data->publish)) { $project_count = $project_data->publish; } else { $project_count = '0'; }
														 	}

														 	echo $project_count
														 ?>
														</div>
														<div class="desc"><?php _e( 'Total Projects', 'dxcrm' ); ?></div>
													</div>
													<div class="more">
													<?php if( current_user_can('administrator') ){ ?>
														<a href="post-new.php?post_type=<?php echo DX_CRM_POST_TYPE_PROJECTS; ?>" class="dx-crm-box-bottom">
															<label class="dx-crm-add-new"><?php _e( 'Add New', 'dxcrm' ); ?></label>
														</a>
														<a href="javascript:void(0);" title="Add New Project" class="dx-crm-box-bottom dx-crm-add-project-link">
											            	<label class="dx-crm-quick-add"><?php _e( 'Quick Add', 'dxcrm' ); ?></label>
											            </a>
													<?php } ?>
														<a href="edit.php?post_type=<?php echo DX_CRM_POST_TYPE_PROJECTS; ?>" title="View All Projects" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>
													</div>													
												</div>
											</div>										
											<div class="col-lg-6 col-md-12">
												<div class="dx-crm-dashboard-stat dx-crm-dashboard-stat-light red-soft">
													<div class="visual"><i class="fa fa-wrench"></i></div>													
													<div class="details">
														<div class="number">
														 <?php
														 	$company_data = wp_count_terms(DX_CRM_PRO_TAXONOMY);
														 	if(isset($company_data) & !empty($company_data)) { echo $company_data; } else { echo '0'; }
														 ?>
														</div>
														<div class="desc"><?php _e( 'Total Project Types', 'dxcrm' ); ?></div>
													</div>
													<div class="more">	
													<?php if( current_user_can('administrator') ){ ?>
														<a href="edit-tags.php?taxonomy=<?php echo DX_CRM_PRO_TAXONOMY; ?>&post_type=<?php echo DX_CRM_POST_TYPE_PROJECTS; ?>" title="View All Project Types" class="dx-crm-box-bottom">
															<label class="dx-crm-view-more"><?php _e( 'View more', 'dxcrm' ); ?></label>
														</a>
													<?php } ?>
													</div>
												</div>
											</div>								
										</div><!-- End row div -->
										
									</div>
								</div><!-- End inside div -->
																
							</div><!-- End dashboard quick press div -->

						</div>
					</div><!-- End postbox container 2 div -->
					
				</div>
			</div><!-- End dashboard widget wrap div -->
		
		</div><!-- End wrap div -->
		<?php
	}
	
}

// Global variables
global $dx_crm_dashboard;

$dx_crm_dashboard = new Dx_Crm_Dashboard_Settings();
$dx_crm_dashboard->dx_crm_dashboard_boxes();

?>