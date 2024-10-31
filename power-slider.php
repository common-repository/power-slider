<?php

	/*
	  Plugin Name: Power slider
	  Plugin URI: http://gndev.info/power-slider/
	  Version: 1.0.3
	  Author: Vladimir Anokhin
	  Author URI: http://gndev.info/
	  Description: Content slider by posts from specified category
	  Text Domain: power-slider
	  Domain Path: /languages
	  License: GPL2
	 */

	/**
	 * Plugin initialization
	 */
	function pows_plugin_init() {

		// Make plugin available for translation
		load_plugin_textdomain( pows_slug(), false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		// Load includes
		require_once( dirname( __FILE__ ) . '/includes/admin.php' );
		require_once( dirname( __FILE__ ) . '/includes/images.php' );
		require_once( dirname( __FILE__ ) . '/includes/metabox.php' );

		// Register styles
		wp_register_style( 'power-slider', pows_url() . '/css/style.css', false, pows_version(), 'all' );
		wp_register_style( 'power-slider-admin', pows_url() . '/css/admin.css', false, pows_version(), 'all' );

		// Register scripts
		wp_register_script( 'power-slider', pows_url() . '/js/power-slider.js', false, pows_version(), false );
		wp_register_script( 'power-slider-admin', pows_url() . '/js/admin.js', false, pows_version(), false );

		// Front-end scripts and styles
		if ( !is_admin() ) {

			// Enqueue styles
			wp_enqueue_style( 'power-slider' );

			// Enqueue scripts
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'power-slider' );
		}

		// Back-end scripts and styles
		elseif ( isset( $_GET['page'] ) && $_GET['page'] == pows_slug() ) {

			// Enqueue styles
			wp_enqueue_style( 'power-slider-admin' );

			// Enqueue scripts
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'power-slider-admin' );
		}
	}

	add_action( 'init', 'pows_plugin_init' );

	/**
	 * Returns current plugin version.
	 *
	 * @return string Plugin version
	 */
	function pows_version() {
		if ( !function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
		$plugin_file = basename( ( __FILE__ ) );
		return $plugin_folder[$plugin_file]['Version'];
	}

	/**
	 * Returns current plugin slug.
	 *
	 * @return string Plugin slug
	 */
	function pows_slug() {
		return 'power-slider';
	}

	/**
	 * Returns current plugin url
	 *
	 * @return string Plugin url
	 */
	function pows_url() {
		return plugins_url( basename( __FILE__, '.php' ), dirname( __FILE__ ) );
	}

	/**
	 * Power slider template tag and main function
	 *
	 * @param mixed $options Slider options
	 * @param bool $echo Echo or return result
	 * @return string Slider markup
	 */
	function the_power_slider( $options = false, $echo = true ) {

		// Default parameters
		$defaults = array(
			'category' => 1,
			'limit' => 10,
			'width' => 600,
			'height' => 300,
			'style' => 'white',
			'animation' => 'fade',
			'speed' => 600,
			'delay' => 4000
		);

		// Options presented as array
		if ( is_array( $options ) )
			$options_array = $options;

		// Options presented as string
		elseif ( $options )
			parse_str( $options, $options_array );

		// Options doesn't presented
		else
			$options_array = $defaults;

		// Put actual options in array
		foreach ( $defaults as $key => $val ) {
			$option[$key] = ( isset( $options_array[$key] ) ) ? $options_array[$key] : $defaults[$key];
		}

		// Unique slider ID
		$slider_id = uniqid( 'power-slider_' );

		// Custom posts loop
		$custom_loop = new WP_Query( 'cat=' . $option['category'] . '&posts_per_page=' . $option['limit'] );

		// Posts found
		if ( $custom_loop->have_posts() ) {

			// Start the loop
			while ( $custom_loop->have_posts() ) {
				$custom_loop->the_post();

				// Slide settings
				$slide_layout = ( get_post_meta( get_the_ID(), 'pows_layout', true ) ) ? get_post_meta( get_the_ID(), 'pows_layout', true ) : 'left';
				$slide_link = ( get_post_meta( get_the_ID(), 'pows_link', true ) ) ? get_post_meta( get_the_ID(), 'pows_link', true ) : get_permalink();
				$slide_title = get_the_title();

				// Slide markup based on layout type
				switch ( $slide_layout ) {

					// Left-aligned layout
					case 'left':
						$slides .= '<div class="pows-slide pows-slide-layout-' . $slide_layout . '" style="width:' . $option['width'] . 'px;height:' . $option['height'] . 'px"><a href="' . $slide_link . '" class="pows-slide-thumbnail">' . pows_post_image( round( ( $option['width'] / 2 ) - 15 ), $option['height'], $slide_title, get_the_ID() ) . '</a><div class="pows-slide-info" style="left:' . round( ( $option['width'] / 2 ) + 15 ) . 'px;width:' . round( ( $option['width'] / 2 ) - 45 ) . 'px;height:' . round( $option['height'] - 60 ) . 'px"><h2 class="pows-slide-title"><a href="' . $slide_link . '">' . $slide_title . '</a></h2><div class="pows-slide-description">' . get_the_excerpt() . '</div></div></div>';
						break;

					// Right-aligned layout
					case 'right':
						$slides .= '<div class="pows-slide pows-slide-layout-' . $slide_layout . '" style="width:' . $option['width'] . 'px;height:' . $option['height'] . 'px"><a href="' . $slide_link . '" class="pows-slide-thumbnail">' . pows_post_image( round( ( $option['width'] / 2 ) - 15 ), $option['height'], $slide_title, get_the_ID() ) . '</a><div class="pows-slide-info" style="right:' . round( ( $option['width'] / 2 ) + 15 ) . 'px;width:' . round( ( $option['width'] / 2 ) - 45 ) . 'px;height:' . round( $option['height'] - 60 ) . 'px"><h2 class="pows-slide-title"><a href="' . $slide_link . '">' . $slide_title . '</a></h2><div class="pows-slide-description">' . get_the_excerpt() . '</div></div></div>';
						break;

					// Fullsize layout
					case 'full':
						$slides .= '<div class="pows-slide pows-slide-layout-' . $slide_layout . '" style="width:' . $option['width'] . 'px;height:' . $option['height'] . 'px"><a href="' . $slide_link . '" class="pows-slide-thumbnail">' . pows_post_image( $option['width'], $option['height'], get_the_title(), get_the_ID() ) . '</a><h2 class="pows-slide-title"><a href="' . $slide_link . '">' . get_the_title() . '</a></h2><div class="pows-slide-description">' . get_the_excerpt() . '</div></div>';
						break;
				}
			}

			// Reset query to default
			wp_reset_query();
		}

		// Posts not found
		else {

			// Error message
			$slides = '<div class="pows-error" style="width:' . $option['width'] . 'px;height:' . $option['height'] . 'px">Power slider: ' . __( 'no posts found', pows_slug() ) . '</div>';
		}

		// Complete markup
		$return = '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#' . $slider_id . '").bxSlider({prevText:"&laquo;",nextText:"&raquo;",mode:"' . $option['animation'] . '",speed:"' . $option['speed'] . '",pause:' . $option['delay'] . ',auto:true,autoHover:true,pager:true});});</script>';
		$return .= '<div id="' . $slider_id . '_wrapper" class="pows-wrapper pows-style-' . $option['style'] . '" style="width:' . $option['width'] . 'px;height:' . $option['height'] . 'px"><div id="' . $slider_id . '" class="pows-list" style="width:' . $option['width'] . 'px;height:' . $option['height'] . 'px">' . $slides . '</div></div>';

		// Print/return
		if ( $echo )
			echo $return;
		else
			return $return;
	}

	/**
	 * Power slider shortcode for using in posts or widgets
	 *
	 * @return string Slider markup
	 */
	function pows_shortcode( $atts, $content = null ) {
		return the_power_slider( $atts, $echo = false );
	}

	add_shortcode( 'power_slider', 'pows_shortcode' );

	/**
	 * Add help link to plugins dashboard
	 *
	 * @param array $links Links
	 * @return array Links
	 */
	function pows_add_settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=' . pows_slug() ) . '">' . __( 'Help', pows_slug() ) . '</a>';
		return $links;
	}

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pows_add_settings_link', -10 );

	/**
	 * Hook to translate plugin information
	 */
	function pows_add_locale_strings() {
		$strings = array(
			__( 'Power slider', pows_slug() ),
			__( 'Vladimir Anokhin', pows_slug() ),
			__( 'Content slider by posts from specified category', pows_slug() )
		);
	}
?>