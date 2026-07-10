<?php
/**
 * Dispatcher for the `icons` route (`/icons/{segments}`).
 *
 * The 2.x page handler this route was ported from split the URL itself. The port kept
 * the catch-all path but named a resource view — `resources/icons.php` — that was never
 * created, so every /icons/* URL raised ResourceNotFoundException and 404'd. The plugin
 * activated cleanly and nothing warned: the avatar upload and crop pages simply never
 * worked. (bd elgg-migrate-ckn0c)
 *
 * URL shape, per the route's own contract (see tests/.../PluginRegistrationTest.php,
 * which generates `/icons/icon/123/upload`):
 *
 *     /icons/{icon_type}/{guid}/{action}
 *
 * The real page bodies live in `resources/icons/upload.php` and `resources/icons/crop.php`
 * and both read `guid` and `icon_type` from $vars.
 */

use Elgg\Exceptions\Http\PageNotFoundException;

$segments = (string) elgg_extract('segments', $vars, '');
$parts = array_values(array_filter(explode('/', trim($segments, '/')), 'strlen'));

$icon_type = $parts[0] ?? '';
$guid = isset($parts[1]) ? (int) $parts[1] : 0;
$action = $parts[2] ?? '';

// Only the actions that have a resource view. Anything else is a 404, not a 500.
$allowed = ['upload', 'crop'];

if ($icon_type === '' || $guid <= 0 || !in_array($action, $allowed, true)) {
	throw new PageNotFoundException();
}

if (!get_entity($guid)) {
	throw new PageNotFoundException();
}

echo elgg_view_resource("icons/{$action}", [
	'guid' => $guid,
	'icon_type' => $icon_type,
]);
