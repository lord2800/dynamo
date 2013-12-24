<?php

use Dynamo\WebApp;

class MockWebApp extends WebApp {
	public function config() {}
	public function getInjector() { return $this->injector; }
}

// TODO this is horrible!
class FakeResponse {}

class WebAppTest extends PHPUnit_Framework_TestCase {
	private $app;

	public function setUp() {
		$router = $this->getMock('Convey\\Router', ['route']);
		$request = $this->getMock('http\\Env\\Request');
		$response = new FakeResponse();
		$injector = new DI\Injector();

		$injector->provide('injector', $injector);

		$this->app = new MockWebApp($injector, $router, $request, $response);
	}

	public function testHasRequestAndResponseAndRouterObjects() {
		$request = $this->app->getInjector()->retrieve('request');
		$response = $this->app->getInjector()->retrieve('response');
		$router = $this->app->getInjector()->retrieve('router');

		$this->assertInstanceOf('http\\Env\\Request', $request);
		$this->assertInstanceOf('FakeResponse', $response);
		$this->assertInstanceOf('Convey\\Router', $router);
	}

	public function testRunFunctionRoutesAndReturnsResponse() {
		$called = false;
		$self = $this;
		$router = $this->app->getInjector()->retrieve('router');
		$router->expects($this->once())
				->method('route')
				->will($this->returnValue([['a' => 5], function ($params) use(&$called, $self) {
					$self->assertEquals(5, $params['a']);
					$called = true;
				}]));

		$response = $this->app->run();
		$this->assertTrue($called);
		$this->assertNotNull($response);
		$this->assertInstanceOf('FakeResponse', $response);
	}
}