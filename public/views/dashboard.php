<?php
	global $current_user;
	$user_id = $current_user->ID;
?>

<?php if ( $user_id ) : ?>

	<?php
		$args = array(
			'post_type' => 'vidrack_video',
			'author' => $user_id,
			'posts_per_page' => 9999,
		);
		$the_query = new WP_Query( $args );
	?>

	<?php if ( $the_query->have_posts() ) : ?>
		<ul class="vidrack-dashboard">

		<?php while ( $the_query->have_posts() ) : ?>
			<?php
				$the_query->the_post();

				$post_id = get_the_ID();
				$post_title = get_post( $post_id )->post_title;
				$video_name = explode( '.', $post_title )[0];
			?>
			<li class="vidrack-dashboard__item">

				<a href="https://<?php echo vidrack_get_s3_bucket_name(); ?>.s3.amazonaws.com/<?php echo $post_title; ?>"
				   title="Play"
				   class="vidrack-play-video-link vidrack-dashboard__thumbnail"
				   data-vidrack-title="<?php echo $video_name; ?>">
					<img src="<?php echo 'https://s3.amazonaws.com/vidrack-transcoder-thumbnails/' . $post_title . '.mp4-00001.png'; ?>"
						 onerror="this.onerror=null;this.src='<?php echo VIDRACK_ASSETS_DIR_URL ?>img/vidrack-video-thumb_192.png';"
						 alt="">
				</a>

				<!--<div class="vidrack-item__title">--><?php //echo $post_title ?><!--</div>-->
				<ul class="vidrack-dashboard__actions">
					<li class="vidrack-actions__item">
						<a class="vidrack-play-video-link"
						   href="https://<?php echo vidrack_get_s3_bucket_name(); ?>.s3.amazonaws.com/<?php echo $post_title; ?>"
						   data-vidrack-title="<?php echo $video_name; ?>"><?php echo __( 'Play', 'video-capture' ) ?></a>
					</li>
					<li class="vidrack-actions__item">
						<a download href="https://s3.amazonaws.com/vidrack-transcoder-output/<?php echo $post_title ?>.mp4"><?php echo __( 'Download', 'video-capture' ) ?></a>
					</li>
				</ul>
			</li>
		<?php endwhile; ?>

		</ul>
	<?php else : ?>
		<?php _e( 'There were no videos', 'video-capture' ); ?>
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>

<?php else : ?>
	<?php _e( 'You are not authorized', 'video-capture' ); ?>
<?php endif;
