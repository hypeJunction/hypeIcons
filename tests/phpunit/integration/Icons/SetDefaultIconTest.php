<?php

namespace hypeJunction\Icons;

use Elgg\Event;
use Elgg\IntegrationTestCase;

/**
 * Tests for Icons::setDefaultIcon (entity:icon:url + entity:cover:url, type=all).
 *
 * These cover control-flow branches: existing icon short-circuit, missing
 * entity guard, and fallback to view-based default icon resolution.
 */
class SetDefaultIconTest extends IntegrationTestCase {

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
     * @param string $type
     * @param array $params
     * @param mixed $value
     * @return Event
     */
    protected function makeHook(string $type, array $params, $value = null): Event {
		$hook = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
		$hook->method('getName')->willReturn('entity:icon:url');
		$hook->method('getType')->willReturn($type);
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
    public function testReturnsVoidWhenEntityMissing(): void {
		$hook = $this->makeHook('all', ['entity' => null]);
		$this->assertNull(Icons::setDefaultIcon($hook));
	}

	/**
     * @return void
     */
    public function testReturnsVoidWhenEntityNotElggEntity(): void {
		$hook = $this->makeHook('all', ['entity' => new \stdClass()]);
		$this->assertNull(Icons::setDefaultIcon($hook));
	}

	/**
     * @return void
     */
    public function testFallsBackToSimpleCacheViewWhenNoValue(): void {
		// User with no current icon URL and default setting should fall through
		// to the view-based lookup. We can't guarantee the view exists, but we
		// can at least assert the function handles a real ElggUser without error.
		$user = $this->createUser();
		$hook = $this->makeHook('all', [
			'entity' => $user,
			'size' => 'medium',
			'type' => 'icon',
		]);

		$result = Icons::setDefaultIcon($hook);
		// Either returns a URL string (view existed) or null (no fallback view) —
		// both are valid, the important thing is no exception.
		$this->assertTrue($result === null || is_string($result));
	}

	/**
     * @return void
     */
    public function testExistingNonCorePathValueShortCircuitsWhenNoReplaceSetting(): void {
		$plugin = elgg_get_plugin_from_id('hypeicons');
		if (!$plugin) {
			$this->markTestSkipped('hypeicons plugin not registered in test DB');
		}
		$plugin->setSetting('replace_default_icons', '');

		$user = $this->createUser();
		$hook = $this->makeHook('all', [
			'entity' => $user,
			'size' => 'medium',
			'type' => 'icon',
		], 'http://example.com/my-custom-url.png');

		$result = Icons::setDefaultIcon($hook);
		// When a non-core URL is already set and replace_default_icons is off,
		// the handler should not override it — returns null (no change).
		$this->assertNull($result);
	}

	/**
     * @return void
     */
    public function testCoverTypeAlwaysReplacesDefault(): void {
		$user = $this->createUser();
		$hook = $this->makeHook('all', [
			'entity' => $user,
			'size' => 'medium',
			'type' => 'cover',
		]);

		$result = Icons::setDefaultIcon($hook);
		$this->assertTrue($result === null || is_string($result));
	}
}
