<?php
/**
 * @package Attachments_as_Custom_Fields
 * @version 0.0.1
 */
/*
 Plugin Name: Attachments as Custom Fields
 Plugin URI: http://www.mikevanwinkle.com	
 Description: Allows users to associate uploaded attachments to the parent post via custom field. 
 Version: 0.0.1
 Author: Mike Van Winkle 
 Author URI: http://www.mikevanwinkle.com
*/



function acf_setup_select_field($form_fields,$post) {
		$form_fields["acf_field"]["label"] = __("Post Custom Field");  
		$form_fields["acf_field"]["input"] = "html";  
		$option_array = acf_get_cfs();
		$old = get_post_meta($post->ID, 'acf_field', true); 
		$options = '
		<option value="" style="font-weight:bold;">None ... </option>
		<option value="acf_add" style="font-weight:bold;">Add new ... </option>
		';
		foreach($option_array as $option) { 
		$new = $option->meta_key;
		$selected = ($old == $new) ? 'selected' : '' ;
		$options .= '<option value="'.$option->meta_key .'" '.$selected .'>'.$option->meta_key .'</option>'; 
		}
		$form_fields["acf_field"]["html"] .= "<select class='target' style='float:left; margin-right: 5px;' name='attachments[{$post->ID}][acf_field_select]' id='attachments[{$post->ID}][acf_field_select]'>$options</select>";
		$form_fields["acf_field"]["html"] .= "<div id=\"acf_input_hidden\"><input style='padding: 5px;border: 1px solid #DFDFDF; background:right; float:left; margin: 0 5px; width: 250px; border-radius:4px; -moz-border-radius:4px; -webkit-border-radius: 4px;' name='attachments[{$post->ID}][acf_field_text]' id='attachments[{$post->ID}][acf_field_text]'></input></div>";
		return $form_fields;
}

add_filter("attachment_fields_to_edit", "acf_setup_select_field", null, 2); 
function acf_save_selected_cf($post, $attachment) {
	if( ($attachment['acf_field_text'] == '' ) ) 
	{
		$val = $attachment['acf_field_select'];
	}
	else 
	{
		$val = $attachment['acf_field_text'];
	}
	$old = get_post_meta($post['ID'],'acf_field',true);
	delete_post_meta($post['post_parent'], $old);	
	update_post_meta($post['ID'], 'acf_field', $val, $old);
	update_post_meta($post['post_parent'], $val, $post['guid'], $old_val);
	return $post;
}

add_filter("attachment_fields_to_save",'acf_save_selected_cf',null,2); 
function acf_get_cfs() {
	global $wpdb;
	$pre = $wpdb->prefix;
	$q = "SELECT DISTINCT meta_key FROM wp_postmeta WHERE meta_key NOT LIKE '\_%'";
	$result = $wpdb->get_results($q);
	return $result;
}

add_action('admin_print_scripts','acf_admin_script',10);
function acf_admin_script() {
	$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	wp_register_script( 'acf-jq', $dir .'acf.js','jquery');
	wp_enqueue_script('acf-jq');
}
?>