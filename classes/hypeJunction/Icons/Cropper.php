<?php

namespace hypeJunction\Icons;

/**
 * Adds cropper integration to file inputs
 */
class Cropper {

	/**
	 * Add cropper class selector to file input
	 *
	 * @param \Elgg\Event $event Event
	 * @return array
	 */
	public static function filterFileInputVars(\Elgg\Event $event) {
		$return = $event->getValue();


		$cropper_params = elgg_extract('use_cropper', $return);
		if ($cropper_params) {
			$class = (array) elgg_extract('class', $return, []);
			$class[] = 'file-input-has-cropper';
			$return['class'] = implode(' ', $class);

			$id = elgg_extract('id', $return);
			if (!$id) {
				$return['id'] = 'elgg-file-input-' . base_convert(mt_rand(), 10, 36);
			}
		}

		return $return;
	}
}
