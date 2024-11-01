let vidrackAdminOptions = {
	popupConf : {
		items: {
			src: '<div class="vidrack-admin-mfp vidrack-admin-mfp_dialog">' +
			'<div class="vidrack-admin-mfp_title">Uploading video to YouTube</div>' +
			'<div class="vidrack-admin-mfp_message">Please wait ...</div>' +
			'</div>',
			type: 'inline'
		},
		showCloseBtn: false
	},
};

let vidrackYT = (function ($) {

	let ajaxurl = YouTubeUpload.ajaxurl;
	let nonce = YouTubeUpload.nonce;

	let btnToYT = $('.upload-video-to-youtube');

	btnToYT.on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();

		let $this = $(this);

		let isUserHasAPIApp = $this.data('has-application');
		if (!isUserHasAPIApp) {
			alert('Please add YouTube credentials in Vidrack Settings');
			return;
		}

		let result = confirm('Do you want to upload this video to YouTube?');
		if(!result) return;

		let is_oauth = $this.data('is-oauth');
		if(is_oauth) {
			youtubeAction($this);
			return;
		}

		let auth_url = $this.data('auth-url');
		if(auth_url) {
			let oauth_window = window.open(auth_url, 'authentication', 'width=600,height=400');
			let timer = setInterval(function() {
				if(oauth_window.closed) {
					clearInterval(timer);
					youtubeAction($this);
				}
			}, 500);
		} else youtubeAction($this);
	});

	function youtubeAction($link) {

		let videoLink = $link.attr('href');
		let videoPostID = $link.data('post-id');

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: "json",
			data: {
				action: 'youtubeAction',
				video_link: videoLink,
				post_id: videoPostID,
				nonce: nonce,
			},
			beforeSend: function(){
				$link.magnificPopup(vidrackAdminOptions.popupConf).magnificPopup('open');
			},
			error: function(){
				alert( 'An error occurred!' );
			},
			success: function(data){
				if ( data.status &&  'success' === data.status ) {
					alert( 'Video was successfully uploaded!' );
				}
				else{
					alert(data.message);
				}

			},
			complete: function(){
				$link.magnificPopup(vidrackAdminOptions.popupConf).magnificPopup('close');
			}
		})
	}

})(jQuery);
