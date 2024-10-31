<?php

	/**
	 * Get post image by post id
	 *
	 * @global mixed $post
	 * @param int $post_id Post ID
	 * @return string Image markup for specified post
	 */
	function pows_post_image( $width = 150, $height = 150, $alt = 'thumbnail', $post_id = false ) {

		global $post;

		$id = ( $post_id ) ? $post_id : $post->ID;
		$default = pows_url() . '/images/thumbnail.png';
		$timthumb = pows_url() . '/includes/timthumb.php';
		$meta = 'thumbnail';

		// Check post-thumbnails theme support
		if ( !current_theme_supports( 'post-thumbnails' ) )
			add_theme_support( 'post-thumbnails' );

		// Get post attachments
		$attachments = get_posts( array(
			'post_type' => 'attachment',
			'numberposts' => 1,
			'order' => 'ASC',
			'post_status' => null,
			'post_parent' => $id
			) );

		### Post thumbnail ###
		if ( has_post_thumbnail( $id ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'full' );
			$src = $image[0];
		}

		### Meta field ###
		elseif ( get_post_meta( $id, $meta, true ) ) {
			$src = get_post_meta( $id, $meta, true );
		}

		### First post attachment ###
		elseif ( $attachments ) {
			$vars = get_object_vars( $attachments[0] );
			$src = $vars['guid'];
		}

		### First post_content image ###
		else {
			ob_start();
			ob_end_clean();
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
			$src = $matches[1][0];
		}

		### Default image ###
		if ( empty( $src ) ) {
			$src = $default;
		}

		return '<img src="' . $timthumb . '?src=' . $src . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;zc=1&amp;q=100" alt="' . $alt . '" width="' . $width . '" height="' . $height . '" />';
	}

?>