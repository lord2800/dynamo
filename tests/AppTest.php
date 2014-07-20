<?php
namespace Dynamo\Tests;

use \Dynamo\App;

class MockApp extends App {
	public function config() { }
}

class AppTest extends \PHPUnit_Framework_TestCase {
	public function testRegisterShouldAddToTheMiddlewareList() {
		$called = false;

		$app = MockApp::create();
		$app->register(function () use(&$called) {
			$called = true;
		});

		$app->run();

		$this->assertTrue($called);
	}

	public function testRunShouldCallAllGeneratorsInOrder() {
		$called1 = false;
		$called2 = false;
		$self = $this;

		$app = MockApp::create();
		$app->register(function () use(&$called1) {
			$called1 = true;
		});
		$app->register(function () use(&$called1, &$called2, $self) {
			$self->assertTrue($called1);
			$called2 = true;
		});

		$app->run();

		$this->assertTrue($called1);
		$this->assertTrue($called2);
	}

	public function testShouldThrowWhenInInfiniteLoop() {
		$this->setExpectedException('\\RuntimeException');

		$app = MockApp::create();
		$app->register(function () {
			while(true) {
				yield 1;
			}
		});

		$app->run();
	}

	public function testShouldRemoveMiddlewareEarlyIfTheyEndBeforeOthers() {
		$calledCount = 0;
		$calledCount2 = 0;

		$app = MockApp::create();
		$app->register(function () use(&$calledCount) {
			$calledCount++;
			yield 1;
			$calledCount++;
			yield 1;
			$calledCount++;
		});
		$app->register(function () use(&$calledCount2) {
			$calledCount2++;
			yield 1;
			$calledCount2++;
		});

		$app->run();

		$this->assertEquals(3, $calledCount);
		$this->assertEquals(2, $calledCount2);
	}
}
