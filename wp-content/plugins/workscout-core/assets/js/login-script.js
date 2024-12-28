	/* ----------------- Start Document ----------------- */
(function($){
"use strict";

$(document).ready(function(){ 
    
   
    
    if(workscout_core.recaptcha_status){
        if(workscout_core.recaptcha_version == 'v3'){
            getRecaptcha();        
        }
    }
   
   

// ------------------ End Document ------------------ //
});

})(this.jQuery);