<?php
namespace Dynamo;

use DI\Injector,
	http\Env\Request,
	http\Env\Response,
	http\Message;

abstract class WebApp extends App {
	protected $routeTable = [];

	protected function __construct(Injector $injector) {
		$http = new \ReflectionExtension('http');
		if(!version_compare($http->getVersion(), '2.0.0', '>=')) {
			throw new \RuntimeException('You must have pecl http 2.0 or above!');
		}

		$injector->provide('request', new Request());
		$injector->provide('response', new Response());
		$this->injector = $injector;
	}

	public function route($method, $path, callable $fn) {
		$method = strtolower($method);
		if(empty($this->routeTable[$method])) {
			$this->routeTable[$method] = [];
		}
		$this->routeTable[$method][$path] = $fn;
	}

	public function run() {
		$self = $this;
		$this->register(function (Request $request, Injector $injector) use($self) {
			$m = strtolower($request->getRequestMethod());
			$p = $request->getRequestUrl();
			if(key_exists($m, $self->routeTable) && key_exists($p, $self->routeTable[$m])) {
				$fn = $injector->inject($self->routeTable[$m][$p]);
				$fn();
			}
		});

		parent::run();

		return $this->injector->retrieve('response');
	}
}
