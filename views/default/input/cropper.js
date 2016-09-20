define(function (require) {

	var $ = require('jquery');
	var spinner = require('elgg/spinner');
	require('cropper');

	var lib = {
		init: function (element) {
			if ($(element).is('[data-file-input]')) {
				var fileInputId = $(element).data('fileInput');
				$(fileInputId).data('cropperInput', element).off('change').on('change', lib.change);
			}
			var $img = $('img.cropper-input-image', $(element));
			if ($img.length === 0) {
				return;
			}
			$img.cropper({
				mode: 3,
				aspectRatio: $(element).data('ratio'),
				data: $img.data(),
				crop: function (data) {
					$('input[data-coord="x1"]', $(element)).val(data.x);
					$('input[data-coord="x2"]', $(element)).val((data.x + data.width));
					$('input[data-coord="y1"]', $(element)).val(data.y);
					$('input[data-coord="y2"]', $(element)).val((data.y + data.height));
				}
			});
		},
		change: function (e) {
			var $elem = $(this);
			var $cropper = $($elem.data('cropperInput'));

			var file = $elem[0].files[0];
			if (!file || !file.type.match(/image.*/)) {
				return;
			}

			if ($('.cropper-input-image', $cropper).length) {
				$('.cropper-input-image', $cropper).cropper('destroy');
				$('.cropper-input-image', $cropper).remove();
			}

			var reader = new FileReader();
			reader.onload = function (e) {
				var img = new Image();
				img.src = reader.result;
				img.alt = file.name;
				$('.cropper-input-image-container', $cropper).html($(img).addClass('cropper-input-image'));
				lib.init($cropper);
			};
			reader.onloadstart = spinner.start;
			reader.onloadend = spinner.stop;
			reader.readAsDataURL(file);
		}
	};

	return lib;

});