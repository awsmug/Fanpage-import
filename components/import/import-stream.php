<?php
/**
 * Facebook Fanpage Import Component.
 * Importing Facebook entries
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

class FacebookFanpageImportFacebookStream {
	/**
	 * @var FacebookFanpageImportFacebookStream
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $name;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $app_id;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $app_secret;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $page_id;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $stream_language;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $update_interval;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $update_num;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $post_type;

	/**
	 * @var $string
	 * @since 1.0.0
	 */
	var $post_status;

	/**
	 * @var string
	 * @since 1.0.0
	 */
	var $post_format;

	/**
	 * @var int
	 * @since 1.0.0
	 */
	var $term_id;

	/**
	 * @var FacebookFanpageConnect
	 * @since 1.0.0
	 */
	var $fpc;

	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->page_id         = get_option( 'fbfpi_fanpage_id' );
		$this->stream_language = get_option( 'fbfpi_fanpage_stream_language' );
		$this->update_interval = get_option( 'fbfpi_import_interval' );
		$this->update_num      = get_option( 'fbfpi_import_num' );
		$this->link_target     = get_option( 'fbfpi_insert_link_target' );
		$this->post_type       = get_option( 'fbfpi_insert_post_type' );
		$this->post_status     = get_option( 'fbfpi_insert_post_status' );
		$this->post_format     = get_option( 'fbfpi_insert_post_format' );
		$this->author_id       = get_option( 'fbfpi_insert_user_id' );
		$this->term_id         = get_option( 'fbfpi_insert_term_id' );

		$this->fpc = new FacebookFanpageConnect( $this->page_id, '', get_locale() );

		if ( '' == $this->page_id ) {
			FacebookFanpageImport::notice( sprintf( __( '<a href="%s">Fanpage ID have to be provided.</a>', 'facebook-fanpage-import' ), admin_url( 'tools.php?page=fanpage-import/components/admin/settings.php' ) ), 'error' );
		}

		if ( '' == $this->stream_language ) {
			$this->stream_language = 'en_US';
		}

		if ( '' == $this->update_interval ) {
			$this->update_interval = 'hourly';
		}

		if ( '' == $this->update_num ) {
			$this->update_num = false;
		}

		if ( 'status' == $this->post_type ) {
			$this->post_type = 'status-message';
		} else {
			$this->post_type = 'post';
		}

		if ( '' == $this->post_status ) {
			$this->post_status = 'draft';
		}

		if ( '' == $this->post_format ) {
			$this->post_format = 'none';
		}

		// Schedule import if interval set
		if ( $this->update_interval != 'never' ) {
			if ( ! wp_next_scheduled( 'fanpage_import' ) ) {
				wp_schedule_event( time(), $this->update_interval, 'fanpage_import' );
			}
		} else {
			// get next scheduled event
			$timestamp = wp_next_scheduled( 'fanpage_import' );

			// unschedule it if there is one
			if ( $timestamp !== false ) {
				wp_unschedule_event( $timestamp, 'fanpage_import' );
			}

			// it's not clear whether wp_unschedule_event() clears everything,
			// so remove existing scheduled hook as well
			wp_clear_scheduled_hook( 'fanpage_import' );
		}

		add_action( 'fanpage_import', array( $this, 'import' ) );
	}

	/**
	 * Instance
	 *
	 * @return FacebookFanpageImportFacebookStream|Singleton
	 * @since 1.1.0
	 */
	public static function instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Importing Stream
	 *
	 * @param $param
	 *
	 * @since 1.0.0
	 */
	public function import() {
		set_time_limit( 240 );

		$fanpage = $this->get_fanpage();
		$entries = $this->get_entries();

		$i = 0;

		if ( count( $entries ) > 0 ) {
			$skip_existing_count  = 0;
			$skip_unknown_count   = 0;
			$skip_without_message = 0;

			foreach ( $entries AS $entry ) {

				if ( $this->entry_exists( $entry->id ) ) // If entry already exists
				{
					$skip_existing_count ++;
					continue;
				}

				$entry = $this->fpc->get_id( $entry->id, array( 'name', 'message', 'story', 'caption', 'description', 'full_picture', 'object_id', 'from', 'link', 'created_time', 'type' ) );

				if( ! in_array( $entry->type, array( 'link', 'photo', 'video', 'status', 'event' ) ) ) {
					$skip_unknown_count ++;
					FacebookFanpageImport::log( 'Skipped:' .chr(13) . print_r( $entry, true ) );
					continue;
				}

				$i ++;

				$post_title   = $this->get_post_title( $entry );
				$post_excerpt = $this->get_post_excerpt( $entry );
				$picture_url  = $this->get_post_picture_url( $entry );
				$post_date    = $this->get_post_date( $entry );
				$tags         = $this->get_post_tags( $entry );

				$entry->message = $this->replace_urls_by_links( $entry->message );

				$post_id = $this->create_post( $post_title, $post_excerpt, $post_date );

				$post = get_post( $post_id );

				if ( count( $tags ) > 0 ) {
					wp_set_post_tags( $post_id, $tags );
				}

				$attach_id = '';
				if ( ! empty( $picture_url ) ) {
					$attach_id = $this->fetch_picture( $picture_url, $post_id, $post_date );
				}

				// Post content
				switch ( $entry->type ) {

					case 'link':
						$post->post_content = $this->get_link_content( $entry, $attach_id );
						break;

					case 'status':
						$post->post_content = $entry->message;

						if ( ! empty( $attach_id ) ) {
							set_post_thumbnail( $post_id, $attach_id );
						}

						break;

					case 'photo':
						$picture_url = $this->fpc->get_photo_by_object( $entry->object_id );

						if ( ! empty( $picture_url ) ) {
							$attach_id = $this->fetch_picture( $picture_url, $post_id, $post_date );
						}

						$post->post_content = $this->get_photo_content( $entry, $attach_id );

						if ( ! empty( $attach_id ) ) {
							set_post_thumbnail( $post_id, $attach_id );
						}

						break;

					case 'video':
						$post->post_content = $this->get_video_content( $entry, $attach_id );

						if ( ! empty( $attach_id ) ) {
							set_post_thumbnail( $post_id, $attach_id );
						}

						break;

					case 'event':
						$post->post_content = $this->get_event_content( $entry, $attach_id );

						if ( ! empty( $attach_id ) ) {
							set_post_thumbnail( $post_id, $attach_id );
						}

						break;

					default:
						break;
				}

				wp_update_post( $post );
				FacebookFanpageImport::log( 'Imported "' . $entry->type . '" with the title "' . $post_title . '" - post ID #' . $post_id . ' as post type "' . $this->post_type . '"');

				// Adding terms
				if( 'post' ===  $this->post_type ) {
					if ( 'none' !== $this->term_id ) {
						$term_ids           = array( intval( $this->term_id ) );
						$term_taxonomy_ids = wp_set_object_terms( $post->ID, $term_ids, 'category' );
						if ( is_wp_error( $term_taxonomy_ids ) ) {
							FacebookFanpageImport::log( 'Error: Term could not be set. ' . count(  $this->term_id ). ' entries' );
						} else {
							FacebookFanpageImport::log( 'Added term #' . $this->term_id . ' to post #. ' . $post_id . ' entries' );
						}
					}
				}

				// Updating post meta
				$ids           = explode( '_', $entry->id );
				$pure_entry_id = $ids[ 1 ];
				$entry_url     = $fanpage->link . '/posts/' . $pure_entry_id;

				if ( property_exists( $entry, 'id' ) ) {
					update_post_meta( $post_id, '_fbfpi_entry_id', $entry->id );
					update_post_meta( $post_id, 'fbfpi_facebook_post_url', 'https://www.facebook.com/' . $entry->id );
				}
				if ( property_exists( $entry, 'message' ) ) {
					update_post_meta( $post_id, '_fbfpi_message', $entry->message );
				}
				if ( property_exists( $entry, 'description' ) ) {
					update_post_meta( $post_id, '_fbfpi_description', $entry->description );
				}

				update_post_meta( $post_id, '_fbfpi_image_url', $picture_url );
				update_post_meta( $post_id, '_fbfpi_fanpage_id', $this->page_id );
				update_post_meta( $post_id, '_fbfpi_fanpage_name', $fanpage->name );
				update_post_meta( $post_id, '_fbfpi_fanpage_link', $fanpage->link );
				update_post_meta( $post_id, '_fbfpi_entry_url', $entry_url );
				update_post_meta( $post_id, '_fbfpi_type', $entry->type );

				if( 'posts' ===  $this->post_type ) {
					if ( 'none' != $this->post_format ) {
						set_post_format( $post_id, $this->post_format );
					}
				}

				/**
				 * Allow plugins to do additional processing.
				 *
				 * @param WP_Post $post  The post object
				 * @param object  $entry The Facebook entry object
				 */
				do_action( 'fbfpi_entry_created', $post, $entry );
			}

			FacebookFanpageImport::log( 'Found ' . count( $entries ). ' entries' );
			FacebookFanpageImport::log( 'Imported ' . $i. ' entries' );

			FacebookFanpageImport::notice( sprintf( __( '%d entries have been found.', 'facebook-fanpage-import' ), count( $entries ) ) );
			FacebookFanpageImport::notice( sprintf( __( '%d entries have been imported.', 'facebook-fanpage-import' ), $i ) );

			if ( $skip_without_message > 0 ) {
				FacebookFanpageImport::notice( sprintf( __( '%d skipped because containing no message.', 'facebook-fanpage-import' ), $skip_without_message ), 'error' );
				FacebookFanpageImport::log( 'Skipped ' . $skip_without_message. ' because containing no message.' );
			}

			if ( $skip_existing_count > 0 ) {
				FacebookFanpageImport::notice( sprintf( __( '%d skipped because already existing.', 'facebook-fanpage-import' ), $skip_existing_count ), 'error' );
				FacebookFanpageImport::log( 'Skipped ' . $skip_existing_count. ' because already existing.' );
			}

			if ( $skip_unknown_count > 0 ) {
				FacebookFanpageImport::notice( sprintf( __( '%d skipped because entry type unknown.', 'facebook-fanpage-import' ), $skip_unknown_count ), 'error' );
				FacebookFanpageImport::log( 'Skipped ' . $skip_unknown_count. ' because entry type unknown.' );
			}
		}
	}

	/**
	 * Getting Fanpage data
	 *
	 * @return array|mixed|object|string
	 *
	 * @since 1.0.0
	 */
	private function get_fanpage(){
		return $this->fpc->get_page();
	}

	/**
	 * Getting Fanpage entries depending on $_POST variables
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	private function get_entries() {
		// get initial posts on first run or via schedule
		if ( ( isset( $_POST ) && array_key_exists( 'fbfpi_now', $_POST ) && '' != $_POST[ 'fbfpi_now' ] ) || doing_action( 'fanpage_import' ) ) {
			$entries = $this->fpc->get_posts( $this->update_num );
		}

		// get paged posts when selecting "next"
		if ( isset( $_POST ) && array_key_exists( 'fbfpi_next', $_POST ) && '' != $_POST[ 'fbfpi_next' ] ) {
			$url = get_option( '_facebook_fanpage_import_next', '' );
			if ( ! empty( $url ) ) {
				$entries = $this->fpc->get_posts_paged( $url );
			}
		}

		$paging = $this->fpc->get_paging();

		if ( is_object( $paging ) && property_exists( $paging, 'next' ) && ! empty( $paging->next ) ) {
			update_option( '_facebook_fanpage_import_next', $paging->next );
		} else {
			delete_option( '_facebook_fanpage_import_next' );
		}

		return $entries;
	}

	/**
	 * Checks if an entry exists
	 *
	 * @param $entry_id
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	private function entry_exists( $entry_id ) {
		global $wpdb;

		$sql        = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts AS p, $wpdb->postmeta AS m WHERE p.ID = m.post_id  AND p.post_type='%s' AND p.post_status <> 'trash' AND m.meta_key = '_fbfpi_entry_id'  AND m.meta_value = '%s'", $this->post_type, $entry_id );
		$post_count = $wpdb->get_var( $sql );

		if ( $post_count > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Getting post title
	 *
	 * @param $entry
	 *
	 * @return array|mixed|string|void
	 *
	 * @since 1.1.0
	 */
	private function get_post_title( $entry ) {
		$post_title = '';
		if ( ! property_exists( $entry, 'message' ) ) {
			if ( property_exists( $entry, 'story' ) && '' != $entry->story ) {
				$post_title     = $entry->story;
				$entry->message = '';
			} else {
				$post_title     = __( 'Untitled post', 'facebook-fanpage-import' );
				$entry->message = '';
			}
		} elseif ( property_exists( $entry, 'message' ) && '' != $entry->message ) {
			$post_title = $entry->message;
		} elseif ( property_exists( $entry, 'description' ) && '' != $entry->description && '' == $post_title ) {
			$post_title = $entry->description;
		}

		$post_title = $this->filter_title( $post_title );

		/**
		 * Allow overrides.
		 *
		 * @param string $post_title  The unfiltered title
		 * @param string $entry The entry
		 *
		 * @return string $title The filtered title
		 * @since 1.0.0
		 */
		$post_title = apply_filters( 'fbfpi_import_post_title', $post_title, $entry );

		return $post_title;
	}

	/**
	 * Filter title
	 *
	 * @param $string
	 *
	 * @return array|mixed
	 */
	private function filter_title( $string ) {
		$title = explode( ':', $string );
		$title = $title[ 0 ];

		$title = explode( '!', $title );
		$title = $title[ 0 ];

		$title = str_replace( '+', '', $title );

		$title = trim( $title );

		$desired_width = 50;

		if ( strlen( $title ) > $desired_width ) {
			$title = wordwrap( $title, $desired_width );
			$i     = strpos( $title, "\n" );
			if ( $i ) {
				$title = substr( $title, 0, $i );
			}
			$title = $title . ' ...';
		}

		/**
		 * Allow overrides.
		 *
		 * @param string $title  The filtered title
		 * @param string $string The unfiltered title
		 *
		 * @return string $title The filtered title
		 * @since 1.0.0
		 */
		return apply_filters( 'fbfpi_entry_title', $title, $string );
	}

	/**
	 * Getting post excerpt
	 *
	 * @param $entry
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_post_excerpt( $entry ) {
		$post_excerpt = '';
		if ( property_exists( $entry, 'message' ) ) {
			$post_excerpt = $entry->message;
		}

		/**
		 * Allow overrides.
		 *
		 * @param string $post_excerpt  The unfiltered Excerpt
		 * @param string $entry The entry
		 *
		 * @return string $title The filtered title
		 * @since 1.0.0
		 */
		$post_excerpt = apply_filters( 'fbfpi_import_post_excerpt', $post_excerpt, $entry );

		return $post_excerpt;
	}

	/**
	 * Getting post picture URL
	 *
	 * @param $entry
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_post_picture_url( $entry ) {
		$picture_url = '';
		if ( property_exists( $entry, 'full_picture' ) ) {
			$picture_url = $entry->full_picture;
		}

		/**
		 * Allow overrides.
		 *
		 * @param string $picture_url  The unfiltered picture URL
		 * @param string $entry The entry
		 *
		 * @return string $title The filtered picture URL
		 * @since 1.0.0
		 */
		$picture_url = apply_filters( 'fbfpi_import_post_picture_url', $picture_url, $entry );

		return $picture_url;
	}

	/**
	 * Getting post date
	 *
	 * @param $entry
	 *
	 * @return bool|mixed|string|void
	 * @since 1.0.0
	 */
	private function get_post_date( $entry ) {
		$date = date( 'Y-m-d H:i:s', strtotime( $entry->created_time ) );

		/**
		 * Allow overrides.
		 *
		 * @param array $date  The unfiltered post date
		 * @param string $entry The entry
		 *
		 * @return string $title The filtered post date
		 * @since 1.0.0
		 */
		$date = apply_filters( 'fbfpi_import_post_date', $date, $entry );

		return $date;
	}

	/**
	 * Getting post tags
	 *
	 * @param $entry
	 *
	 * @return array|mixed|void
	 * @since 1.0.0
	 */
	private function get_post_tags( $entry ) {
		preg_match_all( "/(#\w+)/", $entry->message, $found_hash_tags );
		$found_hash_tags = $found_hash_tags[ 1 ];

		$tags = array();
		foreach ( $found_hash_tags AS $hash_tag ) {
			$tags[] = substr( $hash_tag, 1, strlen( $hash_tag ) );
		}

		/**
		 * Allow overrides.
		 *
		 * @param array $tags  The unfiltered post tags
		 * @param string $entry The entry
		 *
		 * @return string $tags The filtered post tags
		 * @since 1.0.0
		 */
		$tags = apply_filters( 'fbfpi_import_post_tags', $tags, $entry );

		return $tags;
	}

	/**
	 * Replacing URLs with HTML Links
	 *
	 * @param $content
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	private function replace_urls_by_links( $content ) {
		$content = preg_replace( '@(https?://([-\w.]+[-\w])+(:\d+)?(/([\w-.~:/?#\[\]\@!$&\'()*+,;=%]*)?)?)@', '<a href="$1" target="_blank">$1</a>', $content );

		return $content;
	}

	/**
	 * Inserting raw post without content
	 *
	 * @param $post_title
	 * @param $post_excerpt
	 * @param $post_date
	 *
	 * @return int|WP_Error
	 * @since 1.0.0
	 */
	private function create_post( $post_title, $post_excerpt, $post_date ) {
		$post = array(
			'comment_status' => 'closed', // 'closed' means no comments.
			'ping_status'    => 'open', // 'closed' means pingbacks or trackbacks turned off
			'post_date'      => $post_date,
			'post_status'    => $this->post_status,
			'post_title'     => $post_title,
			'post_type'      => $this->post_type,
			'post_excerpt'   => $post_excerpt,
			'post_author'    => $this->author_id
		);

		return wp_insert_post( $post );
	}

	/**
	 * Fetching picture
	 *
	 * @param $picture_url
	 * @param $post_id
	 *
	 * @return int
	 * @since 1.0.0
	 */
	private function fetch_picture( $picture_url, $post_id, $post_date ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$picture = wp_remote_get( $picture_url );
		$type    = wp_remote_retrieve_header( $picture, 'content-type' );

		switch( $type ) {
			case 'image/jpeg':
				$suffix = '.jpg';
				break;
			case 'image/png':
				$suffix = '.png';
				break;
			case 'image/gif':
				$suffix = '.gif';
				break;
			default:
				$suffix = '';
				break;
		}

		$filename = sanitize_file_name( substr( md5( mt_rand() ), 0, 8 ) . $suffix );

		$mirror  = wp_upload_bits( $filename, '', wp_remote_retrieve_body( $picture ), date('Y/m', strtotime( $post_date ) ) );

		$attachment = array(
			'post_title'     => $filename,
			'post_mime_type' => $type,
			'post_date'      => $post_date,
			'post_status'    => 'publish'
		);

		$picture_id = wp_insert_attachment( $attachment, $mirror[ 'file' ], $post_id );

		$attach_data = wp_generate_attachment_metadata( $picture_id, $mirror[ 'file' ] );
		wp_update_attachment_metadata( $picture_id, $attach_data );

		return $picture_id;
	}

	/**
	 * Get link content
	 *
	 * @param $entry
	 * @param $attach_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_link_content( $entry, $attach_id ) {
		$attach_url = wp_get_attachment_url( $attach_id );

		if ( property_exists( $entry, 'caption' ) ) {
			$copyright = '&copy; ' . $entry->caption . ' - ' . $entry->name;
		} else {
			$copyright = '&copy; ' . $entry->name;
		}

		$content = $entry->message;
		$content .= '<div class="fbfpi_link">';
		if ( '' != $attach_url ) {
			$content .= '<div class="fbfpi_image">';
			$content .= '<a href="' . $entry->link . '" target="' . $this->link_target . '" title="' . $copyright . '"><img src="' . $attach_url . '" title="' . $copyright . '"></a>';
			$content .= '</div>';
		}
		$content .= '<div class="fbfpi_text">';
		$content .= '<h4><a href="' . $entry->link . '" target="' . $this->link_target . '" title="' . $copyright . '">' . $entry->name . '</a></h4>';

		if ( property_exists( $entry, 'caption' ) ) {
			$content .= '<p><small>' . $entry->caption . '</small><br /></p>';
		}

		if ( property_exists( $entry, 'description' ) ) {
			$content .= '<p>' . $entry->description . '</p>';
		}
		$content .= '</div>';
		$content .= '</div>';

		/**
		 * Allow overrides.
		 *
		 * @param string  $content   The constructed content
		 * @param object  $entry     The entry object
		 * @param integer $attach_id The numeric ID of the attachment
		 *
		 * @return string $content The constructed content
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'fbfpi_entry_link', $content, $entry, $attach_id );
	}

	/**
	 * Get photo content
	 *
	 * @param $entry
	 * @param $attach_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_photo_content( $entry, $attach_id ) {
		$post[ 'title' ] = '';

		$template_vars['link_target'] = $this->link_target;

		$template_vars['text'] = $entry->message;

		$template_vars['photo_src'] = wp_get_attachment_url( $attach_id );
		$template_vars['photo_url'] = $entry->link;
		$template_vars['photo_title'] = '';
		$template_vars['photo_text']  = '';

		if( ! empty( $entry->title ) && ! empty( $entry->description ) ){
			$template_vars['photo_title'] = $entry->title;
			$template_vars['photo_text']  = $entry->description;
		}

		/**
		 * Filter for adding own variables
		 *
		 * @param array   $template_vars Template variables unfiltered
		 * @param stdObject $entry Facebook Entry object
		 *
		 * @return array $template_vars Template variables filtered
		 * @since 1.0.0
		 */
		$template_vars = apply_filters( 'fbfpi_entry_photo_vars', $template_vars, $entry );

		extract( $template_vars );

		$template_file = locate_fbfpi_template( 'photo.php' );

		ob_start();
		include ( $template_file );
		$content = ob_get_clean();

		$post[ 'content' ] = $content;

		/**
		 * Allow overrides.
		 *
		 * @param string  $content   The constructed content
		 * @param object  $entry     The entry object
		 * @param integer $attach_id The numeric ID of the attachment
		 *
		 * @return string $content The constructed content
		 * @since 1.0.0
		 */
		return apply_filters( 'fbfpi_entry_photo', $content, $entry, $attach_id );
	}

	/**
	 * Get video content
	 *
	 * @param $entry
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_video_content( $entry ) {
		$content = '<div class="fbfpi_video">';

		// support JetPack's "facebook" shortcode for Facebook videos
		if( false !== strpos( $entry->link, 'www.facebook.com' ) ) {
			$content .= '[facebook_video url ="' . $entry->link . '"]';
		} else {
			$content .= '[embed]' . $entry->link . '[/embed]';
		}
		$content .= '<div class="fbfpi_text">';

		if ( property_exists( $entry, 'message' ) ) {
			$content .= '<p>' . $entry->message . '</p>';
		}
		$content .= '</div>';
		$content .= '</div>';

		/**
		 * Allow overrides.
		 *
		 * @param string $content The constructed content
		 * @param object $entry   The entry object
		 *
		 * @return string $content The constructed content
		 * @since 1.1.0
		 */
		return apply_filters( 'fbfpi_entry_video', $content, $entry );
	}

	/**
	 * Get link content
	 *
	 * @param $entry
	 * @param $attach_id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_event_content( $entry, $attach_id ) {
		$attach_url = wp_get_attachment_url( $attach_id );

		if ( property_exists( $entry, 'caption' ) ) {
			$copyright = '&copy; ' . $entry->caption;
		} else {
			$copyright = '&copy; ' . $entry->name;
		}

		$content  = $entry->story;

		$content .= '<div class="fbfpi_link">';
		if ( '' != $attach_url ) {
			$content .= '<div class="fbfpi_image">';
			$content .= '<a href="' . $entry->link . '" target="' . $this->link_target . '" title="' . $copyright . '"><img src="' . $attach_url . '" title="' . $copyright . '"></a>';
			$content .= '</div>';
		}
		$content .= '<div class="fbfpi_text">';
		$content .= '<h4><a href="' . $entry->link . '" target="' . $this->link_target . '" title="' . $copyright . '">' . $entry->name . '</a></h4>';

		if ( property_exists( $entry, 'caption' ) ) {
			$content .= '<p><small>' . $entry->caption . '</small><br /></p>';
		}

		if ( property_exists( $entry, 'description' ) ) {
			$content .= '<p>' . $entry->description . '</p>';
		}
		$content .= '</div>';
		$content .= '</div>';

		/**
		 * Allow overrides.
		 *
		 * @param string  $content   The constructed content
		 * @param object  $entry     The entry object
		 * @param integer $attach_id The numeric ID of the attachment
		 *
		 * @return string $content The constructed content
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'fbfpi_entry_link', $content, $entry, $attach_id );
	}

	/**
	 * Stopping import
	 *
	 * @param $value
	 *
	 * @return mixed
	 * @since 1.1.0
	 */
	public function stop_import() {
		$value = delete_option( '_facebook_fanpage_import_next' );
		return $value;
	}
}

FacebookFanpageImportFacebookStream::instance();
