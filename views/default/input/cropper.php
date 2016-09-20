<?php
/**
 * Displays an image with cropper
 *
 * @uses $vars['id'] Element ID
 * @uses $vars['name'] Input name prefix
 * $uses $vars['ratio'] Crop ratio
 * @uses $vars['src'] Image source
 * @uses $vars['x1'] x1 coordinate at instantiation
 * @uses $vars['y1'] y1 coordinate at instantiation
 * @uses $vars['x2'] x2 coordinate at instantiation
 * @uses $vars['y2'] y2 coordinate at instantiation
 * @uses $vars['input'] ID of the file input that can be used to update an image
 */
$ratio = elgg_extract('ratio', $vars);
if (!isset($ratio)) {
	$ratio = 1;
}

$x = 0;
$y = 0;
$width = 200;
$height = $height * $ratio;

$x1 = (int) elgg_extract('x1', $vars, 0);
$y1 = (int) elgg_extract('y1', $vars, 0);
$x2 = (int) elgg_extract('x2', $vars, 0);
$y2 = (int) elgg_extract('x2', $vars, 0);

if ($x2 > $x1 && $y2 > $y1) {
	$x = $x1;
	$y = $y1;
	$width = $x2 - $x1;
	$height = $y2 - $y1;
}

$src = elgg_extract('src', $vars);
if ($src) {
	$img = elgg_view('output/img', [
		'src' => $src,
		'class' => 'cropper-input-image',
		'alt' => elgg_echo('cropper:instructions'),
		'data-x' => $x,
		'data-y' => $y,
		'data-width' => $width,
		'data-height' => $height,
	]);
} else {
	$img = '';
}

$body = elgg_format_element('div', ['class' => 'cropper-input-image-container'], $img);

$name = elgg_extract('name', $vars, 'crop_coords');
foreach (['x1', 'y1', 'x2', 'y2'] as $coord) {
	$body .= elgg_view('input/hidden', [
		'name' => "{$name}[{$coord}]",
		'value' => elgg_extract($coord, $vars),
		'data-coord' => $coord,
	]);
}

$id = elgg_extract('id', $vars);
if (!$id) {
	$id = md5(serialize($vars));
}

$file_input_id = false;
$input = elgg_extract('input', $vars);
if ($input) {
	$file_input_id = "#{$input}";
}
echo elgg_format_element('div', [
	'id' => $id,
	'class' => 'cropper-input',
	'data-file-input' => $file_input_id,
	'data-ratio' => $ratio,
		], $body);
?>
<script>
	require(['input/cropper'], function(input) {
		input.init('#<?= $id ?>');
	});
</script>
