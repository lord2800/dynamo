<?php

require('vendor/autoload.php');

use Dynamo\WebApp,
	Dynamo\HttpRequest,
	Dynamo\HttpResponse;

class SampleApp extends WebApp {
	public function config() {
		$this->injector->provide('logger', function () {
			return function ($msg) { file_put_contents('php://stderr', $msg . PHP_EOL); };
		});

		$this->register(new Dynamo\Middleware\RequestDuration());
		$this->register(new Dynamo\Middleware\CORS(['http://localhost:8080']));
		$this->register(new Dynamo\Middleware\Directory('src'));

		$this->register(function ($logger, HttpRequest $request, HttpResponse $response) {
			yield;
			$logger(sprintf('[%s] url: %s response: %d',
				(new \DateTime())->format(\DateTime::W3C),
				$request->getUrl(),
				$response->getStatus()
			));
		});

		$this->router->get('/', function (HttpResponse $response) {
			$response->setBody('Hello, world!');
		});
	}
}

SampleApp::create()->run()->send();
