<?php 
/**
 * Skip Select Class
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Select extends Form_Element{
	/**
	 * Constructor
	 * @since 1.0
	 * @param string $name Name of Color field.
	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $label = FALSE, $args = array() ){
		$defaults = array(
			'size' => '',
			'multiselect' => FALSE,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$args[ 'label' ] = $label;
		$args[ 'close_tag' ] = TRUE;
		
		parent::__construct( 'select', $name, $args );
		
		if( '' != $args[ 'size' ] )
			$this->add_param( 'size', $args[ 'size' ] );
		
		if( $args[ 'multiselect' ] )
			$this->add_param( 'multiple', 'multiple' );
	}
	
	/**
	 * Adding element
	 * 
	 * Adds an option to the select field.
	 * 
	 * <code>
	 * $element = array(
	 * 	'label' => 'Option 1',
	 * 	'value' => 1
	 * );
	 * </code>
	 * Example for an element which can be added. 
	 * 
	 * @since 1.0
	 * @param array/string $element The element
	 */
	public function add_element( $element = array() ){
		$defaults = array(
			'id' => id(),
			'value' => '',
			'label' => $element['value'],
			'disabled' => FALSE,
			'params' => array()
		);
		
		$element = wp_parse_args( $element, $defaults );
		
		if( !$element['disabled'] )
			unset( $element['disabled'] );
		
		parent::add_element( $element );
	}
	
	/**
	 * Rendering Select field
	 * @package Skip
	 * @since 1.0
	 * @return string $html Returns The HTML Code.
	 */	
	public function render(){
		$this->content = '';
		
		foreach( $this->elements AS $element ):
			$params = $element['params'];
			unset( $element['params'] );
			
			$label = $element['label'];
			unset( $element['label'] );
			
			$params = array_merge( $element, $params );
			
			if( $params[ 'value' ] == $this->value )
				$params[ 'selected' ] = 'selected';
			
			$this->content.= '<option' . $this->params( $params ) . '>' . $label . '</option>';
		endforeach;
		
		return parent::render();
	}		
}

/**
 * Select getter Function
 * @see skip_select()
 * @ignore
 */
function get_select( $name, $elements, $label = FALSE, $args = array(), $return = 'html' ){
	$select = new Select( $name, $label, $args );
	
	if( count( $elements ) > 0 ):
		if( is_array( $elements ) ):
			foreach ( $elements AS $element )
				$select->add_element( $element );
		else:
			$values = explode( ',', $elements );
			foreach ( $values AS $value )
				$select->add_element( array( 'value' => $value ) );
		endif;
	endif;
		
	return $select->render();
} 
/**
 * <pre>skip_select( $name, $elements, $args );</pre>
 * 
 * Adding a select field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_select( 'myselect', '1,2,3,4,5,6,7,8,9,10' );
 * </code>
 * This will create an automated saved select field with values 1 till 10.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $name // (string) (required) The name of the field.
 * $elements // (array/string) (required) The elements to show in Select.
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
 * Creating a select field with values Opion 1 till 3 in a form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $elements = array(
 * 	array( 'label' => 'Option 1', 'value' => '1' ),
 *  	array( 'label' => 'Option 2', 'value' => '2' ),
 * 	array( 'label' => 'Option 3', 'value' => '3' ),
 * );
 * 
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'Option'
 * );
 * skip_select( 'myselect', $elements, $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $value = skip_value( 'myformname', 'myselect' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name The name of the checkbox field.
 * @param array/string $elements The elements to show in Select.
 * @param array/string $args List of Arguments.
 */
function select( $name, $elements, $label = FALSE, $args = array() ){
	echo get_select( $name, $elements, $label, $args );
}