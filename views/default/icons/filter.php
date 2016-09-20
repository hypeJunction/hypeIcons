<?php

$entity = elgg_extract('entity', $vars);
if (!$entity) {
	return;
}

$icon_type = elgg_extract('icon_type', $vars, 'icon');
$filter_context = elgg_extract('filter_context', $vars, 'upload');

$tabs = [];

if (!$entity instanceof ElggFile && $entity->simpletype != 'image') {
	$tabs[] = 'upload';
}

if ($entity->hasIcon('master', $icon_type)) {
	$tabs[] = 'crop';
}

foreach ($tabs as $tab) {
	elgg_register_menu_item('filter', [
		'name' => $tab,
		'text' => elgg_echo("icons:$tab"),
		'href' => "icons/$icon_type/$entity->guid/$tab",
		'selected' => $tab == $filter_context,
	]);
}

echo elgg_view_menu('filter', [
	'sort_by' => 'priority',
	'entity' => $entity,
	'icon_type' => $icon_type,
	'filter_context' => $filter_context,
]);
