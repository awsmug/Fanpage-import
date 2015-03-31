<?php
/**
 * Skip Textarea class
 * @package Skip
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Textarea extends Form_Element{		/**	 * Constructor	 * @since 1.0	 * @param string $name Name of field.
 	 * @param array/string $args List of Arguments.	 
	 */	function __construct( $name, $label = FALSE, $args = array() ){
		$defaults = array(
			'rows' => '',
			'cols' => '',
			'description' => '',
			'default' => '',
			'disabled' => FALSE
		);
		
		$args = wp_parse_args( $args, $defaults );
		$args[ 'label' ] = $label;
		$args[ 'close_tag' ] = TRUE; // No Close tag for Input type Text				parent::__construct( 'textarea', $name, $args );
		
		if( '' != $args[ 'rows' ] )
			$this->add_param( 'rows', $args[ 'rows' ] );
		
		if( '' != $args[ 'cols' ] )
			$this->add_param( 'cols',  $args[ 'cols' ] );
		
		if( '' != $this->value )
			$this->add_element( $this->value );
		
		$this->del_param( 'value' ); // Not needed here
	}}
/**
 * Textarea getter Function
 * @see skip_textarea()
 * @ignore
 */
function get_textarea( $name, $label = FALSE, $args = array() ){
	$textarea = new Textarea( $name, $label, $args );
	return $textarea->render();
} 
/**
 * <pre>skip_textarea( $name, $args );</pre>
 * 
 * Adding a Textarea.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_textarea( 'mytext' );
 * </code>
 * This will create an automated saved textarea field.
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
 * 	<li>cols (int) Number of columns of the Textarea.</li>
 * 	<li>rows (int) Number of rows of the Textarea.</li>
 * 	<li>classes (string) Name of CSS Classes which will be inserted into HTML seperated by empty space.</li>
 * 	<li>before_element (string) Content before the element.</li>
 *	<li>after_element (string) Content after the element.</li>
 * </ul>
 * 
 * <b>Example</b>
 * 
 * Creating a labeled Textarea with 10 rows in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * skip_textarea( 'mytext', 'label=Name&rows=10' );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $value = skip_value( 'mytext', 'name' );
 * </code>
 *
 * @package Skip\Forms
 * @since 1.0
 * @see function skip_value(), function skip_values()
 * @param string $name Name of Textarea field.
 * @param array/string $args List of Arguments.
 */
function textarea( $name, $label = FALSE, $args = array() ){	echo get_textarea( $name, $label, $args );}