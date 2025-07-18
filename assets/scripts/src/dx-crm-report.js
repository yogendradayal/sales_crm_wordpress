jQuery( document ).ready(function() {
	
	jQuery( '.dx-crm-report-table-result' ).DataTable();
	jQuery(".chosen-select").chosen({
		placeholder_text_single: "Please select..",
		placeholder_text_multiple: "Please select.."
	});
		
	jQuery( ".add-datepicker" ).datepicker();
	
	jQuery("#crm-company-form").submit(function( event ) {
		
		event.preventDefault(); // Prevent html submit
				
		jQuery.ajax({			 		 
			 url : CrmSystem.ajaxurl,
			 type: "post",			 
			 data : { 
				action: 'ajaxcrm_chckrprtquery',
				content: jQuery("#crm-company-form").serialize()
				},
			 success: function(data) {
				console.log(data);				
				
				if( data == "success" ){
					jQuery("#report-notice").text('You have successfully generate new report!');
					jQuery("#report-notice").removeClass('report-error');
					jQuery("#report-notice").addClass('report-success');
					
					jQuery("#crm-company-form").off('submit');
					jQuery("#crm-company-form").submit({submit: true});
					
				} else {
					jQuery("#report-notice").text('No report matched your criteria. Please try again!');
					jQuery("#report-notice").removeClass('report-success');
					jQuery("#report-notice").addClass('report-error');
				}
				
				setTimeout(function(){ 
					jQuery("#report-notice").text('');
					jQuery("#report-notice").removeClass('report-success');
					jQuery("#report-notice").removeClass('report-error');
				}, 5000);
			 }
		});
		
	}); 
	
});