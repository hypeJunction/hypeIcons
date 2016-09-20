<?php

use hypeJunction\Icons\Settings;

$entity = elgg_extract('entity', $vars);
$icon_type = elgg_extract('icon_type', $vars, 'icon');

if (!$entity->canEdit() || !Settings::hasIconSupport($entity, $icon_type)) {
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

echo elgg_view_input('icon', [
	'name' => 'icon',
	'label' => elgg_echo('icons:file'),
	'icon_type' => $icon_type,
	'entity' => $entity,
]);

?>
<div class="elgg-foot">
	<?php
	echo elgg_view('input/submit', [
		'value' => elgg_echo('icons:upload'),
	]);

	if ($entity->hasIcon('small', $icon_type)) {
		echo elgg_view('output/url', [
			'text' => elgg_echo('icons:delete'),
			'href' => elgg_http_add_url_query_elements("action/icons/remove", [
				'guid' => $entity->guid,
				'icon_type' => $icon_type,
			]),
			'is_action' => true,
			'confirm' => true,
			'class' => 'elgg-button elgg-button-delete float-alt',
		]);
	}
	?>
</div>
