<?php
/*
 * Facebook Fanpage Import Showdata Component.
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

class FacebookFanpageImportAdmin{
	var $name;
	
	/**
	 * Initializes the Component.
	 * @since 1.0.0
	 */
	function __construct() {
		$this->name = get_class( $this );
		$this->includes();
		
		
		if( 'status' == skip\value( 'fbfpi_settings', 'insert_post_type' ) )
			add_action( 'init', array( $this, 'custom_post_types' ), 11 );
		
		
	    // Functions in Admin
	    if( is_admin() ):
			// add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		endif;
	} // end constructor
	
	
	/**
	 * Creates Custom Post Types
	 * @since 1.0.0
	 */	
	public function custom_post_types(){
		$args_post_type = array(
			'labels' => array(
				'name' => __( 'Status Messages', 'fbfpi-locale' ),
				'singular_name' => __( 'Status Message', 'fbfpi-locale' )
			),
			'public' => TRUE,
			'has_archive' => TRUE,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'rewrite' => array(
	            'slug' => 'status-message',
	            'with_front' => TRUE
            )
		); 
		register_post_type( 'status-message', $args_post_type );		
	}
	
	/**
	 * Including needed Files.
	 * @since 1.0.0
	 */	
	private function includes(){
		include( dirname(__FILE__) . '/settings.php' );
	}
}

$FacebookFanpageImportAdmin = new FacebookFanpageImportAdmin();
