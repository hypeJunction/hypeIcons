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
	return elgg_error_response(elgg_echo("icons:$icon_type:crop:fail"));
}

$coords = get_input('crop_coords');

if ($entity instanceof ElggFile && $size == 'self') {
	$file = $entity;
} else {
	if (!$entity->hasIcon($size, $icon_type)) {
		return elgg_error_response(elgg_echo("icons:$icon_type:crop:fail"));
	}
	$file = $entity->getIcon($size, $icon_type);
}

if (!$entity->saveIconFromElggFile($file, $icon_type, $coords)) {
	return elgg_error_response(elgg_echo("icons:$icon_type:crop:fail"));
}

$type = $entity->type;
$subtype = $entity->getSubtype() ? : 'default';

$event = $type == 'user' ? 'profileiconupdate' : "update:$icon_type";

if (elgg_trigger_event($event, $entity->type, $entity)) {
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

return elgg_ok_response('', elgg_echo("icons:$icon_type:crop:success"), REFERER);
