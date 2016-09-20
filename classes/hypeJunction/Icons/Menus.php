<?php

namespace hypeJunction\Icons;

use ElggEntity;
use ElggFile;
use ElggGroup;
use ElggMenuItem;
use ElggObject;
use ElggUser;

class Menus {
	
	/**
	 * Prepare Edit icon menu item
	 * 
	 * @param ElggEntity $entity Entity
	 * @return ElggMenuItem|void
	 */
	public static function prepareEditIconItem(\ElggEntity $entity) {
		if ($entity->canEdit() && Settings::hasIconSupport($entity, 'icon')) {
			$action = $entity->hasIcon('small', 'icon') ? 'edit' : 'add';
			if ($entity instanceof ElggUser) {
				$label = elgg_echo("icons:$action:avatar");
			} else if ($entity instanceof ElggFile) {
				$label = elgg_echo("icons:$action:thumb");
			} else {
				$label = elgg_echo("icons:$action:icon");
			}
			if (elgg_is_active_plugin('menus_api')) {
				$text = $label;
			} else {
				$text = elgg_view_icon('picture-o');
			}
			return ElggMenuItem::factory([
				'name' => 'edit:icon',
				'href' => "icons/icon/$entity->guid",
				'text' => $text,
				'title' => $label,
				'data' => [
					'icon' => 'picture-o',
				],
				'priority' => 700,
			]);
		}
	}

	/**
	 * Prepare Edit cover menu item
	 *
	 * @param ElggEntity $entity Entity
	 * @return ElggMenuItem|void
	 */
	public static function prepareEditCoverItem(\ElggEntity $entity) {
		if ($entity->canEdit() && Settings::hasIconSupport($entity, 'cover')) {
			$action = $entity->hasIcon('small', 'cover') ? 'edit' : 'add';
			$label = elgg_echo("icons:$action:cover");
			if (elgg_is_active_plugin('menus_api')) {
				$text = $label;
			} else {
				$text = elgg_view_icon('picture-o');
			}
			return ElggMenuItem::factory([
				'name' => 'edit:cover',
				'href' => "icons/cover/$entity->guid",
				'text' => $text,
				'title' => $label,
				'data' => [
					'icon' => 'picture-o',
				],
				'priority' => 700,
			]);
		}
	}

	/**
	 * Setup entity menu
	 * 
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:entity"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupEntityMenu($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof ElggObject) {
			return;
		}

		if ($edit_icon = self::prepareEditIconItem($entity)) {
			$return[] = $edit_icon;
		}

		if ($edit_cover = self::prepareEditCoverItem($entity)) {
			$return[] = $edit_cover;
		}

		return $return;
	}

	/**
	 * Setup user hover menu
	 *
	 * @param string         $hook   "register"
	 * @param string         $type   "menu:user_hover"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupUserHoverMenu($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof ElggUser) {
			return;
		}

		if ($edit_icon = self::prepareEditIconItem($entity)) {
			$edit_icon->setText($edit_icon->getTooltip());
			$section = elgg_is_admin_logged_in() ? 'admin' : 'actions';
			$edit_icon->setSection($section);
			$return[] = $edit_icon;
		}

		if ($edit_cover = self::prepareEditCoverItem($entity)) {
			$edit_cover->setText($edit_cover->getTooltip());
			$section = elgg_is_admin_logged_in() ? 'admin' : 'actions';
			$edit_cover->setSection($section);
			$return[] = $edit_cover;
		}

		return $return;
	}

	/**
	 * Setup user hover menu
	 *
	 * @param string         $hook   "profile_buttons"
	 * @param string         $type   "group"
	 * @param ElggMenuItem[] $return Menu
	 * @param array          $params Hook params
	 * @return ElggMenuItem[]
	 */
	public static function setupGroupProfileMenu($hook, $type, $return, $params) {

		$entity = elgg_extract('entity', $params);
		if (!$entity instanceof ElggGroup) {
			return;
		}

		if ($edit_icon = self::prepareEditIconItem($entity)) {
			$edit_icon->setText($edit_icon->getTooltip());
			$edit_icon->setLinkClass('elgg-button elgg-button-action');
			$edit_icon->setSection($section);
			$return[] = $edit_icon;
		}

		if ($edit_cover = self::prepareEditCoverItem($entity)) {
			$edit_cover->setText($edit_cover->getTooltip());
			$edit_cover->setLinkClass('elgg-button elgg-button-action');
			$return[] = $edit_cover;
		}

		return $return;
	}
}
