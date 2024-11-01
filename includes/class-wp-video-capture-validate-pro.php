<?php
/**
 * Vidrack Pro
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Validate_PRO' ) ) {

	/**
	 * Class WP_Video_Capture_Validate_PRO
	 */
	class WP_Video_Capture_Validate_PRO {
		/**
		 * Check if Pro Version.
		 *
		 * @return bool result validating Pro account version.
		 *
		 * @todo refactoring if if if
		 */
		public static function validate_pro_account() {
			$pro_account_key   = get_option( 'vidrack_pro_account_key' );
			$pro_account_email = get_option( 'vidrack_pro_account_email' );
			if ( $pro_account_key && $pro_account_email ) {
				$instance = str_replace( array(
					'http://',
					'https://',
					'www.',
				), '', get_site_url() );
				$status_url_parameters = array(
					'wc-api'      => 'am-software-api',
					'licence_key' => $pro_account_key,
					'instance'    => $instance,
					'email'       => $pro_account_email,
					'request'     => 'status',
					'product_id'  => 'Pro Version',
				);
				$status_url = 'https://vidrack.com/?' . http_build_query( $status_url_parameters );
				$responce = wp_remote_get( $status_url );

				// check $responce .
				if ( ! is_wp_error( $responce ) || wp_remote_retrieve_response_code( $responce ) === 200 ) {

					$response_activate = json_decode( $responce['body'], true );

					if ( isset( $response_activate['status_check'] ) && 'active' === $response_activate['status_check'] ) {

						return true;

					} else {
						// $message = 'NO NO NO!';
						$activate_url_parameters = array(
							'wc-api'      => 'am-software-api',
							'licence_key' => $pro_account_key,
							'instance'    => $instance,
							'email'       => $pro_account_email,
							'request'     => 'activation',
							'product_id'  => 'Pro Version',
						);
						$activate_url = 'https://vidrack.com/?' . http_build_query( $activate_url_parameters );
						$responce = wp_remote_get( $activate_url );

						// check $responce .
						if ( ! is_wp_error( $responce ) || wp_remote_retrieve_response_code( $responce ) === 200 ) {

							$response_activate = json_decode( $responce['body'], true );

							if ( isset( $response_activate['activated'] ) && true === $response_activate['activated'] ) {

								return true;

							} elseif ( isset( $response_activate['activated'] ) && 'inactive' === $response_activate['activated'] && isset($_GET['post_type']) && $_GET['post_type'] === 'vidrack_video' ) {

								switch ( $response_activate['code'] ) {
									case 101:
										new WP_Video_Capture_Message( 'error', $response_activate['error'] );
										break;
									case 103:
										new WP_Video_Capture_Message( 'update-nag', 'This key was already used to activate Vidrack Pro' );
										break;
									default:
										new WP_Video_Capture_Message( 'update-nag', 'Please enter valid Pro License credentials' );
								}

								add_action( 'admin_init', array( __CLASS__, 'hide_pro_activation_success_notice' ) );

								return false;

							}
						}
					} // End if().
				} else {
					$message = 'Something went wrong';
					if ( $responce->get_error_message() ) {
						$message .= ' Error:' . $responce->get_error_message();
					}
					new WP_Video_Capture_Message( 'error', $message );
				} // End if().
			} else {
				return false;
			} // End if().
		}


		/**
		 * Hide registration notice if not Pro version.
		 */
		static function hide_pro_activation_success_notice() {
			update_user_meta( get_current_user_id(), '_wp-video-capture_hide_pro_activation_success_notice', false );
		}

		/**
		 * Register Pro version JS and CSS resources.
		 */
		/*public static function  register_resources_pro(){
			// JS.
			wp_enqueue_script( 'select2', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/js/select2.min.js', array('jquery'), '', true );
			wp_enqueue_script( 'datepicker', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/js/datepicker.min.js', array('jquery'), '', true );
			// CSS.
			wp_enqueue_style( 'select2', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/css/select2.min.css' );
			wp_enqueue_style( 'datepicker', plugin_dir_url( VIDRACK_PLUGIN ) . 'lib/css/datepicker.min.css' );
		}*/
	}
} // End if().

