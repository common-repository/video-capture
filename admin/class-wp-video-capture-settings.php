<?php
/**
 * Settings page
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Settings' ) ) {

	/**
	 * Class WP_Video_Capture_Settings
	 */
	class WP_Video_Capture_Settings {

		/**
		 * Validate email.
		 *
		 * @param string $email email.
		 *
		 * @return string $email, if correct $email param.
		 *
		 * @todo refactor function return
		 */
		public static function validate_email( $email ) {
			if ( ! is_email( $email ) && '' !== $email ) {
				add_settings_error( 'vidrack_notifications_email', 'video-capture-invalid-email', __( 'Please enter a correct email', 'video-capture' ) );
			} else {
				return $email;
			}

			return false;
		}

		/**
		 * Settings Page Options
		 */
		public static function admin_init() {

			/**
			 * Register a setting and its data
			 */
			register_setting( 'wp_video_capture-group', 'vidrack_notifications_email', array(
				__CLASS__,
				'validate_email',
			) );

			/**
			 * Add a email section to a settings page
			 */
			add_settings_section(
				'wp_video_capture-section-email',
				__( 'Notifications Email Settings', 'video-capture' ),
				'',
				'wp_video_capture-email'
			);

			/**
			 * Email setting field
			 */
			add_settings_field(
				'wp_video_capture-notifications_email',
				__( 'Notifications email', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_email' ),
				'wp_video_capture-email',
				'wp_video_capture-section-email',
				array(
					'field' => 'vidrack_notifications_email',
				)
			);

			/**
			 * Register additional settings
			 */
			register_setting( 'wp_video_capture-group', 'vidrack_pro_account_key' );
			register_setting( 'wp_video_capture-group', 'vidrack_pro_account_email' );

			// Add Pro account settings section.
			add_settings_section(
				'wp_video_capture-section-pro',
				__( 'Pro account credentials', 'video-capture' ),
				array( __CLASS__, 'settings_section_wp_video_capture_pro' ),
				'wp_video_capture_pro'
			);
			// Add Pro account key.
			add_settings_field(
				'vidrack_pro_account_key',
				__( 'License key', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture_pro',
				'wp_video_capture-section-pro',
				array(
					'field' => 'vidrack_pro_account_key',
				)
			);
			// Add Pro account email.
			add_settings_field(
				'vidrack_pro_account_email',
				__( 'License email', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture_pro',
				'wp_video_capture-section-pro',
				array(
					'field' => 'vidrack_pro_account_email',
				)
			);

			register_setting( 'wp_video_capture-group', 'vidrack_js_callback' );
			// Add JS callback setting.
			add_settings_field(
				'wp_video_capture-js_callback',
				__( 'JavaScript Callback Function', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture',
				'wp_video_capture-section',
				array(
					'field' => 'vidrack_js_callback',
				)
			);

			register_setting( 'wp_video_capture-group', 'vidrack_desktop_upload' );
			register_setting( 'wp_video_capture-group', 'vidrack_window_modal' );
			register_setting( 'wp_video_capture-group', 'vidrack_form_position' );
			add_settings_section(
				'wp_video_capture-section',
				__( 'Settings', 'video-capture' ),
				array( __CLASS__, 'settings_section_content' ),
				'wp_video_capture'
			);

			// Add desktop upload button.
			add_settings_field(
				'wp_video_capture-desktop_upload',
				__( 'Desktop upload', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_checkbox' ),
				'wp_video_capture',
				'wp_video_capture-section',
				array(
					'field' => 'vidrack_desktop_upload',
				)
			);

			// Open Vidraсk Popup.
			add_settings_field(
				'wp_video_capture-window_modal',
				__( 'Display recorder in a pop-up', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_checkbox' ),
				'wp_video_capture',
				'wp_video_capture-section',
				array(
					'field' => 'vidrack_window_modal',
				)
			);

			// Set Vidrack Form before Vidrack Recorder
			add_settings_field(
				'wp_video_capture_form-position',
				__( 'Set Vidrack Form before Recorder', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_checkbox' ),
				'wp_video_capture',
				'wp_video_capture-section',
				array(
					'field' => 'vidrack_form_position',
				)
			);
		}

		/**
		 * Settings Page Options PRO
		 */
		public static function admin_init_pro() {

			/**
			 * Google API Youtube
			 */
			register_setting( 'wp_video_capture-group', 'vidrack_youtube_api_id' );
			register_setting( 'wp_video_capture-group', 'vidrack_youtube_api_secret' );
			/**
			 * Add a Youtube section to a settings page
			 */
			add_settings_section(
				'wp_video_capture-section-youtube',
				__( 'YouTube Settings', 'video-capture' ),
				'',
				'wp_video_capture-youtube'
			);
			/**
			 * Add YouTube API id field
			 */
			add_settings_field(
				'wp_video_capture-youtube_api_id',
				__( 'YouTube API id', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-youtube',
				'wp_video_capture-section-youtube',
				array(
					'field' => 'vidrack_youtube_api_id',
				)
			);
			/**
			 * Add YouTube API secret key field
			 */
			add_settings_field(
				'wp_video_capture-youtube_api_secret',
				__( 'YouTube API secret key', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-youtube',
				'wp_video_capture-section-youtube',
				array(
					'field' => 'vidrack_youtube_api_secret',
				)
			);

			/**
			 * Register collect options
			 */
			register_setting( 'wp_video_capture-group', 'vidrack_collect_email' );
			register_setting( 'wp_video_capture-group', 'vidrack_collect_name' );
			register_setting( 'wp_video_capture-group', 'vidrack_collect_phone' );
			register_setting( 'wp_video_capture-group', 'vidrack_collect_birthday' );
			register_setting( 'wp_video_capture-group', 'vidrack_collect_additional_data' );
			register_setting( 'wp_video_capture-group', 'vidrack_collect_language' );
			register_setting( 'wp_video_capture-group', 'vidrack_collect_location' );

			/**
			 * Add a Collect user data section to a settings page
			 */
			add_settings_section(
				'wp_video_capture-section-collect',
				__( 'Collect user data Settings', 'video-capture' ),
				array( __CLASS__, 'settings_pro_section_content' ),
				'wp_video_capture-collect'
			);
			/**
			 * Add name collect settings field
			 */
			add_settings_field(
				'wp_video_capture-collect_name_options',
				__( 'Collect Name', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect',
				'wp_video_capture-section-collect',
				array(
					'field' => 'vidrack_collect_name',
				)
			);
			/**
			 * Add email collect settings field
			 */
			add_settings_field(
				'wp_video_capture-collect_email_options',
				__( 'Collect Email', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect',
				'wp_video_capture-section-collect',
				array(
					'field' => 'vidrack_collect_email',
				)
			);
			/**
			 * Add phone collect settings field
			 */
			add_settings_field(
				'wp_video_capture-collect_phone_options',
				__( 'Collect Phone', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect',
				'wp_video_capture-section-collect',
				array(
					'field' => 'vidrack_collect_phone',
				)
			);
			/**
			 * Add date of birth collect settings field
			 */
			add_settings_field(
				'wp_video_capture-collect_birthday_options',
				__( 'Collect Date of birth', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect',
				'wp_video_capture-section-collect',
				array(
					'field' => 'vidrack_collect_birthday',
				)
			);
			/**
			 * Add location collect settings field
			 */
			add_settings_field(
				'wp_video_capture-collect_location_options',
				__( 'Collect Location', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect',
				'wp_video_capture-section-collect',
				array(
					'field' => 'vidrack_collect_location',
				)
			);
			/**
			 * Add language collect settings field
			 */
			add_settings_field(
				'wp_video_capture-collect_language_options',
				__( 'Collect Language', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect',
				'wp_video_capture-section-collect',
				array(
					'field' => 'vidrack_collect_language',
				)
			);
			/**
			 * Add additional data collect settings field
			 */
			add_settings_field(
				'wp_video_capture-collect_additional_data_options',
				__( 'Collect Additional message', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect',
				'wp_video_capture-section-collect',
				array(
					'field' => 'vidrack_collect_additional_data',
				)
			);

			register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_name_1' );
			register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_value_1' );
			/**
			 * Add a Custom Collect Data 1
			 */
			add_settings_section(
				'wp_video_capture-section-collect_custom_1',
				__( 'Custom Collect Data field 1', 'video-capture' ),
				null,
				'wp_video_capture-collect_custom'
			);
			/**
			 * Add Custom Collect Data field NAME 1
			 */
			add_settings_field(
				'wp_video_capture-collect_custom_collect_data_value_name_1',
				__( 'Name', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-collect_custom',
				'wp_video_capture-section-collect_custom_1',
				array(
					'field' => 'vidrack_custom_collect_data_name_1',
				)
			);
			/**
			 * Add Custom Collect Data field 2
			 */
			add_settings_field(
				'wp_video_capture-collect_custom_collect_data_value_1',
				__( 'Required', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect_custom',
				'wp_video_capture-section-collect_custom_1',
				array(
					'field' => 'vidrack_custom_collect_data_value_1',
				)
			);

			register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_name_2' );
			register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_value_2' );
			/**
			 * Add a Custom Collect Data 1
			 */
			add_settings_section(
				'wp_video_capture-section-collect_custom_2',
				__( 'Custom Collect Data field 2', 'video-capture' ),
				null,
				'wp_video_capture-collect_custom'
			);
			/**
			 * Add Custom Collect Data field NAME 2
			 */
			add_settings_field(
				'wp_video_capture-collect_custom_collect_data_value_name_2',
				__( 'Name', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-collect_custom',
				'wp_video_capture-section-collect_custom_2',
				array(
					'field' => 'vidrack_custom_collect_data_name_2',
				)
			);
			/**
			 * Add Custom Collect Data field 2
			 */
			add_settings_field(
				'wp_video_capture-collect_custom_collect_data_value_2',
				__( 'Required', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect_custom',
				'wp_video_capture-section-collect_custom_2',
				array(
					'field' => 'vidrack_custom_collect_data_value_2',
				)
			);

			register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_name_3' );
			register_setting( 'wp_video_capture-group', 'vidrack_custom_collect_data_value_3' );
			/**
			 * Add a Custom Collect Data 3
			 */
			add_settings_section(
				'wp_video_capture-section-collect_custom_3',
				__( 'Custom Collect Data field 3', 'video-capture' ),
				null,
				'wp_video_capture-collect_custom'
			);
			/**
			 * Add Custom Collect Data field NAME 3
			 */
			add_settings_field(
				'wp_video_capture-collect_custom_collect_data_value_name_3',
				__( 'Name', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-collect_custom',
				'wp_video_capture-section-collect_custom_3',
				array(
					'field' => 'vidrack_custom_collect_data_name_3',
				)
			);
			/**
			 * Add Custom Collect Data field 3
			 */
			add_settings_field(
				'wp_video_capture-collect_custom_collect_data_value_3',
				__( 'Required', 'video-capture' ),
				array( __CLASS__, 'settings_field_select_collect_data' ),
				'wp_video_capture-collect_custom',
				'wp_video_capture-section-collect_custom_3',
				array(
					'field' => 'vidrack_custom_collect_data_value_3',
				)
			);

			/**
			 * Register AWS settings
			 */
			register_setting( 'wp_video_capture-group', 'vidrack_aws_s3_accesskeyid' );
			register_setting( 'wp_video_capture-group', 'vidrack_aws_s3_secretaccesskey' );
			register_setting( 'wp_video_capture-group', 'vidrack_aws_s3_bucket_region' );
			register_setting( 'wp_video_capture-group', 'vidrack_aws_s3_bucket_name' );
			/**
			 * Add a AWS S3 section to a settings page
			 */
			add_settings_section(
				'wp_video_capture-section-aws-s3',
				__( 'AWS S3 Settings', 'video-capture' ),
				'',
				'wp_video_capture-aws_s3'
			);
			/**
			 * AWS S3 accessKeyID setting field
			 */
			add_settings_field(
				'wp_video_capture-aws_s3_accesskeyid',
				__( 'AWS S3 Access Key ID', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-aws_s3',
				'wp_video_capture-section-aws-s3',
				array(
					'field' => 'vidrack_aws_s3_accesskeyid',
				)
			);
			/**
			 * AWS S3 accessKeyID setting field
			 */
			add_settings_field(
				'wp_video_capture-aws_s3_secretaccesskey',
				__( 'AWS S3 Secret Access Key', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-aws_s3',
				'wp_video_capture-section-aws-s3',
				array(
					'field' => 'vidrack_aws_s3_secretaccesskey',
				)
			);
			/**
			 * AWS S3 Bucket Region setting field
			 */
			add_settings_field(
				'wp_video_capture-aws_s3_bucket_region',
				__( 'AWS S3 Bucket Region', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-aws_s3',
				'wp_video_capture-section-aws-s3',
				array(
					'field' => 'vidrack_aws_s3_bucket_region',
				)
			);
			/**
			 * AWS S3 Bucket Name setting field
			 */
			add_settings_field(
				'wp_video_capture-aws_s3_bucket_name',
				__( 'AWS S3 Bucket Name', 'video-capture' ),
				array( __CLASS__, 'settings_field_input_text' ),
				'wp_video_capture-aws_s3',
				'wp_video_capture-section-aws-s3',
				array(
					'field' => 'vidrack_aws_s3_bucket_name',
				)
			);
		}

		/**
		 * Notification settings about pro activation.
		 */
		public static function settings_section_wp_video_capture_pro() {
			echo '<a href="https://vidrack.com/product/pro-version/" target="_blank">' . __( 'Pro version', 'video-capture' ) . '</a> ' . __( 'license key and email', 'video-capture' );
		}

		/**
		 * <input type=email> template for the Settings.
		 *
		 * @param array $args arguments.
		 */
		public static function settings_field_input_email( $args ) {
			$field = $args['field'];
			$value = get_option( $field );
			echo sprintf( '<input type="email" name="%s" id="%s" value="%s">', esc_html( $field ), esc_html( $field ), esc_html( $value ) );
		}

		/**
		 * <input type=text> template for the Settings.
		 *
		 * @param array $args arguments.
		 */
		public static function settings_field_input_text( $args ) {
			$field = $args['field'];
			$value = get_option( $field );
			echo sprintf( '<input type="text" name="%s" id="%s" value="%s">', esc_html( $field ), esc_html( $field ), esc_html( $value ) );
		}

		/**
		 * <input type="checkbox"> template for Settings.
		 *
		 * @param array $args arguments.
		 */
		public static function settings_field_input_checkbox( $args ) {
			$field = $args['field'];
			$value = get_option( $field );
			echo sprintf( '<input type="checkbox" name="%s" id="%s" value="1" %s>', esc_html( $field ), esc_html( $field ), checked( $value, 1, '' ) );
		}

		/**
		 * Add Vidrack Settings link on Plugins Table (Plugins page)
		 *
		 * @param array $links Array of standard links.
		 *
		 * @return array $links with link to Vidrack Settings.
		 * @todo check current_user_can
		 */
		public static function plugin_settings_link( $links ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.' ) ) );
			}

			$view_settings_path = get_admin_url() . 'edit.php?post_type=vidrack_video&page=settings';
			$settings_link      = '<a href="' . $view_settings_path . '">' . __( 'Settings', 'video-capture' ) . '</a>';
			array_unshift( $links, $settings_link );

			return $links;
		}

		/**
		 * Collecting data settings popup text.
		 */
		public static function settings_section_content() {
			echo __( '<b>JS Callback</b>. In your environment, variables are available: <code>filename</code>, <code>ip</code>, <code>external_id</code>.
			<br><b>JS Callback Valid Example 1:</b> <code>console.log(filename)</code>
			<br><b>JS Callback Valid Example 2:</b> <code>yourFunction(filename, ip, external_id)</code>
			<br><b>JS Callback Valid Example 3:</b> <code>yourFunction()</code>
			<br><b>JS Callback NOT Valid Example:</b> <code>yourFunction</code>' );
		}

		/**
		 * Collecting data settings popup text.
		 */
		public static function settings_pro_section_content() {
			echo __( 'Please choose type of collecting users data.' );
		}

		/**
		 * Select collect data options template for Settings.
		 *
		 * @param array $args arguments.
		 */
		public static function settings_field_select_collect_data( $args ) {
			$field = $args['field'];
			$value = get_option( $field );
			echo sprintf( '<select name="%s" id="%s">
										<option value="mandatory" %s>'.__( 'Mandatory', 'video-capture' ).'</option>
										<option value="optional" %s>'.__( 'Optional', 'video-capture' ).'</option>
										<option value="no" %s>'.__( 'No', 'video-capture' ).'</option>
									</select>', esc_html( $field ), esc_html( $field ), selected( $value, 'mandatory', '' ), selected( $value, 'optional', '' ), selected( $value, 'no', '' ) );
		}

		/**
		 * Vidrack do_settings_sections
		 *
		 * @param string $page argument - setting section
		 */
		public static function do_settings_sections( $page ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections[$page] ) )
				return;

			foreach ( (array) $wp_settings_sections[$page] as $section ) {
				echo "<div class='postbox'>\n";
				if ( $section['title'] )
					echo "<h2 class='hndle'><span>{$section['title']}</span></h2>\n";

				echo '<div class="inside">';

				if ( $section['callback'] )
					call_user_func( $section['callback'], $section );

				if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
					continue;
				echo '<table class="form-table">';
				do_settings_fields( $page, $section['id'] );
				echo '</table>';
				echo '</div>'; // end .inside.
				echo '</div>'; // end .postbox.
			}
		}

	}
}