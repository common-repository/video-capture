<?php
/**
 * Vidrack YouTube loader
 *
 * @package wp-video-capture
 */

if (!class_exists('WP_Video_Capture_YouTube')) {

	/**
	 * WP_Youtube class.
	 */
	class WP_Video_Capture_YouTube {
		/**
		 * The User YouTube client id.
		 *
		 * @var string
		 */
		private $oauth_client_id;

		/**
		 * The User YouTube client secret.
		 *
		 * @var string
		 */
		private $oauth_client_secret;

		/**
		 * Redirect response url.
		 *
		 * @var string
		 */
		private $redirect_url;

		/**
		 * User has application.
		 *
		 * @var bool
		 */
		private $is_user_has_application;

		/**
		 * Google Client object.
		 *
		 * @var object
		 */
		private $client;

		/**
		 * Google YouTube object.
		 *
		 * @var object
		 */
		private $youtube;

		/**
		 * Current has YouTube token.
		 *
		 * @var bool
		 */
		private $is_user_oauth;

		/**
		 * Video download dir.
		 *
		 * @var bool
		 */
		private $download_dir;

		/**
		 * Constructor.
		 */
		public function __construct() {

			require_once plugin_dir_path(__FILE__) . 'vendor/google/autoload.php';

			$oauth_client_id = $this->oauth_client_id = get_option('vidrack_youtube_api_id');
			$oauth_client_secret = $this->oauth_client_secret = get_option('vidrack_youtube_api_secret');
			$redirect_url = $this->redirect_url = site_url();

			if ($oauth_client_id && $oauth_client_secret && $redirect_url) {

				if (!session_id()) {
					session_start();
				}

				$client = new Google_Client();
				$client->setApplicationName('YouTube Uploader');
				$client->setClientId($oauth_client_id);
				$client->setClientSecret($oauth_client_secret);
				$client->setRedirectUri($redirect_url);
				$client->setScopes('https://www.googleapis.com/auth/youtube');
				$youtube = new Google_Service_YouTube($client);

				$this->client = $client;
				$this->youtube = $youtube;
				$this->is_user_has_application = true;

				if (isset($_SESSION['youtube_token']) && $this->validate_access_token($_SESSION['youtube_token'])) {
					$this->is_user_oauth = true;
				} else {
					$this->is_user_oauth = false;
				}

				$this->oauth_url = $this->oauth_url();

				add_action('wp_ajax_youtubeAction', array(&$this, 'youtube_action'));
				add_action('template_redirect', array(&$this, 'oauth_response'));
			} else {
				$this->is_user_oauth = false;
				$this->is_user_has_application = false;
				$this->oauth_url = '';
			}

			$this->download_dir = plugin_dir_path(__FILE__) . 'downloads/';

			add_filter('post_row_actions', array(&$this, 'youtube_upload_link'), 11);

		}

		/**
		 * Check for version update.
		 *
		 * @param Json $access_token_json list data about YouTube access toke.
		 * @return Boll validate YouTube access token.
		 */
		public function validate_access_token($access_token_json) {
			$return = false;

			$access_token = $access_token_json['access_token'];
			$url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $access_token;

			$response = wp_remote_get($url);
			$response_body = json_decode($response['body']);

			if (isset($response_body->issued_to)) {
				$return = true;
			}

			return $return;
		}

		/**
		 * Get url for YouTube oAuth.
		 *
		 * @return string $oauth_url updated current actions.
		 */
		public function oauth_url() {
			if (!defined('DOING_AJAX') && is_admin()) {
				$state = mt_rand();
				$_SESSION['youtube_state'] = $state;
				$this->client->setState($state);
				$oauth_url = $this->client->createAuthUrl();
				return $oauth_url;
			}
		}

		/**
		 * Add YouTube upload link.
		 *
		 * @param Array $actions current actions.
		 * @return Array $actions updated current actions.
		 */
		public function youtube_upload_link($actions) {
			global $current_screen;
			if ('vidrack_video' === $current_screen->post_type) {

				$is_user_has_application = $this->is_user_has_application;
				$oauth_url = $this->oauth_url;
				$is_user_oauth = $this->is_user_oauth;
				$post_id = get_the_ID();
				$actions['upload_to_youtube'] = '<a
					href="https://' . vidrack_get_s3_bucket_name() . '.s3.amazonaws.com/' . get_post($post_id)->post_title . '"
					data-has-application="' . $is_user_has_application . '"
					data-auth-url="' . $oauth_url . '"
					data-is-oauth="' . $is_user_oauth . '"
					data-post-id ="' . $post_id . '"
					title="YouTube upload"
					class="upload-video-to-youtube"
					rel="permalink"
					data-mfp-src="#vidrack-popup">
					' . __('Upload to YouTube', 'video-capture') . '
				</a>';
			}
			return $actions;

		}

		public function oauth_response() {
			if (isset($_GET['code']) && strval(wp_unslash($_SESSION['youtube_state'])) === strval(wp_unslash($_GET['state']))) { // Input var "code" and "youtube_state" is set?
				$this->client->fetchAccessTokenWithAuthCode($_GET['code']);
				$_SESSION['youtube_token'] = $this->client->getAccessToken();
				include plugin_dir_path(__FILE__) . '../admin/views/oauth-success.php';
				die;
			}
		}

		/**
		 * Download video from AS3 to local folder.
		 *
		 * @param string $video_link AS3 video link.
		 * @return string $server_video_path download video path.
		 */
		private function download_to_local($video_link) {

			mkdir($this->download_dir, 0777);
			$server_video_path = $this->download_dir . basename($video_link);
			$remote_file_source = fopen($video_link, 'rb');
			if ($remote_file_source) {
				$server_video_source = fopen($server_video_path, 'wb');
				if ($server_video_source) {
					while (!feof($remote_file_source)) {
						fwrite($server_video_source, fread($remote_file_source, 1024 * 8), 1024 * 8);
					}
				}
			}
			if ($remote_file_source) {
				fclose($remote_file_source);
			}
			if ($remote_file_source) {
				fclose($remote_file_source);
			}
			if (file_exists($server_video_path)) {
				return $server_video_path;
			}
			return false;
		}

		/**
		 * Calling YouTube upload action.
		 */
		public function youtube_action() {

			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_key($_POST['nonce']), 'vidrack_nonce_secret')) { // Input var "nonce" is set?
				echo wp_json_encode(array('status' => 'error', 'message' => __('An error occurred.', 'video-capture')));
				die();
			}


			$client = $this->client;
			$youtube = $this->youtube;

			if (isset($_SESSION['youtube_token'])) {
				$client->setAccessToken($_SESSION['youtube_token']);
			}

			// Check to ensure that the access token was successfully acquired.
			if ($client->getAccessToken() && isset($_POST['video_link']) && isset($_POST['post_id'])) { // Input var "video_link" and "post_id" is set?
				try {
					$video_server_path = $this->download_to_local($_POST['video_link']); // Input var "video_link"  set!

					if ($video_server_path) {

						$video_title = 'Vidrack Video';
						$video_desc = esc_html(get_post_meta($_POST['post_id'], '_vidrack_desc', true));
						$video_tag = esc_html(get_post_meta($_POST['post_id'], '_vidrack_tag', true));

						$snippet = new Google_Service_YouTube_VideoSnippet();
						$snippet->setTitle($video_title);
						$snippet->setDescription($video_desc);
						$snippet->setTags(explode(',', $video_tag));
						$snippet->setCategoryId('22');

						$status = new Google_Service_YouTube_VideoStatus();
						$status->privacyStatus = 'public';

						$video = new Google_Service_YouTube_Video();
						$video->setSnippet($snippet);
						$video->setStatus($status);

						$chunk_size_bytes = (1 * 1024 * 1024);

						$client->setDefer(true);

						$insert_request = $youtube->videos->insert('status,snippet', $video);

						$media = new Google_Http_MediaFileUpload(
							$client,
							$insert_request,
							'video/*',
							null,
							true,
							$chunk_size_bytes
						);
						$media->setFileSize(filesize($video_server_path));

						$status = false;

						$handle = fopen($video_server_path, 'r');

						while (!$status && !feof($handle)) {
							$chunk = fread($handle, $chunk_size_bytes);
							$status = $media->nextChunk($chunk);
						}
						fclose($handle);
						$client->setDefer(false);

						$this->remove_download_dir();

						echo wp_json_encode(array('status' => 'success'));
						die;
					}
				} catch (Google_ServiceException $e) {
					$this->remove_download_dir();

					echo wp_json_encode(array('status' => 'error', 'message' => __('A service error occurred!', 'video-capture')));
					die;

				} catch (Google_Exception $e) {
					$this->remove_download_dir();

					echo wp_json_encode(array('status' => 'error', 'message' => __('A client error occurred! Perhaps you has no YouTube account  or chanel!', 'video-capture')));
					die;
				}
			} else {
				echo wp_json_encode(array('status' => 'error', 'message' => __('An error occurred, please refresh the page and try again!', 'video-capture')));
				die();
			}
		}

		/**
		 * Remove created download dir.
		 */
		private function remove_download_dir() {
			array_map('unlink', glob($this->download_dir . '*'));
			rmdir($this->download_dir);
		}
	}

}
new WP_Video_Capture_YouTube();
