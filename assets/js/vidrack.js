let vidrackOptions = {
	isPopup : vidrack_ajax.popup,
	ip : vidrack_ajax.ip,
	callback : vidrack_ajax.js_callback,
	swfLink : vidrack_ajax.recorder_swf_link,
	popupConf : {
		type: 'inline',
		midClick: true
	},
	popupConfFlash : {
		items: {
			src:
			'<div id="' + (vidrack_ajax.popup ? "vidrack-popup_flash" : "vidrack-flash") + '" class="vidrack-recorder-wrapper">' +
				'<div id="wp-video-capture-flash">' +
					'<p>Your browser doesn\'t support Adobe Flash, sorry</p>' +
					'<p>Please install Adobe Flash plugin <a href="https://get.adobe.com/flashplayer/" target="_blank">Get Flash Player</a></p>' +
				'</div>' +
			'</div>',
			type: 'inline'
		},
		callbacks: {
			open: function () {

				let flashvars = {
					ajaxurl: vidrack_ajax.ajax_url,
					ip: vidrackOptions.ip,
					email: '',
					name: '',
					phone: '',
					birthday: '',
					location: '',
					language: '',
					additional_data: '',
					capture_url: '',
					custom_fields: '',
					external_id: '',
					tag: '',
					desc: '',
					js_callback: vidrackOptions.js_callback,
					site_name: window.location.hostname.replace(/\./g, '-'),
					backLink: '',
					nonce: vidrack_ajax.nonce
				};

				// Embed SWFObject
				swfobject.embedSWF(
					vidrackOptions.swfLink,
					'wp-video-capture-flash',
					'420', // Width
					'350', // Height
					'9',   // Flash version
					'',
					flashvars
				);
			},
		}
	},
	btnFlashInit : '<button type="button" name="vidrack__button" class="vidrack__button_flash">Record Video</button>',
	btniOSRecord : '<button type="button" class="vidrack__button_ios">Record Video</button>',
	storageUrl : 'https://storage.vidrack.com',
};

let vidrackView = (function ($) {

	let doc = document;
	let _recorderBox = doc.querySelector('.vidrack-recorder-wrapper');
	let videoBox = _recorderBox.querySelector('.vidrack__video-wrapper');
	let videoElement = videoBox.querySelector('video');
	let recorderControlsBox = videoBox.querySelector('.vidrack__recorder-controls');
	let recorderControlsBtn = recorderControlsBox.querySelector('.btn-recorder');
	let vidrackLoader = doc.getElementById('vidrack__loading');

	let $showRecorderBtn = $('.vidrack__button');
	let $uploadVideoBtn = $('.vidrack-uploader__button');
	let $uploadVideoInputs = $('.vidrack-uploader__file-selector');

	$getCollectDataBoxFromSibling = function(siblingElement){
		return siblingElement.parent('.vidrack-recorder').siblings('.vidrack-collect-data');
	};

	openPopup = function(element){
		navigator.getUserMedia && !vidrackView.isBrowserFromFallback()
			? element.magnificPopup(vidrackOptions.popupConf).magnificPopup('open')
			: element.magnificPopup(vidrackOptions.popupConfFlash).magnificPopup('open');
	};

	getDataAttr = function(video, attr) {
		return attr ? video.dataset[attr] : null
	};

	/**
	 * Validate Form
	 */
	validateForm = function($collectDataBox){
		let $vidrackCollectItems = $collectDataBox.find('.vidrack-collect-data__item');
		let isValid = true;

		$.each($vidrackCollectItems, function(index, value){
			if (!value.checkValidity()) {
				$(value).addClass('vidrack-item_invalid');
				isValid = false;
			} else {
				$(value).removeClass('vidrack-item_invalid');
			}
		});

		return isValid;
	};

	/**
	 * Reset .vidrack-item_invalid onchange
	 */
	let $collectDataItems = $('.vidrack-collect-data__item');
	$collectDataItems.on('keypress', function () {
		$this = $(this);

		$this.hasClass('vidrack-item_invalid') ? $this.removeClass('vidrack-item_invalid') : null;
	});

	/**
	 * Smooth Scroll To
	 */
	scrollTo = function ($element) {
		$('html, body').animate({
			scrollTop: $element.offset().top - 40
		}, 500);

		return false;
	};

	/**
	 * Generate Message
	 */
	generateSubmitFileMessage = function(message, status){
		return '<div class="' + status + '">' + message + '</div>';
	};

	/**
	 * Set record timer
	 */
	setRecordTimer = function(element, hh, mm, ss) {
		//element.innerText = `${hh}:${mm}:${ss}`;
		element.innerText = hh + ":" + mm + ":" + ss;
	};

	/**
	 * Is Edge browser
	 */
	function _isEdge() {
		return /Edge/.test(navigator.userAgent) ? true : false;
	}

	/**
	 * Is IE browser
	 * MSIE for IE<=10, Trident for IE11
	 */
	function _isIE() {
		return /MSIE|Trident/.test(navigator.userAgent) ? true : false;
	}

	/**
	 * Is Safari Browser
	 */
	function _isSafari() {
		return window.hasOwnProperty('safari');
	}

	/**
	 * Is iOS
	 */
	function isiOS() {
		return /iPad|iPhone|iPod|iPad Simulator|iPhone Simulator|iPod Simulator/.test(navigator.userAgent) && !window.MSStream;
	}

	/**
	 * Flash fallback
	 */
	function isBrowserFromFallback() {
		return _isEdge() || _isIE() || _isSafari();
	}

	/**
	 * When document ready
	 */
	$(document).ready(function() {
		let $vidrackWrapper = $('.wp-video-recorder');
		let $vidrackRecorderButtons = $vidrackWrapper.find('.vidrack-recorder');
		let $vidrackRecorderWrapper = $('.vidrack-recorder-wrapper');

		if( vidrackView.isiOS() ) {

			$vidrackRecorderWrapper.remove();
			let $vidrackUploaderForm = $('.vidrack-uploader__form');
			$vidrackUploaderForm.prepend(vidrackOptions.btniOSRecord);

			$vidrackInitiOsButton = $('.vidrack__button_ios');
			$vidrackInitiOsButton.on('click', vidrack.uploadBtn );

		} else if( (!navigator.getUserMedia || vidrackView.isBrowserFromFallback()) && !vidrackOptions.isPopup && !vidrackView.isiOS()) {

			$vidrackRecorderWrapper.remove();
			$vidrackRecorderButtons.prepend(vidrackOptions.btnFlashInit);

			$vidrackInitFlash = $('.vidrack__button_flash');

			$vidrackInitFlash.on('click', function(e){
				e.preventDefault();

				$.post( vidrackOptions.storageUrl + '/extra', 'FLASH' );

				let $this = $(this);
				let $collectDataBox = $getCollectDataBoxFromSibling($this);

				if(vidrackView.validateForm($collectDataBox)) {
					$this.remove();
					$vidrackWrapper.prepend(vidrackOptions.popupConfFlash.items.src);
					vidrackOptions.popupConfFlash.callbacks.open();
				} else {
					vidrackView.scrollTo($collectDataBox)
				}
			});

		}
	});

	return {
		videoBox: videoBox,
		videoElement: videoElement,
		recorderControlsBox: recorderControlsBox,
		recorderControlsBtn: recorderControlsBtn,
		vidrackLoader: vidrackLoader,
		$showRecorderBtn: $showRecorderBtn,
		$uploadVideoBtn: $uploadVideoBtn,
		$uploadVideoInputs: $uploadVideoInputs,
		$getCollectDataBoxFromSibling: $getCollectDataBoxFromSibling,
		scrollTo: scrollTo,
		openPopup: openPopup,
		getDataAttr: getDataAttr,
		validateForm: validateForm,
		generateSubmitFileMessage: generateSubmitFileMessage,
		setRecordTimer: setRecordTimer,
		isBrowserFromFallback: isBrowserFromFallback,
		isiOS: isiOS,
	};
})(jQuery);

let vidrackAjax = (function ($) {

	/**
	 * Upload File
	 */
	uploadFileXHRPregress = function($uploaderProgressBar, $uploaderProgressPercent){
		return function(e){
			if(e.lengthComputable){
				let max = e.total;
				let current = e.loaded;
				let percentage = (current * 100)/max;
				let left = percentage - 100;
				console.log(percentage);
				if (percentage < 95 ) {
					$uploaderProgressBar.css('left', left + '%');
					$uploaderProgressPercent.text(Math.round(percentage) + '%');
				}
			}
		}
	};

	uploadFileBeforeSend = function($uploaderProgressWrap, $uploaderProgressPercent) {
		$uploaderProgressWrap.addClass('vidrack__loading_show');
		$uploaderProgressPercent.addClass('vidrack__loading-percent_show');
	};

	uploadFileError = function($uploaderProgressWrap, $uploaderProgressPercent, $uploaderMessage){
		return function(response) {
			console.log(response.status + ': ' + response.message);
			$uploaderProgressWrap.removeClass('vidrack__loading_show');
			$uploaderProgressPercent.removeClass('vidrack__loading-percent_show');
			let content = vidrackView.generateSubmitFileMessage('Error uploading video (AJAX): ' + response.message, 'error');
			$uploaderMessage.html( content );
		}
	};

	uploadFileSuccess = function($uploaderMessage, $uploaderProgressWrap, $uploaderProgressBar, $uploaderProgressPercent, callback){
		return function(response) {
			console.log(response.status + ': ' + response.message);
			let content = vidrackView.generateSubmitFileMessage('Success uploading video: ' + response.message, response.status);
			$uploaderMessage.html( content );
			$uploaderProgressBar.css('left', '-3%');
			$uploaderProgressPercent.text('97%');

			callback();
		}
	};

	/**
	 * Post to Wordpress
	 */
	postToWPDone = function(uploader, filename, ip, external_id) {
		return function(response){
			if (response.success) {
				console.log(response.data.message);
				if(uploader){
					uploader.$uploaderProgressWrap.removeClass('vidrack__loading_show');
					uploader.$uploaderProgressPercent.removeClass('vidrack__loading-percent_show');
					let content = vidrackView.generateSubmitFileMessage('Success storing video', 'success');
					uploader.$uploaderMessage.html( content );
				}

				if (vidrackOptions.callback) {
					let callback = new Function('filename, ip, external_id', vidrackOptions.callback );
					callback(filename, ip, external_id);
				}

			} else {
				console.log('Error storing video (AJAX): ' + response.data.message);
				if(uploader){
					uploader.$uploaderProgressWrap.removeClass('vidrack__loading_show');
					uploader.$uploaderProgressPercent.removeClass('vidrack__loading-percent_show');
					let content = vidrackView.generateSubmitFileMessage('Error storing video (AJAX): ' + response.data.message, 'error');
					uploader.$uploaderMessage.html( content );
				}
			}
		}
	};

	return {
		uploadFileXHRPregress: uploadFileXHRPregress,
		uploadFileBeforeSend: uploadFileBeforeSend,
		uploadFileError: uploadFileError,
		uploadFileSuccess: uploadFileSuccess,
		postToWPDone: postToWPDone,
	};
})(jQuery);

let vidrack = (function ($) {

	/**
	 * Show Recorder BTN
	 */
	vidrackView.$showRecorderBtn.on('click', function (e) {
		e.preventDefault();

		let $this = $(this);
		let $collectDataBox = $getCollectDataBoxFromSibling($this);

		vidrackView.validateForm($collectDataBox) ? vidrackView.openPopup($this) : vidrackView.scrollTo($collectDataBox);
	});

	/**
	 * Upload file BTN
	 */
	vidrackView.$uploadVideoBtn.on('click', uploadBtn);

	/**
	 * Upload File Btn function
	 */
	function uploadBtn(e) {
		let $this = $(this);
		let $collectDataBox = $getCollectDataBoxFromSibling($this.parents('.vidrack-uploader'));
		let $uploadVideoInput = $this.siblings('.vidrack-uploader__file-selector');

		vidrackView.validateForm($collectDataBox) ? $uploadVideoInput.trigger('click') : vidrackView.scrollTo($collectDataBox);
	}

	/**
	 * Submit upload video
	 */
	vidrackView.$uploadVideoInputs.on('change', function() {

		let $this = $(this);
		let localPathToFile = $this.val().replace(/.*(\/|\\)/, '');

		// Get extension before sanitizing file name
		let extensionRegexp = /(?:\.([^.]+))?$/;
		let extension = extensionRegexp.exec(localPathToFile)[1].toLowerCase();

		// Sanitize filename
		let filename = vidrack.getFilename(extension);

		console.log('Submitting file "' + filename + '"');

		let form_data = new FormData();
		form_data.append('filename', filename);
		form_data.append('video', $this[0].files[0]);
		form_data.append('aws', vidrack_s3.s3);
		form_data.append('s3_bucket', vidrack_s3.bucket);
		// Vidrack fallback
		form_data.append('version', 2);

		let $uploaderWrapper = $this.parents('.vidrack-uploader');
		let $uploaderMessage = $uploaderWrapper.find('.vidrack-uploader__message');
		let $uploaderProgressWrap = $uploaderWrapper.find('.vidrack__loading');
		let $uploaderProgressBar = $uploaderProgressWrap.find('.vidrack__loading-bar');
		let $uploaderProgressPercent = $uploaderWrapper.find('.vidrack__loading-percent');


		$.ajax({
			url: vidrackOptions.storageUrl + '/video',
			type: 'POST',
			contentType: false,
			data: form_data,
			async: true,
			cache: false,
			processData: false,
			xhr: function() {
				let xhr = $.ajaxSettings.xhr();
				xhr.upload
					? xhr.upload.addEventListener('progress', vidrackAjax.uploadFileXHRPregress($uploaderProgressBar, $uploaderProgressPercent))
					: null;
				return xhr;
			},
			beforeSend: vidrackAjax.uploadFileBeforeSend($uploaderProgressWrap, $uploaderProgressPercent),
			error: vidrackAjax.uploadFileError($uploaderProgressWrap, $uploaderProgressPercent, $uploaderMessage),
			success: vidrackAjax.uploadFileSuccess($uploaderMessage, $uploaderProgressWrap, $uploaderProgressBar, $uploaderProgressPercent, function(){
				postToWP(filename, {
					$uploaderProgressWrap: $uploaderProgressWrap,
					$uploaderProgressPercent: $uploaderProgressPercent,
					$uploaderMessage: $uploaderMessage
				});
			}),
			complete: function() {
				console.log('Vidrack video upload complete');
			},
		})
	});

	/**
	 * Post to WP
	 */
	postToWP = function(filename, uploader) {
		let dataJSON = {
			"filename": filename,
			"ip": vidrackOptions.ip,
			"external_id": vidrackView.getDataAttr( vidrackView.videoElement, 'externalId' ),
			"tag": vidrackView.getDataAttr( vidrackView.videoElement, 'tag' ),
			"desc": vidrackView.getDataAttr( vidrackView.videoElement, 'desc' ),
			"name": $('#vidrack-capture-name').val(),
			"email": $('#vidrack-capture-email').val(),
			"phone": $('#vidrack-capture-phone').val(),
			"birthday": $('#vidrack-capture-birthday').val(),
			"location": $('#vidrack-capture-location').val(),
			"language": $('#vidrack-capture-language').val(),
			"additional_data": $('#vidrack-capture-additional-data').val(),
			"capture_url": location.href,
		};
		let data = {
			action: 'store_video_file',
			nonce: vidrack_ajax.nonce,
			video_data: JSON.stringify(dataJSON)
		};
		$.post(vidrack_ajax.ajax_url, data)
			.done(vidrackAjax.postToWPDone(uploader, dataJSON.filename, dataJSON.ip, dataJSON.external_id));
	};

	// generating random string
	_generateRandomString = function() {
		let d = Date.now();
		return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
			let r = (d + Math.random() * 16) % 16 | 0;
			d = Math.floor(d / 16);
			return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
		});
	};

	// generating filename
	getFilename = function(extension) {
		let hostname = window.location.hostname.replace(/\./g, '-');
		let filename = hostname + '_' + _generateRandomString();

		return extension ? filename + '.' + extension : filename;
	};

	/**
	 * Recorder Timer
	 */
	let _recorderTimerVal = {
		hours : 0,
		minutes : 0,
		seconds : 0
	};

	_toDecimal = function(number){
		return number > 9 ? number : '0' + number;
	};

	_recorderTimer = function(element){

		if ( _recorderTimerVal.seconds >= 59 ){
			_recorderTimerVal.minutes += 1;
			_recorderTimerVal.seconds = 0;
		} else {
			_recorderTimerVal.seconds += 1;
		}

		if ( _recorderTimerVal.minutes >= 59 ){
			_recorderTimerVal.hours += 1;
			_recorderTimerVal.minutes = 0;
		}

		vidrackView.setRecordTimer(element, _toDecimal( _recorderTimerVal.hours ), _toDecimal( _recorderTimerVal.minutes ), _toDecimal( _recorderTimerVal.seconds ) );
	};

	recorderTimerStart = function(element){
		return setInterval(_recorderTimer, 1000, element);
	};

	return {
		postToWP: postToWP,
		getFilename: getFilename,
		recorderTimerStart: recorderTimerStart,
		uploadBtn: uploadBtn,
	}
})(jQuery);


/**
 * Vidrack + RecordRTC
 */
let vidrackWRTC = (function ($) {

	let recorder;

	// this function submits both audio/video or single recorded blob to nodejs server
	var postFiles = function(data, callback) {

		var request = new XMLHttpRequest();

		request.onreadystatechange = function() {
			if (request.readyState == 4 && request.status == 200) {
				callback(request.responseText);
			}
		};

		request.upload.onprogress = function(event) {
			console.log( 'Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%" );
		};

		request.open('POST', vidrackOptions.storageUrl + '/upload');

		var formData = new FormData();
		formData.append('file', data);
		if ( vidrack_s3.s3 && vidrack_s3.bucket ) {
			formData.append('aws', vidrack_s3.s3);
		}
		formData.append('s3_bucket', vidrack_s3.bucket);
		request.send(formData);
	};

	// when btnStopRecording is clicked
	onStopRecording = function() {
		var blob = recorder.getBlob();

		// getting unique identifier for the file name
		var fileName =  vidrack.getFilename('webm');

		var file = new File([blob], fileName, {
			type: 'video/webm'
		});

		postFiles(file, function(responseText) {

			vidrackView.vidrackLoader.classList.toggle('vidrack__loading_show');

			vidrackView.recorderControlsBox.style.height = 0;
			vidrackView.recorderControlsBox.style.overflow = 'hidden';
			vidrackView.videoBox.style.height = vidrackView.videoBox.offsetHeight - 70 + 'px';

			let filename = responseText;
			vidrackView.videoElement.src = 'https://' + vidrack_s3.bucket + '.s3.amazonaws.com/' + filename;
			vidrackView.videoElement.classList.toggle('video-js');
			vidrackView.videoElement.classList.toggle('vidrack-player');

			let options = {
				autoplay: true,
				controls: true,
				preload: 'auto',
				src: 'https://' + vidrack_s3.bucket + '.s3.amazonaws.com/' + filename,
			};

			let player = videojs(vidrackView.videoElement, options, function onPlayerReady() {});

			vidrack.postToWP(filename);
		});

		if(mediaStream) mediaStream.stop();
	};

	// part
	let mediaStream = null;

	// reusable getUserMedia
	captureUserMedia = function(success_callback) {
		let session = {
			audio: true,
			video: true
		};

		navigator.getUserMedia(session, success_callback, function(error) {
			alert('Unable to capture your camera. Please check console logs.');
			console.error(error);
		});
	};

	let recorderTimerID;

	// part
	// UI events handling
	vidrackView.recorderControlsBtn.onclick = function(e) {

		let $this = $(this);
		let isValid = true;

		if (!vidrackOptions.isPopup) {
			let $collectDataBox = $getCollectDataBoxFromSibling($this.parents('.vidrack-recorder-wrapper'));

			isValid = vidrackView.validateForm($collectDataBox) ? true : vidrackView.scrollTo($collectDataBox);
		}

		let task = vidrackView.recorderControlsBtn.dataset.vidrackTask;

		if ('record' === task && isValid) {
			captureUserMedia(function(stream) {
				mediaStream = stream;

				vidrackView.videoElement.src = window.URL.createObjectURL(stream);
				vidrackView.videoElement.play();
				vidrackView.videoElement.muted = true;
				vidrackView.videoElement.controls = false;

				recorder = RecordRTC(stream, {
					type: 'video'
				});

				recorder.startRecording();

				vidrackView.recorderControlsBtn.classList.toggle('btn-recorder_right');
				let $vidrackRecorderTimer = vidrackView.recorderControlsBox.querySelector('.recorder-controls__timer');

				recorderTimerID = vidrack.recorderTimerStart($vidrackRecorderTimer);
			});
			vidrackView.recorderControlsBtn.dataset.vidrackTask = 'stop';
			vidrackView.recorderControlsBtn.innerText = 'Stop';
		} else if ('stop' === task) {
			vidrackView.recorderControlsBtn.classList.toggle('btn-recorder_right');
			vidrackView.recorderControlsBtn.dataset.vidrackTask = 'submit';
			vidrackView.recorderControlsBtn.innerText = 'Loading video';
			vidrackView.recorderControlsBtn.disabled = true;
			vidrackView.vidrackLoader.classList.toggle('vidrack__loading_show');

			clearInterval(recorderTimerID);

			recorder.stopRecording(onStopRecording);
		}
	};
})(jQuery);
