<?php

$entity = elgg_extract('entity', $vars);

$form = elgg_view_form('icons/upload', [
	'enctype' => 'multipart/form-data',
], [
	'entity' => $entity,
	'icon_type' => 'icon',
]);

echo elgg_view_module('info', elgg_echo('avatar:upload'), $form);