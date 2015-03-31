<?php
/*
 * Facebook Fanpage Import Showdata Shortcodes Component.
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

class FacebookFanpageImportShowdataShortcodes{
	var $name;
	
	/**
	 * Initializes the Component.
	 * @since 1.0.0
	 */
	function __construct() {
		$this->name = get_class( $this );
		
		add_shortcode( 'fanpagestream', array( $this, 'show_stream' ) );
	} // end constructor
	
	public function show_stream( $atts ){
		global $paged, $wp_query;
		
		extract( shortcode_atts( array(
			'entries' => (int) get_option( 'posts_per_page' ),
		), $atts ) );
		
		$args = array(
			'posts_per_page' => $entries,
			'orderby' => 'post_date',
			'post_type' => 'fanpage-entries',
			'paged' => fbfpi_get_url_var( 'page' )
		);
		
		$wp_query = new WP_Query( $args );
		
		$paged_old = $paged;
		$paged = ( get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$content = '<div class="fbfpi_stream">';
		if ($wp_query->have_posts()) : 
			while ( $wp_query->have_posts() ) :
				$wp_query->the_post();
				
			    // setup_postdata( $post );
			    $post_id = $wp_query->post->ID;
				
				$fanpage_id = get_post_meta( $post_id, 'fanpage_id', TRUE );
				$fanpage_name = get_post_meta( $post_id, 'fanpage_name', TRUE );
				$fanpage_link = get_post_meta( $post_id, 'fanpage_link', TRUE );
				
				$id = get_post_meta( $post_id, 'post_id', TRUE );
				$entry_id = get_post_meta( $post_id, 'entry_id', TRUE );
				$message = nl2br( get_post_meta( $post_id, 'message', TRUE ) );
				$description = get_post_meta( $post_id, 'description', TRUE );
				$permalink = get_post_meta( $post_id, 'permalink', TRUE );
				$type = get_post_meta( $post_id, 'type', TRUE );
				
				$link_target = skip_value( 'fbfpi_settings', 'link_target' );
				
				switch ( $type ) {
					case 'link':
						$action =  sprintf( __( '<a href="%s" target="%s">%s</a> shared a link.', 'fbfpi' ), $fanpage_link, $link_target, $fanpage_name );
						break;
					case 'photo':
						$action =  sprintf( __( '<a href="%s" target="%s">%s</a> shared a photo.', 'fbfpi' ), $fanpage_link, $link_target, $fanpage_name );
						break;
					default:
						if( '' != $description ) $action = $description;
						else $action =  sprintf( __( '<a href="%s" target="%s">%s</a> shared a status.', 'fbfpi' ), $fanpage_link, $link_target, $fanpage_name );
					break;
				}
				
				$has_attachment = get_post_meta( $post_id, 'has_attachment', TRUE );
				
				/*
				 * Writing Entry
				 */
				$content.= '<div class="fbfpi_entry">';
				
				if( $message )	$content.= '<p>' . $message . '</p>';
				else $content.= '<p>' . $action . '</p>';
				
					// attachment Data			
					if( $has_attachment ):
						
						$picture = '';
						
						$attachment_name = get_post_meta( $post_id, 'attachment_name', TRUE );
						$attachment_description = nl2br( substr( get_post_meta( $post_id, 'attachment_description', TRUE ), 0, 200 ) );
						$attachment_caption = get_post_meta( $post_id, 'attachment_caption', TRUE );
						$attachment_href = get_post_meta( $post_id, 'attachment_href', TRUE );
						$attachment_src = get_post_meta( $post_id, 'attachment_src', TRUE );
						
						if( 'event' == $type ):
							$start_time = get_post_meta( $post_id, 'attachment_start_time', TRUE );
							$location = get_post_meta( $post_id, 'attachment_location', TRUE );
							$attachment_caption = sprintf( __( 'Start %s %s.', 'fbfpi' ), date_i18n( get_option( 'date_format' ),  strtotime( $start_time ) ), date_i18n( get_option( 'time_format' ),  strtotime( $start_time ) ) );
							$attachment_caption.= '<br />' . $location;
						endif;
						
						if( is_array( $attachment_src ) )
							$attachment_src = $attachment_src[ 0 ];
						
						echo '<pre>';
						print_r( $attachment_src );
						echo '</pre>';
						
						echo '<img src="' . $attachment_src . '">';
						
						$attachment_suffix = strtolower( substr( $attachment_src, strlen( $attachment_src ) - 3, strlen( $attachment_src ) ) );
						
						if( 'jpg' == $attachment_suffix || 'gif' == $attachment_suffix || 'png' == $attachment_suffix ):
							$picture = str_replace( '_s.' . $attachment_suffix, '_n.' . $attachment_suffix, $attachment_src );
						endif;
						
						$content.= '<div class="fbfpi_content">';
						
							// Picture
							if( $picture ):
								if( '' != $attachment_name  || '' != $attachment_description )
									$content.= '<div class="fbfpi_content_picture fbfpi_content_picture_small">';
								else
									$content.= '<div class="fbfpi_content_picture fbfpi_content_picture_fullsize">';
								
								if( $permalink )
									$content.= '<a href="' . $permalink . '" class="fbfp_picture" target="' . $link_target . '"><img src="' . $picture . '" /></a>';
								else
									$content.= '<img src="' . $picture . '" />';
								
								$content.= '</div>';
							
							endif; 
							
							if( '' != $attachment_name  || '' != $attachment_description ):
								// Content
								$content.= '<div class="fbfpi_content_text fbfpi_link_content_text">';
								
								if( $attachment_href  && '' != $attachment_name )
									$content.= '<h4><a href="' . $permalink . '" title="' . sprintf( __( 'Link to: %s', 'fbfpi' ), $attachment_name ) .'" target="' . $link_target . '">' . $attachment_name . '</a></h4>';
								elseif( '' != $attachment_name )
									$content.= '<h4>' . $attachment_name . '</h4>';
								
								if( '' !=  $attachment_description )
									$content.= '<p>' . $attachment_description . ' ...</p>';#
									
								if( '' !=  $attachment_caption && $attachment_href  && '' != $attachment_name )
									$content.= '<small><a href="' . $permalink . '" title="' . sprintf( __( 'Link to: %s', 'fbfpi' ), $attachment_name ) .'" target="' . $link_target . '">' . $attachment_caption . '</a></small>';
								elseif ( '' !=  $attachment_caption )
									$content.= '<small>' . $attachment_caption . '</small>';
								
								$content.= '</div>';
							endif;
						
						$content.= '<div class="fbfpi_clear"></div>';
						$content.= '</div>';
						
					endif;
				
				$content.= '<div class="fbfpi_clear"></div>';
				$content.= '</div>';
				
			endwhile;

		
			$content.= '<div id="nav-below" class="navigation">';
				$content.= '<div class="nav-previous">' . get_next_posts_link( __( '<span class="meta-nav">&larr;</span>Older entries', 'fbfpi' ) ) . '</div>';
				$content.= '<div class="nav-next">' . get_previous_posts_link( __( 'Newer entries <span class="meta-nav">&rarr;</span>', 'fbfpi' ) ) . '</div>';
			$content.= '<div class="fbfpi_clear"></div></div>';
	
			$content.= '</div>';
		
		endif;
		$paged = $paged_old;
		wp_reset_query();
		
		return $content;
	}
}

$FacebookFanpageImportShowdataShortcodes = new FacebookFanpageImportShowdataShortcodes();
