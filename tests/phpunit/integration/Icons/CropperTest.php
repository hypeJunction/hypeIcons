<?php

namespace hypeJunction\Icons;

use Elgg\Hook;
use Elgg\IntegrationTestCase;

/**
 * Tests for Cropper::filterFileInputVars (view_vars input/file).
 */
class CropperTest extends IntegrationTestCase {

	public function up() {
	}

	public function down() {
	}

	public function getPluginID(): string {
		return '';
	}

	protected function makeHook(array $return): Hook {
		$hook = $this->getMockBuilder(Hook::class)->getMock();
		$hook->method('getName')->willReturn('view_vars');
		$hook->method('getType')->willReturn('input/file');
		$hook->method('getValue')->willReturn($return);
		return $hook;
	}

	public function testReturnsInputUnchangedWhenUseCropperMissing(): void {
		$hook = $this->makeHook(['class' => 'foo']);
		$result = Cropper::filterFileInputVars($hook);

		$this->assertIsArray($result);
		$this->assertEquals('foo', $result['class']);
		$this->assertArrayNotHasKey('id', $result);
	}

	public function testReturnsInputUnchangedWhenUseCropperFalsy(): void {
		$hook = $this->makeHook(['use_cropper' => false, 'class' => 'bar']);
		$result = Cropper::filterFileInputVars($hook);

		$this->assertEquals('bar', $result['class']);
	}

	public function testAppendsCropperClassWhenStringClass(): void {
		$hook = $this->makeHook([
			'use_cropper' => true,
			'class' => 'elgg-input-file',
			'id' => 'my-id',
		]);
		$result = Cropper::filterFileInputVars($hook);

		$this->assertStringContainsString('elgg-input-file', $result['class']);
		$this->assertStringContainsString('file-input-has-cropper', $result['class']);
		$this->assertEquals('my-id', $result['id']);
	}

	public function testAppendsCropperClassWhenArrayClass(): void {
		$hook = $this->makeHook([
			'use_cropper' => ['ratio' => 1],
			'class' => ['foo', 'bar'],
			'id' => 'x',
		]);
		$result = Cropper::filterFileInputVars($hook);

		$this->assertStringContainsString('foo', $result['class']);
		$this->assertStringContainsString('bar', $result['class']);
		$this->assertStringContainsString('file-input-has-cropper', $result['class']);
	}

	public function testAddsClassWhenNoClassPresent(): void {
		$hook = $this->makeHook([
			'use_cropper' => true,
			'id' => 'some-id',
		]);
		$result = Cropper::filterFileInputVars($hook);
		$this->assertEquals('file-input-has-cropper', trim($result['class']));
	}

	public function testGeneratesIdWhenMissing(): void {
		$hook = $this->makeHook(['use_cropper' => true]);
		$result = Cropper::filterFileInputVars($hook);

		$this->assertNotEmpty($result['id']);
		$this->assertStringStartsWith('elgg-file-input-', $result['id']);
	}

	public function testGeneratedIdsAreUnique(): void {
		$r1 = Cropper::filterFileInputVars($this->makeHook(['use_cropper' => true]));
		$r2 = Cropper::filterFileInputVars($this->makeHook(['use_cropper' => true]));
		$this->assertNotEquals($r1['id'], $r2['id']);
	}

	public function testPreservesExistingId(): void {
		$hook = $this->makeHook([
			'use_cropper' => true,
			'id' => 'keep-me',
		]);
		$result = Cropper::filterFileInputVars($hook);
		$this->assertEquals('keep-me', $result['id']);
	}
}
