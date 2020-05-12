<?php

/**
 * Facebook Fanpage Import Component.
 * This class initializes the component.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0-beta.8
 * @since   1.0.0
 * @license GPL 2
 *          Copyright 2016 Awesome UG (very@awesome.ug)
 *          This program is free software; you can redistribute it and/or modify
 *          it under the terms of the GNU General Public License, version 2, as
 *          published by the Free Software Foundation.
 *          This program is distributed in the hope that it will be useful,
 *          but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          GNU General Public License for more details.
 *          You should have received a copy of the GNU General Public License
 *          along with this program; if not, write to the Free Software
 *          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/../../assets/php-graph-sdk/src/Facebook/autoload.php';


class FacebookFanpageConnect {
	/**
	 * @var string Access token for facebook
	 * @since   1.0.0
	 */
	var $access_token;
	
	/**
	 * @var string App ID for facebook
	 * @since   1.0.0-beta.9
	 */
	var $app_id;
	
	/**
	 * @var string App secret for facebook
	 * @since   1.0.0-beta.9
	 */
	var $app_secret;

	/**
	 * @var int Facebook Fanpage ID
	 * @since   1.0.0
	 */
	var $page_id;

	/**
	 * @var int Facebook Fanpage ID as numeral
	 * @since   1.0.0-beta.9
	 */
	var $page_id_num;


	/**
	 * @var Facebook Paging Object
	 * @since   1.0.0
	 */
	var $paging = null;

	/**
	 * @var string Locale settings
	 * @since   1.0.0
	 */
	var $locale;
	
	/**
	 * @var Facebook Class
	 * @since   1.0.0-beta.9
	 */
	 var $fb;

	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	function __construct( $page_id, $access_token = '', $locale = 'en_EN' ) {
		$this->access_token = get_option( 'fbfpi_accesstoken' );
		$this->graph_url    = 'https://graph.facebook.com/v7.0'; // no ending "/"
		$this->locale       = $locale;
		$this->app_id       = get_option( 'fbfpi_appid' );
		$this->app_secret   = get_option( 'fbfpi_appsecret' );

		if ( '' != $access_token ) {
			$this->access_token = $access_token;
		}

		$this->page_id = $page_id;
		if (( '' != $this->access_token ) && ( '' != $this->app_id ) && ( '' != $this->app_secret ) ){

			$this->fb = new \Facebook\Facebook([
				  'app_id' => $this->app_id,
				  'app_secret' => $this->app_secret,
				  'default_graph_version' => 'v6.0',
				  'default_access_token' => $this->access_token
				]);
		}
	}

	/**
	 * Creates Access Token
	 *
	 * @param $app_id
	 * @param $app_secret
	 *
	 * @return mixed
	 * @since   1.0.0
	 */
	function create_access_token( $app_id, $app_secret ) {
		$access_token = $app_id . '|' . $app_secret;

		return $access_token;
	}

	/**
	 * Getting Page Data
	 *
	 * @return array|mixed|object|string
	 * @since   1.0.0
	 */
	function get_page() {
		$url = '/' . $this->page_id;
		$url .= '?fields=name,id,link';

		$data = $this->fetch_data_sdk( $url );
		
		// save numeral page id 
		$this->page_id_num = $data->id;
		
		return $data;
	}

	/**
	 * Fetching data, PHP SDK
	 *
	 * @param $url
	 *
	 * @return mixed|string
	 * @since   1.0.0
	 */
	private function fetch_data_sdk( $url ) {

		try {
		  // $url e.g. /me/posts?limit=10
		  $response = $this->fb->get( $url );
		  
		} catch(\Facebook\Exception\FacebookResponseException $e) {
		  // When Graph returns an error
		  FacebookFanpageImport::log( 'Graph returned an error: ' . $e->getMessage() );
		  return fetch_data ( $url );
		  
		} catch(\Facebook\Exception\FacebookSDKException $e) {
		  // When validation fails or other local issues
  		  FacebookFanpageImport::log( 'Facebook SDK returned an error: ' . $e->getMessage() );
		  return fetch_data ( $url );
		  
		} catch(\Facebook\Exception\FacebookAuthenticationException $e) {
		  // When authentification fails or GraphURL  is invalid
  		  FacebookFanpageImport::log( 'Facebook Graph Authentification returned an error: ' . $e->getMessage() );
		  return fetch_data ( $url );
		  
		}
		
		$data = json_decode( $response->getBody() );
		
		return $data;
	}
	
	/**
	 * Fetching data, cURL
	 *
	 * @param $url
	 *
	 * @return mixed|string
	 * @since   1.0.0
	 */
	private function fetch_data ( $url ) {
		FacebookFanpageImport::log( "Using cURL..." );
		
		// add https://graph.facebook.com/V6.0 für cURL
		$url = $this->graph_url . $url;

		if ( is_callable( 'curl_init' ) ) {
			$con = curl_init();

			curl_setopt( $con, CURLOPT_URL, $url );
			curl_setopt( $con, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $con, CURLOPT_TIMEOUT, 20 );
			curl_setopt( $con, CURLOPT_SSL_VERIFYPEER, false );

			$data = curl_exec( $con );
			
			$responseCode = curl_getinfo($con, CURLINFO_HTTP_CODE);
			curl_close( $con );


		 	if ($responseCode >= 400) {
		        echo "\n<br>HTTP Error: " . $responseCode;
		        echo "\n<br>Result-Body: ";
		        print_r($data);	
		        echo "\n<br>\n<br>";
		    }
			
			
			
		} elseif ( ini_get( 'allow_url_fopen' ) === true || ini_get( 'allow_url_fopen' ) == 1 ) {
			$data = @file_get_contents( $url );
		} else {
			if ( ! class_exists( 'WP_Http' ) ) {
				include_once( ABSPATH . WPINC . '/class-http.php' );
			}
			$request = new WP_Http;
			$result  = $request->request( $url );
			$data    = $result[ 'body' ];
		}
		$data = json_decode( $data );

		return $data;
	}

	/**
	 * Getting data of an Facebook graph ID
	 *
	 * @param string $id
	 * @param array $fields
	 *
	 * @return array|mixed|object|string
	 * @since   1.0.0
	 */
	function get_id( $id, $fields = array() ) {
		$url .= '/' . $id . '/';

		if ( is_array( $fields ) && count( $fields ) > 0 ) {
			$url = add_query_arg( 'fields', implode( ',', $fields ), $url );
		}

		$data = $this->fetch_data_sdk( $url );
		
		// Changes in response structure since GraphURL v3
		if (property_exists ( $data, 'attachments' ) ) {
			$data->type = ($data->attachments->data[0]->media_type) ?? 'status';
			$data->shared = false;
			// shared photo/link without own message, get description of linked content
			if ( property_exists ( $data, 'parent_id' ) && empty($data->message)) {
				if (substr($data->parent_id, 0, strpos($data->parent_id, '_')) <> $this->page_id_num) {
					$data->shared = true;
				}
				$data->message = $data->attachments->data[0]->description;
			}
			
			// if request for subattachments in photo album
			if ( (!empty($data->type) ) && ($data->type === 'album') && (property_exists ( $data->attachments->data[0], 'subattachments' ) ) ) {
				$data->photos = array();
				foreach ($data->attachments->data[0]->subattachments->data as $photo) {
					$data->photos[] = $photo;
				}
			} else {
				// use old data structure
				$data->title = $data->attachments->data[0]->title;
				$data->description = $data->attachments->data[0]->description;
				$data->link = $data->attachments->data[0]->unshimmed_url;
				$data->object_id = $data->attachments->data[0]->target->id;
			}
			unset($data->attachments);
		} else $data->type = 'status';

		// FacebookFanpageImport::log( "fetched_data: ". print_r($data, true) );

		return $data;
	}

	/**
	 * Getting posts
	 *
	 * @param int $limit
	 *
	 * @return mixed
	 * @since   1.0.0
	 */
	function get_posts( $limit = false ) {
		$url = '/' . $this->page_id . '/posts/';

		if ( false !== $limit ) {
			$url .= '?limit=' . $limit;
		}

		$data = $this->fetch_data_sdk( $url );

		if ( property_exists( $data, 'paging' ) ) {
			$this->paging = $data->paging;
		}

		return $data->data;
	}

	/**
	 * Getting paged posts
	 *
	 * @param string $url The "next page" URL returned by Graph API
	 *
	 * @return mixed
	 * @since   1.0.0
	 */
	function get_posts_paged( $url ) {
		parse_str($url, $query);
		$after = $query['after'];
		$url = '/' . $this->page_id . '/posts?after=' . $after;

		$data = $this->fetch_data_sdk( $url );

		if ( property_exists( $data, 'paging' ) ) {
			$this->paging = $data->paging;
		}

		return $data->data;
	}

	/**
	 * Gets the paging object
	 *
	 * @param string $url The "next page" URL returned by Graph API
	 *
	 * @return mixed
	 * @since   1.0.0
	 */
	function get_paging() {
		return $this->paging;
	}

	/**
	 * Getting picture of a post
	 *
	 * @param $post_id
	 *
	 * @return array|mixed|object|string
	 * @since   1.0.0
	 */
	function get_post_picture( $post_id ) {
		$url = '/'. $post_id;
		$url .= '?fields=full_picture';

		$data = $this->fetch_data_sdk( $url );

		return $data;
	}

	/**
	 * Getting photo by object
	 *
	 * @param $object_id
	 *
	 * @return array|mixed|object|string
	 * @since   1.0.0
	 */
	function get_photo_by_object( $object_id ) {
		$url = '/';
		$url .= $this->page_id . '_';
		$url .= $object_id;
		$url .= '?fields=attachments{media}';

		$data = $this->fetch_data_sdk( $url );

		$data = $data->attachments->data[ 0 ]->media->image->src;

		return $data;
	}

}
