<?php
/**
 * Class IconParserTest
 *
 * @package Awecontent
 */

/**
 * Sample test case.
 */
class SvgExtractTest extends WP_UnitTestCase {
	public function test_extract() {
		$results = WP_Simple_Iconfonts\Utils::glyph_extract( __DIR__ . '/fontawesome-webfont.svg' );

		$this->assertEquals( 'glass', $results[0]['name'] );
		$this->assertEquals( 'f000', $results[0]['code'] );
		$this->assertNotEmpty( $results[0]['path'] );
	}
}
