<?php
/*
 * Facebook Fanpage Import Component.
 *
 * This class initializes the component.
 *
 * @author mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0
 * @since 1.0.0
 * @license GPL 2

  Copyright 2015 Awesome UG (very@awesome.ug)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( !defined( 'ABSPATH' ) ) exit;

class FacebookFanpageConnect{
	var $access_token;
	var $page_id;
	
	/**
	 * Initializes the Component.
	 * @since 1.0.0
	 */
	function __construct( $page_id, $access_token = '' ) {
		$this->access_token = '1412978082344911|a7f5722a2b02f24aad0cda61ae5c4fe9';
		
		if( '' != $access_token )
			$this->access_token = $access_token;
		
		$this->page_id = $page_id;
	}
	
	function create_access_token( $app_id, $app_secret ){
		$access_token = $app_id . '|' . $app_secret;
		return $data;
	}
	
	function get_page(){
		$url = 'https://graph.facebook.com/v2.1/';
		$url.= $this->page_id;
		$url.= '?access_token=' . $this->access_token;
		
		$data = $this->fetch_data( $url );
		$data = json_decode( $data );
		
		return $data;
	}
	
	function get_posts( $limit = 10 ){
		$url = 'https://graph.facebook.com/v2.1/';
		$url.= $this->page_id . '/';
		$url.= 'posts/';
		$url.= '?access_token=' . $this->access_token;
		$url.= '&limit=' . $limit;
		
		$data = $this->fetch_data( $url );
		
		$data = json_decode( $data );
		
		return $data->data;
	}
	
	function get_post_picture( $post_id ){
		$url = 'https://graph.facebook.com/v2.1/';
		$url.= $post_id;
		$url.= '?access_token=' . $this->access_token;
		$url.= '&fields=full_picture';
		
		$data = $this->fetch_data( $url );
		$data = json_decode( $data );
		
		return $data;
	}
	
	function get_photo_by_object( $object_id ){
		$url = 'https://graph.facebook.com/v2.1/';
		$url.= $object_id;
		$url.= '?access_token=' . $this->access_token;
		
		$data = $this->fetch_data( $url );
		$data = json_decode( $data );
		
		$data = $data->images[ 0 ]->source;
		
		return $data;
	}
	
	private function fetch_data( $url ){
	    if( is_callable( 'curl_init' ) ):
	        $con = curl_init();
			
	        curl_setopt( $con, CURLOPT_URL, $url );
	        curl_setopt( $con, CURLOPT_RETURNTRANSFER, 1 );
	        curl_setopt( $con, CURLOPT_TIMEOUT, 20 );
	        curl_setopt( $con, CURLOPT_SSL_VERIFYPEER, false );
			
	        $data = curl_exec( $con );
			
	        curl_close( $con );
	
	    elseif ( ini_get( 'allow_url_fopen' ) === TRUE || ini_get( 'allow_url_fopen' ) == 1  ):
	        $data = @file_get_contents($url);
			
	    else:
	        if( !class_exists( 'WP_Http' ) ) include_once( ABSPATH . WPINC. '/class-http.php' );
	        $request = new WP_Http;
	        $result = $request->request($url);
	        $data = $result['body'];
		endif;
		
	    return $data;
	}
		
}