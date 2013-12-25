<?php
namespace Dynamo;

use DI\Injector,
	Convey\Router,
	http\Message;

abstract class WebApp extends App {
	public $router, $request, $response;

	/**
	  * Not worth testing this because it's a static method that creates an instance
	  */
	/** @codeCoverageIgnore */
	public static function create() {
		$injector = new Injector();
		$injector->provide('injector', $injector);
		$request = new HttpRequest();
		$response = new HttpResponse();
		$router = new Router();
		return new static($injector, $router, $request, $response);
	}

	public function __construct(Injector $injector, Router $router = null, HttpRequest $request = null, HttpResponse $response = null) {
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
		$this->register(function (HttpRequest $request, Injector $injector) use($self) {
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
