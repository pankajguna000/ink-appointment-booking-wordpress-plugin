jQuery.noConflict();
jQuery(document).ready(function(){
jQuery("#fname").attr("required", "true");
jQuery("#aptemail").attr("required", "true");
jQuery("#aptphone").attr("required", "true");
jQuery("#aptcal").attr("required", "true");

jQuery('input[placeholder]').each(function(){  
    var input = jQuery(this);        
   jQuery(input).val(input.attr('placeholder'));
                
 jQuery(input).focus(function(){
        if (input.val() == input.attr('placeholder')) {
           input.val('');
        }
    });
        
 jQuery(input).blur(function(){
       if (input.val() == '' || input.val() == input.attr('placeholder')) {
           input.val(input.attr('placeholder'));
       }
    });
});
jQuery('textarea[placeholder]').each(function(){  
    var textarea = jQuery(this);        
   jQuery(textarea).val(textarea.attr('placeholder'));
                
 jQuery(textarea).focus(function(){
        if (textarea.val() == textarea.attr('placeholder')) {
           textarea.val('');
        }
    });
        
 jQuery(textarea).blur(function(){
       if (textarea.val() == '' || textarea.val() == textarea.attr('placeholder')) {
           textarea.val(textarea.attr('placeholder'));
       }
    });
});

});