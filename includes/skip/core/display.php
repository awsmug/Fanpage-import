<?php
/**
 * Skip Display
 *
 * This class displays Skip Content and renders it.
 *
 * @package Skip
 * @since 1.0
 * @ignore
 */

namespace skip\v1_0_0;
 
class Display{
	
	var $display;

	/**
	 * Constructor
	 * @since 1.0
	 */
	function __construct( $display = '' ){
		$this->display = $display;
	}
	
	/**
	 * Diplay Skip Content
	 * @since 1.0
	 * @param mixed $elements Array / Object of elements which have to be shown
	 * @return string $html The HTML of the display object
	 */	
	function render( $elements = '', $hide = FALSE ) {
		$html = '';
		
		// If element is no array and no object
		if( !is_array( $elements ) && !is_object( $elements ) ){
			// If internal display var is there, use it
			if( is_object( $this->display ) || is_array( $this->display ) ){
				$elements = $this->display;		
			}
		}
		
		// If it's array, run all elements 
		if( is_array( $elements ) ){
			foreach ( $elements AS $element ){
				
				// If subelement is an array
				if( is_array( $element ) ){
					 $html.= $this->render( $element, $hide );
					
				// If it's an object
				}elseif( is_object( $element ) ){
					 $html.= $element->render( $hide_element );
					 
				// It's anything else
				}else{
					if( !$hide )
					 	$html.= $element;
				}
			}
			return $html;
		
		// Objects have to give back their html
		
		// Return the waste! ;)
		}else{
			return $elements;
		}
	}
}
/**
 * Rendering Content
 * 
 * This function will be used for subelements, arrays and skip objects in skip elements.
 *
 * @param mixed $content Content to render
 * @param boolean $hide Hiding content
 * @package Skip
 * @since 1.0
 * @return string The content of the element
 * @ignore
 */
function display( $content, $hide = FALSE ){
	$display = new Display();
	return $display->render( $content, $hide );
}
