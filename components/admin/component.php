<?php
/**
 * Facebook Fanpage Import Showdata Component.
 * This class initializes the component.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0-beta.7
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

class FacebookFanpageImportAdmin {
	var $name;

	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->name = get_class( $this );
		$this->includes();

		if ( 'status' == get_option( 'fbfpi_insert_post_type' ) ) {
			add_action( 'init', array( $this, 'custom_post_types' ), 11 );
			add_action( 'add_meta_boxes', array( $this, 'custom_meta_box' ) );
			add_action( 'save_post', array( $this, 'custom_meta_box_save' ) );
		}
	}

	/**
	 * Including needed Files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {
		require_once( dirname( __FILE__ ) . '/settings.php' );
	}

	/**
	 * Creates Custom Post Types
	 *
	 * @since 1.0.0
	 */
	public function custom_post_types() {
		$args_post_type = array(
			'labels'      => array(
				'name'          => __( 'Status Messages', 'fbfpi-locale' ),
				'singular_name' => __( 'Status Message', 'fbfpi-locale' )
			),
			'public'      => true,
			'has_archive' => true,
			'supports'    => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
			'rewrite'     => array(
				'slug'       => 'status-message',
				'with_front' => true
			)
		);

		register_post_type( 'status-message', $args_post_type );
	}
	
	/**
	 * Add Meta Box for Facebook Post infos
	 *
	 * @since 1.0.1
	 */
	public function custom_meta_box() {
		add_meta_box( 'facebook-post-info-meta-box', __( 'Facebook Post Infos', 'fbfpi-locale' ), array( $this, 'meta_box_output'), 'status-message', 'side', 'high' );
	}

	/**
	 * Output the Meta box on backoffice
	 *
	 * @since 1.0.1
	 */
	public function meta_box_output( $post ) {
		// create a nonce field
		wp_nonce_field( 'my_fbfpi_meta_box_nonce', 'fbfpi_meta_box_nonce' ); ?>

		<p>
			<label for="fbfpi_facebook_post_url"><?php _e( 'Post URL', 'fbfpi-locale' ); ?>:</label>
			<input type="text" name="fbfpi_facebook_post_url" id="fbfpi_facebook_post_url" value="<?php echo $this->get_custom_field( 'fbfpi_facebook_post_url' ); ?>" />
		</p>

		<?php
	}

	/**
	 * Save the Meta box value
	 *
	 * @since 1.0.1
	 */
	public function custom_meta_box_save( $post_id ) {
		// Stop the script when doing autosave
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// Verify the nonce. If insn't there, stop the script
		if( !isset( $_POST['fbfpi_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['fbfpi_meta_box_nonce'], 'my_fbfpi_meta_box_nonce' ) ) return;

		// Stop the script if the user does not have edit permissions
		if( !current_user_can( 'edit_post', get_the_id() ) ) return;

		// Save the textfield
		if( isset( $_POST['fbfpi_facebook_post_url'] ) )
			update_post_meta( $post_id, 'fbfpi_facebook_post_url', esc_attr( $_POST['fbfpi_facebook_post_url'] ) );
	}

	/**
	 * Return the custom field selected
	 *
	 * @since 1.0.1
	 */
	private function get_custom_field( $value ) {
		global $post;

		$custom_field = get_post_meta( $post->ID, $value, true );
		if ( !empty( $custom_field ) )
			return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );

		return false;
	}
}

$FacebookFanpageImportAdmin = new FacebookFanpageImportAdmin();
