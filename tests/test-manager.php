<?php
/**
 * Class ManagerTest
 *
 * @package Wp_Simple_Iconfonts
 */

use WP_Simple_Iconfonts\Iconfonts;
use WP_Simple_Iconfonts\Iconpack;
use WP_Simple_Iconfonts\Imported_Iconpack;
use WP_Simple_Iconfonts\Icons\Dashicons;
use WP_Simple_Iconfonts\Icons\FontAwesome;

class TestIconPack1 extends Iconpack {
	public $id = 'iconpack1';
}
class TestIconPack2 extends Iconpack {
	public $id = 'iconpack2';
}
class TestIconPack3 extends Iconpack {
	public $id = 'iconpack3';
}
class TestIconPack3SameIDButDiff extends Iconpack {
	public $id = 'iconpack3';
	public $name = 'Icon Pack 3';
}

class TestUploadIconPack1 extends Imported_Iconpack {
	public $id = 'iconuploadpack1';
}
class TestUploadIconPack2 extends Imported_Iconpack {
	public $id = 'iconuploadpack2';
}
class TestUploadIconPack3 extends Imported_Iconpack {
	public $id = 'iconuploadpack3';
}

class ManagerTest extends WP_UnitTestCase {
	public function test_register() {
		$m = new Iconfonts;
		$m->register($a = new TestIconPack1);
		$m->register($b = new TestIconPack2);

		$this->assertInstanceOf('TestIconPack1', $m->get('iconpack1'));
		$this->assertInstanceOf('TestIconPack2', $m->get('iconpack2'));
	}

	public function test_register_with_same_id() {
		$m = new Iconfonts;
		$m->register(new TestIconPack3);
		@$m->register(new TestIconPack3SameIDButDiff); // Must not work.
		$this->assertEmpty($m->get('iconpack3')->name);

		$m = new Iconfonts;
		$m->register(new TestIconPack3SameIDButDiff);
		@$m->register(new TestIconPack3); // Must not work.
		$this->assertEquals('Icon Pack 3', $m->get('iconpack3')->name);
	}

	public function test_simple_getter() {
		$m = new Iconfonts;
		$m->register($a = new TestIconPack1);
		$m->register($b = new TestIconPack2);

		$this->assertInstanceOf('TestIconPack1', $m->get('iconpack1'));
		$this->assertInstanceOf('TestIconPack2', $m->get('iconpack2'));
		$this->assertNull($m->get('iconpacknone'));

		$this->assertTrue($m->has('iconpack1'));
		$this->assertTrue($m->has('iconpack2'));
		$this->assertFalse($m->has('iconpacknone'));

		$this->assertArrayHasKey('iconpack1', $m->all());
		$this->assertArrayHasKey('iconpack2', $m->all());
		$this->assertArrayNotHasKey('iconpacknone', $m->all());
	}

	public function test_unregister() {
		$m = new Iconfonts;
		$m->register($a = new TestIconPack1);
		$m->register($b = new TestIconPack2);
		$m->register($c = new TestIconPack3);

		$m->unregister($a);
		$m->unregister($b);

		$this->assertNull($m->get('iconpack1'));
		$this->assertNull($m->get('iconpack2'));
		$this->assertInstanceOf('TestIconPack3', $m->get('iconpack3'));
	}

	public function test_unregister_with_id() {
		$m = new Iconfonts;
		$m->register(new TestIconPack1);
		$m->register(new TestIconPack2);
		$m->register(new TestIconPack3);

		$m->unregister('iconpack1');
		$m->unregister('iconpack2');

		$this->assertNull($m->get('iconpack1'));
		$this->assertNull($m->get('iconpack2'));
		$this->assertInstanceOf('TestIconPack3', $m->get('iconpack3'));
	}

	public function test_getter_with_inactive() {
		update_option( '_wp_simple_iconfonts', [
			'iconuploadpack1' => true,
			'iconuploadpack2' => false,
			'iconuploadpack3' => true,
			'unknownpack'     => true,
		]);

		$m = new Iconfonts;
		$m->register(new TestUploadIconPack1(''));
		$m->register(new TestUploadIconPack2(''));
		$m->register(new TestUploadIconPack3(''));

		// Get without force.
		$this->assertFalse($m->get('iconuploadpack2', false));
		$this->assertInstanceOf('TestUploadIconPack1', $m->get('iconuploadpack1', false));
		$this->assertInstanceOf('TestUploadIconPack3', $m->get('iconuploadpack3', false));
		$this->assertNull($m->get('unknownpack', false));

		// Get with force.
		$this->assertInstanceOf('TestUploadIconPack1', $m->get('iconuploadpack1', true));
		$this->assertInstanceOf('TestUploadIconPack2', $m->get('iconuploadpack2', true));
		$this->assertInstanceOf('TestUploadIconPack3', $m->get('iconuploadpack3', true));
		$this->assertNull($m->get('unknownpack', true));

		// Get all with force.
		$this->assertArrayHasKey('iconuploadpack1', $m->all(false));
		$this->assertArrayNotHasKey('iconuploadpack2', $m->all(false));
		$this->assertArrayHasKey('iconuploadpack3', $m->all(false));

		$this->assertArrayHasKey('iconuploadpack1', $m->all(true));
		$this->assertArrayHasKey('iconuploadpack2', $m->all(true));
		$this->assertArrayHasKey('iconuploadpack3', $m->all(true));
	}
}
