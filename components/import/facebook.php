<?php
/**
 * Facebook Fanpage Import Component.
 *
 * This class initializes the component.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0
 * @since   1.0.0
 * @license GPL 2
 *
 * Copyright 2015 Awesome UG (very@awesome.ug)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if( !defined( 'ABSPATH' ) )
{
	exit;
}

class FacebookFanpageConnect
{
	/**
	 * @var string Access token for facebook
	 */
	var $access_token;

	/**
	 * @var Facebook Fanpage ID
	 */
	var $page_id;

	/**
	 * @var string Locale settings
	 */
	var $locale;

	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	function __construct( $page_id, $access_token = '', $locale = 'en_EN' )
	{
		$this->access_token = '1412978082344911|a7f5722a2b02f24aad0cda61ae5c4fe9';
		$this->graph_url = 'https://graph.facebook.com/v2.1/';
		$this->locale = $locale;

		if( '' != $access_token )
		{
			$this->access_token = $access_token;
		}

		$this->page_id = $page_id;
	}

	/**
	 * Creates Access Token
	 *
	 * @param $app_id
	 * @param $app_secret
	 *
	 * @return mixed
	 */
	function create_access_token( $app_id, $app_secret )
	{
		$access_token = $app_id . '|' . $app_secret;

		return $access_token;
	}

	/**
	 * Getting Page Data
	 *
	 * @return array|mixed|object|string
	 */
	function get_page()
	{
		$url = $this->graph_url;
		$url .= $this->page_id;
		$url .= '?access_token=' . $this->access_token . '&locale=' . $this->locale;

		$data = $this->fetch_data( $url );
		$data = json_decode( $data );

		return $data;
	}

	/**
	 * Fetching data
	 *
	 * @param $url
	 *
	 * @return mixed|string
	 *
	 */
	private function fetch_data( $url )
	{
		if( is_callable( 'curl_init' ) )
		{
			$con = curl_init();

			curl_setopt( $con, CURLOPT_URL, $url );
			curl_setopt( $con, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $con, CURLOPT_TIMEOUT, 20 );
			curl_setopt( $con, CURLOPT_SSL_VERIFYPEER, FALSE );

			$data = curl_exec( $con );

			curl_close( $con );
		}
		elseif( ini_get( 'allow_url_fopen' ) === TRUE || ini_get( 'allow_url_fopen' ) == 1 )
		{
			$data = @file_get_contents( $url );
		}
		else
		{
			if( !class_exists( 'WP_Http' ) )
			{
				include_once( ABSPATH . WPINC . '/class-http.php' );
			}
			$request = new WP_Http;
			$result = $request->request( $url );
			$data = $result[ 'body' ];
		}

		return $data;
	}

	/**
	 * Getting posts
	 *
	 * @param int $limit
	 *
	 * @return mixed
	 */
	function get_posts( $limit = FALSE )
	{
		$url = $this->graph_url;
		$url .= $this->page_id . '/';
		$url .= 'posts/';
		$url .= '?access_token=' . $this->access_token . '&locale=' . $this->locale;;

		if( FALSE !== $limit )
		{
			$url .= '&limit=' . $limit;
		}

		$data = $this->fetch_data( $url );

		$data = json_decode( $data );

		return $data->data;
	}

	/**
	 * Getting picture of a post
	 *
	 * @param $post_id
	 *
	 * @return array|mixed|object|string
	 */
	function get_post_picture( $post_id )
	{
		$url = $this->graph_url;
		$url .= $post_id;
		$url .= '?access_token=' . $this->access_token . '&locale=' . $this->locale;;
		$url .= '&fields=full_picture';

		$data = $this->fetch_data( $url );
		$data = json_decode( $data );

		return $data;
	}

	/**
	 * Getting photo by object
	 *
	 * @param $object_id
	 *
	 * @return array|mixed|object|string
	 */
	function get_photo_by_object( $object_id )
	{
		$url = $this->graph_url;
		$url .= $object_id;
		$url .= '?access_token=' . $this->access_token . '&locale=' . $this->locale;;

		$data = $this->fetch_data( $url );
		$data = json_decode( $data );

		$data = $data->images[ 0 ]->source;

		return $data;
	}

}