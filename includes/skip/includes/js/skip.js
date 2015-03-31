function hide_class(dropdownID) {
	var selval = jQuery("#" + dropdownID).val();

	jQuery("input." + dropdownID).each( function (index, field)
	{
    	if(field.name == 'hide_' + selval){
    		jQuery('.' + field.value).hide();
  		}
  		if(field.name == 'show_' + selval) {
  			jQuery('.' + field.value).show();
  		} else {
  			jQuery('.' + field.value).hide();
  		}
	});
}
jQuery(document).ready(function($){
	$(".skip_delete_file").button({
			icons: {
				primary: 'ui-icon-trash'
			}
		}
	);
	
	/* Deleting wp-dialog class from dialog for clean jquerui css */
	$( ".wp-editor-wrap" ).click(function(){ 
		$( "div" ).removeClass( "wp-dialog" );
	});
});
