<?php
/**
 * Tabs Class
 * 
 * Creates jQuery UI Tabs HTML.
 * 
 * @package Skip\Animated
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Tabs extends HTML_Element{
	
	/**
	 * Tabs constructor
	 * @param array $args Array of [ 'id' ], [ 'classes' ] and [ 'params' ]
	 * @package Skip
	 * @since 1.0
	 */
	function __construct( $args = array() ){
		/*
		 * Additional parent args:
		 * 'id'
		 * 'classes'
		 * 'before_element'
		 * 'after_element'
		 * 'params'
		 */
		parent::__construct( 'div', $args );
	}
	
	/**
	 * Adding tab
	 *
	 * @package Skip
	 * @since 1.0
	 * 
	 * @param string $title Title of the tab
	 * @param string $content Content which appears in the tab
	 * @param array $args Array of [ 'id' ], [ 'classes' ], [ 'params_title' ] and [ 'params_content' ]
	 * 
	 */
	public function add_element( $title, $content, $args = array() ){
		$defaults = array(
			'id' => id(),
			'classes' => '',
			'params_title' => array(),
			'params_content' => array()
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );
		
		$element = array(
			'id' => $id,
			'title' => $title,
			'content' => $content, 
			'classes'=> $classes,
			'params_title' => $params_title,
			'params_content' => $params_content
		);
		
		parent::add_element( $element );
	}
	
	/**
	 * Getting the tabs html
	 *
	 * @package Skip
	 * @since 1.0
	 * 
	 * @return string $html The tabs as html
	 * 
	 */
	public function render(){
		global $skip_javascripts;
		
		// Creating elements
		$skip_javascripts[] = '
			var cookieName_' . $this->params[ 'id' ] . ' = "stickyTabs_' . $this->params[ 'id' ] . '";
			
			$( "#' . $this->params[ 'id' ] . '" ).tabs({
				selected: ( $.cookies.get( cookieName_' . $this->params[ 'id' ] . ' ) || 0 ),
				show: function( event, ui ) {
					$.cookies.set( cookieName_' . $this->params[ 'id' ] . ', $( "#' . $this->params[ 'id' ] . '" ).tabs( "option", "selected" ) );
				}
			});';
		
		$html = '<' . $this->tag . $this->params() . '>';
		
		$html.= '<ul>';
		// Creting navigation elements
		foreach( $this->elements AS $element ):
				// Show tab
				$html.= '<li' . $this->params( $element[ 'params_title' ] ) . '><a href="#' . $element['id'] . '" >';
				$html.= display( $element['title'] );
				$html.= '</a></li>';
		endforeach;
		$html.= '</ul>';
		
		// Creting content elements
		foreach( $this->elements AS $element ):
			
			// Show tab content
			$html.= '<div' . $this->params( $element[ 'params_content' ] ) . ' id="' . $element['id'] . '">';
			$html.= display( $element['content'] );
			$html.= '</div>';
		endforeach;
		
		$html.= '</' . $this->tag . '>';
		
		return $html;
	}
}
/**
 * Tabs getter Function
 * @see skip_tabs()
 * @ignore
 */
function get_tabs( $elements, $args = array(), $return = 'html' ){	
	$tabs = new	Tabs( $args );	
	
	if( count( $elements ) > 0 )
		foreach ( $elements AS $element )
			$tabs->add_element( $element['title'], $element['content'], array_key_exists( 'args', $element ) ? $element['args'] : array() );
	else
		return FALSE;
		
	return element_return( $tabs, $return );	
}

/**
 * Tabs
 * 
 * Creating jQuery UI Tabs
 * 
 * <b>Usage:</b>
 * <code>
 * $elements = array(
 * 	array(
 * 		'title' => 'Tab 1 title',
 * 		'content' => 'Tab 1 content is here.'
 *		),
 * 	array(
 * 		'title' => 'Tab 2 title',
 * 		'content' => 'Tab 2 content is here.'
 *		)
 * );
 * skip_tabs( $elements, $args ); // echo tabs
 * 
 * // If you don't want to print out tabs and get back HTML use the get function
 * $tabs_html = get_skip_tabs( $elements, $args );
 * </code>
 *
 * @package Skip\Animated
 * @since 1.0
 * 
 * @param array $elements Elements array structured [ 'title' ], [ 'content' ] and [ 'args' ]
 * @param array $args Args array of [ 'id' ], [ 'classes' ], [ 'params' ]
 * @param string $return How to return 'echo', 'object' or 'html'
 */
function tabs( $elements, $args = array() ){	
	get_tabs( $elements, $args, 'echo' );
}

?>