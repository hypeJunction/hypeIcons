<?php

namespace hypeJunction\Icons;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

/**
 * Tests for Icons::setDefaultFileIcons (entity:icon:url, type=object).
 *
 * Plugin setting `replace_filetype_icons` on hypeicons controls the handler.
 */
class SetDefaultFileIconsTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	public function getPluginID(): string {
		return 'hypeicons';
	}

	protected function makeHook(array $params, $value = null): Hook {
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$hook->method('getName')->willReturn('entity:icon:url');
		$hook->method('getType')->willReturn('object');
		$hook->method('getValue')->willReturn($value);
		$hook->method('getParams')->willReturn($params);
		$hook->method('getParam')->willReturnCallback(function ($key, $default = null) use ($params) {
			return $params[$key] ?? $default;
		});
		return $hook;
	}

	public function testReturnsVoidWhenSettingDisabled(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}
		$plugin->setSetting('replace_filetype_icons', '');

		$file = $this->getMockBuilder(\ElggFile::class)
			->disableOriginalConstructor()
			->getMock();
		$hook = $this->makeHook(['entity' => $file, 'size' => 'medium']);

		$this->assertNull(Icons::setDefaultFileIcons($hook));
	}

	public function testReturnsVoidWhenEntityIsNotFile(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}
		$plugin->setSetting('replace_filetype_icons', '1');

		$object = $this->getMockBuilder(\ElggObject::class)
			->disableOriginalConstructor()
			->getMock();
		$hook = $this->makeHook(['entity' => $object, 'size' => 'medium']);

		$this->assertNull(Icons::setDefaultFileIcons($hook));
	}

	public function testReturnsVoidWhenFileSubtypeIsNotFile(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}
		$plugin->setSetting('replace_filetype_icons', '1');

		$file = $this->getMockBuilder(\ElggFile::class)
			->disableOriginalConstructor()
			->onlyMethods(['getSubtype'])
			->getMock();
		$file->method('getSubtype')->willReturn('other');

		$hook = $this->makeHook(['entity' => $file, 'size' => 'medium']);
		$this->assertNull(Icons::setDefaultFileIcons($hook));
	}

	public function testImageMimeWithIcontimePassesThroughExistingUrl(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}
		$plugin->setSetting('replace_filetype_icons', '1');

		$file = $this->getMockBuilder(\ElggFile::class)
			->disableOriginalConstructor()
			->onlyMethods(['getSubtype', 'getMimeType', 'getFilenameOnFilestore'])
			->getMock();
		$file->method('getSubtype')->willReturn('file');
		$file->method('getMimeType')->willReturn('image/png');
		$file->method('getFilenameOnFilestore')->willReturn('/tmp/a.png');
		$file->mimetype = 'image/png';
		$file->icontime = time();

		$hook = $this->makeHook(
			['entity' => $file, 'size' => 'medium'],
			'http://example.com/existing.png'
		);

		$this->assertEquals('http://example.com/existing.png', Icons::setDefaultFileIcons($hook));
	}

	public function testPdfFileReturnsSimplecacheUrlForPdfIcon(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}
		$plugin->setSetting('replace_filetype_icons', '1');

		$file = $this->getMockBuilder(\ElggFile::class)
			->disableOriginalConstructor()
			->onlyMethods(['getSubtype', 'getMimeType', 'getFilenameOnFilestore'])
			->getMock();
		$file->method('getSubtype')->willReturn('file');
		$file->method('getMimeType')->willReturn('application/pdf');
		$file->method('getFilenameOnFilestore')->willReturn('/tmp/a.pdf');
		$file->mimetype = 'application/pdf';

		$hook = $this->makeHook(['entity' => $file, 'size' => 'medium']);
		$result = Icons::setDefaultFileIcons($hook);

		// Should be a non-empty URL string from simplecache for some icon view.
		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}
}
