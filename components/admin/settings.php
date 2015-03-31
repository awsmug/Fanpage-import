<?php
/*
 * Facebook Fanpage Import Admin Component.
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

use skip\v1_0_0 as skip;

class FacebookFanpageImportAdminSettings{
	var $name;
	var $errors = array();
	
	/**
	 * Initializes the Component.
	 * @since 1.0.0
	 */
	function __construct() {
		$this->name = get_class( $this );
		
	    // Functions in Admin
	    if( is_admin() ):
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		endif;
		
		if( '' != skip\value( 'fbfpi_settings', 'app_id' ) && '' != skip\value( 'fbfpi_settings', 'app_secret' ) && '' != skip\value( 'fbfpi_settings', 'page_id' ) ):
			$this->test_con();
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		endif;
		
	} // end constructor
	
	/**
	 * Adds the Admin menu.
	 * @since 1.0.0
	 */	
	public function admin_menu(){
		add_submenu_page( 'options-general.php', __( 'Facebook Fanpage Import Settings', 'fbfpi' ), __( 'Fanpage Import', 'fbfpi' ), 'activate_plugins', 'Component' . $this->name, array( $this, 'admin_page' ) );
	}
	
	public function test_con(){
		// init app with app id (APPID) and secret (SECRET)
		
		/*
		echo '<br /><br />Trying:';
		 
		try {
		  $session = $helper->getSessionFromRedirect();
		} catch( FacebookRequestException $ex ) {
		  	print_r( $ex );
		} catch( Exception $ex ) {
		  // When validation fails or other local issues
		  	print_r( $ex );
		}
		
		// see if we have a session
		if ( isset( $session ) ) {
		  // graph api request for user data
		  $request = new FacebookRequest( $session, 'GET', '/me' );
		  $response = $request->execute();
		  // get response
		  $graphObject = $response->getGraphObject();
		   
		  // print data
		  echo  print_r( $graphObject, 1 );
		} else {
		  // show login url
		  echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
		}
		
		
		/*
		$app_id = skip_value( 'fbfpi_settings', 'app_id' );
		$app_secret = skip_value( 'fbfpi_settings', 'app_secret' );
		$page_id = skip_value( 'fbfpi_settings', 'page_id' );
		
		$fb_args = array(
			'appId'  => $app_id,
			'secret' => $app_secret
		);
		
		$fb = new Facebook( $fb_args );
		
		try{
            $fb->api( '/' . $page_id . '?fields=name,link' );
        }catch( Exception $e ){
            $this->errors[] = sprintf( __( '<a href="%s">Data incorrect. Please check your Facebook App ID, App Secret and your Fanpage ID.</a>', 'fbfpi' ), get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=ComponentFacebookFanpageImportAdminSettings' );
		}
		 */
	}
	
	/**
	 * Content of the admin page.
	 * @since 1.0.0
	 */
	public function admin_page(){
		echo '<div class="wrap">';
		
		echo '<div id="icon-options-general" class="icon32 icon32-posts-post"></div>';
		echo '<h2>' . __( 'Facebook Fanpage Import', 'fbfpi' ) . '</h2>';
		echo '<p>' . __( 'Just put in your Fanpage ID and start importing.', 'fbfpi' ) . '</p>';
		
		skip\form_start( 'fbfpi_settings' );
		
		/**
		 * Fanpage ID
		 */
		skip\textfield( 'page_id', __( 'Page ID', 'fbfpi' ) );
		
		/**
		 * Select stream languages
		 */
		$available_languages = get_available_languages();
		
		if( !in_array( 'en_US', $available_languages) )
			$available_languages[] = 'en_US';
		
		foreach( $available_languages AS $language )
			$select_languages[] = array( 'value' => $language );
		skip\select( 'stream_language', $select_languages, __( 'Facebook Language', 'fbfpi' ) );		
		
		/**
		 * Import WP Cron settings
		 */
		$schedules = wp_get_schedules(); // Getting WordPress schedules
		foreach( $schedules AS $key => $schedule )
			$select_schedules[] = array( 'label' => $schedule[ 'display' ], 'value' => $key );
			
		skip\select( 'update_interval', $select_schedules, __( 'Import Interval', 'fbfpi' ) );
		
		/**
		 * Num of entries to import
		 */
		skip\select( 'update_num', '5,10,25,50,100,200',  __( 'Entries to import', 'fbfpi' ) );
		
		/**
		 * Select where to import, as posts or as own post type
		 */
		$args = array(
			array(
				'value' => 'posts',
				'label' => __( 'Posts' )
			),
			array(
				'value' => 'status',
				'label' => __( 'Status message (own post type)', 'fbfpi' )
			)
		);
		skip\select( 'insert_post_type', $args, __( 'Insert Messages as', 'fbfpi' ) );
		
		/**
		 * Select importing User
		 */
		$users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
		$user_list = array();
		foreach( $users AS $user ):
			$user_list[] = array(
				'value' => $user->ID,
				'label' => $user->display_name
			);
		endforeach;
		
		skip\select( 'insert_user_id', $user_list, __( 'Inserting User', 'fbfpi' ) );
		
		/**
		 * Post status
		 */
		$post_status_values = array(
			array(
				'value' => 'publish',
				'label' => __( 'Published' )
			),
			array(
				'value' => 'draft',
				'label' => __( 'Draft' )
			),
		);
		
		skip\select( 'insert_post_status', $post_status_values, __( 'Post status', 'fbfpi' ) );
		
		/**
		 * Link target for imported links
		 */
		$link_select_values = array(
			array(
				'value' => '_self',
				'label' => __( 'same window', 'fbfpi' )
			),
			array(
				'value' => '_blank',
				'label' => __( 'new window', 'fbfpi' )
			),
		);
		
		skip\select( 'link_target', $link_select_values, __( 'Open Links in', 'fbfpi' ) );
		
		/**
		 * Selecting post formats if existing
		 */
		if ( current_theme_supports( 'post-formats' ) ):
    		$post_formats = get_theme_support( 'post-formats' );
			
			if( FALSE != $post_formats ):
				$post_formats = $post_formats[ 0 ];
				$post_format_list = array();
				
				$post_format_list[] = array(
						'value' => 'none',
						'label' => __( '-- None --', 'fbfpi' )
					);
				
				foreach( $post_formats as $post_format ):
					$post_format_list[] = array(
						'value' => $post_format,
						'label' => $post_format
					);
				endforeach;
				skip\select( 'insert_post_format', $post_format_list, __( 'Post format', 'fbfpi' ) );
			endif;
		endif;
		
		skip\checkbox( 'own_css', 'yes', __( 'Deactivate Plugin CSS', 'fbfpi' ) );
		
		do_action( 'fbfpi_settings_form' );
		
		/**
		 * Save Button
		 */
		skip\button( __( 'Save', 'fbfpi' ) );
		
		/**
		 * Import button
		 */
		if( '' != skip\value( 'fbfpi_settings', 'page_id' ) )
			echo ' <input type="submit" name="bfpi-now" value="' . __( 'Import Now', 'fbfpi' ) . '" class="button" style="margin-left:10px;" /> ';		
		
		skip\form_end();
		
		echo '</div>';
	}

	public function admin_notices(){
		if( count( $this->errors ) > 0 ):
				foreach( $this->errors AS $error )
					echo '<div class="updated"><p>' . __( 'Facebook Fanpage Import', 'fbfpi' ) . ': ' . $error . '</p></div>';
		endif;
		
		if( count( $this->notices ) > 0 ):
				foreach( $this->notices AS $notice )
					echo '<div class="updated"><p>' . __( 'Facebook Fanpage Import', 'fbfpi' ) . ': ' . $notice . '</p></div>';
		endif;	
	} 
}

$FacebookFanpageImportAdminSettings = new FacebookFanpageImportAdminSettings();
