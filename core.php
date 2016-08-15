<?php
/**
 * Facebook Fanpage Import Core Class
 * This class initializes the Plugin.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0-beta.4
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

class FacebookFanpageImport {

	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->constants();
		$this->includes();

		add_action( 'init', array( $this, 'load_components' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'updates' ) );

		if( '' === session_id() || ! isset( $_SESSION ) ) {
			session_start();
		}

		if ( is_admin() ) {
			add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
			add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );
		}
	}

	/**
	 * Defining Constants for Use in Plugin
	 *
	 * @since 1.0.0
	 */
	public function constants() {
		define( 'FBFPI_FOLDER', plugin_dir_path( __FILE__ ) );
		define( 'FBFPI_RELATIVE_FOLDER', substr( FBFPI_FOLDER, strlen( WP_PLUGIN_DIR ), strlen( FBFPI_FOLDER ) ) );
		define( 'FBFPI_URLPATH', plugin_dir_url( __FILE__ ) );
		define( 'FBFPI_COMPONENTFOLDER', FBFPI_FOLDER . '/components' );
	}

	/**
	 * Getting include files
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		require_once( FBFPI_FOLDER . '/functions.php' );
	}

	/**
	 * Running updates
	 *
	 * @since 1.1.0
	 */
	public function updates() {
		$script_db_version  = '1.1';
		$current_db_version = get_option( 'fbfpi_db_version', '1.0' );

		if ( false === version_compare( $current_db_version, $script_db_version, '<' ) ) {
			return;
		}

		if ( true === version_compare( $current_db_version, '1.1', '<' ) ) {
			require_once( 'updates/to_1.1.php' );
			fbfbi_db_to_1_1();
			update_option( 'fbfpi_db_version', '1.1' );
		}
	}

	/**
	 * Loads the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'facebook-fanpage-import', false, FBFPI_RELATIVE_FOLDER . '/languages' );
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_styles() {
		wp_enqueue_style( 'fbfpi-admin-styles', fpfpi_get_asset_url( 'admin', 'css' ) );
	}

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_scripts() {
		wp_enqueue_script( 'fbfpi-admin-script', fpfpi_get_asset_url( 'admin', 'js' ) );
	}

	/**
	 * Registers and enqueues plugin-specific styles.
	 *
	 * @since 1.0.0
	 */
	public function register_plugin_styles() {
		if ( 'yes' !== get_option( 'fbfpi_deactivate_css' ) ) {
			wp_enqueue_style( 'fbfpi-plugin-styles', fpfpi_get_asset_url( 'display', 'css' ) );
		}
	}

	/**
	 * Registers and enqueues plugin-specific scripts.
	 *
	 * @since 1.0.0
	 */
	public function register_plugin_scripts() {
		wp_register_script( 'fbfpi-plugin-script', fpfpi_get_asset_url( 'display', 'js' ) );

		$translation_array = array(
			'locale' => get_locale(),
		);
		wp_localize_script( 'fbfpi-plugin-script', 'fbfpi', $translation_array );

		wp_enqueue_script( 'fbfpi-plugin-script' );
	}

	/**
	 * Loading components dynamical
	 *
	 * @since 1.0.0
	 */
	function load_components() {
		$handle = opendir( FBFPI_COMPONENTFOLDER );

		while ( false !== ( $file = readdir( $handle ) ) ):
			$entry = FBFPI_COMPONENTFOLDER . '/' . $file;
			if ( is_dir( $entry ) && '.' != $file && '..' != $file ) {
				if ( file_exists( $entry . '/component.php' ) ) {
					include( $entry . '/component.php' );
				}
			}
		endwhile;

		closedir( $handle );
	}

	/**
	 * Adds a notice to diaplay in admin notices
	 *
	 * @param string $string
	 * @param string $type
	 *
	 * @since 1.0.0
	 */
	public static function notice( $string, $type = 'notice' ) {
		if( '' === session_id() || ! isset( $_SESSION ) ) {
			session_start();
		}

		$notices = array();

		if ( array_key_exists( 'fbfpi_notices', $_SESSION ) ) {
			$notices = $_SESSION[ 'fbfpi_notices' ];
		}

		$notices[ $type ][] = $string;

		$_SESSION[ 'fbfpi_notices' ] = $notices;
	}

	/**
	 * Printing errors in admin notices
	 *
	 * @since 1.0.0
	 */
	public static function admin_notices() {
		if( ! array_key_exists( 'fbfpi_notices', $_SESSION ) ) {
			return;
		}

		$notices = $_SESSION[ 'fbfpi_notices' ];

		if ( array_key_exists( 'notice', $notices ) ) {
			foreach ( $notices['notice'] AS $notice ) {
				echo '<div class="updated"><p>' . __( 'Facebook Fanpage Import', 'facebook-fanpage-import' ) . ': ' . $notice . '</p></div>';
			}
		}

		if ( array_key_exists( 'error', $notices ) ) {
			foreach ( $notices['error'] AS $error ) {
				echo '<div class="error"><p>' . __( 'Facebook Fanpage Import', 'facebook-fanpage-import' ) . ': ' . $error . '</p></div>';
			}
		}

		unset( $_SESSION[ 'fbfpi_notices' ] );
	}

	/**
	 * Adding logs
	 * @param $string
	 */
	public static function log( $string ) {
		$upload_dir = wp_upload_dir();

		$plugin_log_dirname = $upload_dir['basedir'] . '/facebook-fanpage-import-logs/';
		if ( ! file_exists( $plugin_log_dirname ) ) {
			wp_mkdir_p( $plugin_log_dirname );
		}

		$file = fopen( $plugin_log_dirname . 'fbfpi.log', 'a+');
		fputs( $file, $string );
		fclose( $file );
	}

}

$FacebookFanpageImport = new FacebookFanpageImport();
