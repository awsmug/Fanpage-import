<?php
/**
 * Skip Form
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Form extends HTML_Element{
	
	var $option_group;
	
	/**
	 * WP Form constructor
	 *
	 * @package Skip
	 * @since 1.0
	 * 
	 * @param string $name The name of the form
	 * @param string $args Array of [ 'id' ], [ 'classes' ], [ 'before_element' ], [ 'after_element' ] and [ 'params' ]
	 */
	function __construct( $name, $args = array() ){
		global $skip_form_name;
		
		/*
		 * Additional parent args:
		 * 'id'
		 * 'classes'
		 * 'before_element'
		 * 'after_element'
		 * 'params'
		 */
		$defaults = array(
			'enctype' => 'multipart/form-data',
			'classes' => 'skip_form'
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		if( '' == $name )
			return;
		
		$method = 'POST';
		$action = $_SERVER[ 'REQUEST_URI' ];
		
		$args[ 'close_tag' ] = TRUE;
		
		parent::__construct( 'form', $args );
		
		$skip_form_name = $name;
		$this->add_param( 'name', $name );
		
		if( '' != $method )
			$this->add_param( 'method', $method );
		
		if( '' != $action )
			$this->add_param( 'action', $action );
		
		if( '' != $enctype )
			$this->add_param( 'enctype', $enctype );
		
		// Needed Fields for Form
		$needed_fields = wp_nonce_field( 'skip_form_' . $name, $name . '_wpnonce', TRUE , FALSE ) ;
		$needed_fields.= '<input type="hidden" name="MAX_FILE_SIZE" value="' . max_upload() . '" />';
		
		$this->add_element( $needed_fields );
	}
}

/**
 * Skip start form function
 * 
 * Use it if you don't want to put all in the content variable
 *
 * @package Skip\Forms
 * @since 1.0
 * @param string $name The name of the form
 * @param string $args Array of [ 'id' ], [ 'classes' ], [ 'before_element' ], [ 'after_element' ] and [ 'params' ]
 * @param string $return How to return 'echo', 'object' or 'html'
 */
function form_start( $name, $args = array(), $return = 'echo' ){
	global $skip_form, $skip_form_args, $skip_form_return;
	
	$skip_form = new Form( $name, $args );
	
	$skip_form_return = $return;
	
	ob_start( __NAMESPACE__ . '\form_content_buffer' );
}
/**
 * Skip end form function
 * 
 * Use it if you don't want to put all in the content variable
 *
 * @package Skip\Forms
 * @since 1.0
 * @param string $return How to return 'echo', 'object' or 'html'
 */
function form_end(){
	global $skip_form, $skip_form_buffer, $skip_form_return, $skip_form_save_fields;
	ob_end_flush();	
	
	$skip_form->add_element( $skip_form_buffer );
	
	// Saving form field names for early saving fields
	if( is_array( $skip_form_save_fields ) )
		update_option( 'skip_form_save_field_array', $skip_form_save_fields );
	
	echo $skip_form->render();
}

/**
 * Skip form content buffer for internal use
 * 
 * @package Skip
 * @since 1.0
 * @param string $content Content to buffer
 * @ignore
 */
function form_content_buffer( $content ){
	global $skip_form_buffer;
	$skip_form_buffer = $content;
}

/**
 * Skip form save function
 * 
 * @package Skip
 * @since 1.0
 * @ignore
 */
function form_save_fields(){
	global $wpdb;
	
	$skip_form_save_fields = get_option( 'skip_form_save_field_array' );
	
	if( is_array( $skip_form_save_fields ) ): // If there is any data to save
		foreach( $skip_form_save_fields AS $form_name => $form_fields ): // Getting form name and fields of form
			if( array_key_exists( $form_name . '_value', $_POST ) ): // If there was posted something
				if ( wp_verify_nonce( $_POST[ $form_name . '_wpnonce' ], 'skip_form_' . $form_name ) ): // Verifying form
					
					// Running all fields
					foreach( $form_fields AS $field ):
						// If field name is in _POST data
						if( array_key_exists( $field['name'], $_POST[ $form_name . '_value' ] ) ):
							$new_value = '';
							
							$value = $_POST[ $form_name . '_value' ][ $field[ 'name' ] ];
							
							// Getting Value if it's an array
							if( is_array( $value ) && '' != $field[ 'array' ] ):
								$new_value = _cleanup_value( $value, $field[ 'array' ] );
							elseif ( !is_array( $value ) ):
								$new_value = $value;
							endif;
							
							// Saving if there is a value to save
							if( '' != $new_value ):
								update_option( $field[ 'option_name' ], $new_value );
							else:
								delete_option( $field[ 'option_name' ] );
							endif;
						else:
							delete_option( $field[ 'option_name' ] );
						endif;
					endforeach;
				endif;
			endif;
			
		endforeach;
		
		delete_option( 'skip_form_save_fields' );
		
	endif;
	
}
add_action( 'init', __NAMESPACE__ . '\form_save_fields' );

/**
 * _cleanup_value()
 *
 * Returns the pure value of value.
 *
 * @package Skip\Tools
 * @param mixed $value The value got from DB
 * @param array $array The array structure
 * @param int $i The key of the array from array structure
 * @since 1.0
 * @ignore
 */
function _cleanup_value( $value, $array, $i = 0 ){
	if( is_array( $array ) )
		$index = $array[ $i ];
	else
		$index = $array;
	
	if( is_array( $value[ $index ] ) ):
		return _cleanup_value( $value[ $index ], $array, $i+1 );
	else:
		return $value[ $index ];
	endif;
}
