<?php

$entity = elgg_extract('entity', $vars);

$form = elgg_view_form('icons/crop', [
	'enctype' => 'multipart/form-data',
], [
	'entity' => $entity,
	'icon_type' => $icon,
]);

echo elgg_view_module('info', elgg_echo('avatar:crop:title'), $form);