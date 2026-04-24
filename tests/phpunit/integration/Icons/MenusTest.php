<?php

namespace hypeJunction\Icons;

use Elgg\Event;
use Elgg\IntegrationTestCase;

/**
 * Tests for Menus hook handlers.
 *
 * NOTE: setupGroupProfileMenu references an undefined $section variable in
 * the legacy 3.x source. The test simply asserts the handler executes without
 * fatal error on non-group entities (the guard clause path).
 */
class MenusTest extends IntegrationTestCase {

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
     * @param array $value
     * @return Event
     */
    protected function makeHook(string $type, array $params, array $value = []): Event {
		$hook = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
		$hook->method('getName')->willReturn('register');
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
    public function testSetupEntityMenuReturnsVoidForNonObject(): void {
		$user = $this->createUser();
		$hook = $this->makeHook('menu:entity', ['entity' => $user]);
		$this->assertNull(Menus::setupEntityMenu($hook));
	}

	/**
     * @return void
     */
    public function testSetupEntityMenuReturnsValueForObject(): void {
		$user = $this->createUser();
		$object = $this->createObject(['subtype' => 'blog', 'owner_guid' => $user->guid]);

		$hook = $this->makeHook('menu:entity', ['entity' => $object], ['existing' => 'item']);
		$result = Menus::setupEntityMenu($hook);

		// At minimum, handler returns an array that includes the existing item.
		$this->assertIsArray($result);
		$this->assertArrayHasKey('existing', $result);
	}

	/**
     * @return void
     */
    public function testSetupUserHoverMenuReturnsVoidForNonUser(): void {
		$object = $this->createObject(['subtype' => 'blog']);
		$hook = $this->makeHook('menu:user_hover', ['entity' => $object]);
		$this->assertNull(Menus::setupUserHoverMenu($hook));
	}

	/**
     * @return void
     */
    public function testSetupUserHoverMenuReturnsArrayForUser(): void {
		$user = $this->createUser();
		$hook = $this->makeHook('menu:user_hover', ['entity' => $user], ['keep' => 'item']);
		$result = Menus::setupUserHoverMenu($hook);

		$this->assertIsArray($result);
	}

	/**
     * @return void
     */
    public function testSetupGroupProfileMenuReturnsVoidForNonGroup(): void {
		$user = $this->createUser();
		$hook = $this->makeHook('profile_buttons', ['entity' => $user]);
		$this->assertNull(Menus::setupGroupProfileMenu($hook));
	}
}
