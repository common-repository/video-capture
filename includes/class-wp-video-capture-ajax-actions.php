<?php
/**
 * Vidrack Ajax Actions
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Ajax_Actions' ) ) {

	/**
	 * Class WP_Video_Capture_Ajax_Actions
	 */
	class WP_Video_Capture_Ajax_Actions {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Initialize AJAX actions.
			add_action( 'wp_ajax_nopriv_store_video_file', array( __CLASS__, 'store_video_file' ) );
			add_action( 'wp_ajax_store_video_file', array( __CLASS__, 'store_video_file' ) );
			add_action( 'wp_ajax_set_rating_video', array( __CLASS__, 'set_rating_video' ) );
		}

		/**
		 * Send email notification
		 */
		public static function store_video_file() {

			$video_data = filter_input( INPUT_POST, 'video_data' );
			$video_nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
			$video_data = json_decode( $video_data, true );

			$message = array();
			switch ( true ) {
				case ! wp_verify_nonce( $video_nonce, 'vidrack_ajax_nonce' ) :
					$message['message'] = __( 'An error occurred.', 'video-capture' );
				case ! isset( $video_data['filename'] ):
					$message['message'] = __( 'Filename is not set.', 'video-capture' );
				default:
					if ( $message ) {
						wp_send_json_error( $message );
					}
			}

			// Insert new video info into the DB.
			$video_post = array(
				'post_type' => 'vidrack_video',
				'post_title' => sanitize_text_field( wp_unslash( $video_data['filename'] ) ),
				'post_status' => 'publish',
			);
			$post_id = wp_insert_post( $video_post, true );

			if ( is_wp_error( $post_id ) ) {
				wp_die( wp_json_encode( array(
					'status' => 'error',
					'message' => $post_id->get_error_message(),
				) ) );
			}

			// @todo: Redo the addition to the database.
			// Add IP.
			$result_ip = add_post_meta( $post_id, '_vidrack_ip', $video_data['ip'] );

			// Add external id.
			if ( isset( $video_data['external_id'] ) ) {
				$result_ip = add_post_meta( $post_id, '_vidrack_external_id', $video_data['external_id'] );
			}

			// Add tag.
			if ( isset( $video_data['tag'] ) ) {
				$result_ip = add_post_meta( $post_id, '_vidrack_tag', $video_data['tag'] );
			}

			// Add desc.
			if ( isset( $video_data['desc'] ) ) {
				$result_ip = add_post_meta( $post_id, '_vidrack_desc', $video_data['desc'] );
			}

			// Add name.
			if ( isset( $video_data['name'] ) ) {
				$result_name = add_post_meta( $post_id, '_vidrack_name', $video_data['name'] );
			}

			// Add email.
			if ( isset( $video_data['email'] ) ) {
				$result_email = add_post_meta( $post_id, '_vidrack_email', $video_data['email'] );
			}

			// Add phone.
			if ( isset( $video_data['phone'] ) ) {
				$result_phone = add_post_meta( $post_id, '_vidrack_phone', $video_data['phone'] );
			}

			// Add date of birth.
			if ( isset( $video_data['birthday'] ) ) {
				$result_birthday = add_post_meta( $post_id, '_vidrack_birthday', $video_data['birthday'] );
			}

			// Add date of location.
			if ( isset( $video_data['location'] ) ) {
				$result_location = add_post_meta( $post_id, '_vidrack_location', $video_data['location'] );
			}

			// Add date of language.
			if ( isset( $video_data['language'] ) ) {
				$result_language = add_post_meta( $post_id, '_vidrack_language', $video_data['language'] );
			}

			// Add date of additional_data.
			if ( isset( $video_data['additional_data'] ) ) {
				$result_additional_data = add_post_meta( $post_id, '_vidrack_additional_data', $video_data['additional_data'] );
			}

			// Add date of capture_url.
			if ( isset( $video_data['capture_url'] ) ) {
				$result_capture_url = add_post_meta( $post_id, '_vidrack_capture_url', $video_data['capture_url'] );
			}

			// Add custom data 1.
			if ( isset( $video_data['custom_data_1'] ) ) {
				$result_custom_data_1 = add_post_meta( $post_id, '_vidrack_custom_data_1', $video_data['custom_data_1'] );
			}
			// Add custom data 2.
			if ( isset( $video_data['custom_data_2'] ) ) {
				$result_custom_data_2 = add_post_meta( $post_id, '_vidrack_custom_data_2', $video_data['custom_data_2'] );
			}
			// Add custom data 3.
			if ( isset( $video_data['custom_data_3'] ) ) {
				$result_custom_data_3 = add_post_meta( $post_id, '_vidrack_custom_data_3', $video_data['custom_data_3'] );
			}

			$to = get_option( 'vidrack_notifications_email' );
			if ( $to ) {
				// Initialize Mailer class.
				require_once VIDRACK_DIR_PATH . 'includes/class-wp-video-capture-email.php';
				WP_Video_Capture_Email::send_new_video_email( $to,  sanitize_text_field( wp_unslash( $video_data['filename'] ) ) );

				wp_send_json_success( array(
					'message'   => 'Post successfully added. Email sent.',
				) );
			}
		}

		/**
		 * Set video rating on video list page.
		 */
		public static function set_rating_video() {

			$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );
			$rating_value = filter_input( INPUT_POST, 'rating_value', FILTER_SANITIZE_STRING );
			$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_STRING );

			if ( ! wp_verify_nonce( $nonce, 'vidrack_ajax_nonce' ) || ! isset( $rating_value ) && ! isset( $post_id ) ) {
				wp_send_json_error( __( 'An error occurred! Please refresh the page and try again!', 'video-capture' ) );
			} else {
				$result = update_post_meta($post_id, '_vidrack_video_rating', $rating_value);

				if ( $result ) {
					wp_send_json_success();
				}

			}
		}

	}

} // End if().
new WP_Video_Capture_Ajax_Actions();

