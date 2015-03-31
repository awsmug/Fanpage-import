<?php
/**
 * Skip Radiobutton class
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Radio extends Form_Element{		/**	 * Constructor	 * @since 1.0	 * @param string $name Name of field.
	 * @param string $value The value of the checkbox element which will be saved if Radiobutton is checked.
	 * @param array/string $args List of Arguments.
	 */	function __construct( $name, $value, $label = FALSE, $args = array() ){
		/*
		 * Additional parent args:
		 * 'id'
		 * 'classes'
		 * 'before_element'
		 * 'after_element'
		 * 'params'
		 */
		$defaults = array(
			'checked' => FALSE,
		);
		
		$args = wp_parse_args( $args, $defaults );
		$args[ 'label' ] = $label;
		$args[ 'close_tag' ] = FALSE; // No Close tag for Input type Button
		
		parent::__construct( 'input', $name, $args );
		
		$this->add_param( 'type', 'radio' ); // This is a radio button
		$this->add_param( 'value', $value ); // Overwriting value from DB
		
		if( $value == $this->value || $this->value == 'checked' || $args[ 'checked' ] )
			$this->add_param( 'checked', 'checked' );
		
	}}
/**
 * Radiobutton getter Function
 * @see skip_radio()
 * @ignore
 */
function get_radio( $name, $value, $label = FALSE, $args = array(), $return = 'html' ){
	$radio = new Radio( $name, $value, $label, $args );
	return $radio->render();
}
 
/**
 * <pre>skip_radio( $name, $value, $args )</pre>
 * 
 * Adding a Radiobutton field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_radio( 'value', '1' );
 * </code>
 * This will create an automated saved checkbox field.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $name // (string) (required) The name of the field.
 * $value // (string) (required) The value of the checkbox element which will be saved if Radiobutton is checked.
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
 * skip_radio( 'myradiovalue', 'red', 'label=Red' );
 * skip_radio( 'myradiovalue', 'green', 'label=Green' );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $value = skip_value( 'myformname', 'myradiovalue' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name Name of field.
 * @param string $value The value of the checkbox element which will be saved if Radiobutton is checked.
 * @param array/string $args List of Arguments.
 */
function radio( $name, $value, $label = FALSE, $args = array() ){	echo get_radio( $name, $value, $label, $args );}