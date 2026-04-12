<?php

namespace hypeJunction\Icons;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

/**
 * Tests for Icons::setCoverSizes and Icons::saveCoverCroppingCoords.
 */
class CoverSizesTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	public function getPluginID(): string {
		return '';
	}

	protected function makeHook($value, array $params = []): Hook {
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$hook->method('getName')->willReturn('entity:cover:sizes');
		$hook->method('getType')->willReturn('all');
		$hook->method('getValue')->willReturn($value);
		$hook->method('getParams')->willReturn($params);
		$hook->method('getParam')->willReturnCallback(function ($key, $default = null) use ($params) {
			return $params[$key] ?? $default;
		});
		return $hook;
	}

	public function testReturnsVoidWhenValueAlreadySet(): void {
		$hook = $this->makeHook(['original' => []]);
		$this->assertNull(Icons::setCoverSizes($hook));
	}

	public function testReturnsSizeMapWhenValueEmpty(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);

		$this->assertIsArray($sizes);
		$this->assertArrayHasKey('original', $sizes);
		$this->assertArrayHasKey('master', $sizes);
		$this->assertArrayHasKey('large', $sizes);
		$this->assertArrayHasKey('medium', $sizes);
		$this->assertArrayHasKey('small', $sizes);
	}

	public function testMasterSizeIsCorrect(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);

		$this->assertEquals(1000, $sizes['master']['w']);
		$this->assertEquals(370, $sizes['master']['h']);
		$this->assertFalse($sizes['master']['square']);
		$this->assertTrue($sizes['master']['upscale']);
	}

	public function testLargeMediumSmallSizesAreDescending(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);

		$this->assertGreaterThan($sizes['medium']['w'], $sizes['large']['w']);
		$this->assertGreaterThan($sizes['small']['w'], $sizes['medium']['w']);
		$this->assertGreaterThan($sizes['medium']['h'], $sizes['large']['h']);
		$this->assertGreaterThan($sizes['small']['h'], $sizes['medium']['h']);
	}

	public function testAllNonOriginalSizesAreNotSquareAndNotUpscaledExceptMaster(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);

		foreach (['large', 'medium', 'small'] as $key) {
			$this->assertFalse($sizes[$key]['square'], "$key must not be square");
			$this->assertFalse($sizes[$key]['upscale'], "$key must not upscale");
		}
	}

	public function testOriginalHasEmptyConfig(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);
		$this->assertSame([], $sizes['original']);
	}

	public function testSaveCoverCroppingCoordsAssignsAllFour(): void {
		$entity = new \stdClass();
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$params = [
			'entity' => $entity,
			'x1' => 10,
			'y1' => 20,
			'x2' => 110,
			'y2' => 80,
		];
		$hook->method('getParam')->willReturnCallback(function ($key, $default = null) use ($params) {
			return $params[$key] ?? $default;
		});

		Icons::saveCoverCroppingCoords($hook);

		$this->assertEquals(10, $entity->cover_x1);
		$this->assertEquals(20, $entity->cover_y1);
		$this->assertEquals(110, $entity->cover_x2);
		$this->assertEquals(80, $entity->cover_y2);
	}
}
