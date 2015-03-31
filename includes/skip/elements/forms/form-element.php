<?php
/**
 * Skip Form element class
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

abstract class Form_Element extends HTML_Element{
	public $form_name;
	public $name;
	public $value;
	public $save;
	protected $errors = array();
	protected $error_msgs = array();
	protected $array;
	
	/**
	 * Constructor
	 * @package Skip
	 * @since 1.0
	 * @param string $tag The HTML Tag to use for element
	 * @param string $name The name of the element where to save data
	 * @param array $args Arguments
	 */
	function __construct( $tag, $name, $args = array() ){
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
			'label' => '',
			'description' => '',
			'default' => FALSE,
			'value' => '',
			'array' => '',
			'save' => TRUE,
			'disabled' => FALSE,
			'form_name' => $skip_form_name,
			'errors' => array( 
				1 => __( 'Form could not be verified. Field could not be saved.', 'skip_framework' ),
				2 => sprintf( __( 'Upload limit exceeded. %s Bytes uploaded, %s Bytes allowed. Please try to upload one file after another or choose a smaller file.', 'skip_framework' ), array_key_exists( 'CONTENT_LENGTH', $_SERVER ) ? $_SERVER['CONTENT_LENGTH'] : 0, max_upload() )
			)
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );
		
		parent::__construct( $tag, $args );
		
		$this->form_name = $form_name;
		$this->name = $name;
		$this->save = $save;
		$this->default = $default;
		
		// Setting up Array
		if( !is_array( $array ) ):
			$this->array = explode( ',', $array ); // For Values by param String
			
			if( count( $this->array ) == 1 ):
				$this->array = $this->array[ 0 ];
			endif;
			
		// If array is given
		else:
			$this->array = $array;
		endif;
		
		$this->errors = $errors;
		
		$this->field_name = $this->field_name(); // Name of HTML Field 
		$this->option_name = $this->option_name(); // Name of WP Option Field
		
		if( $save )
			$this->save(); // Register for saving
		
		if( '' == $value )
			$this->value = $this->value();
		else 
			$this->value = $value;
		
		// Setting up Tag
		$this->add_param( 'name', $this->field_name );
		$this->add_param( 'value', $this->value );
		
		if( TRUE == $disabled )
			$this->add_param( 'disabled', 'disabled' );
		
		$this->before( '<div class="skip_field">' );
		
		if( '' != $label )
			$this->before( '<div class="skip_field_row skip_field_row_' . $this->params[ 'id' ] . '"><label for="' . $this->params[ 'id' ] . '">' . $label . '</label><div class="skip_row_content">' );
		
		if( '' != $description )
			$this->after( '<div class="skip_field_description skip_field_description_' . $this->params[ 'id' ] . '">' . $description . '</div>' );
		
		if( '' != $label )
			$this->after( '</div><div class="clear"></div></div>' );
		
		$this->after( '</div>' );
	}

	/**
	 * Value
	 * 
	 * Gets the value type and returns the value from the field
	 *
	 * @package Skip
	 * @since 1.0
	 * @return string $value The Value of the field
	 */
	protected final function value(){
		$value = $this->option_value();
		
		if( !$value && $this->default !== FALSE ):
			return $this->default; // If there has been no value before use default
		else:
			return $value;
		endif;
	}
	
	/**
	 * Gets the value from option field type
	 * @package Skip
	 * @since 1.0
	 * @return string $value The Value of the field
	 */
	private final function option_value(){
		return get_option( $this->option_name );
	}
	
	/**
	 * Gets the value from post field type
	 * @package Skip
	 * @since 1.0
	 * @return string $value The Value of the field
	 */
	private final function post_type_value(){
		
	}
	
	/**
	 * Gets the value from taxonomy field type
	 * @package Skip
	 * @since 1.0
	 * @return string $value The Value of the field
	 */
	private final function taxonmy_value(){
		
	}
	
	/**
	 * Gets the name of the element in options table
	 * @package Skip
	 * @since 1.0
	 * @return string $option_name The Name of the element
	 */
	private final function option_name(){
		
		// Normal field
		if( '' == $this->array ):
			$option_name = $this->form_name . '_' . $this->name;
		// Multidimensional Array field
		elseif( is_array( $this->array ) ):
			$option_name = $this->form_name . '_' . $this->name;
	
			foreach ( $this->array as $key )
				$option_name .= SKIP_DELIMITER . $key;
			
		// Array field
		else:
			$option_name = $this->form_name . '_' . $this->name . SKIP_DELIMITER . $this->array;
		endif;
				
		return 'skip_framework_value_' . $option_name;
	}
	
	/**
	 * Gets the HTML name of the element
	 * @package Skip
	 * @since 1.0
	 * @return string $field_name The HTML name of the element
	 */
	private final function field_name(){
		
		// Normal field
		if( '' == $this->array ):
			$field_name = $this->form_name . '_value[' . $this->name . ']';
		// Multidimensional Array field
		elseif( is_array( $this->array ) ):
			$field_name = $this->form_name . '_value[' . $this->name . ']';
	
			foreach ( $this->array as $key )
				$field_name .= '[' . $key . ']';
		// Array field	
		else:
			$field_name = $this->form_name . '_value[' . $this->name . '][' . $this->array . ']';
		endif;
		
		return $field_name;
	}

	/**
	 * Gets the posted value From an Element
	 * @package Skip
	 * @since 1.0
	 * @return string $value The value of the element which was posted by a form
	 */
	private final function posted_value(){
		$fields = $_POST[ $this->form_name . '_value' ];
		
		if( !array_key_exists( $this->name, $fields ) )
			return;
		
		$field = $fields[ $this->name ];
		
		// Normal field
		if( !$this->array )
			return $field;
		
		// Multidimensional Array field
		elseif( is_array( $this->array ) )
			return $this->array_value( $field, $this->array );
		
		// Array field
		else
			return $field[ $this->array ];
	}

	/**
	 * Helper function to get an array value from a multidimesional Array
	 * @package Skip
	 * @since 1.0
	 * @return string $value The value of the array Element
	 */
	private final function array_value( $value, $array, $i = 0 ){
		if(  $i < count( $array ) )
			return $this->array_value( $value[ $array[ $i ] ], $array, ++$i );
		else
			return $value;
	}
	
	/**
	 * Saveing posted Values
	 * @package Skip
	 * @since 1.0
	 */
	protected function save(){
		global $skip_form_name, $skip_form_save_fields;
		$skip_form_save_fields[ $skip_form_name ][] = array( 'name' => $this->name, 'option_name' => $this->option_name, 'array' => $this->array ); // Saving field names for later saving
	}
	
	/**
	 * Rendering element
	 * @package Skip
	 * @since 1.0
	 * @return string $content The content of the element as HTML
	 */
	public function render(){
		if( count( $this->error_msgs ) > 0 )
			foreach( $this->error_msgs AS $message )
				$this->after( '<div class="skip_error ui-state-error ui-corner-all"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>' . $message . '</p></div>' );
		
		return parent::render();
	}
}