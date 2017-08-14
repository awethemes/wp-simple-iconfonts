<?php
/**
 * Class ManagerTest
 *
 * @package Wp_Simple_Iconfonts
 */

use WP_Simple_Iconfonts\Iconfonts;
use WP_Simple_Iconfonts\Iconpack;

class TestIconPack1 extends Iconpack {
	public $id = 'iconpack1';
}
class TestIconPack2 extends Iconpack {
	public $id = 'iconpack2';
}

class ManagerTest extends WP_UnitTestCase {
	public function test_getter() {
		$m = new Iconfonts;
		$m->register($a = new TestIconPack1);
		$m->register($b = new TestIconPack2);

		$this->assertInstanceOf('TestIconPack1', $m->get('iconpack1'));
		$this->assertInstanceOf('TestIconPack2', $m->get('iconpack2'));
		$this->assertNull($m->get('iconpacknone'));

		$this->assertArrayHasKey('iconpack1', $m->all());
		$this->assertArrayHasKey('iconpack2', $m->all());
	}

	public function test_register() {
		$m = new Iconfonts;

		$m->register($a = new TestIconPack1);
		$m->register($b = new TestIconPack2);

		$this->assertInstanceOf('TestIconPack1', $m->get('iconpack1'));
		$this->assertInstanceOf('TestIconPack2', $m->get('iconpack2'));
	}
}
