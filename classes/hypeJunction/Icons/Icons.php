<?php

namespace hypeJunction\Icons;

use ElggEntity;
use ElggFile;
use ElggGroup;
use ElggUser;

class Icons {

	/**
	 * Replace default icons with SVG
	 *
	 * @param string $hook   "entity:icon:url"|"entity:cover:url"
	 * @param string $type   "all"
	 * @param string $return URL
	 * @param array  $params Hook params
	 * @return string
	 */
	public static function setDefaultIcon($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof ElggEntity) {
			return;
		}
		
		$size = elgg_extract('size', $params, 'medium');
		$icon_type = elgg_extract('type', $params, 'icon');

		if (!$return && Settings::hasIconSupport($entity, $icon_type) && $entity->hasIcon($size, $icon_type)) {
			return elgg_get_inline_url($entity->getIcon($size, $icon_type));
		}

		$core_path = false;
		if ($icon_type == 'icon') {
			if ($entity instanceof ElggUser) {
				$core_path = elgg_get_simplecache_url('icons/user/');
			} else if ($entity instanceof ElggGroup) {
				$core_path = elgg_get_simplecache_url('groups/default');
			} else {
				$core_path = elgg_get_simplecache_url('icons/default/');
			}
		}

		$replace_default = $icon_type == 'cover' || elgg_get_plugin_setting('replace_default_icons', 'hypeIcons');
		if (!$return || !is_string($return) || ($replace_default && $core_path && 0 === strpos($return, $core_path))) {
			$type = $entity->getType();
			$subtype = $entity->getSubtype();
			$views = array_filter([
				"$icon_type/$type/$subtype",
				$replace_default ? "$icon_type/$type/default" : '',
				$replace_default ? "$icon_type/default" : '',
			]);
			foreach ($views as $view) {
				foreach (['svg', 'png', 'gif', 'jpg'] as $ext) {
					if (elgg_view_exists("$view.$ext")) {
						return elgg_get_simplecache_url("$view.$ext");
					}
				}
			}
		}
	}

	/**
	 * Replaces file type icons
	 *
	 * @param string $hook   "entity:icon:url"
	 * @param string $type   "object"
	 * @param string $return Icon URL
	 * @param array  $params Hook params
	 * @return string
	 */
	public static function setDefaultFileIcons($hook, $type, $return, $params) {

		if (!elgg_get_plugin_setting('replace_filetype_icons', 'hypeIcons')) {
			return;
		}
		
		$entity = elgg_extract('entity', $params);
		$size = elgg_extract('size', $params);

		if (!$entity instanceof ElggFile || $entity->getSubtype() != 'file') {
			return;
		}

		$mimetype = $entity->mimetype ? : $entity->detectMimeType();
		if (!$mimetype) {
			$mimetype = 'application/otcet-stream';
		}

		if (0 === strpos($mimetype, 'image/') && $entity->icontime && $return) {
			return $return;
		}

		$extension = pathinfo($entity->getFilenameOnFilestore(), PATHINFO_EXTENSION);
		$filetype = self::mapMimeToIconType($mimetype, $extension);

		$view = "icon/object/file/{$filetype}.svg";
		if (!elgg_view_exists($view)) {
			$view = "icon/default.svg";
		}

		return elgg_get_simplecache_url($view);
	}

	/**
	 * Maps mime type to a file type icon
	 *
	 * @param string $mimetype  Mimetype
	 * @param string $extension File name extension
	 * @return string
	 */
	public static function mapMimeToIconType($mimetype = 'application/otcet-stream', $extension = '') {
		switch ($mimetype) {
			case 'application/pdf' :
			case 'application/vnd.pdf' :
			case 'application/x-pdf' :
				return 'pdf';

			case 'application/ogg' :
				return 'audio';

			case 'text/plain' :
			case 'text/richtext' :
				return 'text';

			case 'text/html':
			case 'application/rtf' :
			case 'application/vnd.oasis.opendocument.text' :
			case 'application/wordperfect' :
			case 'application/vnd.google-apps.document' :
				return 'document';


			case 'application/vnd.oasis.opendocument.presentation' :
			case 'application/vnd.google-apps.presentation' :
				return 'presentation';

			case 'text/csv':
			case 'text/x-markdown' :
			case 'application/csv' :
			case 'application/vnd.oasis.opendocument.spreadsheet' :
			case 'application/vnd.google-apps.spreadsheet' :
				return 'spreadsheet';


			case 'image/vnd.adobe.photoshop' :
			case 'application/eps':
			case 'application/vnd.oasis.opendocument.graphics' :
			case 'image/vnd.adobe.premiere':
			case 'application/illustrator' :
			case 'application/vnd.google-apps.drawing' :
				return 'drawing';

			case 'application/vnd.oasis.opendocument.image' :
				return 'image';

			case 'application/xhtml+xml' :
			case 'text/xml' :
			case 'application/json' :
			case 'text/javascript' :
			case 'application/javascript' :
			case 'application/x-javascript' :
			case 'application/rss+xml' :
			case 'text/css' :
			case 'text/php' :
			case 'text/x-php' :
			case 'application/php' :
			case 'application/x-php' :
				return 'code';

			case 'application/x-zip' :
			case 'application/x-gzip' :
			case 'application/x-gtar' :
			case 'application/x-tar' :
			case 'application/x-rar-compressed' :
			case 'application/x-stuffit' :
				return 'archive';

			case 'application/vnd.google-earth.kml+xml' :
			case 'application/vnd.google-earth.kmz' :
			case 'application/gml+xml' :
			case 'application/vnd.geo+json' :
			case 'application/vnd.google-apps.map' :
				return 'map';

			case 'text/v-card' :
			case 'text/directory' :
			case 'text/vcard' :
				return 'vcard';

			case 'text/calendar' :
			case 'application/calendar' :
			case 'application/calendar+json' :
			case 'application/calendar+xml' :
				return 'calendar';

			case 'application/zip' :
			case 'application/vnd.ms-office':
			case 'application/msword' :
			case 'application/excel' :
			case 'application/vnd.ms-excel' :
			case 'application/powerpoint' :
			case 'application/vnd.ms-powerpoint' :
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' :
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' :
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation' :

				switch ($extension) {
					case 'docx':
					case 'doc':
						return 'word';

					case 'xlsx':
					case 'xls':
						return 'excel';

					case 'pptx':
					case 'ppt' :
					case 'pot' :
						return 'powerpoint';

					case 'zip' :
					case 'war' :
					case 'jar' :
					case 'ear' :
						return 'archive';

					default :
						return 'default';
				}
				break;

			default :
				switch ($extension) {
					case 'bin' :
					case 'exe' :
						return 'application';
				}
				if (preg_match('~^(audio|image|video)/~', $mimetype, $m)) {
					return $m[1];
				} else {
					return 'default';
				}
				break;
		}
	}

	/**
	 * Set cover image sizes
	 * 
	 * @param string $hook   "entity:cover:sizes"
	 * @param string $type   "all"
	 * @param array  $return Sizes
	 * @param array  $params Hook params
	 * @return array
	 */
	public static function setCoverSizes($hook, $type, $return, $params) {
		if (!empty($return)) {
			return;
		}
		
		return [
			'original' => [],
			'master' => [
				'w' => 1000,
				'h' => 370,
				'square' => false,
				'upscale' => true,
			],
			'large' => [
				'w' => 500,
				'h' => 185,
				'square' => false,
				'upscale' => false,
			],
			'medium' => [
				'w' => 250,
				'h' => 90,
				'square' => false,
				'upscale' => false,
			],
			'small' => [
				'w' => 125,
				'h' => 45,
				'square' => false,
				'upscale' => false,
			],
		];
	}

	/**
	 * Save cover cropping coordinates
	 *
	 * @param string $hook   "entity:cover:saved"
	 * @param string $type   "all"
	 * @param void   $return Null
	 * @param array  $params Hook params
	 * @return void
	 */
	public static function saveCoverCroppingCoords($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		$entity->cover_x1 = elgg_extract('x1', $params);
		$entity->cover_y1 = elgg_extract('y1', $params);
		$entity->cover_x2 = elgg_extract('x2', $params);
		$entity->cover_y2 = elgg_extract('y2', $params);
	}
}
