<?php
namespace Dynamo;

use DI\Injector,
	Convey\Router,
	http\Env\Request,
	http\Env\Response,
	http\Message;

abstract class WebApp extends App {
	public $router, $request, $response;

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
			throw new \RuntimeException('You must have pecl http 2.0 or above!');
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
