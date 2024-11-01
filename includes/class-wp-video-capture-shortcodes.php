<?php
/**
 * Vidrack Shortcodes
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Shortcodes' ) ) {

	/**
	 * Class WP_Video_Capture_Shortcodes
	 */
	class WP_Video_Capture_Shortcodes {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Initialize shortcode.
			add_shortcode( 'vidrack', array( __CLASS__, 'record_video' ) );
			// [record_video] is added for compatibility with previous versions.
			add_shortcode( 'record_video', array( __CLASS__, 'record_video' ) );

			global $pro_account;

			if ( $pro_account ) {
				// [vidrack_dashboard]
				add_shortcode( 'vidrack_dashboard', array( __CLASS__, 'vidrack_dashboard' ) );
			}
		}

		/**
		 * [vidrack] tag implementation.
		 *
		 * @param string $attr tag attributes (left, right, etc).
		 * @param string $content tag content (empty).
		 *
		 * @return String $record_video_contents data buffer.
		 */
		static function record_video( $attr, $content = null ) {
			// Extract attributes.
			$attr   = shortcode_atts( array(
				'align'  => null,
				'ext_id' => null,
				'tag'    => null,
				'desc'   => null,
			), $attr );
			$align  = $attr['align'];
			$ext_id = $attr['ext_id'];
			$tag    = $attr['tag'];
			$desc   = $attr['desc'];

			$vidrack_popup = get_option( 'vidrack_window_modal' );

			global $vidrack_attr;
			$vidrack_id = '';
			$vidrack_styles = '';
			$vidrack_wrapper_classes = '';
			switch ( true ) {
				//case $align:
				//	$vidrack_styles .= "style='text-align:$align;'";
				case $ext_id:
					$vidrack_attr .= "data-external-id='$ext_id' ";
				case $tag:
					$vidrack_attr .= "data-tag='$tag' ";
				case $desc:
					$vidrack_attr .= "data-desc='$desc'";
			}

			// Enable output buffering.
			ob_start();

			// .wp-video-recorder. if set align
			if ( $align ) {
				echo "<div class='wp-video-recorder wp-video-recorder_$align'>";
			} else {
				echo "<div class='wp-video-recorder'>";
			}

			if ( get_option( 'vidrack_form_position' ) ) {
				self::get_form();
				self::get_recorder( $vidrack_popup );
			} else {
				self::get_recorder( $vidrack_popup );
				self::get_form();
			}

			// .wp-video-recorder. if set align.
			if ( $align ) {
				echo '</div>';
			}

			// Return buffer.
			$record_video_contents = ob_get_contents();
			ob_end_clean();

			if ( $vidrack_popup ) {
				/**
				 * Add popup template after footer
				 */
				function vidrack_popup_template() {
					global $vidrack_attr;

					$vidrack_styles = '';
					// $vidrack_classes = '';.
					$vidrack_wrapper_classes = 'mfp-hide';
					$vidrack_id = 'id="vidrack-popup"';

					include VIDRACK_DIR_PATH . 'public/views/record-video.php';
				}
				add_action( 'wp_footer', 'vidrack_popup_template' );
			}

			return $record_video_contents;
		}

		/**
		 * Collect data
		 *
		 * @return bool
		 */
		static function collect_data() {
			$collect_data = array();

			$collect_data_setting = array(
				'name' => get_option( 'vidrack_collect_name' ),
				'email' => get_option( 'vidrack_collect_email' ),
				'phone' => get_option( 'vidrack_collect_phone' ),
				'birthday' => get_option( 'vidrack_collect_birthday' ),
				'location' => get_option( 'vidrack_collect_location' ),
				'language' => get_option( 'vidrack_collect_language' ),
				'additional_data' => get_option( 'vidrack_collect_additional_data' ),
				'custom_data_1' => get_option( 'vidrack_custom_collect_data_value_1' ),
				'custom_data_2' => get_option( 'vidrack_custom_collect_data_value_2' ),
				'custom_data_3' => get_option( 'vidrack_custom_collect_data_value_3' ),
			);

			foreach ( $collect_data_setting as $key => $value ) {
				if ( $value && 'no' !== $value ) {
					if ( 'mandatory' === $value ) {
						$value = 'required';
					}
					$collect_data[ $key ] = $value;
				}
			}

			return $collect_data;
		}

		/**
		 * template Vidrack Form
		 */
		static function get_form() {
			global $pro_account;
			if ( $pro_account ) {
				$collect_data = self::collect_data();
				if ( $collect_data ) {
					$custom_collect_data_names = array(
						'custom_data_1' => get_option( 'vidrack_custom_collect_data_name_1' ),
						'custom_data_2' => get_option( 'vidrack_custom_collect_data_name_2' ),
						'custom_data_3' => get_option( 'vidrack_custom_collect_data_name_3' ),
					);
					include VIDRACK_DIR_PATH . 'public/views/record-video-data.php';
				}
			}
		}

		/**
		 * template Vidrack Recorder and buttons
		 */
		static function get_recorder( $vidrack_popup ) {
			echo "<div class='vidrack-recorder'>";
			if ( $vidrack_popup ) {
				$vidrack_btn_attr = 'data-mfp-src="#vidrack-popup"';
				include VIDRACK_DIR_PATH . 'public/views/record-video-btn.php';
			} else {
				include VIDRACK_DIR_PATH . 'public/views/record-video.php';
			}

			include VIDRACK_DIR_PATH . 'public/views/upload-video.php';

			echo "</div>";
		}

		/**
		 * [vidrack_user_dashboard] tag implementation.
		 *
		 * @return Source $dashboard_contents data buffer.
		 */
		function vidrack_dashboard() {
			// Enable output buffering.
			ob_start();
			// Render template.
			include VIDRACK_DIR_PATH . 'public/views/dashboard.php';
			// Return buffer.
			$dashboard_contents = ob_get_contents();
			ob_end_clean();
			return $dashboard_contents;
		}

	}
} // End if().
new WP_Video_Capture_Shortcodes();
