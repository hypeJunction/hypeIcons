<?php

/**
 * @uses $vars['entity']    Entity to which the icon belongs
 * @uses $vars['icon_type'] Icon type: icon|cover
 */

$entity = elgg_extract('entity', $vars);
$value = false;
$image = '';

if (!isset($vars['name'])) {
	$vars['name'] = 'icon';
}

$vars['accept'] = "image/*";

if ($entity instanceof ElggEntity) {
	$icon_type = elgg_extract('icon_type', $vars, 'icon');
	$image = elgg_view('output/img', [
		'src' => $entity->getIconURL([
			'size' => 'small',
			'type' => $icon_type,
		]),
		'width' => 40,
		'alt' => elgg_echo('icons:current'),
	]);
}

$body = elgg_view('input/file', $vars);

echo elgg_view_image_block('', $body, [
	'image_alt' => $image,
]);