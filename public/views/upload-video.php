<?php
/**
 * Vidrack Uploader template.
 *
 * @package wp-video-capture
 */

?>
<?php
	$style = '';
	if ( ! get_option( 'vidrack_desktop_upload' )  ) {
		$style = 'style="display:none"';
	}
?>
<div class="vidrack-uploader">
	<form class="vidrack-uploader__form" method="post" action="https://storage.vidrack.com/video">
		<input class="vidrack-uploader__file-selector" type="file" accept="video/*;capture=camcoder" />
		<button type="button" name="vidrack-upload-button" class="vidrack-uploader__button" <?php echo $style; ?>>
			<i class="vidrack-icon_upload"></i> <span><?php esc_html_e( 'Video Upload','video-capture' );?></span>
		</button>
	</form>
	<div class="vidrack-uploader__progress">
		<div class="vidrack__loading">
			<div class="vidrack__loading-bar"></div>
		</div>
		<div class="vidrack__loading-percent"></div>
	</div>
	<div class="vidrack-uploader__message"></div>
</div>
