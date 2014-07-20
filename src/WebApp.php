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
		$injector->bind(Injector::class, $injector);

		$request = HttpRequest::create();
		$response = new HttpResponse();
		$router = new Router();

		return new static($injector, $router, $request, $response);
	}

	public function __construct(Injector $injector, Router $router = null, HttpRequest $request = null, HttpResponse $response = null) {
		parent::__construct($injector);
		$this->router = $router;

		$injector->bind(HttpRequest::class, $request);
		$injector->bind(HttpResponse::class, $response);
		$injector->bind(Router::class, $router);
	}

	public function run() {
		ob_start();
		$self = $this;
		$this->register(function (HttpRequest $request, Injector $injector) use($self) {
			$m = $request->getMethod();
			$p = $request->getUrl();
			foreach($self->router->route($m, $p) as $route) {
				$route();
			}
		});

		parent::run();
		ob_end_clean();

		return $this->injector->get(HttpResponse::class);
	}
}
