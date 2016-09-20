<?php

/**
 * hypeIcons
 *
 * Interface for uploading and cropping entity icons and covers
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2016, Ismayil Khayredinov
 */

use hypeJunction\Icons\Cropper;
use hypeJunction\Icons\Icons;
use hypeJunction\Icons\Menus;
use hypeJunction\Icons\Router;

require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', function() {

	elgg_extend_view('elements/icons.css', 'elements/avatar.sizes.css');

	elgg_register_plugin_hook_handler('entity:icon:url', 'all', [Icons::class, 'setDefaultIcon'], 900);
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', [Icons::class, 'setDefaultFileIcons'], 600);

	elgg_register_plugin_hook_handler('entity:cover:url', 'all', [Icons::class, 'setDefaultIcon'], 900);
	elgg_register_plugin_hook_handler('entity:cover:sizes', 'all', [Icons::class, 'setCoverSizes']);
	elgg_register_plugin_hook_handler('entity:cover:saved', 'all', [Icons::class, 'saveCoverCroppingCoords']);
	
	elgg_register_plugin_hook_handler('register', 'menu:entity', [Menus::class, 'setupEntityMenu']);
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', [Menus::class, 'setupUserHoverMenu']);
	elgg_register_plugin_hook_handler('profile_buttons', 'group', [Menus::class, 'setupGroupProfileMenu']);

	elgg_extend_view('page/layouts/elements/header', 'page/layouts/elements/cover', 1);
	
	elgg_register_page_handler('icons', [Router::class, 'handleIcons']);

	elgg_register_action('icons/upload', __DIR__ . '/actions/icons/upload.php');
	elgg_register_action('icons/crop', __DIR__ . '/actions/icons/crop.php');
	elgg_register_action('icons/remove', __DIR__ . '/actions/icons/remove.php');

	elgg_extend_view('elements/forms.css', 'cropper.css');
	elgg_extend_view('elements/forms.css', 'input/cropper.css');
	elgg_extend_view('input/file', 'elements/input/file/cropper');
	elgg_extend_view('theme_sandbox/forms', 'theme_sandbox/forms/cropper');

	elgg_register_js('jquery.cropper', elgg_get_simplecache_url('cropper.js')); // BC
	elgg_unregister_css('jquery.cropper'); // BC

	elgg_register_plugin_hook_handler('view_vars', 'input/file', [Cropper::class, 'filterFileInputVars']);

});
