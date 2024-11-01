let vidrackAdminAjax = (function ($) {

	postToWPDone = function(response) {
		! response.success ? alert( response.data ) : null;
	};

	return {
		postToWPDone
	}

})(jQuery);

let vidrackAdmin = (function ($) {

	let $postsLint = $('#the-list');
	let $postsRatingColumn = $postsLint.find('.column-vidrack_video_rating');

	$postsRatingColumn.each(function(){

		//let $this = $(this);
		let ratingVal = this.innerText;
		this.innerHTML = '';

		$(this).starRating({
			setRating: 1,
			activeColor: '#FFA500',
			useGradient: false,
			starSize: 16,
			disableAfterRate: false,
			initialRating: ratingVal,
			callback: function(currentRating, $el){
				let post_id =  $el.parent().attr('id').split('-').pop();

				let data = {
					action: 'set_rating_video',
					nonce: vidrack_ajax.nonce,
					post_id: post_id,
					rating_value: currentRating
				};

				$.post(vidrack_ajax.ajax_url, data)
					.done(vidrackAdminAjax.postToWPDone);
			}
		});

	});

	// Play video
	let $btnPlay = $('.vidrack-play-video-link');

	$btnPlay.on('click', function(e){
		e.preventDefault();

		let $this = $(this);
		let videoLink = this.href;
		let player;

		let videoFormat = videoLink.slice(-3);

		switch(videoFormat) {
			case 'flv':
				videoFormat = 'flv';
				break;
			case 'mp4':
				videoFormat = 'mp4';
				break;
			default:
				videoFormat = 'webm';
		}

		let bucketName = vidrack_s3.bucket;
		let filename = vidrack_s3.dashboard ? $this.attr('data-vidrack-title') : $this.parents('.column-primary').find('.vidrack-list-table__title').text();
		let videoLinkMP4;
		let videoSourceNative;
		let videoSourceMp4;

		if ( 'vidrack-media' === bucketName ) {
			videoLinkMP4 = 'https://vidrack-transcoder-output.s3.amazonaws.com/' + filename + '.' + videoFormat + '.mp4';

			// check video mp4 200
			$.ajax({
				url: videoLinkMP4,
				type: 'HEAD',
				error: function(){
					videoSourceNative = '<source src="' + videoLink + '" type="video/' + videoFormat + '"></source>';
				},
				success: function(data){
					videoSourceMp4 = '<source src="https://vidrack-transcoder-output.s3.amazonaws.com/' + filename + '.' + videoFormat + '.mp4" type="video/mp4"></source>';
				},
				complete: function(){
					openVideoPopup();
				}
			});

		}

		function openVideoPopup() {
			let popupPlayerOptions = {
				items: {
					src:
					'<div class="vidrack-admin-mfp vidrack-admin-mfp_player">' +
					'<video id="vidrack-player" class="video-js vidrack-player" >' +
					( videoSourceMp4 ? videoSourceMp4 : videoSourceNative ) +
					'<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank"> supports HTML5 video</a></p>' +
					'</video>' +
					'</div>',
					type: 'inline'
				},
				callbacks: {
					beforeOpen: function() {
						console.log('Start of popup initialization');
					},
					open: function() {
						let options = {
							autoplay: true,
							controls: true,
							preload: 'auto',
							src: videoSourceMp4 ? videoLinkMP4 : videoLink,
						};
						player = videojs('vidrack-player', options, function onPlayerReady() {
							//videojs.log('Your player is ready!');

							// In this context, `this` is the player that was created by Video.js.
							//this.play();

							// How about an event listener?
							//this.on('ended', function() {
							//	videojs.log('Awww...over so soon?!');
							//});
						});
					},
					close: function() {
						player.dispose();
					}
				}
			};

			$this.magnificPopup(popupPlayerOptions).magnificPopup('open');
		}
	});

})(jQuery);
