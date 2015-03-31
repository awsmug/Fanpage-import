<?php
/**
 * Skip Autocomplete Class
 * @package Skip/Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Autocomplete extends Textfield{
	/**
	 * Constructor
	 * @since 1.0
	 * @param string $name Name of Autocomplete field.
	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $label = FALSE, $args = array() ){
		parent::__construct( $name, $label, $args );
	}
	
	/**
	 * Rendering Field
	 * @since 1.0
	 * @return string $html Returns The HTML Code.
	 */	
	public function render(){
		global $skip_javascripts;

		$autocomplete_values = array();
		foreach( $this->elements AS $key => $value )
			array_push( $autocomplete_values, '"' .  $value . '"' );
		
		$values = implode( ',', $autocomplete_values );
		
		// If there are any autocomplete values show JS
		if( count( $autocomplete_values ) > 0 ):
			$skip_javascripts[] = '$("#' . $this->params[ 'id' ] . '").autocomplete({ source: [' . $values . '] });';
		endif;
		
	  	return parent::render();
	}
}
/**
 * Autocomplete getter Function
 * @see skip_autocomplete()
 * @ignore
 */
function get_autocomplete( $name, $elements, $label = FALSE, $args = array() , $return = 'html' ){
	$autocomplete = new Autocomplete( $name, $label, $args );
	
	if( is_array( $elements ) ):
		foreach ( $elements AS $element )
			$autocomplete->add_element( $element );
	else:
		$values = explode( ',', $elements );
		
		foreach ( $values AS $value )
			$autocomplete->add_element( $value );
	endif;
	
	return $autocomplete->render();
}
/**
 * <pre>skip_autocomplete( $name, $elements, $args );</pre>
 * 
 * Adding a jQuery UI autocomplete field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_autocomplete( 'city', 'Berlin,London,New York,Paris,Tokyo' );
 * </code>
 * This will create an automated saved field with Autocomplete values Berlin, London, New York, Paris and Tokyo.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $name // (string) (required) The name of the field.
 * $elements // (array/comma separated string) (required) The elements for the autocompleting values.
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
 * Creating a labeled Textfield with a set cities as autocomplete Values in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $cities = array( 'Berlin', 'London', 'New York', 'Paris', 'Tokyo' );
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'City'
 * );
 * skip_autocomplete( 'city', $cities, $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $city = skip_value( 'myformname', 'city' );
 * </code>
 *
 * @package Skip\Forms
 * @since 1.0
 * @see function skip_value(), function skip_values()
 * @param string $name Name of Autocomplete field.
 * @param array/string $elements List of Elements.
 * @param array/string $args List of Arguments.
 */
function autocomplete( $name, $elements, $label = FALSE, $args = array() ){
	echo get_autocomplete( $name, $elements, $label, $args );
}
