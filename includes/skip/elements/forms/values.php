<?php

/**
 * <pre>skip_values( $form )</pre>
 * 
 * Get all Values of a form.
 * 
 * <b>Default Usage</b>
 * <code>
 * $value = skip_value( 'myformname', 'fieldname' );
 * </code>
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $form // (string) (required) The name of the form.
 * </code>
 * 
 * <b>Example</b>
 * 
 * Getting back all data from 'myformname' form.
 * <code>
 * $values = skip_values( 'myformname' );
 * skip_print( $values ); // Printing out all values of the form.
 * </code>
 * 
 * @param string $form The name of the form
 * @package Skip\Forms
 * @since 1.0
 */
 
namespace skip\v1_0_0;
 
function values( $form_name ){
	global $wpdb;
	
	$rows = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE %s ", 'skip_framework_value_' . $form_name . '%' ) );
	
	$start_field = strlen( 'skip_framework_value_' . $form_name . '_' );
	
	$values = array();
	
	foreach( $rows AS $row ):
		$values_new = array();
		$length_field = strlen( $row->option_name );
		$field_name = substr( $row->option_name, $start_field, $length_field );
		$fieldname_array = split( SKIP_DELIMITER, $field_name );
		
		$values_new[ $fieldname_array[ 0 ] ] = _reconstruct_value( $fieldname_array, $row->option_value );
		$values = array_merge_recursive_distinct( $values, $values_new );
	endforeach;
	
	return $values;
}
/**
 * _skip_reconstruct_value()
 *
 * Reconstructs Array read from Options table.
 *
 * @package Skip\Tools
 * @param mixed $fieldname_array The array structure got from the option name
 * @param string $value The value to add to the array structure
 * @param int $i Internal integer for running array
 * @since 1.0
 * @ignore
 */
function _skip_reconstruct_value( $fieldname_array, $value, $i = 1 ){
	
	if( array_key_exists( $i, $fieldname_array ) ):
		$array[ $fieldname_array[ $i ] ] = _reconstruct_value( $fieldname_array, $value, $i+1 );
		$value = $array;
	endif;
	
	return $value;
}
/**
 * <pre>skip_value( $form_name, $field_name )</pre>
 * 
 * Geting a specific value of a form.
 *
 * <b>Default Usage</b>
 * <code>
 * $value = skip_value( 'myformname', 'fieldname' );
 * </code>
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $form_name // (string) (required) The name of the form.
 * $field_name // (string) (required) The name of the field.
 * </code>
 * 
 * @param string $form_name The name of the form.
 * @param string $field_name The name of the value to get.
 * @package Skip\Forms
 * @since 1.0
 */
function value( $form_name, $field_name ){
	return get_option( 'skip_framework_value_' . $form_name . '_' . $field_name );
}
/**
 * <pre>skip_delete_value( $form, $name )</pre>
 * 
 * Deleting a Value from DB.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_delete_value( 'myformname', 'fieldname' );
 * </code>
 * Deletes the field 'fieldname' from 'myformname' form.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $form // (string) (required) The name of the form.
 * $name // (string) (required) The name of the field.
 * </code>
 * 
 * @param string $form_name The name of the form
 * @param string $field_name The name of the value
 * @package Skip\Forms
 * @since 1.0
 */
function delete_value( $form_name, $field_name ){
	return delete_option( 'skip_framework_value_' . $form_name . '_' . $field_name );
}
/**
 * <pre>skip_delete_form_values( $form_name )</pre>
 * 
 * Deleting a Values of a Form from DB.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_delete_form_values( 'myformname' );
 * </code>
 * Deletes all fields of form 'myformname' form.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $form_name // (string) (required) The name of the form.
 * </code>
 * 
 * @param string $form_name The name of the form
 * @package Skip\Forms
 * @since 1.0
 */
function delete_form_values( $form_name ){
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s ", 'skip_framework_value_' . $form_name . '%'  ) );
}
