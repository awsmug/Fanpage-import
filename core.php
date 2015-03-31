<?php
/*
 * Facebook Fanpage Import Core Class
 *
 * This class initializes the Plugin.
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
 
class FacebookFanpageImport {
	 
	/**
	 * Initializes the plugin.
	 * @since 1.0.0
	 */
	function __construct() {
		$this->constants();
		$this->includes();
		$this->framework();
		
		add_action( 'init', array( $this, 'load_components' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		
		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		// register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );

	    // Functions on Frontend
	    if( is_admin() ):
			// Register admin styles and scripts
			add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		else:
			// Register plugin styles and scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
		endif;
	} // end constructor
	
	/**
	 * Fired when the plugin is activated.
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 * @since 1.0.0 
	 */
	public function activate( $network_wide ) {
		// TODO:	Define activation functionality here
	} // end activate
	
	/**
	 * Fired when the plugin is deactivated.
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function deactivate( $network_wide ) {
		// TODO:	Define deactivation functionality here		
	} // end deactivate
	
	/**
	 * Fired when the plugin is uninstalled.
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 * @since 1.0.0 
	 */
	public function uninstall( $network_wide ) {
		// TODO:	Define uninstall functionality here		
	} // end uninstall

	/**
	 * Loads the plugin text domain for translation.
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		// TODO: replace "plugin-name-locale" with a unique value for your plugin
		load_plugin_textdomain( 'fbfpi', false, FBFPI_RELATIVE_FOLDER . '/languages' );
	} // end plugin_textdomain

	/**
	 * Registers and enqueues admin-specific styles.
	 * @since 1.0.0
	 */
	public function register_admin_styles() {
		// TODO:	Change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'fbfpi-admin-styles', FBFPI_URLPATH . '/includes/css/admin.css' );
	
	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 * @since 1.0.0
	 */	
	public function register_admin_scripts() {
		wp_enqueue_script( 'fbfpi-admin-script', FBFPI_URLPATH . '/includes/js/admin.js' );
	
	} // end register_admin_scripts
	
	/**
	 * Registers and enqueues plugin-specific styles.
	 * @since 1.0.0
	 */
	public function register_plugin_styles() {
		if( '' == skip\value( 'fbfpi_settings', 'own_css' ) )
			wp_enqueue_style( 'fbfpi-plugin-styles', FBFPI_URLPATH . '/includes/css/display.css' );
	
	} // end register_plugin_styles
	
	/**
	 * Registers and enqueues plugin-specific scripts.
	 * @since 1.0.0
	 */
	public function register_plugin_scripts() {
		wp_enqueue_script( 'fbfpi-plugin-script',  FBFPI_URLPATH . '/includes/js/display.js' );
	
	} // end register_plugin_scripts
	
	/**
	 * Defining Constants for Use in Plugin
	 * @since 1.0.0
	 */
	public function constants(){
		define( 'FBFPI_FOLDER', 			$this->get_folder() ); 
		define( 'FBFPI_RELATIVE_FOLDER', 	substr( FBFPI_FOLDER, strlen( WP_PLUGIN_DIR ), strlen( FBFPI_FOLDER ) ) ); 
		define( 'FBFPI_URLPATH', 			$this->get_url_path() );
		define( 'FBFPI_COMPONENTFOLDER', FBFPI_FOLDER . '/components' );
	}

	/**
	 * Defining Constants for Use in Plugin
	 * @since 1.0.0
	 */
	public function framework(){
		// Loading Skip
		include( FBFPI_FOLDER . '/includes/skip/loader.php' ); 
		skip\start();
	}	
	
	/**
	 * Getting include files
	 * @since 1.0.0
	 */
	public function includes(){
		// Loading functions
		include( FBFPI_FOLDER . '/functions.php' );
	}

	/**
	 * Loading components dynamical
	 * @since 1.0.0
	 */
	function load_components(){
		// Loading Components
		$handle = opendir( FBFPI_COMPONENTFOLDER ); // TODO: Rename Constant
		
		while ( FALSE !== ( $file = readdir( $handle) ) ):
			$entry = FBFPI_COMPONENTFOLDER . '/' . $file;
			if( is_dir( $entry ) && '.' != $file && '..' != $file )
				if( file_exists( $entry . '/component.php' ) )
					include( $entry . '/component.php' );
		endwhile;
		
		closedir($handle);
	}
	
	/**
	* Getting URL
	* @since 1.0.0
	*/
	private function get_url_path(){
		$sub_path = substr( FBFPI_FOLDER, strlen( ABSPATH ), ( strlen( FBFPI_FOLDER ) - 11 ) );
		$script_url = get_bloginfo( 'wpurl' ) . '/' . $sub_path;
		return $script_url;
	}
	
	/**
	* Getting Folder
	* @since 1.0.0
	*/
	private function get_folder(){
		$sub_folder = substr( dirname(__FILE__), strlen( ABSPATH ), ( strlen( dirname(__FILE__) ) - strlen( ABSPATH ) ) );
		$script_folder = ABSPATH . $sub_folder;
		return $script_folder;
	}
	
} // end class

$FacebookFanpageImport = new FacebookFanpageImport();