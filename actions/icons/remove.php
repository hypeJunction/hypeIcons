<?php

/**
 * Remove entity icons
 */
$guid = get_input('guid');
$entity = get_entity($guid);

$icon_type = get_input('icon_type', 'icon');

if (!$entity || !$entity->canEdit()) {
	return elgg_error_response(elgg_echo("icons:$icon_type:remove:fail"));
}

$entity->deleteIcon($icon_type);

return elgg_ok_response('', elgg_echo("icons:$icon_type:remove:success"), REFERER);
