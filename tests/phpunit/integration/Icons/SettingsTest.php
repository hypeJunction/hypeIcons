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

	public function getPluginID(): string {
		return 'hypeicons';
	}

	public function testUserWithExistingIconReturnsTrue(): void {
		$user = $this->createUser();
		$mock = $this->getMockBuilder(\ElggUser::class)
			->disableOriginalConstructor()
			->onlyMethods(['hasIcon', 'getSubtype'])
			->getMock();
		$mock->method('hasIcon')->willReturn(true);
		$mock->method('getSubtype')->willReturn('user');
		// Public dynamic prop for ->type read by code under test.
		$mock->type = 'user';

		$this->assertTrue(Settings::hasIconSupport($mock, 'icon'));
	}

	public function testFallsBackToPluginSettingWhenNoIcon(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}

		$mock = $this->getMockBuilder(\ElggObject::class)
			->disableOriginalConstructor()
			->onlyMethods(['hasIcon', 'getSubtype'])
			->getMock();
		$mock->method('hasIcon')->willReturn(false);
		$mock->method('getSubtype')->willReturn('blog');
		$mock->type = 'object';

		$plugin->setSetting('icon:object:blog', '1');
		$this->assertTrue(Settings::hasIconSupport($mock, 'icon'));

		$plugin->setSetting('icon:object:blog', '');
		$this->assertFalse(Settings::hasIconSupport($mock, 'icon'));
	}

	public function testCoverTypeUsesCoverSetting(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}

		$mock = $this->getMockBuilder(\ElggGroup::class)
			->disableOriginalConstructor()
			->onlyMethods(['hasIcon', 'getSubtype'])
			->getMock();
		$mock->method('hasIcon')->willReturn(false);
		$mock->method('getSubtype')->willReturn('group');
		$mock->type = 'group';

		$plugin->setSetting('cover:group:group', '1');
		$this->assertTrue(Settings::hasIconSupport($mock, 'cover'));

		$plugin->setSetting('cover:group:group', '');
		$this->assertFalse(Settings::hasIconSupport($mock, 'cover'));
	}
}
