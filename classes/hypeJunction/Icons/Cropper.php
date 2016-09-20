<?php

namespace hypeJunction\Icons;

class Cropper {

	/**
	 * Add cropper class selector to file input
	 *
	 * @param string $hook   "view_vars"
	 * @param string $type   "input/file"
	 * @param array  $return View vars
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function filterFileInputVars($hook, $type, $return, $params) {

		$cropper_params = elgg_extract('use_cropper', $return);
		if ($cropper_params) {
			$class = (array) elgg_extract('class', $return, []);
			$class[] = 'file-input-has-cropper';
			$return['class'] = implode(' ', $class);

			$id = elgg_extract('id', $return);
			if (!$id) {
				$return['id'] = "elgg-file-input-" . base_convert(mt_rand(), 10, 36);
			}
		}

		return $return;
	}

}
