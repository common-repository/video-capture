<?php
/**
 * Settings page template.
 *
 * @package wp-video-capture
 */

?>

<div class="wrap">

	<form method="POST" action="options.php">

		<h2><?php esc_html_e( 'Video Recorder','video-capture' );?></h2>

		<!-- Errors -->
		<?php settings_errors(); ?>

		<div id="poststuff" class="metabox-holder has-right-sidebar">

			<div class="inner-sidebar">
				<div id="side-sortables">

					<?php global $pro_account; ?>
					<?php if ( ! $pro_account ) : ?>
						<div class="postbox vidrack_pro_ad">
							<a href="https://vidrack.com/product/pro-version/" target="_blank">
								<img src="<?php echo VIDRACK_ASSETS_DIR_URL ?>img/ad_vidrack_pro.jpg" alt="Buy Vidrack PRO">
							</a>
						</div>
					<?php endif; ?>

					<div class="postbox">
						<h3 class="hndle"><span><?php esc_html_e( 'Rate Us:','video-capture' );?></span></h3>
						<div class="inside">
							<p style="font-size: 16px;">If you like Vidrack please leave us a <a href="https://wordpress.org/support/plugin/video-capture/reviews?rate=5#new-post" target="_blank">★★★★★</a> rating. A huge thanks in advance!</p>
						</div>
					</div>

					<div class="postbox">
						<h3 class="hndle"><span><?php esc_html_e( 'Links:','video-capture' );?></span></h3>
						<div class="inside">
							<ul>
								<li><a href="https://vidrack.com/product/install/" target="_blank"><?php _e( 'Help to Install', 'video-capture' ) ?></a></li>
								<li><a href="https://vidrack.me/account/signup/" target="_blank"><?php _e( 'Try Vidrack Web App', 'video-capture' ) ?></a></li>
								<li><a href="https://vidrack.com/shop/" target="_blank"><?php _e( 'Shop', 'video-capture' ) ?></a></li>
								<li><a href="https://vidrack.com/invest/" target="_blank"><?php _e( 'Invest', 'video-capture' ) ?></a></li>
								<li><a href="https://vidrack.com/donate/" target="_blank"><?php _e( 'Donate', 'video-capture' ) ?></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div id="post-body-content" class="has-sidebar-content">

				<?php
					settings_fields( 'wp_video_capture-group' );

					WP_Video_Capture_Settings::do_settings_sections('wp_video_capture-email');
					WP_Video_Capture_Settings::do_settings_sections( 'wp_video_capture_pro' );
					WP_Video_Capture_Settings::do_settings_sections( 'wp_video_capture' );
					WP_Video_Capture_Settings::do_settings_sections( 'wp_video_capture-collect' );
					WP_Video_Capture_Settings::do_settings_sections( 'wp_video_capture-collect_custom' );
					WP_Video_Capture_Settings::do_settings_sections( 'wp_video_capture-youtube' );
					WP_Video_Capture_Settings::do_settings_sections( 'wp_video_capture-aws_s3' );
				?>

				<div class="postbox">
					<h2 class="hndle"><span><?php _e( 'Using Vidrack', 'video-capture' ) ?></span></h2>
					<div class="inside">
						<p>Using video recording: <code>[vidrack]</code></p>
						<p><strong>[vidrack] options:</strong></p>
						<dl>
							<dt>align</dt>
							<dd>Horizontal alignment<br>Default: left</dd>
							<dt>ext_id</dt>
							<dd>Unique ID for 3rd party integrations</dd>
							<dt>tag</dt>
							<dd>A comma-separated list of tags</dd>
							<dt>desc</dt>
							<dd>Video description</dd>
						</dl>
						<p><strong>Examples:</strong></p>
						<p>
							<code>[vidrack align="right"]</code><br>
							<code>[vidrack ext_id="123"]</code><br>
							<code>[vidrack tag="one, two"]</code><br>
							<code>[vidrack align="center" ext_id="345" desc="some description"]</code>
						</p>

						<?php if ( ! $pro_account ) : ?>

						<?php endif; ?>
						<p>Display collected videos: <code>[vidrack_dashboard]</code>.
							<?php if ( ! $pro_account ) : ?>
								<strong>PRO VERSION ONLY – <a href="https://vidrack.com/product/pro-version/" target="_blank">
										<?php _e( 'Buy PRO', 'video-capture' ) ?></a></strong>
							<?php endif; ?>
						</p>
					</div>
				</div>

				<?php submit_button(); ?>

			</div>

		</div>
	</form>

</div>
