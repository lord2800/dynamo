<?php

use Dynamo\WebApp;

class MockWebApp extends WebApp {
	public function config() {}
	public function getInjector() { return $this->injector; }
}

class WebAppTest extends PHPUnit_Framework_TestCase {
	private $app;

	public function setUp() {
		$router = $this->getMock('Convey\\Router', ['route']);
		$request = $this->getMockBuilder('Dynamo\\HttpRequest')->disableOriginalConstructor()->getMock();
		$response = $this->getMockBuilder('Dynamo\\HttpResponse')->disableOriginalConstructor()->getMock();
		$injector = new DI\Injector();

		$injector->provide('injector', $injector);

		$this->app = new MockWebApp($injector, $router, $request, $response);
	}

	public function testHasRequestAndResponseAndRouterObjects() {
		$request = $this->app->getInjector()->retrieve('request');
		$response = $this->app->getInjector()->retrieve('response');
		$router = $this->app->getInjector()->retrieve('router');

		$this->assertInstanceOf('Dynamo\\HttpRequest', $request);
		$this->assertInstanceOf('Dynamo\\HttpResponse', $response);
		$this->assertInstanceOf('Convey\\Router', $router);
	}

	public function testRunFunctionRoutesAndReturnsResponse() {
		$called = false;
		$self = $this;
		$router = $this->app->getInjector()->retrieve('router');
		$router->expects($this->once())
				->method('route')
				->will($this->returnValue([[['a' => 5], function ($params) use(&$called, $self) {
					$self->assertEquals(5, $params['a']);
					$called = true;
				}]]));

		$response = $this->app->run();
		$this->assertTrue($called);
		$this->assertNotNull($response);
		$this->assertInstanceOf('Dynamo\\HttpResponse', $response);
	}
}