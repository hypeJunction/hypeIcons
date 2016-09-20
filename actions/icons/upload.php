<?php

/**
 * Upload a new entity icon
 */
use hypeJunction\Icons\Settings;

$guid = get_input('guid');
$entity = get_entity($guid);

$icon_type = get_input('icon_type', 'icon');

if (!$entity || !$entity->canEdit() || !Settings::hasIconSupport($entity, $icon_type)) {
	register_error(elgg_echo("icons:$icon_type:upload:fail"));
	forward(REFERER);
}

$error = elgg_get_friendly_upload_error($_FILES['icon']['error']);
if ($error) {
	register_error($error);
	forward(REFERER);
}

$coords = [];
if ($icon_type == 'cover') {
	// Elgg's cropper maintains aspect ratio of the original image for non-square icons
	// For covers, we want to crop the image out from the center to fit the cropping box

	$sizes = elgg_get_icon_sizes($entity->getType(), $entity->getSubtype(), 'cover');

	$natural_size = getimagesize($_FILES['icon']['tmp_name']);
	$natural_width = $natural_size[0];
	$natural_height = $natural_size[1];

	$crop_width = $sizes['master']['w'];
	$crop_height = $sizes['master']['h'];

	if ($natural_width && $natural_height && $crop_width && $crop_height) {
		$natural_center_x = round($natural_width / 2);
		$natural_center_y = round($natural_height / 2);

		$crop_center_x = round($crop_width / 2);
		$crop_center_y = round($crop_height / 2);

		$coords['x1'] = max(0, $natural_center_x - $crop_center_x);
		$coords['y1'] = max(0, $natural_center_y - $crop_center_y);

		$coords['x2'] = min($natural_width, $natural_center_x + $crop_center_x);
		$coords['y2'] = min($natural_height, $natural_center_y + $crop_center_y);
	}
}

if (!$entity->saveIconFromUploadedFile('icon', $icon_type, $coords)) {
	register_error(elgg_echo("icons:$icon_type:upload:fail"));
	forward(REFERER);
}

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

forward(REFERER);
