<?php

namespace hypeJunction\Icons;

class Router {

	/**
	 * Page handler
	 *
	 * @param array  $segments   URL segments
	 * @return bool
	 */
	public static function handleIcons($segments) {

		$icon_type = array_shift($segments);
		$guid = array_shift($segments);
		$tab = array_shift($segments) ? : 'upload';

		echo elgg_view_resource("icons/$tab", [
			'guid' => $guid,
			'icon_type' => $icon_type,
		]);
		
		return true;
	}

}
