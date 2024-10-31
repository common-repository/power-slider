<?php

	/**
	 * Share buttons for admin page
	 */
	function pows_share() {

		// Share data
		$title = str_replace( '+', '%20', urlencode( __( 'Power slider', pows_slug() ) ) );
		$text = str_replace( '+', '%20', urlencode( __( 'Awesome slider for WordPress. Power slider.', pows_slug() ) ) );
		$url = urlencode( 'http://gndev.info/power-slider/' );
		?>
		<div id="pows-share">
			<!-- Twitter -->
			<iframe src="http://platform.twitter.com/widgets/tweet_button.html?url=<?php echo $url; ?>&amp;via=gndev.info&amp;text=<?php echo $text; ?>&amp;lang=en" style="width:105px;height:21px;" scrolling="no"></iframe>

			<!-- PlusOne -->
			<iframe src="https://plusone.google.com/_/+1/fastbutton?url=<?php echo $url; ?>&amp;size=medium&amp;count=true&amp;annotation=&amp;hl=en-US" style="width:80px;height:21px;" scrolling="no"></iframe>

			<!-- Facebook -->
			<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $url; ?>&amp;send=false&amp;layout=button_count&amp;width=80&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;height=21&amp;locale=en_US" style="width:80px;height:21px;" scrolling="no"></iframe>
		</div>
		<?php
	}

	/**
	 * Register administration page
	 */
	function pows_add_admin() {
		add_options_page( __( 'Power slider', pows_slug() ), __( 'Power slider', pows_slug() ), 'manage_options', pows_slug(), 'pows_admin_page' );
	}

	/**
	 * Administration page
	 */
	function pows_admin_page() {
		?>

		<div class="wrap">

			<div id="icon-options-general" class="icon32"><br /></div>
			<h2><?php _e( 'Power slider', pows_slug() ); ?></h2>

			<div id="pows-wrapper">

				<div id="pows-tabs">
					<a class="pows-current"><span><?php _e( 'About', pows_slug() ); ?></span></a>
					<a><span><?php _e( 'Usage', pows_slug() ); ?></span></a>
				</div>

				<div class="pows-pane">
					<p class="pows-message pows-message-error"><?php _e( 'For full functionality of this page it is recommended to enable JavaScript.', pows_slug() ); ?> <a href="http://www.enable-javascript.com/" target="_blank"><?php _e( 'Instructions', pows_slug() ); ?></a></p>
					<div class="pows-onethird-column">
						<h3><?php _e( 'Free support', pows_slug() ); ?></h3>
						<p><a href="http://wordpress.org/tags/power-slider?forum_id=10" target="_blank"><?php _e( 'Support forum', pows_slug() ); ?></a></p>
						<p><a href="http://gndev.info/power-slider/" target="_blank"><?php _e( 'Plugin homepage', pows_slug() ); ?></a></p>
						<p><a href="http://twitter.com/gn_themes" target="_blank"><?php _e( 'Twitter', pows_slug() ); ?></a></p>
					</div>

					<div class="pows-twothird-column">
						<h3><?php _e( 'Do you love this plugin?', pows_slug() ); ?></h3>
						<p><?php _e( 'Buy author a beer', pows_slug() ); ?> - <a href="http://gndev.info/donate/" target="_blank" style="color:red"><?php _e( 'Donate', pows_slug() ); ?></a></p>
						<p><a href="http://wordpress.org/extend/plugins/power-slider/" target="_blank"><?php _e( 'Rate this plugin at wordpress.org', pows_slug() ); ?></a> (<?php _e( '5 stars', pows_slug() ); ?>)</p>
						<p><?php _e( 'Review this plugin in your blog', pows_slug() ); ?></p>
					</div>

					<div class="pows-clear"></div>
					<?php pows_share(); ?>

				</div>
				<div class="pows-pane">
					<h3><?php _e( 'Template tag', pows_slug() ); ?></h3>
					<pre><code>&lt;?php the_power_slider( array(
	'category' => 1,
	'limit' => 10,
	'width' => 600,
	'height' => 300,
	'style' => 'white', // 'white', 'none'
	'animation' => 'fade', // 'fade', 'horizontal', 'vertical'
	'speed' => 600,
	'delay' => 4000
) ); ?&gt;</code></pre>
					<pre><code>&lt;?php the_power_slider( 'category=1&limit=10&animation=fade' ); ?&gt;</code></pre>
					<h3><?php _e( 'Shortcode', pows_slug() ); ?></h3>
					<pre><code>[power_slider]</code></pre>
					<pre><code>[power_slider category="1" limit="10" animation="fade"]</code></pre>
				</div>
			</div>

		</div>
		<?php
	}

	add_action( 'admin_menu', 'pows_add_admin' );
?>