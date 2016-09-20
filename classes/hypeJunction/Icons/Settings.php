<?php

namespace hypeJunction\Icons;

use ElggEntity;

class Settings {
	
	/**
	 * Is icon support enabled for the entity?
	 * 
	 * @param ElggEntity $entity Entity
	 * @param string     $type   Icon type
	 * @return bool
	 */
	public static function hasIconSupport(ElggEntity $entity, $type = 'icon') {
		if ($entity->hasIcon('small', $type)) {
			return true;
		}
		return (bool) elgg_get_plugin_setting("{$type}:{$entity->type}:{$entity->getSubtype()}", 'hypeIcons');
	}
}
