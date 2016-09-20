<?php

/**
 * Displays a cover image above layout header
 *
 * @uses $vars['entity']     Entity
 * @uses $vars['show_cover'] Toggle cover visibility
 */

if (!elgg_extract('show_cover', $vars, false)) {
	return;
}

$entity = elgg_extract('entity', $vars);
if (!$entity) {
	return;
}

$cover = elgg_view('output/cover', [
	'entity' => $entity,
]);

echo elgg_format_element('div', [
	'class' => 'elgg-layout-cover',
], $cover);


