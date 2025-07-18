<?php
/** 
 * Template Name: DX CRM Customer
 * 
 * Full width page template without sidebar
 *
 * @package CRM System
 * @since 1.0.0
*/
$prefix = DX_CRM_META_PREFIX;

/** 
 * Intialize CRM model global variable
 *
 * @package CRM System
 * @since 1.0.0
*/
global $dx_crm_model, $dx_crm_public;

/** 
 * Get initial project data
 * The rest will be pull using ajax
 *
 * @package CRM System
 * @since 1.0.0
*/
$project_types = get_terms( 'crm_pro_type' , array('hide_empty' => false, 'number' => '5') );

foreach ( $project_types as $project_type ) {
    $pro_type[$project_type->term_id] = $project_type->name;
}

/** 
 * Get initial employer skills
 * The rest will be pull using ajax
 *
 * @package CRM System
 * @since 1.0.0
*/
$skills = get_terms( 'crm_emp_skill' , array('hide_empty' => false) );

foreach ( $skills as $skill ) {
    $skills_arr[$skill->term_id] = $skill->name;
}

/** 
 * Get initial crm customer
 * The rest will be pull using ajax
 *
 * @package CRM System
 * @since 1.0.0
*/
$cust_users = get_users( array('role' => 'dx_crm_customer') );

foreach ( $cust_users as $usr ) {
    $user_arr[$usr->data->ID] = $usr->data->display_name;
}   

/** 
 * Get theme header
 *
 * @package CRM System
 * @since 1.0.0
*/
get_header();

if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {?>
<div id="primary" class="site-content dx-crm-content">
	<div id="content" role="main">
		<h1><?php _e( 'Add Customer' , 'dxcrm' );?></h1>
		<?php
		/** 
		 * Avoid fatal error in submitting without Project Type.
		 * Add friendly message on top of form.
		 * Disable form submit button.
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		$submit_button = "";

		if( empty ( $pro_type ) ){					
			// DISABLE SUBMIT BUTTON
			$submit_button = "disabled";
			do_action( 'dx_crm_notice', __( 'You can\'t add Customer without Project Type. Please notify your system administrator to add!', 'dxcrm' ) );		
		}
		
		/** 
		 * Check if nonce request is submitted
		 *
		 * @package CRM System
		 * @since 1.0.0
		*/
		if( isset ( $_POST['add_customer'] ) ){
			
			/** 
			 * Setup nonce
			 * Use itenary to avoid undefined index notice
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			$nonce = isset ( $_POST['add_customer'] ) && ! empty ( $_POST['add_customer'] ) ? $_POST['add_customer'] : '';
				
			/** 
			 * Proceed only if nonce is valid
			 *
			 * @package CRM System
			 * @since 1.0.0
			*/
			if ( ! wp_verify_nonce( $nonce, 'crm_add_customer' ) ) {
				
				/** 
				 * If nonce is invalid, echo error message
				 * and stops everything from processing
				 *
				 * @package CRM System
				 * @since 1.0.0
				*/
				do_action( 'dx_crm_notice', __( 'Invalid request! Please try again', 'dxcrm' ) ); 
				
			} else {
				
				/** 
				 * Process it
				 *
				 * @package CRM System
				 * @since 1.0.0
				*/					
				$save_customer = $dx_crm_public->dx_crm_save_customer( $_POST );
				
				/** 
				 * Depending on what the output, chech if WP error first
				 *
				 * @package CRM System
				 * @since 1.0.0
				*/	
				if ( is_wp_error( $save_customer ) ) {
				   do_action( 'dx_crm_notice', $save_customer->get_error_message() ); 
				} else {
					do_action( 'dx_crm_notice', $save_customer ); 
				}
			}
		}
		?>
		<form id="crm-customer-form" class="crm-template-form" action="" name="form1" method="POST">
			<?php wp_nonce_field( 'crm_add_customer', 'add_customer' ); ?>
			<fieldset>
				<legend><?php _e( 'Customer information' , 'dxcrm' );?> </legend>
				<div class="form-row">
					<div class="row-left"><label for="cust_name"><?php _e( 'Customer Name' , 'dxcrm' );?> </label></div>
					<div class="row-right"><input id="cust_name" type="text" name="<?php echo $prefix ;?>cust_name" data-validation="required" data-validation-error-msg="<?php _e( 'Please provide Customer Name' , 'dxcrm' );?>"/></div>
				</div>
				<br class="clear">
				<div class="form-row">
					<div class="row-left"><label for="cust_desc"><?php _e( 'Customer Description' , 'dxcrm' );?> </label></div>
					<div class="row-right"><textarea cols="15" rows="5" id="cust_desc" type="text" name="<?php echo $prefix ;?>cust_desc" data-validation="required" data-validation-error-msg="<?php _e( 'Please provide Customer Description' , 'dxcrm' );?>"></textarea></div>
				</div>
				<br class="clear">
                <?php if( ! empty( $skills_arr ) ){?>
					<div class="form-row">
						<div class="row-left"><label for="cust_skills"><?php _e( 'Skills' , 'dxcrm' );?> </label></div>
						<div class="row-right">
						<?php
							foreach ( $skills_arr as $ks => $vs ){
								echo '<input type="checkbox" name="'.$prefix.'cust_skills[]" value="'.$ks.'" />'.$vs;
							}
						?>
						</div>
					</div>
                <?php } ?>
				<br class="clear">
                <?php if( ! empty ( $user_arr ) ){ ?>
                    <div class="form-row">
                        <div class="row-left"><label for="cust_assign_customer"><?php _e( 'Users' , 'dxcrm' );?> </label></div>
                        <div class="row-right">
                        <?php
                            foreach ( $user_arr as $ku => $vu ){
                                echo '<input type="checkbox" name="'.$prefix.'cust_assign_customer[]" value="'.$ku.'" />'.$vu;
                            }
                        ?>
                        </div>
                    </div>
                <?php } ?>
			</fieldset>
			<fieldset>
				<legend><?php _e( 'Customer Details' , 'dxcrm' );?></legend>
				<div class="form-row">
					<div class="row-left"><label for="cust_first_pro_type"><?php _e( 'First Project Type' , 'dxcrm' );?> </label></div>
					<div class="row-right">
					<?php
						/**
						 * We display the first 5 data
						 * then the rest will be pull using ajax 
						 * to avoid large loop
						 *
						 * @package CRM System
						 * @since 1.0.0
						*/
						if( ! empty ( $pro_type ) ){
							echo '<select id="cust_first_pro_type" name="' . DX_CRM_META_PREFIX . 'cust_first_pro_type">';															
								foreach ( $pro_type as $kp => $vp ){
									echo '<option value="'.$kp.'">'.$vp.'</option>';
								} 							
							echo '</select>';
						} else {
							echo sprintf( '<p class="crm-error-field">%s</p>', __( "Please add project first!" , "dxcrm" ) );
						}
					?>
					</div>
				</div>
				<br class="clear">
				<div class="form-row">
					<div class="row-left"><label for="cust_initial_investment"><?php _e( 'Initial Investment' , 'dxcrm' );?> </label></div>
					<div class="row-right"><input id="cust_initial_investment" type="text" name="<?php echo $prefix ;?>cust_initial_investment" data-validation="number" data-validation-allowing="float" data-validation-error-msg="<?php _e( 'Please provide correct Initial Investment' , 'dxcrm' );?>"/></div>
				</div>
				<br class="clear">
				<div class="form-row">
					<div class="row-left"><label for="cust_referral"><?php _e( 'Referral' , 'dxcrm' );?> </label></div>
					<div class="row-right"><input id="cust_referral" type="text" name="<?php echo $prefix ;?>cust_referral" /></div>
				</div>
				<br class="clear">
				<div class="form-row">
					<div class="row-left"><label for="cust_contact_date"><?php _e( 'Contact Date' , 'dxcrm' );?> </label></div>
					<div class="row-right"><input id="cust_contact_date" class="add-datepicker" type="text" name="<?php echo $prefix ;?>cust_contact_date" readonly="readonly" data-validation="date" data-validation-format="dd-mm-yyyy" data-validation="number" data-validation-error-msg="<?php _e( 'Please provide Contact Date' , 'dxcrm' );?>"/></div>
				</div>
				<br class="clear">
			</fieldset>
			<fieldset>
				<legend><?php _e( 'Customer Invoice details' , 'dxcrm' );?></legend>
				<div class="form-row">
					<div class="row-left"><label for="cust_bank_info"><?php _e( 'Bank Info' , 'dxcrm' );?> </label></div>
					<div class="row-right"><textarea cols="15" rows="5" id="cust_bank_info" type="text" name="<?php echo $prefix ;?>cust_bank_info" data-validation="required" data-validation-error-msg="<?php _e( 'Please provide Bank Info' , 'dxcrm' );?>"></textarea></div>
				</div>
				<br class="clear">
				<div class="form-row">
					<div class="row-left"><label for="cust_vat_number"><?php _e( 'VAT number' , 'dxcrm' );?> </label></div>
					<div class="row-right"><input id="cust_vat_number" type="text" name="<?php echo $prefix ;?>cust_vat_number"  data-validation="required" data-validation-error-msg="<?php _e( 'Please provide VAT #' , 'dxcrm' );?>"/></div>
				</div>
				<br class="clear">
				<div class="form-row">
					<div class="row-left"><label for="cust_country"><?php _e( 'Country' , 'dxcrm' );?> </label></div>
					<div class="row-right"><input id="cust_country" type="text" name="<?php echo $prefix ;?>cust_country"  data-validation="required" data-validation-error-msg="<?php _e( 'Please provide Country' , 'dxcrm' );?>"/></div>
				</div>					
			</fieldset>
			<div class="form-row">
				<div class="row-left"><input type="submit" name="<?php echo $prefix ;?>add_cust" value="Add Customer" <?php echo $submit_button; ?>/></div>
			</div>
			<br class="clear">
		</form>
		</div><!-- #content -->
	</div><!-- #primary -->
	<?php
} else {
    echo sprintf( __( 'Please <a href="%s">log in</a> with admin user to add project', 'dxcrm' ), wp_login_url() );
} 

/** 
 * Get theme footer
 *
 * @package CRM System
 * @since 1.0.0
*/ 
get_footer(); 
?>