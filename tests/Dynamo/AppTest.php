<?php

use \Dynamo\App;

class MockApp extends App {
	public function config() { }
}

class AppTest extends PHPUnit_Framework_TestCase {
	private $app;

	public function setUp() {
		$this->app = MockApp::create();
	}

	public function testRegisterShouldAddToTheMiddlewareList() {
		$called = false;
		$this->app->register(function () use(&$called) { $called = true; });
		$this->app->run();
		$this->assertTrue($called);
	}

	public function testRunShouldCallAllGeneratorsInOrder() {
		$called1 = false;
		$called2 = false;
		$self = $this;

		$this->app->register(function () use(&$called1) { $called1 = true; });
		$this->app->register(function () use(&$called1, &$called2, $self) {
			$self->assertTrue($called1);
			$called2 = true;
		});
		$this->app->run();

		$this->assertTrue($called1);
		$this->assertTrue($called2);
	}

	/**
	  * @expectedException RuntimeException
	  * @expectedExceptionMessage iterations reached, aborting
	  */
	public function testShouldThrowWhenInInfiniteLoop() {
		$this->app->register(function () { while(true) yield; });
		$this->app->run();
	}
}
