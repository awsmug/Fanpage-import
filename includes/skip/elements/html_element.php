<?php
/**
 * Skip HTML element
 * @package Skip
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;
 
class HTML_Element extends Element{
	
	var $tag;
	var $close_tag;
	var $echo_tag;
	var $content;
	
	var $params = array();
		
	/**
	 * Skip constructor
	 * @package Skip
	 * @since 1.0
	 */
	public function __construct( $tag, $args = array() ){
		
		$defaults = array(
			'id' => id(),
			'classes' => '',
			'params' => array(),
			'close_tag' => FALSE,
			'echo_tag' => TRUE
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );
		
		parent::__construct( $args );
		
		$this->tag = $tag;
		
		if( $close_tag === TRUE )
			$this->close_tag = TRUE;
		
		if( $echo_tag === TRUE )
			$this->echo_tag = TRUE;
		
		if( '' != $classes )
			$this->add_param( 'class', $classes );
		
		if( '' != $id )
			$this->add_param( 'id', $id );
		
		$this->add_params( $params );
	}
	
	/**
	 * Adding parameter to HTML Element
	 * 
	 * @param string $name Name of the parameter
	 * @param string $value Value of the parameter
	 * @package Skip
	 * @since 1.0
	 * @return string The content of the element
	 */
	public function add_param( $name, $value ){
		$this->params[ $name ] = $value;
	}
	
	/**
	 * Adding parameters in an array
	 * 
	 * @param array $params an array of parameters with name as key and value
	 * @package Skip
	 * @since 1.0
	 * @return string The content of the element
	 */
	public function add_params( $params = array() ){
		if( 0 != count( $params ) ):
			foreach( $params AS $name => $value ):
				if( '' != $key && '' != $param )
					$this->add_param( $name, $value);
			endforeach;
		endif;
	}
	
	/**
	 * Deleting parameter in an array
	 * 
	 * @param string $name Name of the parameter to delete
	 * @package Skip
	 * @since 1.0
	 * @return string The content of the element
	 */
	public function del_param( $name ){
		unset( $this->params[ $name ] );
	}
	
	/**
	 * Render the content
	 *
	 * @package Skip
	 * @since 1.0
	 * @param string $content Content which will be added if close_tag is TRUE
	 * @return string The content of the element
	 */
	public function render(){
		if( $this->close_tag ):
			
			if( count( $this->elements ) > 0 && '' == $this->content )
				foreach( $this->elements AS $element )
					$this->content.= display( $element );
				
			if( $this->echo_tag )
				$html = '<' . $this->tag . $this->params() . '>' . $this->content  . '</' . $this->tag . '>' . chr(13);
			else
				$html = $content;
		else:
			if( $this->echo_tag )
				$html = '<' . $this->tag . $this->params() . ' />' . chr(13);
			else
				$html = $this->content;
		endif;
		
		$html_element = $this->before_element . $html . $this->after_element;
		return $html_element;
	}
	
	public function content( $content ){
		$this->content = $content;
	}
	
	/**
	 * Returns the params
	 *
	 * @param array $params Parameters as array like $params[][ 'name' ] = VALUE
	 * @package Skip
	 * @since 1.0
	 * @return string The content of the element
	 */
	public function params( $params = FALSE ){
		$params_return = '';
		
		if( FALSE === $params )
			$params = $this->params;
		
		if( count( $params ) > 0 )
			foreach( $params AS $param_name => $param )
				$params_return.= ' ' . $param_name . '="' . $param . '"';
			
		return $params_return;
	}
}
/**
 * @ignore
 */
function skip_html_element( $tag, $args = array(), $elements = array(), $return = 'html' ){
	$html_element = new HTML_Element( $tag, $args );
	
	if( count( $elements ) > 0 )
		foreach ( $elements AS $element )
			$html_element->add_element( $element );
		
	return element_return( $html_element, $return );
}
