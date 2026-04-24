<?php

namespace hypeJunction\Icons;

use Elgg\IntegrationTestCase;

/**
 * Tests for Settings::hasIconSupport.
 *
 * Exercises the entity-has-icon short-circuit plus the plugin setting path.
 */
class SettingsTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	/**
     * @return string
     */
    public function getPluginID(): string {
		return 'hypeicons';
	}

	/**
     * @return void
     */
    public function testUserWithExistingIconReturnsTrue(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}

		$object = $this->createObject(['subtype' => 'article']);
		// Use the plugin-setting path — fresh entities have no icon.
		$plugin->setSetting('icon:object:article', '1');
		$this->assertTrue(Settings::hasIconSupport($object, 'icon'));

		$plugin->setSetting('icon:object:article', '');
		$this->assertFalse(Settings::hasIconSupport($object, 'icon'));
	}

	/**
     * @return void
     */
    public function testFallsBackToPluginSettingWhenNoIcon(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}

		$object = $this->createObject(['subtype' => 'blog']);

		$plugin->setSetting('icon:object:blog', '1');
		$this->assertTrue(Settings::hasIconSupport($object, 'icon'));

		$plugin->setSetting('icon:object:blog', '');
		$this->assertFalse(Settings::hasIconSupport($object, 'icon'));
	}

	/**
     * @return void
     */
    public function testCoverTypeUsesCoverSetting(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}

		$group = $this->createGroup();

		$plugin->setSetting('cover:group:group', '1');
		$this->assertTrue(Settings::hasIconSupport($group, 'cover'));

		$plugin->setSetting('cover:group:group', '');
		$this->assertFalse(Settings::hasIconSupport($group, 'cover'));
	}
}
