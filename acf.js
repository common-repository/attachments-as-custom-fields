/*
** acf plugin jquery
*/
	jQuery(document).ready(function() {
				var field = jQuery('#acf_input_hidden');
				var target = jQuery('.target');
				field.hide();
				target.change(function() {
					var str = jQuery('option:selected').val();	
					if(str == 'acf_add') { field.show(); } else { field.hide(); }
				});
			});