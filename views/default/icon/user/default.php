<?php

/**
 * Elgg user icon
 *
 * Rounded avatar corners - CSS3 method
 * uses avatar as background image so we can clip it with border-radius in supported browsers
 *
 * @uses $vars['entity']     The user entity. If none specified, the current user is assumed.
 * @uses $vars['size']       The size - tiny, small, medium or large. (medium)
 * @uses $vars['use_hover']  Display the hover menu? (true)
 * @uses $vars['use_link']   Wrap a link around image? (true)
 * @uses $vars['class']      Optional class added to the .elgg-avatar div
 * @uses $vars['img_class']  Optional CSS class added to img
 * @uses $vars['link_class'] Optional CSS class for the link
 * @uses $vars['href']       Optional override of the link href
 */
$user = elgg_extract('entity', $vars);
if (!($user instanceof ElggUser)) {
	return;
}

$name = $user->getDisplayName();
$class = (array) elgg_extract('class', $vars, array());
if ($user->isBanned()) {
	$class[] = 'elgg-state-banned';
	$name .= ' (' . elgg_echo('banned') . ')';
}

$vars['title'] = $name;
$vars['class'] = $class;

$use_hover = elgg_extract('use_hover', $vars, true);
$show_menu = $use_hover && (elgg_is_admin_logged_in() || !$user->isBanned());

if ($show_menu) {
	$params = array(
		'entity' => $user,
		'username' => $user->username,
		'name' => $name,
	);
	$vars['hover'] = elgg_view_icon('ellipsis-h', [
		'class' => 'elgg-icon-hover-menu'
	]);
	$vars['hover'] .= elgg_view('navigation/menu/user_hover/placeholder', array('entity' => $user));
}

echo elgg_view('icon/default', $vars);
