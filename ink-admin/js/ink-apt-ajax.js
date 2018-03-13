jQuery.noConflict();
jQuery(document).ready(function(){

jQuery('#time').attr("disabled", true); // timeslot disabled
jQuery('#sr_price').attr("disabled", true); // price disabled

    jQuery('#aptcal').change(function(){
	jQuery('#time').attr("disabled", false);  // timeslot enabled
	  // timeslot enabled
	var id = jQuery('#service_select').val()
	var ids = jQuery('#aptcal').val()
      var data = {
        action: 'master_response',
        id:id,        
        ids: ids         
		};
		
    jQuery.post(script_call.ajaxurl, data, function(response) {			
			if(response){
				   //alert(response);
		jQuery("#time").html('');	     
		 jQuery("#time").html(response);	
		 			}
			else{
			jQuery("#time").html('');
			jQuery("#time").html("<option value='notavi'>Not Available</option>");
			jQuery('#sr_price').attr("disabled", true);
			}
	 	});
	 
    });
	    jQuery('#service_select').change(function(){
		var price = jQuery('#service_select').val()
		jQuery('#aptcal').attr("disabled", false); //jquery  time slot enabled
		
      var data = {
        action: 'master_response',
        price: price         
		};
		
		jQuery.post(script_call.ajaxurl, data, function(response) {		
	
			if(response){				
    jQuery("#price").html('');			
		 jQuery("#price").html(response);
		jQuery('#sr_price').attr("disabled", true); // disabled sr_price	
		
			}else{
			
			alert("Error");
			}
			//return false;
	 	});
    });
});
    