<?php
/**
 * Accordion Class
 * 
 * Inserts a jQuery UI Accordion and all needed JS.
 * 
 * @package Skip\Animated
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class Accordion extends HTML_Element{
	
	var $title_tag;
	var $content_tag;
	
	/**
	 * Accordion constructor
	 * @param array $args Optional
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
		$defaults = array(
			'tag' => 'div',
			'title_tag' => 'h3',
			'content_tag' => 'div'
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );
		
		parent::__construct( $tag, $args );
		
		$this->title_tag = $title_tag;
		$this->content_tag = $content_tag;
	}
	
	/**
	 * Adding section to accordion
	 *
	 * @package Skip
	 * @since 1.0
	 * 
	 * @param string $title Title of the tab
	 * @param string $content Content in the tab
	 * @param array $args Array of [ 'id' ], [ 'classes' ], [ 'params_title' ] and [ 'params_content' ]
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
			'title' => $title,
			'content' => $content, 
			'id' => $id, 
			'classes'=> $classes,
			'params_title' => $params_title,
			'params_content' => $params_content
		);
		
		parent::add_element( $element );
	}

	/**
	 * Getting the accordion html
	 *
	 * @package Skip
	 * @since 1.0
	 * 
	 * @return string $html The accordion as html
	 */
	public function render( $hide = FALSE ){
		global $skip_javascripts;
		
		$skip_javascripts[] = '
			var cookieName_' . $this->params[ 'id' ] . ' = "stickyAccordion_' . $this->params[ 'id' ] . '";
			
			$( "#' .  $this->params[ 'id' ] . '" ).accordion({
				header: "' . $this->title_tag . '", 
				autoHeight: false, 
				collapsible:true,
				active: ( $.cookies.get( cookieName_' . $this->params[ 'id' ] . ' ) || 0 ),
				change: function( e, ui )
				{
					$.cookies.set( cookieName_' . $this->params[ 'id' ] . ', $( this ).find( "' . $this->title_tag . '" ).index ( ui.newHeader[0] ) );
				}
			});';
			
		$html = '<' . $this->tag . $this->params() . '>';
		
		foreach( $this->elements AS $element ):

			/*
			 * Title
			 */
			$html.= '<' . $this->title_tag . $this->params( $element[ 'params_title' ] ) . '>';
			$html.= '<a href="#">';
			$html.= display( $element['title'] );
			$html.= '</a>';
			$html.= '</' . $this->title_tag . '>';
			
			/*
			 * Content
			 */
			$html.= '<' . $this->content_tag . $this->params( $element[ 'params_content' ] ) . '>';
			$html.= display( $element['content'] );
			$html.= '</' . $this->content_tag . '>';
				
		endforeach;
		
		$html.= '</' . $this->tag . '>';
			
		return $html;
	}
}

/**
 * Accordion getter Function
 * @see skip_accordion()
 * @ignore
 */
function get_accordion( $elements, $args = array(), $return = 'html' ){
	$accordion = new Accordion( $args );
	
	if( count( $elements ) > 0 )
		foreach ( $elements AS $element )
			$accordion->add_element( $element['title'], $element['content'], array_key_exists( 'args', $element ) ? $element['args'] : array() );
	else
		return FALSE;
		
	return element_return( $accordion, $return );	
}

/**
 * Accordion
 * 
 * Creating a jQuery UI Accordion.
 *
 * @package Skip\Animated
 * @since 1.0
 * @param array $elements Elements array structured [ 'title' ], [ 'content' ] and [ 'args' ]
 * @param array $args Args array of [ 'id' ], [ 'classes' ], [ 'params' ], [ 'tag' ], [ 'title_tag' ] and [ 'content_tag' ]
 * @param string $return How to return 'echo', 'object' or 'html'
 */
function accordion( $elements, $args = array(), $return = 'html' ){
	get_accordion( $elements, $args = array(), 'echo' );
}