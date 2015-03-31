<?php
/**
 * Skip WP Fileupload field
 * @package Skip\Forms
 * @since 1.0.0
 * @ignore
 */
 
namespace skip\v1_0_0;

class WP_File extends Textfield{
	
	/**
	 * Constructor
	 * @since 1.0.0
	 * @param string $name Name of WP File Field.
	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $label = FALSE, $args = array() ){
		
		$defaults = array(
			'wp_browse' =>  __( 'Add Media' )
		);
		
		$args = wp_parse_args( $args, $defaults );
		$this->wp_browse = $args[ 'wp_browse' ];
		
		parent::__construct( $name, $label, $args );
	}
	
	/**
	 * Rendering WP File field
	 * @package Skip
	 * @since 1.0
	 * @return string $html Returns The HTML Code.
	 */	
	public function render(){
		global $skip_javascripts;
		
		$skip_javascripts[] = '	
			$("#skip_filebutton_' . $this->params[ 'id' ] . '").click(function() {
				
				if ( typeof wp !== "undefined" && wp.media && wp.media.editor ){
   		   			wp.media.editor.open( "#skip_filebutton_' . $this->params[ 'id' ] . '" );
					
					wp.media.editor.send.attachment = function( props, attachment ) {
						console.log( attachment );

						if( attachment.url.match(/\.jpg$/i) || attachment.url.match(/\.gif$/i) || attachment.url.match(/\.png$/i) ){
							preview_file = attachment.url;
			        	}else{
			        		preview_file = attachment.icon;
			        	}
						
						$( "#skip_file_' . $this->params[ 'id' ] . '" ).val( attachment.url );
						$( "#skip_filepreview_' . $this->params[ 'id' ] . '" ).attr( "src", preview_file );	
						$( "#skip_filename_' . $this->params[ 'id' ] . ' a" ).text( preview_file.replace( /.*\//, "" ) );
						$( "#skip_filename_' . $this->params[ 'id' ] . ' a" ).attr( "href", preview_file );	
						$( "#skip_filepreview_src_' . $this->params[ 'id' ] . '" ).val( preview_file );
			    	}
		    	}

   			});
   			
			$( "#skip_filepreview_' . $this->params[ 'id' ] . '" ).attr( "src", $( "#skip_filepreview_src_' . $this->params[ 'id' ] . '" ).val() );
			
   			';
		
		
		$html = $this->before_element;
		$html.= '<div class="skip_file ui-state-default ui-corner-all">';
			$html.= '<div class="skip_filepreview">';
				$html.= '<img id="skip_filepreview_' . $this->params[ 'id' ] . '" class="skip_filepreview_image" />';
				if( isset( $this->value ) ) 
					$html.= '<div class="skip_filename" id="skip_filename_' . $this->params[ 'id' ] . '"><a href="' . $this->value . '" target="_blank">' . basename( $this->value ) . '</a></div>';
			
			$html.= '</div>';
			$html.= '<div class="skip_fileuploader">';
				$html.= '<input id="skip_file_' . $this->params[ 'id' ] . '" class="skip_file_name" type="text" name="' . $this->params[ 'name' ] . '" value="' . $this->value . '" />';
				$html.= '<input id="skip_filebutton_' . $this->params[ 'id' ] . '" class="skip_file_button" type="button" value="' . $this->wp_browse . '" />';
				$html.= hidden( $this->name . '_icon_src', array( 'id' => 'skip_filepreview_src_' . $this->params[ 'id' ] ) );
			$html.= '</div>';
		$html.= '</div>';
		$html.= $this->after_element;
		
		return $html;
			
	}
}

/**
 * Fileupload getter Function
 * @see skip_wp_file()
 * @ignore
 */
function get_wp_file( $name, $label = FALSE, $args = array(), $return = 'html' ){
	$file = new WP_File( $name, $label, $args );
	return $file->render();
}

/**
 * <pre>skip_wp_file( $name, $args )</pre>
 * 
 * Adding a File Field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_wp_file( 'myfile' );
 * </code>
 * This will create an automated saved file field.
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
 * 	<li>classes (string) Name of CSS Classes which will be inserted into HTML seperated by empty space.</li>
 * 	<li>before_element (string) Content before the element.</li>
 *	<li>after_element (string) Content after the element.</li>
 * 	<li>save (boolean) TRUE if value of field have to be saved in Database, FALSE if not (default TRUE).</li>
 * </ul>
 * 
 * <b>Example</b>
 * 
 * Creating a labeled WordPress Upload field in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'My File'
 * );
 * skip_wp_file( 'myfile', $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $filename = skip_value( 'myformname', 'myfile' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name Name of File field.
 * @param array/string $args List of Arguments.
 */
function wp_file( $name, $label = FALSE, $args = array() ){
	echo get_wp_file( $name, $label, $args );
}