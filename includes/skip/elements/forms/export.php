<?php
/**
 * Export Button
 * 
 * Shows a Button to export data from a form
 * 
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Export_Button extends WP_Form_Button{
	
	/**
	 * Constructor
	 *
	 * @package Skip
	 * @since 1.0
	 * @param array $args Array of  [ $value Value, $args   ]
	 */
	function __construct( $value, $args = array() ){
		global $skip_form_instance_option_group;
		
		$defaults = array(
			'id' => '',
			'name' => $value,
			'forms' => array(),
			'file_name' => 'export_' . date( 'Ymdhis', time() ) . '.txt',
			'extra' => '',
			'before_element' => '',
			'after_element' => ''
		);
		
		add_filter( 'sanitize_option_' . $skip_form_instance_option_group . '_values', array( $this , 'validate_actions' ), 9999 );
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		parent::__construct( $value, $args );
		
		$this->lookup_name = $name;
		
		$this->submit = TRUE;
		$this->forms = $forms;
		$this->file_name = $file_name;
		$this->extra = $extra;
	}
	
	function validate_actions( $input ){
		global $skip_form_instance_option_group;

		if( $input[ $this->lookup_name ] != '' ){
			download_export_values( $this->forms, $this->file_name );
			$input = get_option( $skip_form_instance_option_group . '_values' );
		}
		return $input;
	}
	
}
/**
 * Exporting values as a string
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
function export_values( $option_groups ){
	foreach( $option_groups AS $option_group ){
		$values = serialize ( (array) get_values( $option_group ) );
		$serialized_val.= $values ;
	}
	return $serialized_val;
}
/**
 * Exporting values in a file as download
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
function download_export_values( $option_groups, $file_name = 'export.skp' ){
	header("Content-Type: text/plain");
	header('Content-Disposition: attachment; filename="' . $file_name . '"');
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	
	echo export_values( $option_groups );
	
	exit;
}
/**
 * Export Button
 * @package Skip\Forms
 * @since 1.0
 */
function export_button( $value, $args, $return = 'echo'  ){
	$export_button = new Export_Button( $value, $args );
	return element_return( $export_button, $return );		
}
