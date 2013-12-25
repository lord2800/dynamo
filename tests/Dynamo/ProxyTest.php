<?php

use Dynamo\Proxy;

class A {
	public $value = 5;
	public $passed;
	public function __construct($v) { $this->passed = $v; }
	public function getStr() { return 'test'; }
	public static function notVisible() { }
}

class ProxyA extends Proxy {
	public function __construct($v) { parent::__construct('\\A', $v); }
}

class ProxyTest extends PHPUnit_Framework_TestCase {
	private $proxy;

	public function setUp() {
		$this->proxy = new ProxyA('info');
	}

	public function testProxyHasAllMethodsOfOriginal() {
		$this->assertEquals(5, $this->proxy->value);
		$this->proxy->value = 10;
		$this->assertEquals(10, $this->proxy->value);
		$this->assertEquals('info', $this->proxy->passed);
		$this->assertEquals('test', $this->proxy->getStr());
		$this->assertTrue(isset($this->proxy->passed));
		unset($this->proxy->passed);
	}
}
