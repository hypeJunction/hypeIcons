<?php

namespace hypeJunction\Icons;

use Elgg\IntegrationTestCase;

/**
 * Pure-function tests for Icons::mapMimeToIconType.
 *
 * These exercise the mime/extension switch table without any Elgg state.
 * Kept as integration test so autoloading + bootstrap is consistent with
 * the rest of the suite.
 */
class MimeMapperTest extends IntegrationTestCase {

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
     * @return array
     */
    public function mimeCases(): array {
		return [
			['application/pdf', '', 'pdf'],
			['application/x-pdf', '', 'pdf'],
			['application/vnd.pdf', '', 'pdf'],
			['application/ogg', '', 'audio'],
			['text/plain', '', 'text'],
			['text/richtext', '', 'text'],
			['text/html', '', 'document'],
			['application/rtf', '', 'document'],
			['application/vnd.oasis.opendocument.text', '', 'document'],
			['application/vnd.oasis.opendocument.presentation', '', 'presentation'],
			['text/csv', '', 'spreadsheet'],
			['application/vnd.oasis.opendocument.spreadsheet', '', 'spreadsheet'],
			['image/vnd.adobe.photoshop', '', 'drawing'],
			['application/illustrator', '', 'drawing'],
			['application/vnd.oasis.opendocument.image', '', 'image'],
			['application/json', '', 'code'],
			['text/javascript', '', 'code'],
			['text/x-php', '', 'code'],
			['application/x-zip', '', 'archive'],
			['application/x-gzip', '', 'archive'],
			['application/x-tar', '', 'archive'],
			['application/vnd.google-earth.kml+xml', '', 'map'],
			['text/vcard', '', 'vcard'],
			['text/calendar', '', 'calendar'],
			['application/calendar+json', '', 'calendar'],
		];
	}

	/**
	 * @dataProvider mimeCases
	 */
	public function testMapsCommonMimes(string $mime, string $ext, string $expected): void {
		$this->assertEquals($expected, Icons::mapMimeToIconType($mime, $ext));
	}

	/**
     * @return void
     */
    public function testOfficeZipWordExtension(): void {
		$this->assertEquals('word', Icons::mapMimeToIconType('application/msword', 'doc'));
		$this->assertEquals('word', Icons::mapMimeToIconType('application/msword', 'docx'));
	}

	/**
     * @return void
     */
    public function testOfficeZipExcelExtension(): void {
		$this->assertEquals('excel', Icons::mapMimeToIconType('application/vnd.ms-excel', 'xls'));
		$this->assertEquals('excel', Icons::mapMimeToIconType('application/vnd.ms-excel', 'xlsx'));
	}

	/**
     * @return void
     */
    public function testOfficeZipPowerpointExtension(): void {
		$this->assertEquals('powerpoint', Icons::mapMimeToIconType('application/vnd.ms-powerpoint', 'ppt'));
		$this->assertEquals('powerpoint', Icons::mapMimeToIconType('application/vnd.ms-powerpoint', 'pptx'));
		$this->assertEquals('powerpoint', Icons::mapMimeToIconType('application/vnd.ms-powerpoint', 'pot'));
	}

	/**
     * @return void
     */
    public function testOfficeZipArchiveExtension(): void {
		$this->assertEquals('archive', Icons::mapMimeToIconType('application/zip', 'zip'));
		$this->assertEquals('archive', Icons::mapMimeToIconType('application/zip', 'jar'));
		$this->assertEquals('archive', Icons::mapMimeToIconType('application/zip', 'war'));
	}

	/**
     * @return void
     */
    public function testOfficeZipUnknownExtensionDefaults(): void {
		$this->assertEquals('default', Icons::mapMimeToIconType('application/zip', 'xyz'));
	}

	/**
     * @return void
     */
    public function testBinaryExtensionMapsToApplication(): void {
		$this->assertEquals('application', Icons::mapMimeToIconType('application/octet-stream', 'bin'));
		$this->assertEquals('application', Icons::mapMimeToIconType('application/octet-stream', 'exe'));
	}

	/**
     * @return void
     */
    public function testImageAudioVideoMimePrefix(): void {
		$this->assertEquals('image', Icons::mapMimeToIconType('image/png', ''));
		$this->assertEquals('audio', Icons::mapMimeToIconType('audio/mpeg', ''));
		$this->assertEquals('video', Icons::mapMimeToIconType('video/mp4', ''));
	}

	/**
     * @return void
     */
    public function testUnknownMimeAndExtensionReturnsDefault(): void {
		$this->assertEquals('default', Icons::mapMimeToIconType('application/x-wat', 'foo'));
	}

	/**
     * @return void
     */
    public function testDefaultArguments(): void {
		// No args: application/otcet-stream [sic] + empty ext -> default
		$this->assertEquals('default', Icons::mapMimeToIconType());
	}
}
