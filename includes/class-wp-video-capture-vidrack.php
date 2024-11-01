<?php
/**
 * Vidrack taxonomy
 *
 * @package wp-video-capture
 */

if ( ! class_exists( 'WP_Video_Capture_Vidrack' ) ) {

	/**
	 * Class WP_Video_Capture_Vidrack
	 */
	class WP_Video_Capture_Vidrack {

		/**
		 * Plugin initialization functions.
		 */
		public static function plugin_init() {
			self::create_post_type();
			self::hooks_init();
			self::add_options();
			self::update_check();
		}

		/**
		 * Create custom post type to store video information.
		 */
		public static function create_post_type() {
			$labels = array(
				'name' => __( 'Videos' ),
				'all_items' => __( 'Dashboard' ),
				'singular_name' => __( 'Video' ),
				'menu_name' => __( 'Vidrack' ),
				'name_admin_bar' => __( 'Vidrack' ),
				'search_items' => __( 'Search Videos' ),
				'not_found' => __( 'No videos found.' ),
			);
			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'capability_type' => 'post',
				'capabilities' => array(
					'create_posts' => false,
				),
				'map_meta_cap' => true,
				"hierarchical" => false,
				'supports' => false,
				'menu_position' => 11,
				'menu_icon' => VIDRACK_ASSETS_DIR_URL . 'img/vidrack_logo.svg',
			);
			register_post_type( 'vidrack_video', $args );

			//add_filter( 'posts_clauses', array( __CLASS__, 'alter_posts_search' ) )

			/*
			 * Disable Yoast SEO on vidrack_video
			 *
			 * @todo check Yoast SEO
			 */
			/*global $pagenow;
			global $wpseo_meta_columns;
			if ( "edit.php" === $pagenow && isset( $_REQUEST['post_type'] ) ) {
				if ( isset( $wpseo_meta_columns ) && "vidrack_video" === $_REQUEST['post_type'] ) {
					remove_action( 'admin_init', array( $wpseo_meta_columns, 'setup_hooks' ) );
				}
			}*/

		}

		/**
		 * Init actions and filters
		 */
		public static function hooks_init() {
			add_action( 'admin_menu', array( __CLASS__, 'vidrack_add_menu_page' ) );

			add_filter( 'manage_vidrack_video_posts_columns', array( __CLASS__, 'add_columns' ) );
			add_filter( 'manage_vidrack_video_posts_custom_column', array( __CLASS__, 'fill_columns' ), 5, 2 );
			add_filter( 'manage_edit-vidrack_video_sortable_columns', array( __CLASS__, 'add_sortable_columns' ) );
			add_filter( 'pre_get_posts', array( __CLASS__, 'add_columns_request' ) );

			add_filter( 'post_row_actions', array( __CLASS__, 'custom_row_actions' ), 10, 2 );
			add_filter( 'bulk_actions-edit-vidrack_video', array( __CLASS__, 'custom_bulk_actions' ) );

			add_action( 'before_delete_post', array( __CLASS__, 'delete_video' ) );

			add_action( 'wp_head', array( __CLASS__, 'vidrack_post_head' ) );
			add_filter( 'the_title', array( __CLASS__, 'vidrack_post_title' ) );
			add_filter( 'the_content', array( __CLASS__, 'vidrack_post_content' ) );

			global $pro_account;
			if ( $pro_account &&  vidrack_isset_yt_api() ) {
					require_once VIDRACK_DIR_PATH . 'includes/class-wp-video-capture-youtube.php';
			}
		}

		/**
		 * Add Vidrack Sub-Menu
		 */
		public static function vidrack_add_menu_page() {
			$view_dashboard_path = 'edit.php?post_type=vidrack_video';

			add_submenu_page(
				$view_dashboard_path,
				'Vidrack Settings',
				'Settings',
				'manage_options',
				'settings',
				array( __CLASS__, 'vidrack_settings_page' )
			);
		}
		/**
		 * Add Vidrack Sub-Menu Callback Function
		 */
		public static function vidrack_settings_page() {
			include VIDRACK_DIR_PATH . 'admin/views/settings.php';
		}

		/**
		 * Create custom columns.
		 *
		 * @param Array $columns existing columns.
		 * @return Array custom columns data list.
		 */
		static function add_columns( $columns ) {

			global $pro_account;

			$num = 2; // After which column on the account to insert new.

			$new_columns = array(
				'vidrack_video_ip' => __( 'IP' ),
				'vidrack_video_external_id' => __( 'External ID' ),
			);

			if ( $pro_account ) {
				$new_columns['vidrack_video_rating'] = __( 'Rating' );
				$new_columns['vidrack_video_tag'] = __( 'Tags' );
				$new_columns['vidrack_video_desc'] = __( 'Description' );
				$new_columns['vidrack_name'] = __( 'Name' );
				$new_columns['vidrack_email'] = __( 'Email' );
				$new_columns['vidrack_phone'] = __( 'Phone' );
				$new_columns['vidrack_birthday'] = __( 'Birthday' );
				$new_columns['vidrack_location'] = __( 'Location' );
				$new_columns['vidrack_language'] = __( 'Language' );
				$new_columns['vidrack_additional_data'] = __( 'Additional Data' );
				$new_columns['vidrack_capture_url'] = __( 'Capture URL' );

				$custom_data_name[] = get_option( 'vidrack_custom_collect_data_name_1' );
				$custom_data_name[] = get_option( 'vidrack_custom_collect_data_name_2' );
				$custom_data_name[] = get_option( 'vidrack_custom_collect_data_name_3' );

				foreach( $custom_data_name as $key => $value ){
					$index = $key + 1;
					if ( '' !== $value && isset($value) ) {
						$new_columns['vidrack_custom_data_' . $index] = $value;
					}
				}

			}

			$return_columns = array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );

			// Replace standart title col.
			if ( $pro_account ) {
				$columns = $return_columns;
				$return_columns = array();

				foreach( $columns as $key => $title ) {
					if ( 'title' === $key ) {
						$return_columns['vidrack_video_title'] = __( 'Title' );
					}
					$return_columns[$key] = $title;
				}

				unset($return_columns['title']);
			}

			return $return_columns;
		}

		/**
		 * Populate custom columns with metadata.
		 *
		 * @param string $colname column.
		 * @param number $post_id current post id.
		 */
		static function fill_columns( $colname, $post_id ) {

			global $pro_account;

			switch ( $colname ) {
				case 'vidrack_video_ip':
					echo filter_var( get_post_meta( $post_id, '_vidrack_ip', true ), FILTER_VALIDATE_IP );
					break;
				case 'vidrack_video_rating':
					echo filter_var( get_post_meta( $post_id, '_vidrack_video_rating', true ) );
					break;
				case 'vidrack_video_external_id':
					echo filter_var( get_post_meta( $post_id, '_vidrack_external_id', true ), FILTER_SANITIZE_STRING );
					break;
				case 'vidrack_video_tag':
					echo filter_var( get_post_meta( $post_id, '_vidrack_tag', true ), FILTER_SANITIZE_STRING );
					break;
				case 'vidrack_video_desc':
					echo filter_var( get_post_meta( $post_id, '_vidrack_desc', true ), FILTER_SANITIZE_STRING );
					break;
			}

			if ( $pro_account ) {
				switch ( $colname ) {
					case 'vidrack_video_title':
						$old_title = get_the_title();
						$new_title = str_replace( array("<span class='sub-title'>", "</span>"), array("", ""), $old_title );
						$title = esc_attr( $new_title );
						// Enable if need Edit post.
						//$title_content = '<a class="row-title" href="' . get_bloginfo('url') . '/wp-admin/post.php?post=' . get_the_ID() . '&amp;action=edit">' .
						//	<img class="vidrack-list-table__thumb" src="https://s3.amazonaws.com/vidrack-transcoder-thumbnails/' . get_post( get_the_ID() )->post_title . '.mp4-00001.png" onerror="this.src=\'' . VIDRACK_ASSETS_DIR_URL . 'img/vidrack-video-thumb.png' . '\'" >' .
						//	$title . '</a>';
						$title_content =
							'<img class="vidrack-list-table__thumb" src="https://s3.amazonaws.com/vidrack-transcoder-thumbnails/' . get_post( get_the_ID() )->post_title . '.mp4-00001.png" onerror="this.src=\'' . VIDRACK_ASSETS_DIR_URL . 'img/vidrack-video-thumb.png' . '\'" >' .
							'<div class="vidrack-list-table__title">' . $title . '</div>';
						echo $title_content;
						break;
					case 'vidrack_name':
						echo filter_var( get_post_meta( $post_id, '_vidrack_name', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_email':
						echo filter_var( get_post_meta( $post_id, '_vidrack_email', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_phone':
						echo filter_var( get_post_meta( $post_id, '_vidrack_phone', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_birthday':
						echo filter_var( get_post_meta( $post_id, '_vidrack_birthday', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_location':
						echo filter_var( get_post_meta( $post_id, '_vidrack_location', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_language':
						echo filter_var( get_post_meta( $post_id, '_vidrack_language', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_additional_data':
						echo filter_var( get_post_meta( $post_id, '_vidrack_additional_data', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_capture_url':
						echo filter_var( get_post_meta( $post_id, '_vidrack_capture_url', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_custom_data_1':
						echo filter_var( get_post_meta( $post_id, '_vidrack_custom_data_1', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_custom_data_2':
						echo filter_var( get_post_meta( $post_id, '_vidrack_custom_data_2', true ), FILTER_SANITIZE_STRING );
						break;
					case 'vidrack_custom_data_3':
						echo filter_var( get_post_meta( $post_id, '_vidrack_custom_data_3', true ), FILTER_SANITIZE_STRING );
						break;
				}
			}

		}

		/**
		 * Customize row actions from vidrack_video post type.
		 *
		 * @param array   $actions current actions.
		 * @param WP_Post $post current actions.
		 * @return array $actions updated current actions.
		 * @todo test PRO
		 */
		static function custom_row_actions( $actions, $post ) {
			global $pro_account;

			if ( 'vidrack_video' === $post->post_type ) {
				unset( $actions['edit'] );
				unset( $actions['view'] );
				unset( $actions['inline hide-if-no-js'] );
				$actions['download'] =
					'<a href="https://' . vidrack_get_s3_bucket_name() . '.s3.amazonaws.com/' .
					get_post( get_the_ID() )->post_title .
					'" title="Download" class="vidrack-download-video-link" rel="permalink" download>' . __( 'Download', 'video-capture' ) . '</a>';
				if ( $pro_account ) {
					$actions['play'] =
						'
						<a
							href="https://' . vidrack_get_s3_bucket_name() . '.s3.amazonaws.com/' . get_post( get_the_ID() )->post_title . '"
							title="Play"
							class="vidrack-play-video-link"
							rel="permalink" play>' . __( 'Play', 'video-capture' ) . '
						</a>
						';

					if ( vidrack_isset_yt_api() ) {
						$actions['upload_to_youtube'] = '';
					}

					$actions['vidrack-share'] = '
						<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( get_permalink( get_the_ID() ) ) . '&t=Vidrack Video"
							onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600\');return false;"
							target="_blank" title="Share on Facebook"
							class="vidrack-share__fb"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 480" height="480" width="480"><defs><clipPath clipPathUnits="userSpaceOnUse"><path d="M0 48 48 48 48 0 0 0 0 48Z"/></clipPath><clipPath clipPathUnits="userSpaceOnUse"><path d="M0 48 48 48 48 0 0 0 0 48Z"/></clipPath><clipPath clipPathUnits="userSpaceOnUse"><path d="M0 48 48 48 48 0 0 0 0 48Z"/></clipPath><clipPath clipPathUnits="userSpaceOnUse"><path d="M0 48 48 48 48 0 0 0 0 48Z"/></clipPath><clipPath clipPathUnits="userSpaceOnUse"/><clipPath clipPathUnits="userSpaceOnUse"/><clipPath clipPathUnits="userSpaceOnUse"><path d="M48 48 0 48 0 0 48 0 48 48Z"/></clipPath><clipPath clipPathUnits="userSpaceOnUse"><path d="M0 48 48 48 48 0 0 0 0 48Z"/></clipPath><clipPath clipPathUnits="userSpaceOnUse"/><clipPath clipPathUnits="userSpaceOnUse"/><clipPath clipPathUnits="userSpaceOnUse"><path d="M0 0 48 0 48 48 0 48 0 0Z"/></clipPath></defs><g transform="matrix(1.25 0 0 -1.25 0 480)"><path d="m304 288 0-48-32 0c-8.8 0-16-7.2-16-16l0-32 48 0 0-48-48 0 0-112-48 0 0 112-32 0 0 48 32 0 0 40c0 31 25.1 56 56 56M32 352c0-116.7 0-215.1 0-320 110 0 225.2 0 320 0 0 110 0 225.2 0 320-110 0-225.2 0-320 0z"/></g></svg>
						</a>
						<a href="https://twitter.com/share?url=' . urlencode( get_permalink( get_the_ID() ) ) . '&text=Vidrack Video"
							onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600\');return false;"
							target="_blank" title="Share on Twitter"
							class="vidrack-share__tw"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M17.7 9.3C17.6 14 14.7 17.1 10.3 17.3 8.5 17.4 7.2 16.8 6 16.1 7.3 16.3 9 15.8 9.9 15 8.6 14.9 7.8 14.2 7.4 13.1 7.8 13.2 8.2 13.2 8.6 13.1 7.4 12.7 6.5 12 6.5 10.4 6.8 10.6 7.2 10.7 7.6 10.7 6.8 10.2 6.1 8.4 6.9 7.2 8.2 8.6 9.8 9.8 12.4 10 11.7 7.2 15.4 5.6 17 7.5 17.6 7.4 18.2 7.1 18.7 6.9 18.5 7.5 18.1 8 17.6 8.3 18.1 8.3 18.6 8.1 19 7.9 18.8 8.5 18.2 8.9 17.7 9.3M20 2H4C2.9 2 2 2.9 2 4V20C2 21.1 2.9 22 4 22H20C21.1 22 22 21.1 22 20V4C22 2.9 21.1 2 20 2Z"/></svg>
						</a>
						<a href="https://plus.google.com/share?url=' . urlencode( get_permalink( get_the_ID() ) ) . '"
							onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=350,width=480\');return false;"
							target="_blank" title="Share on Google+"
							class="vidrack-share__g"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 480" height="480" width="480" id="svg3390"><defs id="defs3394"><clipPath id="clipPath3404"><path id="path3406" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3416"><path id="path3418" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3426"><path id="path3428" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3434"><path id="path3436" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3442"><path id="path3444" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3446"><path id="path3448" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3450"><path id="path3452" d="M48 48H0V0h48v48z"/></clipPath><clipPath id="clipPath3658"><path id="path3660" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3668"><path id="path3670" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3676"><path id="path3678" d="M0 48h48V0H0v48z"/></clipPath><clipPath id="clipPath3680"><path id="path3682" d="M0 0h48v48H0V0z"/></clipPath></defs><g id="g3398"><g id="g4012"><path d="M80.2 40C58 40 40 58 40 80.2v320C40 422 58 440 80.2 440h319.6c22.2 0 40.8-18 40.2-40.2v-310c0-22.3-18.5-49.8-40.7-49.8h-319zm101.4 98c28 0 51.3 10.3 68 27l-27.4 26.6c-7-7-20.3-15.8-40.6-15.8-35 0-62.5 29-62.5 64.2 0 35 27.6 64.2 62.7 64.2 41 0 55.4-29 58.4-43.8h-58.3v-35.2h96c1.5 5.6 1.5 10.2 1.5 17.3 0 58.6-39 99.4-97.4 99.4C125.2 342 80 296.3 80 240s44.7-102 101.6-102zm160 58.2H371v29h29l-.5 29h-29v29h-29v-29h-29v-29h29v-29z" id="path4001"/></g></g><style id="style3999">.st0{fill:#FFFFFF;}</style></svg>
						</a>
						';
				}
			}

			return $actions;
		}

		/**
		 * Remove video from S3 once it's deleted from Trash.
		 *
		 * @param string $post_id post id.
		 * @todo test delete on production server
		 */
		function delete_video( $post_id ) {
			global $post_type;
			if ( 'vidrack_video' !== $post_type ) {
				return;
			}

			$video = get_post( $post_id );

			$vidrack_is_custom_aws = vidrack_is_custom_aws();
			if ( $vidrack_is_custom_aws ) {
				$url = 'https://storage.vidrack.com/video/' . $video->post_title . '/' . $vidrack_is_custom_aws['aws_options_base64'] . '/' . vidrack_get_s3_bucket_name();
			} else {
				$url = 'https://storage.vidrack.com/video/' . $video->post_title;
			}

			$options = array(
				'http' => array(
					'method' => 'DELETE',
				),
			);
			$context  = stream_context_create( $options );
			$result = file_get_contents( $url, false, $context );
		}

		/**
		 * Set custom bulk actions on Vidrack video list page.
		 *
		 * @param Array $actions bulk actions.
		 * @return Array $actions updated bulk actions.
		 */
		static function custom_bulk_actions( $actions ) {
			unset( $actions['edit'] );
			return $actions;
		}

		/**
		 * Custom sortable fields.
		 *
		 * @param Array $sortable_columns existing columns.
		 * @return Array custom columns data list.
		 *
		 * @todo need pro check?
		 */
		static function add_sortable_columns( $sortable_columns ) {

			global $pro_account;

			$sortable_columns['vidrack_video_title'] = array(
				'vidrack_video_title',
				'desc',
			);
			$sortable_columns['vidrack_video_ip'] = array(
				'vidrack_video_ip',
				'desc',
			);
			$sortable_columns['vidrack_video_rating'] = array(
				'vidrack_video_rating',
				'desc',
			);
			$sortable_columns['vidrack_video_external_id'] = array(
				'vidrack_video_external_id',
				'desc',
			);
			$sortable_columns['vidrack_video_tag'] = array(
				'vidrack_video_tag',
				'desc',
			);
			$sortable_columns['vidrack_video_desc'] = array(
				'vidrack_video_desc',
				'desc',
			);

			if ( $pro_account ) {
				$sortable_columns['vidrack_name'] = array(
					'vidrack_name',
					'desc',
				);
				$sortable_columns['vidrack_email'] = array(
					'vidrack_email',
					'desc',
				);
				$sortable_columns['vidrack_phone'] = array(
					'vidrack_phone',
					'desc',
				);
				$sortable_columns['vidrack_birthday'] = array(
					'vidrack_birthday',
					'desc',
				);
				$sortable_columns['vidrack_location'] = array(
					'vidrack_location',
					'desc',
				);
				$sortable_columns['vidrack_language'] = array(
					'vidrack_language',
					'desc',
				);
				$sortable_columns['vidrack_additional_data'] = array(
					'vidrack_additional_data',
					'desc',
				);
				$sortable_columns['vidrack_capture_url'] = array(
					'vidrack_capture_url',
					'desc',
				);
				$sortable_columns['vidrack_custom_data_1'] = array(
					'vidrack_custom_data_1',
					'desc',
				);
				$sortable_columns['vidrack_custom_data_2'] = array(
					'vidrack_custom_data_2',
					'desc',
				);
				$sortable_columns['vidrack_custom_data_3'] = array(
					'vidrack_custom_data_3',
					'desc',
				);
			}

			return $sortable_columns;
		}

		/**
		 * Custom queries for posts grid when sort.
		 *
		 * @param object $object existing pieces of query.
		 * @todo refactor this
		 * @todo need pro check?
		 */
		static function add_columns_request( $object ) {
			$isset_meta_key = false;
			switch ( $object->get( 'orderby' ) ) {
				case 'vidrack_video_ip':
					$object->set( 'meta_key', '_vidrack_ip' );
					$isset_meta_key = true;
					break;
				case 'vidrack_video_rating':
					$object->set( 'meta_key', '_vidrack_video_rating' );
					$isset_meta_key = true;
					break;
				case 'vidrack_video_external_id':
					$object->set( 'meta_key', '_vidrack_external_id' );
					$isset_meta_key = true;
					break;
				case 'vidrack_video_tag':
					$object->set( 'meta_key', '_vidrack_tag' );
					$isset_meta_key = true;
					break;
				case 'vidrack_video_desc':
					$object->set( 'meta_key', '_vidrack_desc' );
					$isset_meta_key = true;
					break;
				case 'vidrack_name':
					$object->set( 'meta_key', '_vidrack_name' );
					$isset_meta_key = true;
					break;
				case 'vidrack_email':
					$object->set( 'meta_key', '_vidrack_email' );
					$isset_meta_key = true;
					break;
				case 'vidrack_phone':
					$object->set( 'meta_key', '_vidrack_phone' );
					$isset_meta_key = true;
					break;
				case 'vidrack_birthday':
					$object->set( 'meta_key', '_vidrack_birthday' );
					$isset_meta_key = true;
					break;
				case 'vidrack_location':
					$object->set( 'meta_key', '_vidrack_location' );
					$isset_meta_key = true;
					break;
				case 'vidrack_language':
					$object->set( 'meta_key', '_vidrack_language' );
					$isset_meta_key = true;
					break;
				case 'vidrack_additional_data':
					$object->set( 'meta_key', '_vidrack_additional_data' );
					$isset_meta_key = true;
					break;
				case 'vidrack_capture_url':
					$object->set( 'meta_key', '_vidrack_capture_url' );
					$isset_meta_key = true;
					break;
				case 'vidrack_custom_data_1':
					$object->set( 'meta_key', '_vidrack_custom_data_1' );
					$isset_meta_key = true;
					break;
				case 'vidrack_custom_data_2':
					$object->set( 'meta_key', '_vidrack_custom_data_2' );
					$isset_meta_key = true;
					break;
				case 'vidrack_custom_data_3':
					$object->set( 'meta_key', '_vidrack_custom_data_3' );
					$isset_meta_key = true;
					break;
				default:
					return;
			} // End switch().

			if ( $isset_meta_key ) {
				$object->set( 'orderby', 'meta_value_num' );
			}
		}

		/**
		 * Frontend vidrack post head.
		 */
		static function vidrack_post_head() {
			global $post;
			if( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {
				$title = 'Vidrack Video';
				$desc = ( get_post_meta( $post->ID, '_vidrack_desc', true ) ) ? get_post_meta( $post->ID, '_vidrack_desc', true ) : 'Created by Vidrack Video Plugin';
				$output ='
					<meta property="og:title" content="'.$title.'" />
					<meta property="og:description" content="'.$desc.'" />
					<meta property="og:image" content="' . VIDRACK_ASSETS_DIR_URL . 'img/banner-500x162.jpg" />
					<meta property="og:type" content="article"/>
					';
				echo $output;
			}
		}

		/**
		 * Frontend vidrack post title.
		 */
		static function vidrack_post_title( $title ) {
			global $post;
			if( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {
				$title = substr( $title, 0 , ( strrpos( $title, "." ) ) );
			}
			return $title;
		}

		/**
		 * Frontend vidrack post content.
		 */
		static function vidrack_post_content( $content ) {
			global $post;

			if( isset( $post->post_type ) && 'vidrack_video' === $post->post_type ) {

				$video_format = substr( $post->post_title, -3 );

				switch ( $video_format ) {
					case 'flv':
						$video_format = 'flv';
						break;
					case 'mp4':
						$video_format = 'mp4';
						break;
					default:
						$video_format = 'webm';
				}

				$content =
					'
					<video
						id="my-player"
						class="video-js vidrack-player"
						controls
						preload="auto"
						poster="https://s3.amazonaws.com/vidrack-transcoder-thumbnails/' . $post->post_title . '.mp4-00001.png"
						data-setup=\'{}\'>
						
						<source src="https://' . vidrack_get_s3_bucket_name() . '.s3.amazonaws.com/' . $post->post_title . '" type="video/' . $video_format . '"></source>
						
						<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank"> supports HTML5 video</a></p>
					</video>
					<p>Powered by <a href="https://vidrack.com" target="_blank">vidrack.com</a></p>
					';
			}
			return $content;
		}

		/**
		 * Check for version update.
		 */
		private static function update_check() {
			/*$installed_ver = get_option( 'vidrack_version' );

			if ( $installed_ver === VIDRACK_VERSION ) {
				return;
			}

			// [1.6] Remove old options.
			if ( version_compare( $installed_ver, '1.6', '<' ) ) {
				delete_option( 'registration_email' );
				delete_option( 'display_branding' );
			}

			// [1.7.1] Migrate videos table to custom posts and add JS callback option.
			if ( version_compare( $installed_ver, '1.7.1', '<' ) ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'video_capture';

				// Migrate data.
				$items = $wpdb->get_results( $wpdb->prepare( 'SELECT filename, ip, uploaded_at FROM  %s', $table_name ) ); // Db call ok.
				foreach ( $items as $item ) {
					$video = array(
						'post_type' => 'vidrack_video',
						'post_title' => $item->filename,
						'post_status' => 'publish',
						'post_date' => $item->uploaded_at,
					);
					$post_id = wp_insert_post( $video, true );
					add_post_meta( $post_id, '_vidrack_ip', $item->ip, true );
				}

				// Remove old database table.
				$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', $table_name ) ); // Db call ok.
			}*/

			// Bump up the version after successful update.
			update_option( 'vidrack_version', VIDRACK_VERSION );
		}

		/**
		 * Add options on init.
		 */
		private static function add_options() {
			// Add settings options.
			// 'add_option' does nothing if option already exists.
			add_option( 'vidrack_js_callback' );
			add_option( 'vidrack_desktop_upload' );
			add_option( 'vidrack_window_modal', 1 );
			add_option( 'vidrack_collect_name', 'no' );
			add_option( 'vidrack_collect_email', 'no' );
			add_option( 'vidrack_collect_phone', 'no' );
			add_option( 'vidrack_collect_birthday', 'no' );
			add_option( 'vidrack_collect_location', 'no' );
			add_option( 'vidrack_collect_language', 'no' );
			add_option( 'vidrack_collect_additional_data', 'no' );
			add_option( 'vidrack_collect_capture_url', 'no' );
			add_option( 'vidrack_custom_collect_data_name_1', 'Name' );
			add_option( 'vidrack_custom_collect_data_value_1', 'no' );
			add_option( 'vidrack_custom_collect_data_name_2', 'Name' );
			add_option( 'vidrack_custom_collect_data_value_2', 'no' );
			add_option( 'vidrack_custom_collect_data_name_3', 'Name' );
			add_option( 'vidrack_custom_collect_data_value_3', 'no' );
			add_option( 'vidrack_version', VIDRACK_VERSION );
		}

	}

}
