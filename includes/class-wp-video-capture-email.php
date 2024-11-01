<?php
/**
 * Email library
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Email' ) ) {

	/**
	 * WP_Video_Capture_Email class
	 */
	class WP_Video_Capture_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			require_once plugin_dir_path( __FILE__ ) . 'vendor/mandrill/Mandrill.php';
		}

		/**
		 * Notifies user about newly uploaded video.
		 *
		 * @param string $to user email.
		 * @param string $filename video name.
		 *
		 * @throws string $e error message
		 */
		public static function send_new_video_email( $to, $filename ) {

			$mandrill = new Mandrill( 'H7MZ7BsNhBb7-fnBTXH7AA' );

			$html = '
				<p>' . __( 'Hello', 'video-capture' ) . ',<br/>
				<br/>
				' . __( 'You have a new video at', 'video-capture' ) . ' ' . HOSTNAME . '!<br/>
				<a href="https://vidrack-media.s3.amazonaws.com/' . $filename . '" download>' . __( 'Click here to download', 'video-capture' ) . '</a><br/>
				<br/>
				<p>' . __( 'Have trouble playing videos? Download', 'video-capture' ) . ' <a href="https://www.videolan.org/" target="_blank">VLC media player</a>!</p>
				<br/>
				' . __( 'Kind regards', 'video-capture' ) . ',<br/>
				' . __( 'Vidrack Team', 'video-capture' ) . '<br/>
				<br/>
				<a href="https://vidrack.com" target="_blank">vidrack.com</a>
			';

			try {

				$message = array(
					'subject'    => __( 'New video recorded at', 'video-capture' ) . ' ' . HOSTNAME . ' website',
					'html'       => $html,
					'from_email' => 'info@vidrack.com',
					'from_name'  => 'Vidrack', //optional
					'to'         => array(
						array( // add more sub-arrays for additional recipients
							'email' => $to,
						)
					),
				);

				$result = $mandrill->messages->send( $message );
				//print_r($result); //only for debugging.

			} catch ( Mandrill_Error $e ) {

				echo 'A mandrill error occurred: ' . get_class( $e ) . ' - ' . $e->getMessage();

				throw $e;
			}

		}
	}

} // End if.
new WP_Video_Capture_Email();
