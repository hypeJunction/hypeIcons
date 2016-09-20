<?php

/**
 * Generic icon view.
 *
 * @package Elgg
 * @subpackage Core
 *
 * @uses $vars['entity']     The entity the icon represents - uses getIconURL() method
 * @uses $vars['size']       topbar, tiny, small, medium (default), large, master
 * @uses $vars['href']       Optional override for link
 * @uses $vars['img_class']  Optional CSS class added to img
 * @uses $vars['link_class'] Optional CSS class for the link
 * @uses $vars['corners']    Corner type
 */
$entity = elgg_extract('entity', $vars);

$size = elgg_extract('size', $vars, 'small');
$icon_type = elgg_extract('icon_type', $vars, 'icon');
$width = elgg_extract('width', $vars);
$height = elgg_extract('height', $vars);

if (!$entity instanceof ElggEntity) {
	return;
}

$icon_sizes = elgg_get_icon_sizes($entity->getType(), $entity->getSubtype());
if (!array_key_exists($size, $icon_sizes)) {
	$size = 'medium';
}

$href = $entity->getURL();
if (isset($vars['href'])) {
	$href = $vars['href'];
}

$img_class = (array) elgg_extract('img_class', $vars, array());
$img_class[] = 'elgg-avatar-image';

$title = elgg_extract('title', $vars, $entity->getDisplayName());

$img = elgg_view('output/img', array(
	'src' => $entity->getIconURL([
		'size' => $size,
		'type' => $icon_type
	]),
	'alt' => $title,
	'width' => $width,
	'height' => $height,
	'class' => $img_class,
		));

if ($href && elgg_extract('use_link', $vars, true)) {
	$link_class = (array) elgg_extract('link_class', $vars, array());
	$link_class[] = 'elgg-tooltip';
	$img = elgg_view('output/url', array(
		'is_trusted' => true,
		'class' => $link_class,
		'text' => $img,
		'href' => $href,
		'title' => $title,
	));
}

$wrapper_class = (array) elgg_extract('class', $vars, array());
$wrapper_class[] = "elgg-avatar";
$wrapper_class[] = "elgg-avatar-$size";
$type = $entity->getType();
$wrapper_class[] = "elgg-avatar-$type";
$subtype = $entity->getSubtype();
if ($subtype) {
	$wrapper_class[] = "elgg-avatar-$type-$subtype";
}
if ($icon_type !== 'icon') {
	$wrapper_class[] = "elgg-avatar-{$icon_type}";
}

$corners = elgg_extract('corners', $vars);
if (!isset($corners) && in_array($size, array('topbar', 'tiny', 'small', 'medium'))) {
	$corners = elgg_get_plugin_setting('corners', 'ui_icons');
}

if ($corners) {
	$wrapper_class[] = "elgg-avatar-$corners";
}

$hover = elgg_extract('hover', $vars);
echo elgg_format_element('div', [
	'class' => $wrapper_class,
		], $img . $hover);
