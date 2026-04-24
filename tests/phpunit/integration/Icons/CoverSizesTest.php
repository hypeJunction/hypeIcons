<?php

namespace hypeJunction\Icons;

use Elgg\Event;
use Elgg\IntegrationTestCase;

/**
 * Tests for Icons::setCoverSizes and Icons::saveCoverCroppingCoords.
 */
class CoverSizesTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	/**
     * @return string
     */
    public function getPluginID(): string {
		return '';
	}

	/**
     * @param mixed $value
     * @param array $params
     * @return Event
     */
    protected function makeHook($value, array $params = []): Event {
		$hook = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
		$hook->method('getName')->willReturn('entity:cover:sizes');
		$hook->method('getType')->willReturn('all');
		$hook->method('getValue')->willReturn($value);
		$hook->method('getParams')->willReturn($params);
		$hook->method('getParam')->willReturnCallback(function ($key, $default = null) use ($params) {
			return $params[$key] ?? $default;
		});
		return $hook;
	}

	/**
     * @return void
     */
    public function testReturnsVoidWhenValueAlreadySet(): void {
		$hook = $this->makeHook(['original' => []]);
		$this->assertNull(Icons::setCoverSizes($hook));
	}

	/**
     * @return void
     */
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

	/**
     * @return void
     */
    public function testMasterSizeIsCorrect(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);

		$this->assertEquals(1000, $sizes['master']['w']);
		$this->assertEquals(370, $sizes['master']['h']);
		$this->assertFalse($sizes['master']['square']);
		$this->assertTrue($sizes['master']['upscale']);
	}

	/**
     * @return void
     */
    public function testLargeMediumSmallSizesAreDescending(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);

		$this->assertGreaterThan($sizes['medium']['w'], $sizes['large']['w']);
		$this->assertGreaterThan($sizes['small']['w'], $sizes['medium']['w']);
		$this->assertGreaterThan($sizes['medium']['h'], $sizes['large']['h']);
		$this->assertGreaterThan($sizes['small']['h'], $sizes['medium']['h']);
	}

	/**
     * @return void
     */
    public function testAllNonOriginalSizesAreNotSquareAndNotUpscaledExceptMaster(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);

		foreach (['large', 'medium', 'small'] as $key) {
			$this->assertFalse($sizes[$key]['square'], "$key must not be square");
			$this->assertFalse($sizes[$key]['upscale'], "$key must not upscale");
		}
	}

	/**
     * @return void
     */
    public function testOriginalHasEmptyConfig(): void {
		$hook = $this->makeHook([]);
		$sizes = Icons::setCoverSizes($hook);
		$this->assertSame([], $sizes['original']);
	}

	/**
     * @return void
     */
    public function testSaveCoverCroppingCoordsAssignsAllFour(): void {
		$entity = new \stdClass();
		$hook = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
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
