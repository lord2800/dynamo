<?php
namespace Dynamo;

use DI\Injector,
	Convey\Router;

abstract class WebApp extends App {
	public $router, $request, $response;

	/**
	  * Not worth testing this because it's a static method that creates an instance with the right dependency-injected things
	  */
	/** @codeCoverageIgnore */
	public static function create() {
		$injector = new Injector();
		$injector->provide('injector', $injector);
		$request = HttpRequest::create();
		$response = new HttpResponse();
		$router = new Router();
		return new static($injector, $router, $request, $response);
	}

	public function __construct(Injector $injector, Router $router = null, HttpRequest $request = null, HttpResponse $response = null) {
		$this->router = $router;

		$injector->provide('request', $request);
		$injector->provide('response', $response);
		$injector->provide('router', $this->router);
		$this->injector = $injector;
		ob_start();
	}

	public function run() {
		$self = $this;
		$this->register(function (HttpRequest $request, Injector $injector) use($self) {
			$m = strtolower($request->getMethod());
			$p = $request->getUrl();
			foreach($self->router->route($m, $p) as $route) {
				list($args, $cb) = $route;
				$self->injector->provide('params', $args);
				$fn = $self->injector->inject($cb);
				$fn();
			}
		});

		parent::run();

		return $this->injector->retrieve('response');
	}
}
