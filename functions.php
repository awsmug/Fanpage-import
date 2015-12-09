<?php

if( !defined( 'ABSPATH' ) )
{
	exit;
}

/**
 * Getting Plugin Template
 *
 * @since 1.0.0
 */
if( defined( 'FBFPI_FOLDER' ) )
{
	function locate_fbfpi_template( $template_names, $load = FALSE, $require_once = TRUE )
	{
		$located = '';

		$located = locate_template( $template_names, $load, $require_once );

		if( '' == $located )
		{
			foreach( ( array ) $template_names as $template_name )
			{
				if( !$template_name )
				{
					continue;
				}
				if( file_exists( FBFPI_FOLDER . '/templates/' . $template_name ) )
				{
					$located = FBFPI_FOLDER . '/templates/' . $template_name;
					break;
				}
			}
		}

		if( $load && '' != $located )
		{
			load_template( $located, $require_once );
		}

		return $located;
	}

	function fbfpi_get_url_var( $name )
	{

		$strURL = $_SERVER[ 'REQUEST_URI' ];
		$arrVals = explode( '/', $strURL );
		$found = 0;
		foreach( $arrVals as $index => $value )
		{
			if( $value == $name )
			{
				$found = $index;
			}
		}
		$place = $found + 1;

		return $arrVals[ $place ];
	}
}

/**
 * Debugging helper function
 */
if( !function_exists( 'p' ) ){
	function p( $var, $return = FALSE  )
	{
		$content = '<pre>';
		$content.= print_r( $var, TRUE );
		$content.= '</pre>';
		if( !$return ){
			echo $content;
		}
		return $content;
	}
}

