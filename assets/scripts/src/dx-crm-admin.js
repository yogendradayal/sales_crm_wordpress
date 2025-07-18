jQuery(document).ready(function($){
	
	
		 //jQuery("select").each(function (){
	 /* jQuery(".dx-crm-meta-select").each(function() {
	  	 jQuery(this).chosen();
	  	 
	    if(! jQuery(this).hasClass('no-fancy')){
	      jQuery(this).ajaxChosen({
	         type: 'GET',
	         url: ajaxurl,
	         dataType: 'json'
	        
      		});
	    	}
	  	});*/
	// added for quickedit
	jQuery('.inline-editor').livequery(function() {
		
			var id = jQuery(this).attr('id');
			id = id.replace(/^edit-/, '');
			
			if (!id || !parseInt(id)) {
				return;
			}
	
			var assign_customers 		= jQuery('#inline_' + id + '_assign_customers').text();
			var crm_pro_assign_customer = jQuery('#inline_' + id + '_pro_customer').text();
			
			var myArray				= assign_customers.split(",");
			var myArray_customer	= crm_pro_assign_customer.split(",");
			
			// Removing all checked values
			$('.crm-user-check').removeAttr('checked', 'checked');
			$('.crm-customer-check').removeAttr('selected', 'selected');
			
			if( typeof(myArray) != 'undefined' ) {
				$.each( myArray, function( index, value ) {
					
					$('#crm_user_'+value).attr('checked', 'checked');
					
				});
			}
			
			if( typeof(myArray_customer) != 'undefined' ) {
				$.each( myArray_customer, function( index, value ) {
					
					$('#crm_customer_'+value).attr('selected', 'selected');
					
				});
			}
	});
	
	/**
     * DateTimepicker Field.
     *
     * @since 1.0
     */
    jQuery('.dx-crm-meta-datetime').datepicker({dateFormat: "yy-mm-dd",changeYear:true,changeMonth:true, minDate: new Date() });
    
    // start date and end date range for project
    $("#dxcrm_pro_start_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
		minDate: ( !! $("#dxcrm_pro_start_date").val() )? $("#dxcrm_pro_start_date").val(): new Date(),
        onClose: function (selectedDate) {
			var minDate = new Date( selectedDate );
			if( minDate < new Date() ){
				minDate = new Date();
			}
            $("#dxcrm_pro_end_date").datepicker("option", "minDate", minDate );
        }
    });
    
    $("#dxcrm_pro_end_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
        minDate: new Date(),
        onClose: function (selectedDate) {
			var minDate = $("#dxcrm_pro_start_date").val();
			if( !! minDate ){
				$("#dxcrm_pro_start_date").datepicker("option", "minDate", minDate);
			}
            $("#dxcrm_pro_start_date").datepicker("option", "maxDate", selectedDate);
        }
    });
    // start date and end date range for project
    
	
    // start date and end date range for milestone
    $("#dxcrm_pro_real_end_date_first_mile").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
		minDate: ( new Date( $("#dxcrm_pro_real_end_date_first_mile").val() ) < new Date() )? $("#dxcrm_pro_real_end_date_first_mile").val(): new Date(),
    });
    
    $("#dxcrm_pro_real_end_date_last_conversation").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
        minDate: ( new Date( $("#dxcrm_pro_real_end_date_last_conversation").val() ) < new Date() )? $("#dxcrm_pro_real_end_date_last_conversation").val(): new Date(),
    });
    // start date and end date range for milestone

	// start date and end date range for customer
    $("#dxcrm_cust_contact_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
        //minDate: new Date()
    });	
	// start date and end date range for customer
    
    jQuery('.dx-crm-meta-time-picker').timepicker({ampm: true});
    
    /*$( '.dx_crm_cust_name_error' ).html('').hide();
    $( '.dx_crm_cust_email_error' ).html('').hide();*/
    $( '.dx_crm_pro_name_error' ).html('').hide();
    $( '.dx_crm_pro_cost_error' ).html('').hide();
    $( '.dx_crm_mile_name_error' ).html('').hide();
    $( '.dx_crm_mile_extra_cost_error' ).html('').hide();
    $( '.dx_crm_emp_name_error' ).html('').hide();
    $( '.dx_crm_emp_email_error' ).html('').hide();
    $( '.dx_crm_hourly_rate_error' ).html('').hide();
    $( '.dx_crm_comp_cost_error' ).html('').hide();
    
    var error = 'false';
    
    $( document ).on( 'click', '#publish', function() {
		
    	/*// require field validation for customers
		if( $( '.dx-crm-cust-name' ).is( ':visible' ) ) {
			
			$( '.dx_crm_cust_name_error' ).html('').hide();
			$( '.dx_crm_cust_email_error' ).html('').hide();
			
			cust_name 	= $( '.dx-crm-cust-name' ).val();
			cust_email 	= $( '.dx-crm-cust-email' ).val();
			
			if( cust_name == '' ) { // Check email is empty
				
				$( '.dx_crm_cust_name_error' ).html( 'Please enter customer name.' ).show();
				var error = 'true';
			} 
			
			if( cust_email == '' ) { // Check email is empty
				
				$( '.dx_crm_cust_email_error' ).html( 'Please enter customer email.' ).show();
				var error = 'true';
				
			} else {
				
				if( !dx_crm_valid_email( cust_email ) ) { // Check email is valid or not
					
					$( '.dx_crm_cust_email_error' ).html( 'Please enter valid email.' ).show();
					var error = 'true';
				}
			}
			if( error == 'true' ) {
				
				$( this ).parent().find( '.spinner' ).hide();
				$( this ).removeClass( 'button-primary-disabled' );
				
				var voucodecontent = $('#crm_meta_customer'); 
				$('html, body').animate({ scrollTop: voucodecontent.offset().top - 50 }, 500);
				
				return false;
			}
		}*/
		
		// require field validation for projects
		/*if( $( '.dx-crm-pro-name' ).is( ':visible' ) ) {
			
			$( '.dx_crm_pro_name_error' ).html('').hide();
			$( '.dx_crm_pro_cost_error' ).html('').hide();
			
			pro_name 		= $( '.dx-crm-pro-name' ).val();
			pro_agrees_cost = $( '#dx_crm_pro_agreed_cost' ).val();
			
			if( pro_name == '' ) { // Check email is empty
				
				$( '.dx_crm_pro_name_error' ).html( 'Please enter project name.' ).show();
				var error = 'true';
			}
			
			if( pro_agrees_cost != '' && IsNumeric( pro_agrees_cost ) == false ) { // Check email is empty
				
				$( '.dx_crm_pro_cost_error' ).html( 'Please enter valid agreed cost.' ).show();
				var error = 'true';
			} 
			if( error == 'true' ) {
				
				$( this ).parent().find( '.spinner' ).hide();
				$( this ).removeClass( 'button-primary-disabled' );
				
				var voucodecontent = $('#crm_meta_project'); 
				$('html, body').animate({ scrollTop: voucodecontent.offset().top - 50 }, 500);
				
				return false;
			} 
		}*/
		
		// require field validation for milestone
		if( $( '.dx-crm-mile-name' ).is( ':visible' ) ) {
			
			$( '.dx_crm_mile_name_error' ).html('').hide();
			$( '.dx_crm_mile_extra_cost_error' ).html('').hide();
			
			mile_name 	= $( '.dx-crm-mile-name' ).val();
			extra_cost	= $( '#dx_crm_mile_extra_cost' ).val();
			
			if( mile_name == '' ) { // Check email is empty
				
				$( '.dx_crm_mile_name_error' ).html( 'Please enter milestone name.' ).show();
				var error = 'true';
			}
			
			if( extra_cost != '' && IsNumeric( extra_cost ) == false ) { // Check email is empty
				
				$( '.dx_crm_mile_extra_cost_error' ).html( 'Please enter valid extra cost.' ).show();
				var error = 'true';
			}
			
			if( error == 'true' ) {
				
				$( this ).parent().find( '.spinner' ).hide();
				$( this ).removeClass( 'button-primary-disabled' );
				
				var voucodecontent = $('#crm_meta_milestone'); 
				$('html, body').animate({ scrollTop: voucodecontent.offset().top - 50 }, 500);
				
				return false;
			} 
		}
		
		// require field validation for Staff Member Details
		if( $( '.dx-crm-emp-name' ).is( ':visible' ) ) {
			
			$( '.dx_crm_emp_name_error' ).html('').hide();
			$( '.dx_crm_emp_email_error' ).html('').hide();
			$( '.dx_crm_hourly_rate_error' ).html('').hide();
			
			emp_name 	= $( '.dx-crm-emp-name' ).val();
			emp_email 	= $( '#dx_crm_emp_email' ).val();
			hourly_rate	= $( '#dx_crm_emp_hourly_rate' ).val();
			
			if( emp_name == '' ) { // Check email is empty
				
				$( '.dx_crm_emp_name_error' ).html( 'Please enter employee name.' ).show();
				var error = 'true';
			}
			
			/*if( emp_email == '' ) { // Check email is empty
				
				$( '.dx_crm_emp_email_error' ).html( 'Please enter customer email.' ).show();
				var error = 'true';
				
			} else*/ if( emp_email != '' ) {
				
				if( !dx_crm_valid_email( emp_email ) ) { // Check email is valid or not
					
					$( '.dx_crm_emp_email_error' ).html( 'Please enter valid email.' ).show();
					var error = 'true';
				}
			}
			
			if( hourly_rate != '' && IsNumeric( hourly_rate ) == false ) { // Check email is empty
				
				$( '.dx_crm_hourly_rate_error' ).html( 'Please enter valid hourly rate.' ).show();
				var error = 'true';
			}
			 
			if( error == 'true' ) {
				
				$( this ).parent().find( '.spinner' ).hide();
				$( this ).removeClass( 'button-primary-disabled' );
				
				var voucodecontent = $('#crm_meta_staff'); 
				$('html, body').animate({ scrollTop: voucodecontent.offset().top - 50 }, 500);
				
				return false;
			} 
		}
		
		// require field validation for milestone
		if( $( '.com_exp' ).is( ':visible' ) ) {
			
			$( '.dx_crm_comp_cost_error' ).html('').hide();
			
			comp_cost	= $( '#dx_crm_comp_cost' ).val();
			
			if( comp_cost != '' && IsNumeric( comp_cost ) == false ) { // Check email is empty
				
				$( '.dx_crm_comp_cost_error' ).html( 'Please enter valid cost.' ).show();
				var error = 'true';
			}
			
			if( error == 'true' ) {
				
				$( this ).parent().find( '.spinner' ).hide();
				$( this ).removeClass( 'button-primary-disabled' );
				
				var voucodecontent = $('#crm_meta_company'); 
				$('html, body').animate({ scrollTop: voucodecontent.offset().top - 50 }, 500);
				
				return false;
			} 
		}
	});
	
	jQuery( document ).on( 'change', '#role', function() {
		
		var role = jQuery( '#role' ).val();
		var current_user_role = jQuery( '#current_user_role' ).val();
		
		jQuery( '.dxcrm-customer-row' ).hide();
		jQuery( '.crm-user-meta-details-wrp' ).hide();
		
		jQuery( '.dxcrm-customer-row' ).attr( 'disabled', 'disabled' );
		
		if( role == 'dx_crm_customer') { // Check role Staff Member
			
			jQuery( '.crm-customer-select' ).removeAttr( 'disabled' );
			jQuery( '.crm-user-meta-details-wrp' ).show();
			jQuery( '.dxcrm-customer-row' ).show();
		}
	})	;
    
	
	$('.crm_project_status').barrating('show', {
		
		onSelect:function(value, text) {
           
			var res = value.split("_"); 
	    	
	    	project_status = res[0];
	    	post_id = res[1];
	    	
	    	var data = {
							action			: 'crm_change_status',
							post_id			: post_id,
							project_status	: project_status,
						};
		
			$.post(CrmSystem.ajaxurl, data, function(response) {
			});
        }
	});
	
	
	/*$(document).on('change', '.crm_quote_status', function() {
		
		jQuery(this).parent().find('.quote_status_loader').show();
		
		var id			 = jQuery(this).attr('id');
		var post_id 	 = id.replace(/^quote_status_/, '');		
		var quote_status = jQuery(this).val();
    	
    	var data = {
						action			: 'crm_change_status',
						post_id			: post_id,
						quote_status	: quote_status,
					};
	
		$.post(CrmSystem.ajaxurl, data, function(response) {
			
			jQuery('.quote_status_loader').hide();
			
		});
	});*/
	
	//Show add customer popup when click on add new customer button
	$(document).on('click', '.dx-crm-add-customer-link', function() {
		
		$('.dx-crm-cust-title-error').hide();
		$('.dx-crm-cust-email-error').hide();
		$('.dx-crm-cust-title-success').hide();
		$('.dx_crm_customer_title_text').val('');
		$('.dx_crm_customer_email_text').val('');
		
		$('.dx-crm-cust-popup-content').fadeIn('slow');
		$('.dx-crm-cust-popup-overlay').fadeIn('slow');
			    	    	    
		$('html,body').animate({scrollTop: 0}, 800);
		
		return false;
	});
	
	//close customer popup window 
	$( document ).on( "click", ".dx-crm-cust-close-button, .dx-crm-cust-popup-overlay", function() {
		
		$('.dx-crm-cust-popup-overlay' ).fadeOut();
        $('.dx-crm-cust-popup-content' ).fadeOut();
       
	});
	
	//Add customer using ajax
	$( document ).on( "click", ".dx_crm_add_customer_button", function() {
		
		$('.dx-crm-cust-title-error').hide();
		$('.dx-crm-cust-email-error').hide();
		$('.dx-crm-cust-title-success').hide();
				
		var customer_title = $.trim($('.dx_crm_customer_title_text').val());
		var customer_email = $.trim($('.dx_crm_customer_email_text').val());
		
		if ( customer_title != '' && customer_email != '' ) {
		
			var data = {
						action			: 'crm_add_customer',
						customer_title	: customer_title,
						customer_email	: customer_email,					
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
					$('.dx-crm-cust-popup-overlay' ).fadeOut();
        			$('.dx-crm-cust-popup-content' ).fadeOut();
        			
        			$('select#dxcrm_joined_customer').append('<option value="' + json_response.post_id + '">' + json_response.customer_title + '</option>'); 
        			$('.dx_crm_custom_select_customers_list').trigger( 'chosen:updated' );
        			
        			$('select#dxcrm_joined_pro_customer').append('<option value="' + json_response.post_id + '">' + json_response.customer_title + '</option>');
        			$('.dx_crm_custom_select_customers_list').trigger( 'chosen:updated' );

        			$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');	
				}
			});
		
		} else {
			
			if( customer_title == '' ) {
				$('.dx-crm-cust-title-error').show();	
			}
			
			if( customer_email == '' ) {
				$('.dx-crm-cust-email-error').show();	
			}
			
		}
    	return false;
	});
	
	//Add customer using ajax and More
	$( document ).on( "click", ".dx_crm_add_more_customer_button", function() {
		
		$('.dx-crm-cust-title-error').hide();
		$('.dx-crm-cust-email-error').hide();
		$('.dx-crm-cust-title-success').hide();
				
		var customer_title = $.trim($('.dx_crm_customer_title_text').val());
		var customer_email = $.trim($('.dx_crm_customer_email_text').val());
		
		if ( customer_title != '' && customer_email != '' ) {
		
			var data = {
						action			: 'crm_add_customer',
						customer_title	: customer_title,
						customer_email	: customer_email,					
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
					$('.dx_crm_customer_title_text').val('');
					$('.dx_crm_customer_email_text').val('');
        			
        			$('.dx-crm-cust-title-success').show();
        			
        			$('select#dxcrm_joined_customer').append('<option value="' + json_response.post_id + '">' + json_response.customer_title + '</option>'); 
        			$('.dx_crm_custom_select_customers_list').trigger( 'chosen:updated' );
        			
        			$('select#dxcrm_joined_pro_customer').append('<option value="' + json_response.post_id + '">' + json_response.customer_title + '</option>');
        			$('.dx_crm_custom_select_customers_list').trigger( 'chosen:updated' );
        		
        			$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');	
				}
			});
		
		} else {
			
			if( customer_title == '' ) {
				$('.dx-crm-cust-title-error').show();	
			}
			
			if( customer_email == '' ) {
				$('.dx-crm-cust-email-error').show();	
			}
			
		}
    	return false;
	});
	
	//Show add company popup when click on add new company button
	$(document).on('click', '.dx-crm-add-company-link', function() {
		
		$('.dx-crm-comp-title-error').hide();
		$('.dx-crm-comp-title-success').hide();
		$('.dx_crm_company_title_text').val('');
		
		$('.dx-crm-comp-popup-content').fadeIn('slow');
		$('.dx-crm-comp-popup-overlay').fadeIn('slow');
		
		$('html,body').animate({scrollTop: 0}, 800);
		
		return false;
	});
	
	//close company popup window 
	$( document ).on( "click", ".dx-crm-comp-close-button, .dx-crm-comp-popup-overlay", function() {
		
		$('.dx-crm-comp-popup-overlay' ).fadeOut();
        $('.dx-crm-comp-popup-content' ).fadeOut();
       
	});
	
	//Add company using ajax
	$( document ).on( "click", ".dx_crm_add_company_button", function() {
		
		$('.dx-crm-comp-title-error').hide();
		$('.dx-crm-comp-title-success').hide();
				
		var company_title = $.trim($('.dx_crm_company_title_text').val());
		
		if ( company_title != '' ) {
		
			var data = {
						action			: 'crm_add_company',
						company_title	: company_title,						
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
					$('.dx-crm-comp-popup-overlay' ).fadeOut();
        			$('.dx-crm-comp-popup-content' ).fadeOut();
        			
        			$('select#dxcrm_joined_company').append('<option value="' + json_response.post_id + '">' + json_response.company_title + '</option>');
        			$('.dx_crm_custom_select_company').trigger( 'chosen:updated' );
        			
        			$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');
				}
			});
		
		} else {
			
			$('.dx-crm-comp-title-error').show();
		}
    	return false;
	});
	
	
	//Add company using ajax and More
	$( document ).on( "click", ".dx_crm_add_more_company_button", function() {
		
		$('.dx-crm-comp-title-error').hide();
		$('.dx-crm-comp-title-success').hide();
				
		var company_title = $.trim($('.dx_crm_company_title_text').val());
		
		if ( company_title != '' ) {
		
			var data = {
						action			: 'crm_add_company',
						company_title	: company_title,						
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
        			$('.dx_crm_company_title_text').val('');
        			
        			$('.dx-crm-comp-title-success').show();
        			
        			$('select#dxcrm_joined_company').append('<option value="' + json_response.post_id + '">' + json_response.company_title + '</option>');
        			$('.dx_crm_custom_select_company').trigger( 'chosen:updated' );
        			
        			$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');
				}
			});
		
		} else {
			
			$('.dx-crm-comp-title-error').show();
		}
    	return false;
	});
		
	//Show add project popup when click on add new project button
	$(document).on('click', '.dx-crm-add-project-link', function() {
		
		$('.dx-crm-pro-title-error').hide();
		$('.dx-crm-pro-title-success').hide();
		$('.dx_crm_project_title_text').val('');
		
		$('.dx-crm-pro-popup-content').fadeIn('slow');
		$('.dx-crm-pro-popup-overlay').fadeIn('slow');			
    
		$('html,body').animate({scrollTop: 0}, 800);
		
		return false;
	});
	
	//close project popup window 
	$( document ).on( "click", ".dx-crm-pro-close-button, .dx-crm-pro-popup-overlay", function() {
		
		$('.dx-crm-pro-popup-overlay' ).fadeOut();
        $('.dx-crm-pro-popup-content' ).fadeOut();
       
	});
	
	//Add project using ajax
	$( document ).on( "click", ".dx_crm_add_project_button", function() {
		
		$('.dx-crm-pro-title-error').hide();
		$('.dx-crm-pro-title-success').hide();
				
		var title = $.trim($('.dx_crm_project_title_text').val());
		
		if ( title != '' ) {
		
			var data = {
						action			: 'crm_add_project',
						title			: title,						
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
					$('.dx-crm-pro-popup-overlay' ).fadeOut();
        			$('.dx-crm-pro-popup-content' ).fadeOut();
        			
        			$('select#dxcrm_joined_project').append('<option value="' + json_response.post_id + '">' + json_response.project_title + '</option>');
        			$('.dx_crm_custom_select_project_list').trigger( 'chosen:updated' );
        			
        			$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');
				}
			});
		
		} else {
			
			$('.dx-crm-pro-title-error').show();
		}
    	return false;
	});

	//Add project using ajax and More
	$( document ).on( "click", ".dx_crm_add_more_project_button", function() {
		
		$('.dx-crm-pro-title-error').hide();
		$('.dx-crm-pro-title-success').hide();
				
		var title = $.trim($('.dx_crm_project_title_text').val());
		
		if ( title != '' ) {
		
			var data = {
						action			: 'crm_add_project',
						title			: title,						
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
        			$('.dx_crm_project_title_text').val('');
        			
        			$('.dx-crm-pro-title-success').show();
        			
        			$('select#dxcrm_joined_project').append('<option value="' + json_response.post_id + '">' + json_response.project_title + '</option>');
        			$('.dx_crm_custom_select_project_list').trigger( 'chosen:updated' );
        			
        			$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');
				}
			});
		
		} else {
			
			$('.dx-crm-pro-title-error').show();
		}
    	return false;
	});
	
	//Show add project type popup when click on add new project type button
	$(document).on('click', '.dx-crm-add-project-type', function() {
		
		$('.dx-crm-pro-type-title-error').hide();
		$('.dx-crm-pro-type-title-success').hide();
		$('.dx_crm_project_type_title_text').val('');
		
		$('.dx-crm-pro-type-popup-content').fadeIn('slow');
		$('.dx-crm-pro-type-popup-overlay').fadeIn('slow');			
    
		$('html,body').animate({scrollTop: 0}, 800);
		
		return false;
	});
	
	//close project type popup window 
	$( document ).on( "click", ".dx-crm-pro-type-close-button, .dx-crm-pro-type-popup-overlay", function() {
		
		$('.dx-crm-pro-type-popup-overlay' ).fadeOut();
        $('.dx-crm-pro-type-popup-content' ).fadeOut();
       
	});
	
	//Add project type using ajax
	$( document ).on( "click", ".dx_crm_add_project_type_button", function() {
		
		$('.dx-crm-pro-type-title-error').hide();
		$('.dx-crm-pro-type-title-success').hide();
				
		var title = $.trim($('.dx_crm_project_type_title_text').val());
		
		if ( title != '' ) {
		
			var data = {
						action			: 'crm_add_project_type',
						title			: title,						
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
					$('.dx-crm-pro-type-popup-overlay' ).fadeOut();
        			$('.dx-crm-pro-type-popup-content' ).fadeOut();
        			
        			$('select.dx_crm_custom_select_company_type').append('<option value="' + json_response.term_id + '">' + json_response.term_title + '</option>');
        			$('.dx_crm_custom_select_company_type').trigger( 'chosen:updated' );
        			
        			//$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');
				}
			});
		
		} else {
			
			$('.dx-crm-pro-type-title-error').show();
		}
    	return false;
	});
	
	//Add project type using ajax and More
	$( document ).on( "click", ".dx_crm_add_more_project_type_button", function() {
		
		$('.dx-crm-pro-type-title-error').hide();
		$('.dx-crm-pro-type-title-success').hide();
				
		var title = $.trim($('.dx_crm_project_type_title_text').val());
				
		if ( title != '' ) {
		
			var data = {
						action			: 'crm_add_project_type',
						title			: title,						
					};
	
			$.post(CrmSystem.ajaxurl, data, function(response) {
				
				var json_response = JSON.parse(response);
					
				if ( json_response.success ){
					
        			$('.dx_crm_project_type_title_text').val('');
        			
        			$('.dx-crm-pro-type-title-success').show();
        			
        			$('select.dx_crm_custom_select_company_type').append('<option value="' + json_response.term_id + '">' + json_response.term_title + '</option>');
        			$('.dx_crm_custom_select_company_type').trigger( 'chosen:updated' );
        			
        			//$('#dashboard-widgets-wrap').load(window.location.href + ' #dashboard-widgets-wrap');
				}
			});
		
		} else {
			
			$('.dx-crm-pro-type-title-error').show();
		}
    	return false;
	});
	
	$( document ).on( 'click', '.crm-dm-create-dir-button', function() {

		var data = {
			'action': 'crm_dm_create_dir'
		};
		jQuery(this).html("Creating folder...");
		jQuery.post(ajaxurl, data, function(dir_created) {
			if( 'success' == dir_created) {
				jQuery('#crm_dm_dir_none').hide();
				jQuery('#crm_dm_dir_exists').show();				
			}
		});
	});
	
	/*** Get Company list using ajax ***/
	if($(".dx_crm_custom_select_company").length > 0) {
		
		// Company List by ajax
		$(".dx_crm_custom_select_company").chosen();
	
	
		jQuery('.dx_crm_custom_select_company').ajaxChosen({
	
			method: 		'GET',
			url: 			ajaxurl,
			dataType: 		'json',
			minTermLength: 	2,
			data: {
					action: 'dx_crm_ajax_company_list'
				}
			}, function (data) {
			
				var terms = {};
				jQuery.each(data, function (i, val) {
					terms[i] = val;
				});
				
			return terms;
		});
	}
	
	/*** Get Projects list using ajax ***/
	if($(".dx_crm_custom_select_project_list").length > 0) {
		
		// Projects List by ajax
		$(".dx_crm_custom_select_project_list").chosen();
	
	
		jQuery('.dx_crm_custom_select_project_list').ajaxChosen({
	
			method: 		'GET',
			url: 			ajaxurl,
			dataType: 		'json',
			minTermLength: 	2,
			data: {
					action: 'dx_crm_ajax_projects_list'
				}
			}, function (data) {
			
				var terms = {};
				jQuery.each(data, function (i, val) {
					terms[i] = val;
				});
				
			return terms;
		});
	}
	
	/*** Get Company type(categories) list using ajax ***/
	if($(".dx_crm_custom_select_company_type").length > 0) {
		
		// Company type(categories) List by ajax
		$(".dx_crm_custom_select_company_type").chosen();
	
	
		jQuery('.dx_crm_custom_select_company_type').ajaxChosen({
	
			method: 		'GET',
			url: 			ajaxurl,
			dataType: 		'json',
			minTermLength: 	2,
			data: {
					action: 'dx_crm_ajax_company_type_list'
				}
			}, function (data) {
			
				var terms = {};
				jQuery.each(data, function (i, val) {
					terms[i] = val;
				});
				
			return terms;
		});
	}
	
	/*** Get Customers list using ajax ***/
	if($(".dx_crm_custom_select_customers_list").length > 0) {
		
		// Company Customers List by ajax
		$(".dx_crm_custom_select_customers_list").chosen();
	
		jQuery('.dx_crm_custom_select_customers_list').ajaxChosen({
	
			method: 		'GET',
			url: 			ajaxurl,
			dataType: 		'json',
			minTermLength: 	2,
			data: {
					action: 'dx_crm_ajax_customers_list'
				}
			}, function (data) {
			
				var terms = {};
				jQuery.each(data, function (i, val) {
					terms[i] = val;
				});
				
			return terms;
		});
	}
	
});

// validation of email
function dx_crm_valid_email(emailStr) {
	var checkTLD=1;
	var knownDomsPat=/^(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum)$/;
	var emailPat=/^(.+)@(.+)$/;
	var specialChars="\\(\\)><@,;:\\\\\\\"\\.\\[\\]";
	var validChars="\[^\\s" + specialChars + "\]";
	var quotedUser="(\"[^\"]*\")";
	var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	var atom=validChars + '+';
	var word="(" + atom + "|" + quotedUser + ")";
	var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
	var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
	var matchArray=emailStr.match(emailPat);
	if (matchArray==null) {
		//alert("Email address seems incorrect (check @ and .'s)");
		return false;
	}
	var user=matchArray[1];
	var domain=matchArray[2];
	// Start by checking that only basic ASCII characters are in the strings (0-127).
	for (i=0; i<user.length; i++) {
		if (user.charCodeAt(i)>127) {
			//alert("Ths username contains invalid characters in e-mail address.");
			return false;
		}
	}
	for (i=0; i<domain.length; i++) {
		if (domain.charCodeAt(i)>127) {
			//alert("Ths domain name contains invalid characters in e-mail address.");
			return false;
		}
	}
	if (user.match(userPat)==null) {
		//alert("The username doesn't seem to be valid in e-mail address.");
		return false;
	}
	var IPArray=domain.match(ipDomainPat);
	if (IPArray!=null) {
		for (var i=1;i<=4;i++) {
			if (IPArray[i]>255) {
				alert("Destination IP address is invalid!");
				return false;
	   		}
		}
		return true;
	}
	var atomPat=new RegExp("^" + atom + "$");
	var domArr=domain.split(".");
	var len=domArr.length;
	for (i=0;i<len;i++) {
		if (domArr[i].search(atomPat)==-1) {
			//alert("The domain name does not seem to be valid in e-mail address.");
			return false;
	   }	
	}
	if (checkTLD && domArr[domArr.length-1].length!=2 && 
		domArr[domArr.length-1].search(knownDomsPat)==-1) {
		//alert("The address must end in a well-known domain or two letter " + "country.");
		return false;
	}

	if (len<2) {
		//alert("This e-mail address is missing a hostname!");
		return false;
	}	
	return true;
}

function IsNumeric( input ) {
    return (input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0;
}
//********************* END of function for email-id validation  ****************************//