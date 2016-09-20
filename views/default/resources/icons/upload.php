<?php

use hypeJunction\Icons\Settings;

elgg_push_context('icons/upload');

$guid = elgg_extract('guid', $vars);

$icon_type = elgg_extract('icon_type', $vars, 'icon');

elgg_entity_gatekeeper($guid);

$entity = get_entity($guid);

if (!$entity->canEdit() || !Settings::hasIconSupport($entity, $icon_type)) {
	forward('', '403');
}

if ($entity instanceof ElggFile) {
	forward("icons/$icon_type/$guid/crop");
}

if (elgg_instanceof($entity, 'user') || elgg_instanceof($entity, 'group')) {
	elgg_set_page_owner_guid($entity->guid);
} else {
	elgg_set_page_owner_guid($entity->container_guid);
}

elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());

$title = elgg_echo("icons:$icon_type:upload");
elgg_push_breadcrumb($title, current_page_url());

if (elgg_is_sticky_form('icons/upload')) {
	$sticky_values = elgg_get_sticky_values('icons/upload');
	if (is_array($sticky_values)) {
		$vars = array_merge($vars, $sticky_values);
	}
}

$vars['entity'] = $entity;

$content = elgg_view_form('icons/upload', [
	'enctype' => 'multipart/form-data',
	'validate' => true,
		], $vars);

if (elgg_is_xhr()) {
	echo $content;
	return;
}

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => elgg_view('icons/filter', [
		'filter_context' => 'upload',
		'entity' => $entity,
		'icon_type' => $icon_type,
	]),
		]);

echo elgg_view_page($title, $body);
