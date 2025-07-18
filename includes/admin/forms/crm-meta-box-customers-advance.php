<?php
/**
 * Handles the CRM System custom meta box functionality
 *
 * @package CRM System
 * @since 1.0.0
 */

// Exit if accessed directly 
if ( !defined( 'ABSPATH' ) ) exit;

global $post, $dx_crm_model;

$model = $dx_crm_model;

$prefix = DX_CRM_META_PREFIX;

wp_nonce_field( DX_CRM_BASENAME, 'at_dx_crm_meta_box_nonce' );

$cust_assign_customer = array();
$cust_assign_customer = get_post_meta( $post->ID, $prefix . 'cust_assign_customer', true );
?>

<!--<select class="postform" name="<?php echo $prefix ?>cust_assign_customer" id="<?php echo $prefix.'cust_assign_customer'.$customer_user->ID; ?>">-->
<select class="postform" name="<?php echo $prefix ?>cust_assign_customer">

<option value="">None</option>
	<?php
		$args = array(
						'role' => DX_CRM_CUSTOMER_ROLE,
					);
					
		$customer_users = get_users( $args );
		
		foreach ( $customer_users as $customer_user ) {
		?>
		<?php /* ?>	<li> <input type="checkbox" id="<?php echo $prefix.'cust_assign_customer'.$customer_user->ID; ?>" name="<?php echo $prefix ?>cust_assign_customer[]" value="<?php echo $customer_user->ID; ?>" <?php if( !empty( $cust_assign_customer ) ) { checked( in_array( $customer_user->ID, $cust_assign_customer ), true ); } ?>> <label for="<?php echo $prefix.'cust_assign_customer'.$customer_user->ID; ?>"> <?php echo $customer_user->display_name; ?> </label> </li><?php */ ?>
		<option value="<?php echo $customer_user->ID; ?>" <?php if( !empty( $cust_assign_customer ) && $cust_assign_customer == $customer_user->ID ) { echo 'selected';  } ?>><?php echo $customer_user->display_name; ?></option>
		<?php
		}
	?>
</select>