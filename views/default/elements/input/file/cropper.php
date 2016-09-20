<?php

/**
 * Extension for the input/file view
 */
$params = elgg_extract('use_cropper', $vars);

if (empty($params)) {
	return;
}

$file_input_id = elgg_extract('id', $vars);
if (!$file_input_id) {
	return;
}

if (!is_array($params)) {
	$params = array();
}

$params['input'] = $file_input_id;

echo elgg_view('input/cropper', $params);
