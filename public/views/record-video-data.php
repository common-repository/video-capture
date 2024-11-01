<?php
/**
 * Vidrack Uploader Collect data template.
 *
 * @package wp-video-capture
 */

?>

<div class="vidrack-collect-data">

<?php if ( isset( $collect_data['name'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-name"><?php esc_html_e( 'Your name','video-capture' );?></label>
		<input type="text" id="vidrack-capture-name" name='vidrack-capture-name' class="vidrack-collect-data__item" autocomplete="off" placeholder=" " minlength="2" <?php echo filter_var( $collect_data['name'] ); ?>>
	</div>
<?php endif; ?>
<?php if ( isset( $collect_data['email'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-email"><?php esc_html_e( 'Your email','video-capture' );?></label>
		<input type="email" id="vidrack-capture-email" name='vidrack-capture-email' class="vidrack-collect-data__item" autocomplete="off" placeholder=" " minlength="7"  <?php echo filter_var( $collect_data['email'] ); ?>>
	</div>
<?php endif; ?>
<?php if ( isset( $collect_data['phone'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-phone"><?php esc_html_e( 'Your phone','video-capture' );?></label>
		<input type="tel" id="vidrack-capture-phone" name='vidrack-capture-phone' class="vidrack-collect-data__item" autocomplete="off" placeholder=" "  minlength="4" <?php echo filter_var( $collect_data['phone'] ); ?>>
	</div>
<?php endif; ?>
<?php if ( isset( $collect_data['birthday'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-birthday"><?php esc_html_e( 'Your date of birth','video-capture' );?></label>
		<input type="date" id="vidrack-capture-birthday" name='vidrack-capture-birthday' class="vidrack-collect-data__item" autocomplete="off"  <?php echo filter_var( $collect_data['birthday'] ); ?>>
	</div>
<?php endif; ?>
<?php if ( isset( $collect_data['location'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-location"><?php esc_html_e( 'Your location','video-capture' );?></label>
		<input type="text" id="vidrack-capture-location" name='vidrack-capture-location' class="vidrack-collect-data__item" autocomplete="off" placeholder=" "  <?php echo filter_var( $collect_data['location'] ); ?>>
	</div>
<?php endif; ?>
<?php if ( isset( $collect_data['language'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-language"><?php esc_html_e( 'Your language','video-capture' );?></label>
		<select id="vidrack-capture-language" name='vidrack-capture-language' class="vidrack-collect-data__item"  <?php echo filter_var( $collect_data['language'] ); ?>>
			<?php
				$languages_json = file_get_contents( VIDRACK_DIR_PATH . 'public/languages.json' );
				$languages_array = json_decode( $languages_json, true );
				foreach ( $languages_array as $key => $value ) {
					echo '<option value=' . filter_var( $key ) . '>' . filter_var( $value ) . '</option>';
				}
			?>
		</select>
	</div>
<?php endif; ?>
<?php if ( isset( $collect_data['additional_data'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-additional-data"><?php esc_html_e( 'Additional message','video-capture' );?></label>
		<textarea name="vidrack-capture-additional-data" id="vidrack-capture-additional-data" class="vidrack-collect-data__item" placeholder=" "  <?php echo filter_var( $collect_data['additional_data'] ); ?>></textarea>
	</div>
<?php endif; ?>
<?php if ( '' !== $custom_collect_data_names['custom_data_1'] && isset( $collect_data['custom_data_1'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-custom_1"><?php echo filter_var( $custom_collect_data_names['custom_data_1'] ); ?></label>
		<input type="text" id="vidrack-capture-custom_1" name='vidrack-capture-custom_1' class="vidrack-collect-data__item" autocomplete="off" placeholder=" " <?php echo filter_var( $collect_data['custom_data_1'] ); ?>>
	</div>
<?php endif; ?>
<?php if ( '' !== $custom_collect_data_names['custom_data_2'] && isset( $collect_data['custom_data_2'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-custom_2"><?php echo filter_var( $custom_collect_data_names['custom_data_2'] ); ?></label>
		<input type="text" id="vidrack-capture-custom_2" name='vidrack-capture-custom_2' class="vidrack-collect-data__item" autocomplete="off" placeholder=" " <?php echo filter_var( $collect_data['custom_data_2'] ); ?>>
	</div>
<?php endif; ?>
<?php if ( '' !== $custom_collect_data_names['custom_data_3'] && isset( $collect_data['custom_data_3'] ) ) : ?>
	<div class="vidrack-collect-data__group">
		<label for="vidrack-capture-custom_3"><?php echo filter_var( $custom_collect_data_names['custom_data_3'] ); ?></label>
		<input type="text" id="vidrack-capture-custom_3" name='vidrack-capture-custom_3' class="vidrack-collect-data__item" autocomplete="off" placeholder=" " <?php echo filter_var( $collect_data['custom_data_3'] ); ?>>
	</div>
<?php endif; ?>

</div>
