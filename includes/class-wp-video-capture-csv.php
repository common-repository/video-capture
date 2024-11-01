<?php

if ( ! class_exists( 'WP_Video_Capture_CSV' ) ) {

	class WP_Video_Capture_CSV {

		/**
		 * Constructor.
		 */
		public function __construct() {
			//Custom Vidrack actions.
			add_action( 'template_redirect', array( __CLASS__, 'custom_actions') );

			//Vidrack export CSV link.
			add_action('admin_head-edit.php', array( __CLASS__, 'export_btn') );
		}

		/**
		 * Add Vidrack CSV export links.
		 *
		 * @todo Refactoring
		 */
		public static function export_btn(){
			global $post;
			global $pro_account;
			if ( isset( $post->post_type ) && 'vidrack_video' === $post->post_type && $pro_account ) {
				$export_csv_url = '/?vidrack_action=csv_video_export&nonce='.wp_create_nonce("vidrack_nonce_secret");
				echo "<script type='text/javascript'>
                        jQuery(document).ready( function($) {
                             jQuery('.wrap h1:first-child').append('<a href=$export_csv_url target=_blank class=page-title-action >CSV Export</a>');
                        });
                     </script>";
			}
		}

		/**
		 * Vidrack run custom actions.
		 */
		public static function custom_actions() {
			if ( isset( $_GET['vidrack_action'] ) && isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_key( $_GET['nonce'] ), 'vidrack_nonce_secret' )  ) { // Input var "post_type" is set.
				$action = $_GET['vidrack_action'];
				switch ($action){
					case 'csv_video_export':
						self::csv_video_export();
						break;
					default:
						break;
				}
				return;
			} else{
				return;
			}
		}

		/**
		 * Vidrack video posts CSV export.
		 *
		 * @todo custom_collect_data set isset
		 */
		public static function csv_video_export() {

			$filename = time()."_vidrack.csv";

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=' . $filename);

			$output = fopen('php://output', 'w');

			// Headings.
			$csv_headings = array(
				__( 'Title' ),
				__( 'Download link' ),
				__( 'IP' ),
				__( 'Rating' ),
				__( 'External ID' ),
				__( 'Tags' ),
				__( 'Date' ),
				__( 'Description' ),
				__( 'Name' ),
				__( 'Email' ),
				__( 'Phone' ),
				__( 'Birthday' ),
				__( 'Location' ),
				__( 'Language' ),
				__( 'Additional Data' ),
				get_option( 'vidrack_custom_collect_data_name_1' ),
				get_option( 'vidrack_custom_collect_data_name_2' ),
				get_option( 'vidrack_custom_collect_data_name_3' ),
			);
			fputcsv( $output, $csv_headings );

			// Rows.
			$query = array(
				'post_type' => 'vidrack_video',
				'posts_per_page' => -1
			);

			$posts = get_posts( $query );

			foreach ($posts as $post) {

				$post_id = $post->ID;
				$post_meta = get_post_meta( $post_id );

				$post_title = $post->post_title;
				$post_download_link = 'https://' . vidrack_get_s3_bucket_name() . '.s3.amazonaws.com/'.$post->post_title;
				$post_ip = isset( $post_meta['_vidrack_ip'][0] ) ? $post_meta['_vidrack_ip'][0] : '';
				$post_rating = isset( $post_meta['_vidrack_video_rating'][0] ) ? $post_meta['_vidrack_video_rating'][0] : '';
				$post_external_id = isset( $post_meta['_vidrack_external_id'][0] ) ? $post_meta['_vidrack_external_id'][0] : '';
				$post_tags = isset( $post_meta['_vidrack_tag'][0] ) ? $post_meta['_vidrack_tag'][0] : '';
				$post_date = $post->post_date;
				$post_description = isset( $post_meta['_vidrack_desc'][0] ) ? $post_meta['_vidrack_desc'][0] : '';
				$post_name = isset( $post_meta['_vidrack_name'][0] ) ? $post_meta['_vidrack_name'][0] : '';
				$post_email = isset( $post_meta['_vidrack_email'][0] ) ? $post_meta['_vidrack_email'][0] : '';
				$post_phone = isset( $post_meta['_vidrack_phone'][0] ) ? $post_meta['_vidrack_phone'][0] : '';
				$post_birthday = isset( $post_meta['_vidrack_birthday'][0] ) ? $post_meta['_vidrack_birthday'][0] : '';
				$post_location = isset( $post_meta['_vidrack_location'][0] ) ? $post_meta['_vidrack_location'][0] : '';
				$post_language = isset( $post_meta['_vidrack_language'][0] ) ? $post_meta['_vidrack_language'][0] : '';
				$post_additional_data = isset( $post_meta['_vidrack_additional_data'][0] ) ? $post_meta['_vidrack_additional_data'][0] : '';
				$post_vidrack_custom_data_1 = isset( $post_meta['_vidrack_custom_data_1'][0] ) ? $post_meta['_vidrack_custom_data_1'][0] : '';
				$post_vidrack_custom_data_2 = isset( $post_meta['_vidrack_custom_data_2'][0] ) ? $post_meta['_vidrack_custom_data_2'][0] : '';
				$post_vidrack_custom_data_3 = isset( $post_meta['_vidrack_custom_data_3'][0] ) ? $post_meta['_vidrack_custom_data_3'][0] : '';


				fputcsv( $output, array(
					$post_title,
					$post_download_link,
					$post_ip,
					$post_rating,
					$post_external_id,
					$post_tags,
					$post_date,
					$post_description,
					$post_name,
					$post_email,
					$post_phone,
					$post_birthday,
					$post_location,
					$post_language,
					$post_additional_data,
					$post_vidrack_custom_data_1,
					$post_vidrack_custom_data_2,
					$post_vidrack_custom_data_3,
				) );

			}

			fclose( $output );
			exit;

		}

	}

}
new WP_Video_Capture_CSV();
