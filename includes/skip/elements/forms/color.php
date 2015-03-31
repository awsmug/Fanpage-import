<?php
/**
 * Skip Color Field Class
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;
 
class Color extends Textfield{		/**	 * Constructor	 * @package Skip	 * @since 1.0	 * @param string $name Name of Color field.
	 * @param array/string $args List of Arguments.
	 */	function __construct( $name, $label = FALSE, $args = array() ){
		/*
		 * Additional parent args:
		 * 'id'
		 * 'classes'
		 * 'before_element'
		 * 'after_element'
		 * 'params'
		 * 'default_value'
		 */
		parent::__construct( $name, $label, $args );
	}
	
	/**
	 * Rendering Color field
	 * @package Skip
	 * @since 1.0
	 * @return string $html Returns The HTML Code.
	 */	
	public function render(){
		global $skip_javascripts;
		
		$skip_javascripts[] = '
			$("#' . $this->params[ 'id' ] . '").colorpicker({
				altField: "#' . $this->params[ 'id' ] . '",
				altProperties: "background-color",
			});
		';
		
	  	return parent::render();
	}}
/**
 * Color field getter Function
 * @see skip_color()
 * @ignore
 */
function get_color( $name, $label = FALSE, $args = array() ){
	$color = new Color( $name, $label, $args );
	return $color->render();
}
/**
 * <pre>skip_color( $name, $args = array() );</pre>
 *
 * Adding a jQuery UI color field.
 * 
* <b>Default Usage</b>
 * <code>
 * skip_color( 'backgroundcolor' );
 * </code>
 * This will create an automated saved field with a jQueryui Colorpicker.
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
 * Creating a labeled Colorfield in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'Background Color'
 * );
 * skip_color( 'backgroundcolor', $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $city = skip_value( 'myformname', 'backgroundcolor' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name Name of Color field.
 * @param array/string $args List of Arguments.
 */function color( $name, $label = FALSE, $args = array() ){
	echo get_color( $name, $label, $args );}