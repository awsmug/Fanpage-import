<?php
/**
 * Facebook Fanpage Import Admin Component.
 * This class initializes the component.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.1-beta.8
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

class FacebookFanpageImportAdminSettings {

	var $name;
	var $errors = array();
	var $notices = array();


	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->name = get_class( $this );

		add_action( 'init', array( $this, 'start_session' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'import' ) );
		
	}

	public function start_session() {
		session_start();
	}

	public function import() {
		if ( array_key_exists( 'fbfpi_now', $_POST ) || array_key_exists( 'fbfpi_next', $_POST ) ) {
			$fbfpi_import = FacebookFanpageImportFacebookStream::instance();
			$fbfpi_import->import();
		}

		if ( array_key_exists( 'fbfpi_stop', $_POST  ) ) {
			$fbfpi_import = FacebookFanpageImportFacebookStream::instance();
			$fbfpi_import->stop_import();
		}
	}

	/**
	 * Testing Connection to Facebook API
	 *
	 * @todo Adding functionality
	 */
	public function test_con() {
	}

	/**
	 * Adds the Admin menu.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_submenu_page( 'tools.php', __( 'Fanpage Import', 'facebook-fanpage-import' ), __( 'Fanpage Import', 'facebook-fanpage-import' ), 'manage_options', __FILE__, array( $this, 'admin_page' ) );
	}

	/**
	 * Register Settings
	 */
	public function register_settings() {
		register_setting( 'fbfpi_options', 'fbfpi_fanpage_id' );
		register_setting( 'fbfpi_options', 'fbfpi_appid' );
		register_setting( 'fbfpi_options', 'fbfpi_appsecret' );
		register_setting( 'fbfpi_options', 'fbfpi_accesstoken' );
		register_setting( 'fbfpi_options', 'fbfpi_accesstoken_expire' );
		register_setting( 'fbfpi_options', 'fbfpi_fanpage_stream_language' );
		register_setting( 'fbfpi_options', 'fbfpi_import_interval' );
		register_setting( 'fbfpi_options', 'fbfpi_import_num' );
		register_setting( 'fbfpi_options', 'fbfpi_insert_post_type' );
		register_setting( 'fbfpi_options', 'fbfpi_insert_term_id' );
		register_setting( 'fbfpi_options', 'fbfpi_insert_user_id' );
		register_setting( 'fbfpi_options', 'fbfpi_insert_post_status' );
		register_setting( 'fbfpi_options', 'fbfpi_insert_link_target' );
		register_setting( 'fbfpi_options', 'fbfpi_insert_post_format' );
		register_setting( 'fbfpi_options', 'fbfpi_deactivate_css' );
	}

	/**
	 * Content of the admin page.
	 *
	 * @since 1.0.0
	 */
	public function admin_page() {

		echo '<div class="wrap">';

		echo '<div id="icon-options-general" class="icon32 icon32-posts-post"></div>';
		echo '<h2>' . __( 'Facebook Fanpage Import', 'facebook-fanpage-import' ) . '</h2>';

		echo '<div class="fbfpi-form">';
		echo '<form method="post" action="options.php">';
		settings_fields( 'fbfpi_options' );
		do_settings_sections( 'fbfpi_options' );


		$fanpage_id              = get_option( 'fbfpi_fanpage_id' );
		$fbfpi_accesstoken       = get_option( 'fbfpi_accesstoken' );
		$fbfpi_accesstoken_expire= get_option( 'fbfpi_accesstoken_expire' );
		$fbfpi_appid             = get_option( 'fbfpi_appid' );
		$fbfpi_appsecret         = get_option( 'fbfpi_appsecret' );
		$reimport_format	     = get_option( 'fbfpi_reimport_format' );
		$fanpage_stream_language = get_option( 'fbfpi_fanpage_stream_language' );
		$import_interval         = get_option( 'fbfpi_import_interval' );
		$import_num              = get_option( 'fbfpi_import_num' );
		$insert_post_type        = get_option( 'fbfpi_insert_post_type' );
		$insert_term_id          = get_option( 'fbfpi_insert_term_id' );
		$insert_user_id          = get_option( 'fbfpi_insert_user_id' );
		$insert_post_status      = get_option( 'fbfpi_insert_post_status' );
		$insert_link_target      = get_option( 'fbfpi_insert_link_target' );
		$insert_post_format      = get_option( 'fbfpi_insert_post_format' );
		$deactivate_css          = get_option( 'fbfpi_deactivate_css' );
		
		if ( ($fbfpi_appid == '') || ($fbfpi_appsecret == '') ) {
			FacebookFanpageImport::notice( sprintf( '<a href="%s">'.__( 'App ID and App Secret have to be provided', 'facebook-fanpage-import' ).'</a>', admin_url( 'tools.php?page=facebook-fanpage-import/components/admin/settings.php' ) ), 'error' );

		}
		if ( ($fbfpi_accesstoken == '') ) {
			FacebookFanpageImport::notice( sprintf( '<a href="%s">'.__( 'Facebook access token has to be provided', 'facebook-fanpage-import' ).'</a>', admin_url( 'tools.php?page=facebook-fanpage-import/components/admin/settings.php' ) ), 'error' );
		}
		if ( (($fbfpi_accesstoken_expire != '0') && ((int)$fbfpi_accesstoken_expire-time() < 604800 ) ) ) {
			FacebookFanpageImport::notice( sprintf( '<a href="%s">'.__( 'Your Facebook access token will expire in a few days!', 'facebook-fanpage-import' ).'</a>', admin_url( 'tools.php?page=facebook-fanpage-import/components/admin/settings.php' ) ), 'error' );
		}
		

		$imported_until = '';
		parse_str( get_option( '_facebook_fanpage_import_next', false ), $opt );
		if( ! empty( $opt['until'] ) ) {
			$imported_until = date_i18n( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ), $opt[ 'until' ] );
		}
		if ((!empty($fbfpi_appid)) && (!empty($fbfpi_appsecret))) {
		$fb = new \Facebook\Facebook([
		  'app_id' => $fbfpi_appid,
		  'app_secret' => $fbfpi_appsecret,
		  'default_graph_version' => 'v7.0'
		]);
		
		$helper = $fb->getRedirectLoginHelper();
		$permissions = [];
		$loginUrl = $helper->getLoginUrl( plugin_dir_url(__FILE__) . 'facebook-callback.php', $permissions);
		} else $loginUrl = "javascript:void(0)";
		

		/**
		 * Fanpage ID
		 */
		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_fanpage_id">' . __( 'Page ID', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<input type="text" name="fbfpi_fanpage_id" id="fbfpi_fanpage_id" value="' . $fanpage_id . '" /><br /><small>' . __( 'Copy the fanpage ID from your Facebook fanpage info page.', 'facebook-fanpage-import' ) . '</small>';
		echo '</div>';
		echo '</div>';
		
		/**
		 * App ID
		 */
		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_appid">' . __( 'App ID', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<input type="text" name="fbfpi_appid" id="fbfpi_appid" value="' . $fbfpi_appid . '" /><br /><small>' . __( 'Paste the app ID from your Facebook App Settings.', 'facebook-fanpage-import' ) . '</small>';
		echo '</div>';
		echo '</div>';
		
		/**
		 * App Secret
		 */
		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_appsecret">' . __( 'App Secret', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<input type="text" name="fbfpi_appsecret" id="fbfpi_appsecret" value="' . $fbfpi_appsecret . '" /><br /><small>' . __( 'Paste the app secret from your Facebook App Settings.', 'facebook-fanpage-import' ) . '</small>';
		echo '</div>';
		echo '</div>';


		/**
		 * fbfpi_accesstoken
		 */
		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_accesstoken">' . __( 'Facebook App Access Token', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<textarea name="fbfpi_accesstoken" id="fbfpi_accesstoken" rows="9">'. $fbfpi_accesstoken . '</textarea><br /><small>' . __( 'Create a Facebook App and add the accesstoken here  (will expire after certain time).', 'facebook-fanpage-import' ) . '</small>';
		echo '<br/><a href="' . $loginUrl . '">' . __( 'Login with Facebook to get a new AccessToken', 'facebook-fanpage-import' ). '</a>';
		echo '<br/><small>' . __( 'You need to be a Facebook developer, admin of the Facebook App with the ID above and admin of the Facebook page.', 'facebook-fanpage-import' ). '</small>';
		echo '</div>';
		echo '</div>';

		/**
		 * fbfpi_accesstoken Expire Date
		 */
		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_accesstoken_expire">' . __( 'Access Token Expiration', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<input type="text" name="fbfpi_accesstoken_expire" id="fbfpi_accesstoken_expire" disabled="disabled" value="' . (($fbfpi_accesstoken_expire > 0) ? date("Y-m-d H:i:s", $fbfpi_accesstoken_expire) : __( 'never', 'facebook-fanpage-import' )) . '" /><br /><small>' . __( 'Fomat is Y-m-d H:i:s, UTC. If you have undergone the Facebook review, your token may ever expire.', 'facebook-fanpage-import' ) . '</small>';
		echo '</div>';
		echo '</div>';

		/**
		 * Select stream languages
		 */
		$available_languages = get_available_languages();

		if ( ! in_array( 'en_US', $available_languages ) ) {
			$available_languages[] = 'en_US';
		}

		foreach ( $available_languages AS $language ) {
			$select_languages[] = array( 'value' => $language );
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_fanpage_stream_language">' . __( 'Facebook Language', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<select name="fbfpi_fanpage_stream_language" id="fbfpi_fanpage_stream_language">';
		foreach ( $select_languages AS $language ) {
			$selected = '';
			if ( $language[ 'value' ] === $fanpage_stream_language ) {
				$selected = ' selected="selected"';
			}

			echo '<option value="' . $language[ 'value' ] . '"' . $selected . '>' . $language[ 'value' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';

		/**
		 * Import WP Cron settings
		 */
		$select_schedules = array( array( 'label' => __( 'Never', 'facebook-fanpage-import' ), 'value' => 'never' ) );
		$schedules        = wp_get_schedules(); // Getting WordPress schedules
		foreach ( $schedules AS $key => $schedule ) {
			$select_schedules[] = array( 'label' => $schedule[ 'display' ], 'value' => $key );
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_import_interval">' . __( 'Import Interval', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<select name="fbfpi_import_interval" id="fbfpi_import_interval">';
		foreach ( $select_schedules AS $schedule ) {
			$selected = '';
			if ( $schedule[ 'value' ] === $import_interval ) {
				$selected = ' selected="selected"';
			}

			echo '<option value="' . $schedule[ 'value' ] . '"' . $selected . '>' . $schedule[ 'label' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';

		/**
		 * Num of entries to import
		 */
		$import_num_values = apply_filters( 'fbfpi_num_values', array( 5, 10, 25, 50, 100 ) );

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_import_num">' . __( 'Entries to import', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<select name="fbfpi_import_num" id="fbfpi_import_num">';
		foreach ( $import_num_values AS $num ) {
			$selected = '';
			if ( (int) $num == (int) $import_num ) {
				$selected = ' selected="selected"';
			}

			echo '<option value="' . $num . '"' . $selected . '>' . $num . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';

		/**
		 * Select where to import, as posts or as own post type
		 */
		$insert_post_types = array(
			array(
				'value' => 'posts',
				'label' => __( 'Posts' )
			),
			array(
				'value' => 'status',
				'label' => __( 'Status message (own post type)', 'facebook-fanpage-import' )
			)
		);

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_insert_post_type">' . __( 'Insert Messages as', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<select name="fbfpi_insert_post_type" id="fbfpi_insert_post_type">';
		foreach ( $insert_post_types AS $post_type ) {
			$selected = '';
			if ( $insert_post_type === $post_type[ 'value' ] ) {
				$selected = ' selected="selected"';
			}

			echo '<option value="' . $post_type[ 'value' ] . '"' . $selected . '>' . $post_type[ 'label' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';

		if( 'posts' === $insert_post_type ) {
			/**
			 * Select a category to apply to imported entries
			 */
			$insert_post_terms = array(
				array(
					'value' => 'none',
					'label' => __( 'No category', 'facebook-fanpage-import' ),
				)
			);

			$terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );
			foreach ( $terms AS $term ) {
				$insert_post_terms[] = array(
					'value' => $term->term_id,
					'label' => $term->name,
				);
			}

			echo '<div class="fbfpi-form-field">';
			echo '<label for="fbfpi_insert_term_id">' . __( 'Categorise Messages as', 'facebook-fanpage-import' ) . '</label>';
			echo '<div class="input">';
			echo '<select name="fbfpi_insert_term_id" id="fbfpi_insert_term_id">';
			foreach ( $insert_post_terms AS $term ) {
				$selected = '';
				if ( (int) $insert_term_id === (int) $term[ 'value' ] ) {
					$selected = ' selected="selected"';
				}

				echo '<option value="' . $term[ 'value' ] . '"' . $selected . '>' . $term[ 'label' ] . '</option>';
			}
			echo '</select>';
			echo '<br /><small>' . sprintf( __( 'Add new categories in the <a href="%s">posts section</a>.', 'facebook-fanpage-import' ), admin_url( 'edit-tags.php?taxonomy=category' ) ) . '</small>';
			echo '</div>';
			echo '</div>';
		}

		/**
		 * Select importing User
		 */
		$users     = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
		$user_list = array();

		foreach ( $users AS $user ) {
			$user_list[] = array(
				'value' => $user->ID,
				'label' => $user->display_name
			);
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_insert_user_id">' . __( 'Inserting User', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<select name="fbfpi_insert_user_id" id="fbfpi_insert_user_id">';
		foreach ( $user_list AS $user ) {
			$selected = '';
			if ( $insert_user_id === $user[ 'value' ] ) {
				$selected = ' selected="selected"';
			}

			echo '<option value="' . $user[ 'value' ] . '"' . $selected . '>' . $user[ 'label' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';

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

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_insert_post_status">' . __( 'Post status', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<select name="fbfpi_insert_post_status" id="fbfpi_insert_post_status">';
		foreach ( $post_status_values AS $post_status_value ) {
			$selected = '';
			if ( $insert_post_status === $post_status_value[ 'value' ] ) {
				$selected = ' selected="selected"';
			}

			echo '<option value="' . $post_status_value[ 'value' ] . '"' . $selected . '>' . $post_status_value[ 'label' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';

		/**
		 * Link target for imported links
		 */
		$insert_link_target_values = array(
			array(
				'value' => '_self',
				'label' => __( 'same window', 'facebook-fanpage-import' )
			),
			array(
				'value' => '_blank',
				'label' => __( 'new window', 'facebook-fanpage-import' )
			),
		);

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_insert_link_target">' . __( 'Open Links in', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<select name="fbfpi_insert_link_target" id="fbfpi_insert_link_target">';
		foreach ( $insert_link_target_values AS $insert_link_target_value ) {
			$selected = '';
			if ( $insert_link_target === $insert_link_target_value[ 'value' ] ) {
				$selected = ' selected="selected"';
			}

			echo '<option value="' . $insert_link_target_value[ 'value' ] . '"' . $selected . '>' . $insert_link_target_value[ 'label' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';
		echo '</div>';

		/**
		 * Selecting post formats if existing
		 */
		if ( current_theme_supports( 'post-formats' ) && 'posts' === $insert_post_type ) {
			$post_formats = get_theme_support( 'post-formats' );

			if ( false != $post_formats ) {
				$post_formats = $post_formats[ 0 ];

				$insert_post_format_values[] = array(
					'value' => 'none',
					'label' => __( '-- None --', 'facebook-fanpage-import' )
				);

				foreach ( $post_formats as $post_format ) {
					$insert_post_format_values[] = array(
						'value' => $post_format,
						'label' => $post_format
					);
				}

				echo '<div class="fbfpi-form-field">';
				echo '<label for="fbfpi_insert_post_format">' . __( 'Post format', 'facebook-fanpage-import' ) . '</label>';
				echo '<div class="input">';
				echo '<select name="fbfpi_insert_post_format" id="fbfpi_insert_post_format">';
				foreach ( $insert_post_format_values AS $insert_post_format_value ) {
					$selected = '';
					if ( $insert_post_format === $insert_post_format_value[ 'value' ] ) {
						$selected = ' selected="selected"';
					}

					echo '<option value="' . $insert_post_format_value[ 'value' ] . '"' . $selected . '>' . $insert_post_format_value[ 'label' ] . '</option>';
				}
				echo '</select>';
				echo '</div>';
				echo '</div>';
			}
		}

		$checked = '';
		if ( 'yes' === $deactivate_css ) {
			$checked = ' checked="checked"';
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fbfpi_deactivate_css">' . __( 'Deactivate Plugin CSS', 'facebook-fanpage-import' ) . '</label>';
		echo '<div class="input">';
		echo '<input type="checkbox" value="yes" name="fbfpi_deactivate_css" id="fbfpi_deactivate_css"' . $checked . ' />';
		echo '</div>';
		echo '</div>';

		do_action( 'fbfpi_settings_form' );

		/**
		 * Import button
		 */
		if ( ! empty( $fanpage_id ) ) {
			if ( ! get_option( '_facebook_fanpage_import_next', false ) ) {
				echo ' <input type="submit" name="fbfpi_now" value="' . __( 'Import Now', 'facebook-fanpage-import' ) . '" class="button" /> ';
			} else {
				echo ' <input type="submit" name="fbfpi_next" value="' . __( 'Import Next', 'facebook-fanpage-import' ) . '" class="button" /> <input type="submit" name="fbfpi_stop" value="' . __( 'Reset Import', 'facebook-fanpage-import' ) . '" class="button" style="margin-left:10px;" /> ';
			}
			if( ! empty( $imported_until ) ) {
				echo '<div class="fbfpi-form-infotext">';
				echo sprintf( __( 'Imported entries until %s', 'facebook-fanpage-import' ), $imported_until );
				echo '</div>';
			}
		}

		/**
		 * Save Button
		 */
		submit_button();

		echo '</form>';
		echo '</div>';
		echo '</div>';
	}
}

$FacebookFanpageImportAdminSettings = new FacebookFanpageImportAdminSettings();
