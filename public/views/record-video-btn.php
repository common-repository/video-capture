<?php
/**
 * Vidrack Record Video button template.
 *
 * @package wp-video-capture
 */

?>
<button type="button" name="vidrack__button" class="vidrack__button" <?php echo filter_var( $vidrack_btn_attr ) ?>>
	<?php esc_html_e( 'Record Video','video-capture' );?>
</button>
