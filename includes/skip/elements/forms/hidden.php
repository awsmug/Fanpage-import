<?php
/**
 * Skip Hidden Field class
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Hidden extends Form_Element{
	/**
	 * Constructor
	 * @since 1.0
	 * @param string $name The name of field.
 	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $args = array() ){
		global $post, $skip_form_option_group;
		
		$args = wp_parse_args( $args );
		extract( $args , EXTR_SKIP );
		
		$args[ 'close_tag' ] = FALSE; // No Close tag for Input type Text
		parent::__construct( 'input', $name, $args );
		
		$this->add_param( 'type', 'hidden' ); // This is a text field!
	}
}
/**
 * Hidden Field getter Function
 * @see skip_hidden()
 * @ignore
 */
function get_hidden( $name, $args = array(), $return = 'html' ){
	$hidden = new Hidden( $name, $args );
	return element_return( $hidden, $return );
}
/**
 * <pre>skip_hidden( $name, $args )</pre>
 * 
 * Adding a Hidden field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_hidden( 'myhiddenfield' );
 * </code>
 * This will create an automated saved hidden field.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $name // (string) (required) The name of the field.
 * $args // (array) (optional) Values for further settings.
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
 * 	<li>save (boolean) TRUE if value of field have to be saved in Database, FALSE if not (default TRUE).</li>
 * </ul>
 * 
 * <b>Example</b>
 * 
 * Creating a hidden field in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'Content'
 * );
 * skip_editor( 'myhiddenfield', $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $value = skip_value( 'myformname', 'myhiddenfield' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name Name of field.
 * @param array/string $args List of Arguments.
 */
function hidden( $name, $args = array() ){
	echo get_hidden( $name, $args, 'echo' );
}