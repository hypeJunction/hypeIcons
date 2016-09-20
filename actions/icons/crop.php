<?php

/**
 * Crop entity icon
 */

use hypeJunction\Icons\Settings;

$guid = get_input('guid');
$entity = get_entity($guid);

$icon_type = get_input('icon_type', 'icon');
$size = get_input('size', 'master');

if (!$entity || !$entity->canEdit() || !Settings::hasIconSupport($entity, $icon_type)) {
	register_error(elgg_echo("icons:$icon_type:crop:fail"));
	forward(REFERER);
}

$coords = get_input('crop_coords');

if ($entity instanceof ElggFile && $size == 'self') {
	$file = $entity;
} else {
	if (!$entity->hasIcon($size, $icon_type)) {
		register_error(elgg_echo("icons:$icon_type:crop:fail"));
		forward(REFERER);
	}
	$file = $entity->getIcon($size, $icon_type);
}

if (!$entity->saveIconFromElggFile($file, $icon_type, $coords)) {
	register_error(elgg_echo("icons:$icon_type:crop:fail"));
	forward(REFERER);
}

system_message(elgg_echo("icons:$icon_type:crop:success"));

$type = $entity->type;
$subtype = $entity->getSubtype() ? : 'default';

$event = $type == 'user' ? 'profileiconupdate' : "update:$icon_type";

if (elgg_trigger_event($event, $entity->type, $entity)) {
	system_message(elgg_echo("icons:$icon_type:upload:success"));

	if ($type == 'user') {
		$view = "river/user/default/profileiconupdate";
	} else {
		$view = "river/$type/$subtype/$icon_type";
	}

	elgg_delete_river([
		'subject_guid' => $entity->guid,
		'view' => $view
	]);

	elgg_create_river_item(array(
		'view' => $view,
		'action_type' => 'update',
		'subject_guid' => elgg_get_logged_in_user_guid(),
		'object_guid' => $entity->guid,
	));
}

forward(REFERRER);
