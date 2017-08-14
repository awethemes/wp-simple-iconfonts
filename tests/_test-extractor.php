<?php
/**
 * Class IconParserTest
 *
 * @package Awecontent
 */

/**
 * Sample test case.
 */
class IconParserTest extends WP_UnitTestCase {

	public function test_register_icon() {
		$icon_manager = AC_Icon_Manager::get_instance();

		$icon_manager->register_icon( 'test_empty_icon', array(
			'label' => 'Test Empty Icon Pack',
			'icons' => array(),
		) );

		$this->assertTrue( $icon_manager->has_icon( 'test_empty_icon' ) );
		$this->assertEmpty( $icon_manager->get_icon( 'test_empty_icon' )->icons );

		$icon_manager->unregister_icon( 'test_empty_icon' );
		$this->assertFalse( $icon_manager->has_icon( 'test_empty_icon' ) );
	}

	/**
	 * Test guest parser.
	 */
	public function test_parser_ok() {
		WP_Filesystem();
		global $wp_filesystem;

		$paths = array(
			'AC_Icon_Parser_Ionicons'     => __DIR__ . '/icons/ionicons-2.0.1.zip',
			'AC_Icon_Parser_Icomoon'      => __DIR__ . '/icons/icomoon-60-vicons.zip',
			'AC_Icon_Parser_FontAwesome'  => __DIR__ . '/icons/font-awesome-4.6.3.zip',
			'AC_Icon_Parser_Fontello'     => __DIR__ . '/icons/fontello-fontelico.zip',
		);

		// 'AC_Icon_Parser_Pixeden',
		// 'AC_Icon_Parser_Icomoon',
		// 'AC_Icon_Parser_Fontello',
		// 'AC_Icon_Parser_Ionicons',
		// 'AC_Icon_Parser_Octicons',
		// 'AC_Icon_Parser_Foundation',
		// 'AC_Icon_Parser_Fontawesome',
		// 'AC_Icon_Parser_PaymentFont',
		// 'AC_Icon_Parser_Elusive',
		// 'AC_Icon_Parser_Themify',

		$icon_manager = AC_Icon_Manager::get_instance();
		$basepath = $this->get_test_directory( true );

		foreach ( $paths as $name => $path ) {
			list( $directory, $working_directory ) = $icon_manager->unzip_file( $path, $basepath );
			$parser = $icon_manager->parse( $directory );

			$this->assertTrue( $parser instanceof AC_Icon_Parser_Abstract );
			$this->assertEquals( get_class( $parser ), $name );
			$this->assertEquals( $parser->is_error(), false );
			$this->assertNotEmpty( $parser->icons() );

			switch ( get_class( $parser ) ) {
				case 'AC_Icon_Parser_Fontawesome':
					$this->assertEquals( $parser->icon_name(), 'font-awesome' );
					break;

				case 'AC_Icon_Parser_Ionicons':
					$this->assertEquals( $parser->icon_name(), 'ionicons' );
					$this->assertEquals( count( $parser->icons() ), 733 );
					break;

				case 'AC_Icon_Parser_Icomoon':
					$this->assertEquals( $parser->icon_name(), '60-vicons' );
					$this->assertEquals( count( $parser->icons() ), 60 );
					break;

				case 'AC_Icon_Parser_Fontello':
					$this->assertEquals( $parser->icon_name(), 'fontelico-icons' );
					$this->assertEquals( count( $parser->icons() ), 31 );
					break;
			}
		}
	}

	/**
	 * Return test directory.
	 *
	 * @param  boolean $clean Clean directory.
	 * @return string
	 */
	protected function get_test_directory( $clean = false ) {
		global $wp_filesystem;

		$content_dir = $wp_filesystem->wp_content_dir();
		$testing_directory = $content_dir . '/ac-testing-icons';

		if ( $clean && $wp_filesystem->is_dir( $testing_directory ) ) {
			$wp_filesystem->rmdir( $testing_directory, true );
		}

		if ( ! $wp_filesystem->is_dir( $testing_directory ) ) {
			$wp_filesystem->mkdir( $testing_directory );
		}

		return trailingslashit( $testing_directory );
	}
}
