<?php
/**
 * Facebook Fanpage Import Showdata Shortcodes Component.
 * This class initializes the component.
 *
 * @author  mahype, awesome.ug <very@awesome.ug>
 * @package Facebook Fanpage Import
 * @version 1.0.0-beta.5
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

class FacebookFanpageImportShowdataShortcodes {
	var $name;

	/**
	 * Initializes the Component.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$this->name = get_class( $this );

		add_shortcode( 'fanpagestream', array( $this, 'show_stream' ) );
		add_shortcode( 'facebook_video', array( $this, 'facebook_video' ) );
	}

	/**
	 * Show stream sortcode
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function show_stream( $atts ) {
		global $paged, $wp_query;

		extract( shortcode_atts( array( 'entries' => (int) get_option( 'posts_per_page' ), ), $atts ) );

		$args = array(
			'posts_per_page' => $entries,
			'orderby'        => 'post_date',
			'post_type'      => 'fanpage-entries',
			'paged'          => fbfpi_get_url_var( 'page' )
		);

		$wp_query = new WP_Query( $args );

		$paged_old = $paged;
		$paged     = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$content = '<div class="fbfpi_stream">';
		if ( $wp_query->have_posts() ) {
			while ( $wp_query->have_posts() ){
				$wp_query->the_post();

				// setup_postdata( $post );
				$post_id = $wp_query->post->ID;

				$fanpage_id   = get_post_meta( $post_id, '_fbfpi_fanpage_id', true );
				$fanpage_name = get_post_meta( $post_id, '_fbfpi_fanpage_name', true );
				$fanpage_link = get_post_meta( $post_id, '_fbfpi_fanpage_link', true );

				$id          = get_post_meta( $post_id, '_fbfpi_post_id', true );
				$entry_id    = get_post_meta( $post_id, '_fbfpi_entry_id', true );
				$message     = nl2br( get_post_meta( $post_id, '_fbfpi_message', true ) );
				$description = get_post_meta( $post_id, '_fbfpi_description', true );
				$permalink   = get_post_meta( $post_id, '_fbfpi_permalink', true );
				$type        = get_post_meta( $post_id, '_fbfpi_type', true );

				$link_target = get_option( 'fbfpi_insert_link_target' );

				switch ( $type ) {
					case 'link':
						$action = sprintf( __( '<a href="%s" target="%s">%s</a> shared a link.', 'facebook-fanpage-import' ), $fanpage_link, $link_target, $fanpage_name );
						break;
					case 'photo':
						$action = sprintf( __( '<a href="%s" target="%s">%s</a> shared a photo.', 'facebook-fanpage-import' ), $fanpage_link, $link_target, $fanpage_name );
						break;
					default:
						if ( '' != $description ) {
							$action = $description;
						} else {
							$action = sprintf( __( '<a href="%s" target="%s">%s</a> shared a status.', 'facebook-fanpage-import' ), $fanpage_link, $link_target, $fanpage_name );
						}
						break;
				}

				$has_attachment = get_post_meta( $post_id, 'has_attachment', true );

				/*
				 * Writing Entry
				 */
				$content .= '<div class="fbfpi_entry">';

				if ( $message ) {
					$content .= '<p>' . $message . '</p>';
				} else {
					$content .= '<p>' . $action . '</p>';
				}

				// attachment Data
				if ( $has_attachment ) {

					$picture = '';

					$attachment_name        = get_post_meta( $post_id, 'attachment_name', true );
					$attachment_description = nl2br( substr( get_post_meta( $post_id, 'attachment_description', true ), 0, 200 ) );
					$attachment_caption     = get_post_meta( $post_id, 'attachment_caption', true );
					$attachment_href        = get_post_meta( $post_id, 'attachment_href', true );
					$attachment_src         = get_post_meta( $post_id, 'attachment_src', true );

					if ( 'event' == $type ) {
						$start_time         = get_post_meta( $post_id, 'attachment_start_time', true );
						$location           = get_post_meta( $post_id, 'attachment_location', true );
						$attachment_caption = sprintf( __( 'Start %s %s.', 'facebook-fanpage-import' ), date_i18n( get_option( 'date_format' ), strtotime( $start_time ) ), date_i18n( get_option( 'time_format' ), strtotime( $start_time ) ) );
						$attachment_caption .= '<br />' . $location;
					}

					if ( is_array( $attachment_src ) ) {
						$attachment_src = $attachment_src[ 0 ];
					}

					echo '<pre>';
					print_r( $attachment_src );
					echo '</pre>';

					echo '<img src="' . $attachment_src . '">';

					$attachment_suffix = strtolower( substr( $attachment_src, strlen( $attachment_src ) - 3, strlen( $attachment_src ) ) );

					if ( 'jpg' == $attachment_suffix || 'gif' == $attachment_suffix || 'png' == $attachment_suffix ) {
						$picture = str_replace( '_s.' . $attachment_suffix, '_n.' . $attachment_suffix, $attachment_src );
					}

					$content .= '<div class="fbfpi_content">';

					// Picture
					if ( $picture ) {
						if ( '' != $attachment_name || '' != $attachment_description ) {
							$content .= '<div class="fbfpi_content_picture fbfpi_content_picture_small">';
						} else {
							$content .= '<div class="fbfpi_content_picture fbfpi_content_picture_fullsize">';
						}

						if ( $permalink ) {
							$content .= '<a href="' . $permalink . '" class="fbfp_picture" target="' . $link_target . '"><img src="' . $picture . '" /></a>';
						} else {
							$content .= '<img src="' . $picture . '" />';
						}

						$content .= '</div>';
					}

					if ( '' != $attachment_name || '' != $attachment_description ) {
						// Content
						$content .= '<div class="fbfpi_content_text fbfpi_link_content_text">';

						if ( $attachment_href && '' != $attachment_name ) {
							$content .= '<h4><a href="' . $permalink . '" title="' . sprintf( __( 'Link to: %s', 'facebook-fanpage-import' ), $attachment_name ) . '" target="' . $link_target . '">' . $attachment_name . '</a></h4>';
						} elseif ( '' != $attachment_name ) {
							$content .= '<h4>' . $attachment_name . '</h4>';
						}

						if ( '' != $attachment_description ) {
							$content .= '<p>' . $attachment_description . ' ...</p>';
						}

						if ( '' != $attachment_caption && $attachment_href && '' != $attachment_name ) {
							$content .= '<small><a href="' . $permalink . '" title="' . sprintf( __( 'Link to: %s', 'facebook-fanpage-import' ), $attachment_name ) . '" target="' . $link_target . '">' . $attachment_caption . '</a></small>';
						} elseif ( '' != $attachment_caption ) {
							$content .= '<small>' . $attachment_caption . '</small>';
						}

						$content .= '</div>';
					}

					$content .= '<div class="fbfpi_clear"></div>';
					$content .= '</div>';
				}

				$content .= '<div class="fbfpi_clear"></div>';
				$content .= '</div>';
			}

			$content .= '<div id="nav-below" class="navigation">';
			$content .= '<div class="nav-previous">' . get_next_posts_link( __( '<span class="meta-nav">&larr;</span>Older entries', 'facebook-fanpage-import' ) ) . '</div>';
			$content .= '<div class="nav-next">' . get_previous_posts_link( __( 'Newer entries <span class="meta-nav">&rarr;</span>', 'facebook-fanpage-import' ) ) . '</div>';
			$content .= '<div class="fbfpi_clear"></div></div>';

			$content .= '</div>';
		}
		$paged = $paged_old;
		wp_reset_query();

		return $content;
	}

	public function facebook_video( $atts ) {
		if ( empty( $atts['url'] ) )
			return;

		$content = sprintf( '<div class="fb-video" data-allowfullscreen="true" data-href="%s"></div>', esc_url( $atts['url'] ) );

		return $content;
	}
}

$FacebookFanpageImportShowdataShortcodes = new FacebookFanpageImportShowdataShortcodes();
