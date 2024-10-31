jQuery(document).ready(function(e) {
  /**
 * Dropdown Images
 */
  try {
    jQuery("body select.dropimg").msDropDown();
  } catch(e) {
    console.log(e.message);
  }
  
  /**
 * Check Agreement
 */
  try {
    agreement();
  } catch(e) {
    console.log(e.message);
  }
  
   /**
 * Notify FadeOut
 */
  jQuery("#notify").fadeOut(5000);
  
  
  /**
 * Accept Agreement
 */
  jQuery("#agree").live("click", function(event) {
    agreement();
  });

});

/**
 * Term and Condition switch
 */
function agreement(e) {
  if(jQuery("input#agree").is(':checked')){
    jQuery("#active").show();
    jQuery("#deactive").hide();
  }else{
    jQuery("#active").hide();
    jQuery("#deactive").show();
  }
}

