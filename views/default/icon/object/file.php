<?php

if (!isset($vars['corners'])) {
	$vars['corners'] = 'square';
}

echo elgg_view('icon/default', $vars);

