<?php
/**
 * Vidrack Recorder template.
 *
 * @package wp-video-capture
 */

?>
<div <?php echo filter_var( $vidrack_id )?> class="vidrack-recorder-wrapper <?php echo filter_var( $vidrack_wrapper_classes );?>" >
	<div class="vidrack">
		<div class="vidrack__video-wrapper">
			<video <?php echo filter_var( $vidrack_attr ); ?>></video>
			<div class="vidrack__recorder-controls">
				<div class="recorder-controls__timer">00:00:00</div>
				<div id="vidrack__loading" class="vidrack__loading">
					<div class="vidrack__loading-bar"></div>
				</div>
				<button class="btn-recorder" data-vidrack-task="record">Record</button>
			</div>
			<div class="vidrack-copyright">
				<a href="https://vidrack.com/" target="_blank">Powered by VIDRACK.com</a>
			</div>
		</div>
	</div>
</div>
