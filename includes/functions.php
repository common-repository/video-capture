<?php
/**
 * Vidrack additional functions
 *
 * @package wp-video-capture
 */

/**
 * Register JS and CSS resources. Common
 */
function vidrack_register_resources_common() {

	/**
	 * VideoJS
	 */
	wp_register_script(
		'videojs',
		VIDRACK_DIR_URL . 'libs/js/video.min.js',
		array(),
		'5.19',
		true
	);

	/**
	 * Magnific Popup Custom build = inline
	 */
	wp_register_script(
		'magnific-popup',
		VIDRACK_DIR_URL . 'libs/js/jquery.magnific-popup.min.js',
		array( 'jquery' ),
		'v1.1.0#inline',
		true
	);
	wp_register_style(
		'magnific-popup',
		VIDRACK_DIR_URL . 'libs/css/magnific-popup.css',
		null,
		'v1.1.0',
		'all'
	);

	/**
	 * RecordRTC Library
	 */
	wp_register_script(
		'recordrtc-js',
		VIDRACK_DIR_URL . 'libs/js/RecordRTC.min.js',
		null,
		'5.4.1',
		true
	);

	/**
	 * Flash legacy
	 */
	wp_register_script(
		'swfobject',
		VIDRACK_DIR_URL . 'libs/js/swfobject.js',
		null,
		'2.2',
		true
	);

	/**
	 * Input type=date polyfill
	 */
	wp_register_script(
		'html5-simple-date-input-polyfill',
		VIDRACK_DIR_URL . 'libs/js/html5-simple-date-input-polyfill.min.js',
		null,
		'v1.0',
		true
	);
	wp_register_style(
		'html5-simple-date-input-polyfill',
		VIDRACK_DIR_URL . 'libs/css/html5-simple-date-input-polyfill.css',
		null,
		'v1.0',
		'all'
	);

	/**
	 * jQuery Star-rating-svg
	 */
	wp_register_script(
		'star-rating-svg',
		VIDRACK_DIR_URL . 'libs/js/jquery.star-rating-svg.min.js',
		array('jquery'),
		'1.2.0',
		true
	);

	/**
	 * Vidrack
	 */
	wp_register_script(
		'vidrack',
		VIDRACK_ASSETS_DIR_URL . 'js/vidrack.js',
		array( 'jquery', 'magnific-popup', 'videojs', 'swfobject' ),
		VIDRACK_VERSION,
		true
	);

	wp_localize_script( 'vidrack', 'vidrack_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'vidrack_ajax_nonce' ),
		'ip' => $_SERVER['REMOTE_ADDR'],
		'popup' => get_option( 'vidrack_window_modal' ),
		'js_callback' => get_option( 'vidrack_js_callback' ),
		'recorder_swf_link' => VIDRACK_DIR_URL . 'libs/swf/recorder.swf',
	));

	$config = vidrack_is_custom_aws();
	if ( false !== $config ) {
		wp_localize_script( 'vidrack', 'vidrack_s3', array(
			's3' => $config['aws_options_base64'],
			'bucket' => $config['bucket'],
		));
	} else {
		wp_localize_script( 'vidrack', 'vidrack_s3', array(
			'bucket' => 'vidrack-media',
		));
	}

	wp_localize_script( 'vidrack', 'vidrackVersion',  VIDRACK_VERSION );

	wp_register_style(
		'vidrack',
		VIDRACK_DIR_URL . 'public/css/record-video.css',
		array( 'magnific-popup' ),
		VIDRACK_VERSION,
		'all'
	);

	/**
	 * Vidrack YouTube
	 */
	wp_register_script(
		'vidrack-admin-youtube',
		VIDRACK_DIR_URL . 'admin/js/vidrack-admin-youtube.js',
		array('jquery', 'magnific-popup'),
		VIDRACK_VERSION,
		true
	);

	wp_localize_script( 'vidrack-admin-youtube', 'YouTubeUpload', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'vidrack_nonce_secret' ),
	));

	/**
	 * Vidrack Admin
	 */
	wp_register_style(
		'vidrack-admin',
		VIDRACK_DIR_URL . 'admin/css/vidrack-admin.css',
		null,
		VIDRACK_VERSION,
		'all'
	);
	wp_register_script(
		'vidrack-admin',
		VIDRACK_DIR_URL . 'admin/js/vidrack-admin.js',
		array('jquery'),
		VIDRACK_VERSION,
		true
	);

	wp_localize_script( 'vidrack-admin', 'vidrack_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'vidrack_ajax_nonce' ),
	));

	$config = vidrack_is_custom_aws();
	if ( false !== $config ) {
		wp_localize_script( 'vidrack-admin', 'vidrack_s3', array(
			'bucket' => $config['bucket'],
		));
	} else {
		wp_localize_script( 'vidrack-admin', 'vidrack_s3', array(
			'bucket' => 'vidrack-media',
		));
	}

	/**
	 * Vidrack Public Dashboard
	 */
	wp_register_style(
		'vidrack-dashboard',
		VIDRACK_DIR_URL . 'public/css/dashboard.css',
		array('magnific-popup'),
		VIDRACK_VERSION,
		'all'
	);

}
// Initialize COMMON JS and CSS resources.
add_action( 'wp_enqueue_scripts', 'vidrack_register_resources_common' );
add_action( 'admin_enqueue_scripts', 'vidrack_register_resources_common' );

/**
 * Register JS and CSS resources.
 */
function vidrack_register_resources() {

	global $post;
	if ( has_shortcode( $post->post_content, 'record_video' ) || has_shortcode( $post->post_content, 'vidrack' ) ) {

		/**
		 * Vidrack Recorder Scripts
		 */
		wp_enqueue_script( 'vidrack' );
		wp_enqueue_script( 'recordrtc-js' );
		wp_enqueue_style( 'vidrack' );

		$date_input = get_option( 'vidrack_collect_birthday' );
		if ( $date_input && 'no' != $date_input ) {
			wp_enqueue_script( 'html5-simple-date-input-polyfill' );
			wp_enqueue_style( 'html5-simple-date-input-polyfill' );
		}

	} elseif ( has_shortcode( $post->post_content, 'vidrack_dashboard' ) ) {

		/**
		 * Vidrack Public Dashboard Scripts
		 */

		$config = vidrack_is_custom_aws();
		if ( false !== $config ) {
			wp_localize_script( 'vidrack-admin', 'vidrack_s3', array(
				'bucket' => $config['bucket'],
				'dashboard' => true,
			));
		} else {
			wp_localize_script( 'vidrack-admin', 'vidrack_s3', array(
				'bucket' => 'vidrack-media',
				'dashboard' => true,
			));
		}

		wp_enqueue_script('magnific-popup');
		wp_enqueue_script('videojs');
		wp_enqueue_script('vidrack-admin');
		wp_enqueue_style( 'vidrack-dashboard' );

	} elseif ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {

		wp_enqueue_style( 'vidrack' );
		wp_enqueue_script('vidrack');

	}

}
// Initialize JS and CSS resources.
add_action( 'wp_enqueue_scripts', 'vidrack_register_resources' );

/**
 * Add the resource to video list and Vidrack settings page.
 */
function vidrack_admin_enqueue_scripts() {
	global $pro_account, $post;
	if ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type || 'vidrack_video' === get_current_screen()->post_type ) {
		if ( $pro_account ) {
			wp_enqueue_script('magnific-popup');
			wp_enqueue_script('star-rating-svg');
			wp_enqueue_script('videojs');
			wp_enqueue_style( 'magnific-popup' );
		}

		wp_enqueue_style( 'vidrack-admin' );
		wp_enqueue_script('vidrack-admin');

		if ( vidrack_isset_yt_api() && $pro_account ) {
			wp_enqueue_script('vidrack-admin-youtube');
		}
	}
}
add_action( 'admin_enqueue_scripts', 'vidrack_admin_enqueue_scripts' );

/**
 * AWS Integrations
 * isset AWS keys and options
 */
function vidrack_is_custom_aws() {
	$vidrack_is_custom_aws = false;
	$vidrack_aws_s3_accesskeyid = str_replace( ' ', '', get_option( 'vidrack_aws_s3_accesskeyid' ) );
	$vidrack_aws_s3_secretaccesskey = str_replace( ' ', '', get_option( 'vidrack_aws_s3_secretaccesskey' ) );
	$vidrack_aws_s3_bucket_region = str_replace( ' ', '', get_option( 'vidrack_aws_s3_bucket_region' ) );
	$vidrack_aws_s3_bucket_name = str_replace( ' ', '', get_option( 'vidrack_aws_s3_bucket_name' ) );
	if ( $vidrack_aws_s3_accesskeyid && $vidrack_aws_s3_secretaccesskey && $vidrack_aws_s3_bucket_region && $vidrack_aws_s3_bucket_name ) {
		$vidrack_is_custom_aws['isCustom'] = true;
		$vidrack_is_custom_aws['bucket'] = $vidrack_aws_s3_bucket_name;
		$vidrack_is_custom_aws['aws_options_base64'] = base64_encode( $vidrack_aws_s3_accesskeyid . ',' . $vidrack_aws_s3_secretaccesskey . ',' . $vidrack_aws_s3_bucket_region . ',' . $vidrack_aws_s3_bucket_name );
	}
	return $vidrack_is_custom_aws;
}

/**
 * AWS Integrations
 * Get S3 Bucket Name
 */
function vidrack_get_s3_bucket_name() {
	$vidrack_s3_bucket_name = 'vidrack-media';
	if ( get_option( 'vidrack_s3_bucket_name' ) ) {
		$vidrack_s3_bucket_name = get_option( 'vidrack_s3_bucket_name' );
	}
	return $vidrack_s3_bucket_name;
}

/**
 * Add additional links to the Plugins page.
 *
 * @param array  $meta list of current links on the Plugins list page.
 * @param string $plugin_file current modified.
 * @return array $meta updated links.
 */
function vidrack_plugin_row_meta( $meta, $plugin_file ) {
	if ( false === strpos( $plugin_file, VIDRACK_PLUGIN_BASENAME ) ) {
		return $meta;
	}

	global $pro_account;
	$meta[] = '<a href="https://vidrack.com/product/install/" target="_blank">' . __( 'Help to Install', 'video-capture' ) . '</a>';
	$meta[] = '<a href="https://vidrack.me/account/signup/" target="_blank">' . __( 'Try Vidrack Web App', 'video-capture' ) . '</a>';
	$meta[] = '<a href="https://vidrack.com/shop/" target="_blank">' . __( 'Shop', 'video-capture' ) . '</a>';
	$meta[] = '<a href="https://vidrack.com/invest/" target="_blank">' . __( 'Invest', 'video-capture' ) . '</a>';
	$meta[] = '<a href="https://vidrack.com/donate/" target="_blank">' . __( 'Donate', 'video-capture' ) . '</a>';

	if ( ! $pro_account ) {
		$meta[] = '<a href="https://vidrack.com/product/pro-version/" target="_blank" style="font-weight:bold;color:darkgreen;">' . __( 'Upgrade to Pro', 'video-capture' ) . '</a>';
	}

	return $meta;
}
add_filter( 'plugin_row_meta', 'vidrack_plugin_row_meta', 10, 4 );

/**
 * Admin page footer text.
 *
 * @param string $text standart footer text.
 * @return string Vidrack footer text.
 */
function vidrack_admin_footer( $text ) {
	global $post;
	global $pro_account;

	if ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type || 'vidrack_video' === get_current_screen()->post_type ) {
		$text = '<ul class="vidrack-admin-footer-text">';
		if ( ! $pro_account ) {
			$text .= '<li><a href="https://vidrack.com/product/pro-version/" class="vidrack-admin-footer-text__link_pro" target="_blank">' . __( 'Upgrade to Pro', 'video-capture' ) . '</a></li>';
		}
		$text .= '<li><a href="https://vidrack.com/terms-conditions/" target="_blank">' . __( 'Terms and Conditions', 'video-capture' ) . '</a></li>';
		$text .= '<li>' . __( 'Powered by', 'video-capture' ) . ' <a href="https://vidrack.com" target="_blank">vidrack.com</a></li>';
		$text .= '</ul>';
	}

	return $text;
}
add_action( 'admin_footer_text', 'vidrack_admin_footer' );

/**
 * Youtube Integration
 * isset google console keys
 */
function vidrack_isset_yt_api() {
	$return = false;
	if ( NULL != get_option('vidrack_youtube_api_secret') && NULL != get_option('vidrack_youtube_api_id') ) {
		$return = true;
	}
	return $return;
}