<?php
/**
 * Skip Textfield Class
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Text extends Form_Element{
	
	/**
	 * Constructor
	 * @since 1.0
	 * @param string $name Name of field.
	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $args = array() ){
		$args = wp_parse_args( $args );
		
		$args[ 'close_tag' ] = FALSE; // No Close tag for Input type Text
		parent::__construct( 'input', $name, $args );
		
		$this->add_param( 'type', 'text' ); // This is a text field!
	}
}
/**
 * Textfield getter Function
 * @see skip_text()
 * @ignore
 */
function get_text( $name, $args = array(), $return = 'html' ){
	$textfield = new Text( $name, $args );
	return element_return( $textfield, $return );
}

/**
 * <pre>skip_text( $name, $args );</pre>
 * 
 * Adding a Textfield.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_text( 'name' );
 * </code>
 * This will create an automated saved textfield.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $name // (string) (required) The name of the field.
 * $args // (array/string) (optional) Values for further settings.
 * </code>
 * 
 * <b>$args Settings</b>
 * 
 * <ul>
 * 	<li>id (string) ID if the HTML Element.</li> 
 * 	<li>label  (string) Label for Element.</li> 
 * 	<li>default (string) Default Value if no Value is set before.</li>
 * 	<li>classes (string) Name of CSS Classes which will be inserted into HTML seperated by empty space.</li>
 * 	<li>before_element (string) Content before the element.</li>
 *	<li>after_element (string) Content after the element.</li>
 * </ul>
 * 
 * <b>Example</b>
 * 
 * Creating a labeled Textfield in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * skip_text( 'name', 'label=Name' );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $city = skip_value( 'myformname', 'name' );
 * </code>
 *
 * @package Skip\Forms
 * @since 1.0
 * @see function skip_value(), function skip_values()
 * @param string $name Name of Autocomplete field.
 * @param array/string $args List of Arguments.
 */
function text( $name, $args = array() ){
	get_text( $name, $args, 'echo' );
}