<?php
/**
 * Import Button
 * 
 * Shows a Button to import data for a form
 * 
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Import_Button extends WP_Fileuploader{
	/**
	 * Constructor
	 *
	 * @package Skip
	 * @since 1.0
	 * 
	 * @param string $name Name of colorfield
	 * @param array $args Array of [ $id , $extra Extra colorfield code, option_groupOption group to save data, $before_textfield Code before colorfield, $after_textfield Code after colorfield   ]
	 */
	function __construct( $name, $args = array() ){
		global $post, $skip_form_instance_option_group;
		
		$defaults = array(
			'id' => substr( md5 ( time() * rand() ), 0, 10 ),
			'extra' => '',
			'before_element' => '',
			'uploader' => 'file',
			'after_element' => '',
			'option_group' => $skip_form_instance_option_group
		);
		
		// Adding file actions
		// add_filter( 'sanitize_option_' . $skip_form_instance_option_group . '_values', array( $this , 'validate_actions' ), 9999 );
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );
		
		$this->id = $id;
		$this->delete = TRUE;
		$this->insert_attachement = FALSE;
		
		$this->done_import = FALSE;
		
		parent::__construct( $name, $args );
	}
	
	function validate_actions( $input ){
		global $skip_form_instance_option_group;
		
		// If error occured
		if( $_FILES[ $skip_form_instance_option_group . '_values' ][ 'error' ][ $this->wp_name ] != 0  ){
			$input[ $this->wp_name ] = $this->value;
			
		}else{
			$file[ 'tmp_name' ] = $_FILES[ $skip_form_instance_option_group . '_values' ][ 'tmp_name' ][ $this->wp_name ];
			$input = import_values( $skip_form_instance_option_group, $file[ 'tmp_name' ] );			
		}
		
		return $input;
	}

	function get_html(){
		$import_button = form_button( __( 'Import settings', 'skip_framework' ), array( 'name' => 'import_settings' ) ); 
		$this->after_element = $import_button . $this->after_element;
		$html = parent::get_html();
		
		return $html;
	}
}
/**
 * Importing values from a file
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
function import_values( $option_group, $file_name ){
	
	if( !file_exists( $file_name ) )
		return FALSE;
	
	$file_data = implode ( '', file ( $file_name ) );
	
	$values = unserialize( $file_data );
	
	return $values;
}
/**
 * Import Button
 * @package Skip\Forms
 * @since 1.0
 */ 
function import_button( $name, $args, $return = 'echo' ){
	$import_button = new Import_Button( $name, $args );
	return element_return( $import_button, $return );
}
