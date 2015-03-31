<?php
/**
 * Skip Editor Class
 * @package Skip
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Editor extends Form_Element{
	var $buffer;
	
	/**
	 * Constructor
	 * @since 1.0
	 * @param string $name Name of field.
 	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $label = FALSE, $args = array() ){
		$args = wp_parse_args( $args );
		$args[ 'label' ] = $label;
		$args[ 'echo_tag' ] = FALSE;
		
		parent::__construct( 'textarea', $name, $args );
	}
	
	/**
	 * Rendering Editor field
	 * @package Skip
	 * @since 1.0
	 * @return string $html Returns The HTML Code.
	 */	
	public function render(){
		global $skip_used_dialog;
		
		$skip_used_dialog = TRUE;
		
		if( array_key_exists( 'class', $this->params ) )
			$class = $this->params[ 'class' ] . ' skip_editor';
		else 
			$class = 'skip_editor';
		
		$settings = array(
			'textarea_name' => $this->field_name,
			'editor_class' => $class
		);
		
		ob_start( array( $this, 'buffer' ) );
		wp_editor( stripslashes( $this->value ), $this->params['id'], $settings );
		ob_end_flush();
		
		$this->content = $this->buffer ;
		
		return parent::render();
	}
	
	function buffer( $content ){
		$this->buffer = $content;
	}
}
/**
 * Editor getter Function
 * @see skip_editor()
 * @ignore
 */
function get_editor( $name, $label = FALSE, $args = array() ){
	$textarea = new Editor( $name, $label, $args );
	return $textarea->render();
} 
/**
 * <pre>skip_editor( $name, $args )</pre>
 * 
 * Adding a WP Editor field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_editor( 'mycontent' );
 * </code>
 * This will create an automated saved editor field.
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
 * 	<li>save (boolean) TRUE if value of field have to be saved in Database, FALSE if not (default TRUE).</li>
 * </ul>
 * 
 * <b>Example</b>
 * 
 * Creating a labeled Editor field in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'Content'
 * );
 * skip_editor( 'mycontent', $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $city = skip_value( 'myformname', 'mycontent' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name Name of Editor field.
 * @param array/string $args List of Arguments.
 */
function editor( $name, $label = FALSE, $args = array() ){
	echo get_editor( $name, $label, $args );
}