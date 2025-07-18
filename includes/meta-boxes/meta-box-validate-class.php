<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Meta Box Validate Class
 *
 * Handles all the functions to validate meta box data.
 *
 * @package WP Meta Box
 * @since 1.0.0
 */

if ( ! class_exists( 'at_Demo_Meta_Box_Validate' ) ) {

class at_Demo_Meta_Box_Validate {

	public $model;
	
	//class constructor
	public function __construct() {
		
		global $wpd_mb_model;
		
		$this->model = $wpd_mb_model;
	}
	
	public function date_str_to_time($data){
            return strtotime($data);
    }
    
    public function escape_html($data){
	
    	return $this->model->wpd_mb_escape_slashes_deep($data); 
    }
	
} // End Class

} // End Check Class Exists
?>