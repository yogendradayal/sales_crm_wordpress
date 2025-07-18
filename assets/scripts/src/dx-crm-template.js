jQuery(document).ready(function() {
	
	/**
	 * DateTimepicker
	 */
    jQuery('.add-datepicker').datepicker({
        dateFormat : 'dd-mm-yy'
    });
    
    /*** Get Customers list using ajax on CRM Project Template ***/
	if(jQuery("#proj_assign_customer").length > 0) {
		
		// Customers List by ajax
		jQuery("#proj_assign_customer").chosen();
	
	
		jQuery('#proj_assign_customer').ajaxChosen({
	
			method: 		'GET',
			url: 			CrmSystem.ajax_url,
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
	
	/*** Get Customers list using ajax on CRM Company Template ***/
	if(jQuery("#company_assign_customer").length > 0) {
		
		// Customers List by ajax
		jQuery("#company_assign_customer").chosen({ width: '100%' });
	
	
		jQuery('#company_assign_customer').ajaxChosen({
	
			method: 		'GET',
			url: 			CrmSystem.ajax_url,
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
				jQuery("#company_assign_customer").chosen({ width: '100%' });
			return terms;
		});
	}
	
	/*** Get Project Type list using ajax on CRM Customer Template ***/
	if(jQuery("#cust_first_pro_type").length > 0) {
		
		// Customers List by ajax
		jQuery("#cust_first_pro_type").chosen({ width: '100%' });
	
	
		jQuery('#cust_first_pro_type').ajaxChosen({
	
			method: 		'GET',
			url: 			CrmSystem.ajax_url,
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
				jQuery("#company_assign_customer").chosen({ width: '100%' });
			return terms;
		});
	}
    
	/** Data Validation */
	jQuery.validate({
		form : '.crm-template-form',
		errorMessagePosition: 'element'
	});
	
});/*** document.ready ***/