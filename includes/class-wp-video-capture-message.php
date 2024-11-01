<?php
/**
 * Vidrack Message
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Message' ) ) {

	/**
	 * Class WP_Video_Capture_Message
	 */
	class WP_Video_Capture_Message {
		/** Message
		 *
		 * @var string
		 */
		private $_message;
		/** Notice Class
		 *
		 * @var string
		 */
		private $_class = 'update';

		/**
		 * WP_Video_Capture_Message constructor.
		 *
		 * @param string $class message.
		 * @param string $message message.
		 */
		function __construct( $class, $message ) {
			$this->_class = $class;
			$this->_message = $message;

			add_action( 'admin_notices', array( $this, 'render' ) );
		}

		/**
		 * Generate message block
		 */
		function render() {
			printf( '<div class="%s"><p>%s</p></div>', filter_var( $this->_class ), filter_var( $this->_message ) );
		}
	}
}
