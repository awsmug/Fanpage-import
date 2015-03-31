<?php
/**
 * Skip Element
 * @package Skip
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Element{
	
	var $before_element;
	var $after_element;
	
	var $elements = array();
	
	public function __construct( $args = array() ){
		$defaults = array(
			'before_element' => '',
			'after_element' => '',
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );
		
		$this->before_element = $before_element;
		$this->after_element = $after_element;
	}
	
	public function before( $content ){
		$this->before_element.= $content;
	}
	
	public function after( $content ){
		$this->after_element.= $content;
	}
	
	public function add_element( $element ){
		$this->elements[] = $element;
	}
	
	public function render(){
		foreach( $this->elements AS $element )
			$content.= $element;
		
		$content = $this->before_content . $content . $this->after_element;
		return $content;
	}
}

/**
 * Helping function for handling element objects
 *
 * @param object $object The object to handle
 * @param string $return How to return 'echo', 'object' or 'html'
 * @package Skip
 * @since 1.0
 * @ignore
 */
function element_return( $object, $return = 'html' ){
	return $object->render();
}
