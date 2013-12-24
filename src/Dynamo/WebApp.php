<?php
namespace Dynamo;

use DI\Injector,
	Convey\Router,
	http\Env\Request,
	http\Env\Response,
	http\Message;

abstract class WebApp extends App {
	public $router, $request, $response;

	// This method is inherently untestable--http\Env\Response requires that you are able
	// to capture the response body, and that is just not possible in PHPUnit because it
	// starts output immediately
	/** @codeCoverageIgnore */
	public static function create() {
		$injector = new Injector();
		$injector->provide('injector', $injector);
		$request = new Request();
		$response = new Response();
		$router = new Router();
		return new static($injector, $router, $request, $response);
	}

	public function __construct(Injector $injector, Router $router = null, Request $request = null, /*Response*/ $response = null) {
		$http = new \ReflectionExtension('http');
		if(!version_compare($http->getVersion(), '2.0.0', '>=')) {
			// can't test this exception due to it being impossible to make this fail
			// @codeCoverageIgnoreStart
			throw new \RuntimeException('You must have pecl http 2.0 or above!');
			// @codeCoverageIgnoreEnd
		}

		$this->router = $router;

		$injector->provide('request', $request);
		$injector->provide('response', $response);
		$injector->provide('router', $this->router);
		$this->injector = $injector;
	}

	public function run() {
		$self = $this;
		$this->register(function (Request $request, Injector $injector) use($self) {
			$m = strtolower($request->getRequestMethod());
			$p = $request->getRequestUrl();
			list($args, $cb) = $self->router->route($m, $p);
			$self->injector->provide('params', $args);
			$fn = $self->injector->inject($cb);
			$fn();
		});

		parent::run();

		return $this->injector->retrieve('response');
	}
}
