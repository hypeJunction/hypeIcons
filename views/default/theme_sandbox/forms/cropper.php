<form action="#">
	<fieldset>
		<legend>Cropper Input</legend>
		<?php
		echo elgg_view_input('cropper', array(
			'name' => 'ci1',
			'src' => elgg_get_simplecache_url('theme_sandbox/forms/cropper.jpg'),
			'ratio' => 16 / 9,
			'label' => 'Simple 16:9 cropper input (input/cropper)',
			'help' => 'Photo credit: <a href="http://www.flickr.com/photos/44073224@N04/28952967203">vinyl</a> via <a href="http://photopin.com">photopin</a> <a href="https://creativecommons.org/licenses/by-sa/2.0/">(license)</a>',
		));

		echo elgg_view_input('file', array(
			'name' => 'ci2',
			'use_cropper' => true,
			'label' => 'File input with a basic cropper (input/file)',
		));

		echo elgg_view_input('file', array(
			'name' => 'ci3',
			'use_cropper' => array(
				'name' => 'ccrop_coords',
				'ratio' => 16 / 9,
				'src' => elgg_get_simplecache_url('theme_sandbox/forms/cropper.jpg'),
				'x1' => 100,
				'y1' => 100,
				'x2' => 260,
				'y2' => 190,
			),
			'label' => 'File input with a preset cropper options (input/file)',
			'help' => 'Photo credit: <a href="http://www.flickr.com/photos/44073224@N04/28952967203">vinyl</a> via <a href="http://photopin.com">photopin</a> <a href="https://creativecommons.org/licenses/by-sa/2.0/">(license)</a>',
		));
		?>
	</fieldset>
</form>