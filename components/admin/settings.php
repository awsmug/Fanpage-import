<?php
/**
 * Facebook Fanpage Import Admin Component.
 *
 * This class initializes the component.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0
 * @since   1.0.0
 * @license GPL 2
 *
 * Copyright 2016 Awesome UG (very@awesome.ug)
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

class FacebookFanpageImportAdminSettings
{
	var $name;
	var $errors = array();
	var $notices = array();

	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	function __construct()
	{
		$this->name = get_class( $this );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		/*if( '' != skip\value( 'fbfpi_settings', 'app_id' ) && '' != skip\value( 'fbfpi_settings', 'app_secret' ) && '' != skip\value( 'fbfpi_settings', 'page_id' ) )
		{
			$this->test_con();
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}*/

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Testing Connection to Facebook API
	 *
	 * @todo Adding functionality
	 */
	public function test_con()
	{
	}

	/**
	 * Adds the Admin menu.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu()
	{
		add_submenu_page( 'tools.php', __( 'Facebook Fanpage Import Settings', 'facebook-fanpage-import' ), __( 'Fanpage Import', 'facebook-fanpage-import' ), 'activate_plugins', 'Component' . $this->name, array(
			$this,
			'admin_page'
		) );
	}

	/**
	 * Register Settings
	 */
	public function register_settings() {

	}

	/**
	 * Content of the admin page.
	 *
	 * @since 1.0.0
	 */
	public function admin_page()
	{
		$fanpage_id                 = get_option( 'fbfpi_fanpage_id' );
		$fanpage_stream_language    = get_option( 'fbfpi_fanpage_stream_language' );
		$import_interval            = get_option( 'fbfpi_import_interval' );
		$import_num                 = get_option( 'fbfpi_import_num' );
		$insert_post_type           = get_option( 'fbfpi_insert_post_type' );
		$insert_term_id             = get_option( 'fbfpi_insert_term_id' );
		$insert_user_id             = get_option( 'fbfpi_insert_user_id' );
		$insert_post_status         = get_option( 'fbfpi_insert_post_status' );
		$insert_link_target         = get_option( 'fbfpi_insert_link_target' );
		$insert_post_format         = get_option( 'fbfpi_insert_post_format' );
		$deactivate_css             = get_option( 'fbfpi_deactivate_css' );

		echo '<div class="wrap">';

		echo '<div id="icon-options-general" class="icon32 icon32-posts-post"></div>';
		echo '<h2>' . __( 'Facebook Fanpage Import', 'facebook-fanpage-import' ) . '</h2>';
		echo '<p>' . __( 'Just put in your Fanpage ID and start importing.', 'facebook-fanpage-import' ) . '</p>';

		echo '<div class="fbfpi-form">';
		echo '<form method="post" action="options.php">';
		settings_fields( 'fbfpi-options' );
		do_settings_sections( 'fbfpi-options' );

		/**
		 * Fanpage ID
		 */
		 */
		echo '<div class="fbfpi-form-field">';
		echo '<label for="fanpage-id">' . __( 'Page ID', 'facebook-fanpage-import' ) . '</label>';
		echo '<input type="text" name="fanpage_id" id="fanpage_id" value="' . $fanpage_id . '" />'
		echo '</div>';

		/**
		 * Select stream languages
		 */
		$available_languages = get_available_languages();

		if( !in_array( 'en_US', $available_languages ) )
		{
			$available_languages[] = 'en_US';
		}

		foreach( $available_languages AS $language )
		{
			$select_languages[] = array( 'value' => $language );
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fanpage-stream-language">' . __( 'Facebook Language', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_stream_language" id="fanpage-stream-language">';
		foreach( $select_languages AS $language )
		{
			$selected = '';
			if( $language === $fanpage_stream_language )
				$selected = ' selected="selected"';

			echo '<option value="' . $language. '"' . $selected . '>' . $language . '</option>';
		}
		echo '</select>';
		echo '</div>';

		/**
		 * Import WP Cron settings
		 */
		$select_schedules = array( array( 'label' => __( 'Never', 'facebook-fanpage-import' ), 'value' => 'never' ) );
		$schedules = wp_get_schedules(); // Getting WordPress schedules
		foreach( $schedules AS $key => $schedule )
		{
			$select_schedules[] = array( 'label' => $schedule[ 'display' ], 'value' => $key );
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fanpage-import-interval">' . __( 'Import Interval', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_import_interval" id="fanpage-import-interval">';
		foreach( $select_schedules AS $schedule )
		{
			$selected = '';
			if( $import_interval === $schedule )
				$selected = ' selected="selected"';

			echo '<option value="' . $schedule. '"' . $selected . '>' . $schedule . '</option>';
		}
		echo '</select>';
		echo '</div>';

		/**
		 * Num of entries to import
		 */
		$import_num_values = apply_filters( 'fbfpi_num_values', array( 5,10,25,50,100,250 ) );

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fanpage-import-interval">' . __( 'Entries to import', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_import_interval" id="fanpage-import-interval">';
		foreach( $import_num_values AS $num )
		{
			$selected = '';
			if( $import_num === $num )
				$selected = ' selected="selected"';

			echo '<option value="' . $num. '"' . $selected . '>' . $num . '</option>';
		}
		echo '</select>';
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
		echo '<label for="fanpage-insert-post-type">' . __( 'Insert Messages as', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_insert_post_type" id="fanpage-insert-post-type">';
		foreach( $insert_post_types AS $post_type )
		{
			$selected = '';
			if( $insert_post_type === $post_type )
				$selected = ' selected="selected"';

			echo '<option value="' . $post_type. '"' . $selected . '>' . $post_type . '</option>';
		}
		echo '</select>';
		echo '</div>';

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
		foreach( $terms AS $term ) {
			$insert_post_terms[] = array(
				'value' => $term->term_id,
				'label' => $term->name,
			);
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fanpage-insert-term-id">' . __( 'Categorise Messages as', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_insert_term_id" id="fanpage-insert-term-id">';
		foreach( $insert_post_terms AS $term )
		{
			$selected = '';
			if( $insert_term_id === $term[ 'value' ] )
				$selected = ' selected="selected"';

			echo '<option value="' . $term[ 'value' ] . '"' . $selected . '>' . $term[ 'label' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';

		/**
		 * Select importing User
		 */
		$users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
		$user_list = array();

		foreach( $users AS $user )
		{
			$user_list[] = array(
					'value' => $user->ID,
					'label' => $user->display_name
			);
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fanpage-insert-user-id">' . __( 'Inserting User', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_insert_user_id" id="fanpage-insert-user-id">';
		foreach( $user_list AS $user )
		{
			$selected = '';
			if( $insert_user_id === $user[ 'value' ] )
				$selected = ' selected="selected"';

			echo '<option value="' . $user[ 'value' ] . '"' . $selected . '>' . $user[ 'label' ] . '</option>';
		}
		echo '</select>';
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
		echo '<label for="fanpage-insert-post-status">' . __( 'Post status', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_insert_post_status" id="fanpage-insert-post-status">';
		foreach( $post_status_values AS $post_status_value )
		{
			$selected = '';
			if( $insert_post_status === $post_status_value[ 'value' ] )
				$selected = ' selected="selected"';

			echo '<option value="' . $post_status_value[ 'value' ] . '"' . $selected . '>' . $post_status_value[ 'label' ] . '</option>';
		}
		echo '</select>';
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
		echo '<label for="fanpage-insert-link-target">' . __( 'Open Links in', 'facebook-fanpage-import' ) . '</label>';
		echo '<select name="fanpage_insert_link_target" id="fanpage-insert-link-target">';
		foreach( $insert_link_target_values AS $insert_link_target_value )
		{
			$selected = '';
			if( $insert_link_target === $insert_link_target_value[ 'value' ] )
				$selected = ' selected="selected"';

			echo '<option value="' . $insert_link_target_value[ 'value' ] . '"' . $selected . '>' . $insert_link_target_value[ 'label' ] . '</option>';
		}
		echo '</select>';
		echo '</div>';

		/**
		 * Selecting post formats if existing
		 */
		if( current_theme_supports( 'post-formats' ) )
		{
			$post_formats = get_theme_support( 'post-formats' );

			if( FALSE != $post_formats )
			{
				$post_formats = $post_formats[ 0 ];
				$post_format_list = array();

				$insert_post_format_values[] = array(
						'value' => 'none',
						'label' => __( '-- None --', 'facebook-fanpage-import' )
				);

				foreach( $post_formats as $post_format )
				{
					$insert_post_format_values[] = array(
							'value' => $post_format,
							'label' => $post_format
					);
				}

				echo '<div class="fbfpi-form-field">';
				echo '<label for="fanpage-insert-post-format">' . __( 'Post format', 'facebook-fanpage-import' ) . '</label>';
				echo '<select name="fanpage_insert_post_format" id="fanpage-insert-post-format">';
				foreach( $insert_post_format_values AS $insert_post_format_value )
				{
					$selected = '';
					if( $insert_post_format === $insert_post_format_value[ 'value' ] )
						$selected = ' selected="selected"';

					echo '<option value="' . $insert_post_format_value[ 'value' ] . '"' . $selected . '>' . $insert_post_format_value[ 'label' ] . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}
		}

		if( 'yes' === $deactivate_css ) {
			$checked = ' checked="checked"';
		}

		echo '<div class="fbfpi-form-field">';
		echo '<label for="fanpage-insert-post-format">' . __( 'Deactivate Plugin CSS', 'facebook-fanpage-import' ) . '</label>';
		echo '<input type="checkbox" name="fanpage_insert_post_format" id="fanpage-insert-post-format"' . $checked . ' />';
		echo '</div>';

		do_action( 'fbfpi_settings_form' );

		/**
		 * Save Button
		 */
		submit_button();

		/**
		 * Import button
		 */
		if( ! empty( $fanpage_id ) )
		{
			if ( ! get_option( '_facebook_fanpage_import_next', false ) )
			{
				echo ' <input type="submit" name="bfpi_now" value="' . __( 'Import Now', 'facebook-fanpage-import' ) . '" class="button" style="margin-left:10px;" /> ';
			} else {
				echo ' <input type="submit" name="bfpi_next" value="' . __( 'Import Next', 'facebook-fanpage-import' ) . '" class="button" style="margin-left:10px;" /> <input type="submit" name="bfpi-stop" value="' . __( 'Stop', 'facebook-fanpage-import' ) . '" class="button" style="margin-left:10px;" /> ';
			}
		}
		echo '</form>'
		echo '</div>';
		echo '</div>';
	}

	public function admin_notices()
	{
		if( count( $this->errors ) > 0 )
		{
			foreach( $this->errors AS $error )
				echo '<div class="updated"><p>' . __( 'Facebook Fanpage Import', 'facebook-fanpage-import' ) . ': ' . $error . '</p></div>';
		}

		if( count( $this->notices ) > 0 )
		{
			foreach( $this->notices AS $notice )
			{
				echo '<div class="updated"><p>' . __( 'Facebook Fanpage Import', 'facebook-fanpage-import' ) . ': ' . $notice . '</p></div>';
			}
		}
	}
}

$FacebookFanpageImportAdminSettings = new FacebookFanpageImportAdminSettings();
