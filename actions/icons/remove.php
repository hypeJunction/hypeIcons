<?php

/**
 * Remove entity icons
 */
$guid = get_input('guid');
$entity = get_entity($guid);

$icon_type = get_input('icon_type', 'icon');

if (!$entity || !$entity->canEdit()) {
	register_error(elgg_echo("icons:$icon_type:remove:fail"));
	forward(REFERER);
}

$entity->deleteIcon($icon_type);

system_message(elgg_echo("icons:$icon_type:remove:success"));
