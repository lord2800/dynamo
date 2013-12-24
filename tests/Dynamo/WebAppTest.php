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
		$router = $this->getMock('Convey\\Router');
		$request = $this->getMock('http\\Env\\Request');
		$response = new FakeResponse();
		$this->app = new MockWebApp(new DI\Injector(), $router, $request, $response);
	}

	public function testHasRequestAndResponseAndRouterObjects() {
		$request = $this->app->getInjector()->retrieve('request');
		$response = $this->app->getInjector()->retrieve('response');
		$router = $this->app->getInjector()->retrieve('router');

		$this->assertInstanceOf('http\\Env\\Request', $request);
		$this->assertInstanceOf('FakeResponse', $response);
		$this->assertInstanceOf('Convey\\Router', $router);
	}
}