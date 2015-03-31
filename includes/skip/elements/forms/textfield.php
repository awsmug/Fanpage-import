<?php
/**
 * Skip Textfield Class
 * @package Skip
 * @since 1.0.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Textfield extends Form_Element{
	
	/**
	 * Constructor
	 * @since 1.0.0
	 * @param string $name Name of field.
 	 * @param string $label Label of field.
 	 * @param string $description Description of field which is shown under element
	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $label = FALSE, $args = array() ){
		$defaults = array(
			'description' => '',
			'default' => '',
			'disabled' => FALSE
		);
		$args = wp_parse_args( $args, $defaults );
		$args[ 'label' ] = $label;
		
		parent::__construct( 'input', $name, $args );
		
		$this->add_param( 'type', 'text' ); // This is a text field!
	}
}
/**
 * Textfield getter Function
 * @since 1.0.0
 * @param string $name Name of field.
 * @param string $label Label of field.
 * @param string $description Description of field which is shown under element
 * @see textfield()
 * @ignore
 */
function get_textfield( $name, $label = FALSE, $description = FALSE  ){
	$textfield = new Textfield( $name, $label, $description );
	return $textfield->render();
}

/**
 * <pre>skip_text( $name, $args );</pre>
 * 
 * Adding a Textfield.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip\textfield( 'name' );
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
 * skip\form_start( 'myformname' );
 * skip\textfield( 'name', 'labelname' );
 * skip\form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $value= skip\value( 'myformname', 'name' );
 * </code>
 *
 * @package Skip\Forms
 * @since 1.0.0
 * @param string $name Name of field.
 * @param string $label Label of field.
 * @param string $description Description of field which is shown under element
 * @see get_textfield()
 */
function textfield( $name, $label = FALSE, $description = FALSE ){
	echo get_textfield( $name, $label, $description );
}