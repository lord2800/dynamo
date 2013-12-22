<?php

require('vendor/autoload.php');

use Dynamo\WebApp,
	http\Env\Request,
	http\Env\Response,
	http\Message\Body;

class SampleApp extends WebApp {
	public function config() {
		$this->injector->provide('logger', function () {
			return function ($msg) { file_put_contents('php://stderr', $msg); };
		});

		$this->register(function (Response $response) {
			$start = microtime(true);
			yield;
			$end = microtime(true);
			$response->addHeader('X-Duration', ($end - $start) * 1000);
		});

		$this->register(function (Request $request, Response $response) {
			if($request->getHeader('Origin')) {
				$response->setHeader('Access-Control-Allow-Origin', $request->getHeader('Origin'));
			}
		});

		$this->register(function ($logger, Request $request, Response $response) {
			yield;
			$logger(sprintf('[%s] url: %s response: %d',
				(new \DateTime())->format(\DateTime::W3C),
				$request->getRequestUrl(),
				$response->getResponseCode()
			));
		});

		$this->route('get', '/', function (Response $response) {
			$body = new Body();
			$body->append('Hello, world!');
			$response->setBody($body);
		});
	}
}

SampleApp::create()->run()->send();
