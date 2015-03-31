<?php
/**
 * Skip Checkbox class
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Checkbox extends Form_Element{
	
	/**
	 * Constructor
	 * @since 1.0
	 * @param string $name The name of the checkbox field.
	 * @param string $value The value of the checkbox element which will be saved if box is checked.
	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $value, $label = FALSE, $args = array() ){
		global $skip_hidden_elements;

		$defaults = array(
			'checked' => FALSE,
		);
		$args = wp_parse_args( $args, $defaults );
		$args[ 'label' ] = $label;
		$args[ 'close_tag' ] = FALSE; // No Close tag for Input type Button

		parent::__construct( 'input', $name, $args );
		
		$this->add_param( 'type', 'checkbox' );
		$this->add_param( 'value', $value ); // Overwriting value from DB
		
		if( '' != $this->value || $this->value == 'checked' || $args[ 'checked' ]  )
			$this->add_param( 'checked', 'checked' );
		
	}		
}
/**
 * Checkbox getter Function
 * @see checkbox()
 * @ignore
 */
function get_checkbox( $name, $value, $label = FALSE, $args = array() ){
	$checkbox = new Checkbox( $name, $value, $label, $args  );
	return $checkbox->render();
}

/**
 * <pre>skip_checkbox( $name, $value, $elements, $args );</pre>
 * 
 * Adding a checkbox field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_checkbox( 'overeighteen', 'yes' );
 * </code>
 * This will create an automated saved checkbox field.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $name // (string) (required) The name of the field.
 * $value // (string) (required) The value of the checkbox element which will be saved if box is checked.
 * $args // (array/string) (optional) Values for further settings.
 * </code>
 * 
 * <b>$args Settings</b>
 * 
 * <ul>
 * 	<li>id (string) ID if the HTML Element.</li> 
 * 	<li>label  (string) Label for Element.</li> 
 * 	<li>default (string) Default Value if no Value is set before ('checked' if value have to be checked on default).</li>
 * 	<li>classes (string) Name of CSS Classes which will be inserted into HTML seperated by empty space.</li>
 * 	<li>before_element (string) Content before the element.</li>
 *	<li>after_element (string) Content after the element.</li>
 * 	<li>save (boolean) TRUE if value of field have to be saved in Database, FALSE if not (default TRUE).</li>
 * </ul>
 * 
 * <b>Example</b>
 * 
 * Creating a labeled Checkbox in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'City'
 * );
 * skip_autocomplete( 'overeighteen', 'yes', $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $city = skip_value( 'myformname', 'overeighteen' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name The name of the checkbox field.
 * @param string $value The value of the checkbox element which will be saved if box is checked.
 * @param array/string $args List of Arguments.
 */
function checkbox( $name, $value, $label = FALSE, $args = array() ){
	echo get_checkbox( $name, $value, $label, $args );
}