<?php

/**
 * Display entity cover
 * @uses $vars['entity'] Entity
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

if (!\hypeJunction\Icons\Settings::hasIconSupport($entity, 'cover') || !$entity->hasIcon('master', 'cover')) {
	return;
}

$cover_url = $entity->getIconUrl([
	'type' => 'cover',
	'size' => 'master',
]);

echo elgg_format_element('div', [
	'style' => [
		"background-image: url($cover_url);"
	],
	'class' => 'elgg-entity-cover',
]);
