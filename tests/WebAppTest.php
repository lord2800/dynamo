<?php
namespace Dynamo\Tests;

use Dynamo\WebApp;

class InspectableWebApp extends WebApp {
	public function getInjector() { return $this->injector; }
	public function config() {}
}

class WebAppTest extends \PHPUnit_Framework_TestCase {
	public function testHasRequestAndResponseAndRouterObjects() {
		$app = InspectableWebApp::create();
		$request = $app->getInjector()->get(\Dynamo\HttpRequest::class);
		$response = $app->getInjector()->get(\Dynamo\HttpResponse::class);
		$router = $app->getInjector()->get(\Convey\Router::class);

		$this->assertInstanceOf(\Dynamo\HttpRequest::class, $request);
		$this->assertInstanceOf(\Dynamo\HttpResponse::class, $response);
		$this->assertInstanceOf(\Convey\Router::class, $router);
	}

	public function testRunFunctionRoutesAndReturnsResponse() {
		$called = false;
		$self = $this;

		$injector = new \DI\Injector();
		$injector->bind(\DI\Injector::class, $injector);

		$request = $this->getMockBuilder(\Dynamo\HttpRequest::class)
						->disableOriginalConstructor()
						->getMock();
		$response = $this->getMock(\Dynamo\HttpResponse::class);

		$router = $this->getMock(\Convey\Router::class);
		$router->expects($this->once())
				->method('route')
				->will($this->returnValue([
					function () use(&$called, $self) {
						$called = true;
					}
				]));

		$app = new InspectableWebApp($injector, $router, $request, $response);

		$response = $app->run();
		$this->assertTrue($called);
		$this->assertNotNull($response);
		$this->assertInstanceOf('Dynamo\\HttpResponse', $response);
	}
}
