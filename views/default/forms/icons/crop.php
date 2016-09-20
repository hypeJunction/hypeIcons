<?php

use hypeJunction\Icons\Settings;

$entity = elgg_extract('entity', $vars);
$icon_type = elgg_extract('icon_type', $vars, 'icon');

if (!$entity->canEdit() || !Settings::hasIconSupport($entity, $icon_type)) {
	return;
}

$sizes = elgg_get_icon_sizes($entity->getType(), $entity->getSubtype(), $icon_type);

$size = false;
if ($entity instanceof ElggFile && $entity->simpletype == 'image') {
	$size = 'self';
} else {
	foreach (['original', 'master', 'large'] as $s) {
		if ($entity->hasIcon($s, $icon_type)) {
			$size = $s;
			break;
		}
	}
}

if (!$size) {
	echo elgg_format_element('p', [
			'class' => 'elgg-no-results',
		], elgg_echo('icon:crop:no_image'));
		return;
}

echo elgg_view_input('hidden', [
	'name' => 'guid',
	'value' => $entity->guid,
]);

echo elgg_view_input('hidden', [
	'name' => 'icon_type',
	'value' => $icon_type,
]);

echo elgg_format_element('p', [
	'class' => 'elgg-text-help',
], elgg_echo('icons:crop:instructions'));

if ($size == 'self') {
	$src = elgg_get_inline_url($entity);
} else {
	$src = $entity->getIconURL([
		'size' => $size,
		'type' => $icon_type,
	]);
}

if ($icon_type == 'cover') {
	$x1 = $entity->cover_x1;
	$y1 = $entity->cover_y1;
	$x2 = $entity->cover_x2;
	$y2 = $entity->cover_y2;
} else {
	$x1 = $entity->x1;
	$y1 = $entity->y1;
	$x2 = $entity->x2;
	$y2 = $entity->y2;
}

$ratio = $sizes['small']['w'] / $sizes['small']['h'];

echo elgg_view_input('hidden', [
	'name' => 'size',
	'value' => $size,
]);

echo elgg_view_input('cropper', [
	'src' => $src,
	'name' => 'crop_coords',
	'x1' => $x1,
	'y1' => $y1,
	'x2' => $x2,
	'y2' => $y2,
	'ratio' => $ratio,
]);

echo elgg_view_input('submit', [
	'value' => elgg_echo('icons:crop'),
	'field_class' => 'elgg-foot',
]);
